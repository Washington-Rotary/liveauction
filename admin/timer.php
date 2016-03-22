<?php

require_once '../config.php';
require_once '../lib.php';

global $baseURL;


?>

<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
<meta http-equiv="refresh" content="<?php print $refreshSeconds; ?>; url="<?php print $baseURL; ?>timer.php">
<title>TIMER - Washington Rotary Club - Radio Auction <?php print $year; ?></title>

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

showTimerMenu();

$f=$_POST['f'];

if ($f == 'st') {
	$tid=$_POST['id'];
	$stmt = $mysqli->prepare("update item set timer = now() where id = ?");
        $stmt->bind_param("i",$tid);
        $result=$stmt->execute();
	print "<span class='alert'>Timer started!</span>";
	$f='si';
}

if ($f == 'av') {
	showAddView();
} else {
	showTimerView("Timer Auctions Dashboard");
}


?>
</center>
</body>
</html>
