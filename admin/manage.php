<?php

require_once '../config.php';
require_once '../lib.php';

global $baseURL;

?>

<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
<title>ADMIN - Washington Rotary Club - Radio Auction <?php print $year; ?></title>

<link rel="stylesheet" type="text/css" href="<?php echo $baseURL; ?>css/style.css">
<script type="text/javascript" src="<?php echo $baseURL; ?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $baseURL; ?>js/auction.js?v=1.0"></script>
</head>

<body>
<center>
<h1>Washington Rotary Club - Radio Auction <?php print $year; ?> Administration</h1>
<center>
<img src="<?php echo $baseURL; ?>images/banners/WRC_Logo.jpg">
<br><br>
</center>

<?php

showMenu();

$f=$_POST['f'];

if ($f == 'ri') {
	$riId=$_POST['id'];
	$riId=intval($riId);
	print "<span class='alert'>Auction close</span>";
	$stmt = $mysqli->prepare("update item set status = 2 where id = ?");
	$stmt->bind_param("i",$riId);
	$result=$stmt->execute();
	$f='';
} else if ($f == 'ai') {
	$aiId=$_POST['id'];
	$aiId=intval($aiId);
	print "<span class='alert'>Auction is now live!</span>";
	$stmt = $mysqli->prepare("update item set status = 1 where id = ?");
	$stmt->bind_param("i",$aiId);
	$result=$stmt->execute();

	$biId=$_POST['id'];
	$bidAmount='0.00';
	$bidderName='No Bids';
	$biId=intval($biId);
	$sql='insert into bid (itemId, bidAmount, bidderName) values (?,?,?)';
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ids",$biId,$bidAmount,$bidderName);
	$result=$stmt->execute();

	$f='av';
} else if ($f == 'pb') {
	$biId=$_POST['id'];
	$bidAmount=$_POST['bidAmount'];
	$bidderPhone=$_POST['bidderPhone'];
	$bidderName=$_POST['bidderName'];
	$biId=intval($biId);
	$sql='insert into bid (itemId, bidAmount, bidderName,bidderPhone) values (?,?,?,?)';
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("idss",$biId,$bidAmount,$bidderName,$bidderPhone);
	$result=$stmt->execute();
	print "<span class='alert'>Bid Placed!</span>";
	$f='bi';
}

if ($f == 'av') {
	showAddView();
} else if ($f == 'bi') {
	showBidItemView();
} else if ($f == 'bv') {
	showBidView();
} else {
	showCurrentView("Admin Auctions Dashboard");
	print "<br><br><br>";
	showDailySummary("Daily Auction Summary");
}


?>
</center>
</body>
</html>
