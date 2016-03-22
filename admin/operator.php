<?php

require_once '../config.php';
require_once '../lib.php';

global $baseURL;

?>

<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
<title>OPERATOR - Washington Rotary Club - Radio Auction <?php print $year; ?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $baseURL; ?>css/style.css">
<script type="text/javascript" src="<?php echo $baseURL; ?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $baseURL; ?>js/auction.js?v=1.0"></script>
</head>

<body>
<center>
<h1>Washington Rotary Club - Radio Auction <?php print $year; ?> Operator Administration</h1>
<center>
<img src="<?php echo $baseURL; ?>images/banners/WRC_Logo.jpg">
<br><br>
</center>

<?php

showOpMenu();

$f=$_POST['f'];

if ($f == '') {
	$r=$_GET['r'];
	if ($r != '') {	
		$f=$r;
	} else {
		$f='bv';
	}
}

if ($r == 'bv') {
	print "<span class='alert'>Bid Placed!</span>";
} else if ($r == 'bvf') {
	print "<span class='alert'>Item was already closed.  Bid not accepted!</span>";
} else if ($r == 'bi') {
	print "<span class='alert'>Bid Too Low!</span>";
}



$redirect="";
if ($f == 'pb') {
	$biId=$_POST['id'];
	$bidAmount=$_POST['bidAmount'];
	$bidderPhone=$_POST['bidderPhone'];
	$bidderName=$_POST['bidderName'];
	$biId=intval($biId);

	$stmt = $mysqli->prepare("select i.status, max(bidAmount) from bid b, item i where i.year=? and b.itemId = i.id and i.id=?");
	$stmt->bind_param("ii",$year,$biId);
	$result=$stmt->execute();
	$stmt->bind_result( $status,$maxBidAmount);
	while ( $result=$stmt->fetch()) {
		$stat=$status;
		$max=$maxBidAmount;
	}

	if ($status != '1') {
		print "<span class='alert'>Item was already closed.  Bid not accepted!</span>";
		$f='bvf';
		$redirect="bvf";
	} else if ($bidAmount > $max) {
		$sql='insert into bid (itemId, bidAmount, bidderName,bidderPhone) values (?,?,?,?)';
		$stmt = $mysqli->prepare($sql);
		$stmt->bind_param("idss",$biId,$bidAmount,$bidderName,$bidderPhone);
		$result=$stmt->execute();
		print "<span class='alert'>Bid Placed!</span>";
		$f='bv';
		$redirect="bv";
	} else {
		print "<span class='alert'>Bid Too Low!</span>";
		$f='bi';
		$redirect="bi";
	}
}

if ($f == 'av') {
	showAddView();
} else if ($f == 'bi') {
	showBidItemView();
} else if ($f == 'bv' || $f == 'bvf') {
	showBidView();
} else {
	showCurrentView("Operator Auctions Dashboard");
}

if ($redirect != '') {
	print "<meta http-equiv='refresh' content='0; url=".$baseURL."admin/operator.php?r=$redirect&id=$biId'>";
}

?>
</center>
</body>
</html>
