<?php

require_once 'config.php';
require_once 'lib.php';

?>

<html>
<head>
<title>Washington Rotary Club - Radio Auction <?php print $year; ?></title>

<meta http-equiv="refresh" content="<?php print $refreshSeconds; ?>; url="<?php print $baseURL; ?>">
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
<center>
<!--<h1>Washington Rotary Club - Radio Auction <?php print $year; ?></h1>-->
<center>
<img src="images/banners/WRC_Logo.jpg">
<br><br>
Call to Bid: 888-801-6554&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Listen Live: <a target="kfav" href="http://streamdb2web.securenetsystems.net/v5/KFAV">KFAV</a> or <a target="klpw" href="http://lightningstream.surfernetwork.com/Media/player/detect.aspx?call=KLPW&title=&file=&gatewaySet=">KLPW</a>
<br><br>
<?php showBanner(); ?>
</center>
<br>
<table border='1' width='70%'>
<?php

# item:
# id, year, auctionItem, itemName, itemDescription, donorName, donorAd, itemImageName, itemStatus
# bid
# id, itemId, bidAmount, bidderName
# banner
# id, imageName, link, views

# banner logic
$sql="select b.id, b.imageName, b.link from banner b order by b.views asc limit 1";

#$bidId,$imageName,$link

$sql="update banner b set views = views+1 where b.id = $bidId";


# item listing logic
#$stmt = $mysqli->prepare("select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId and b.bidAmount = (select max(b2.bidAmount) bAmt from bid b2 where b2.itemId=i.id group by b2.id order by bAmt desc limit 1)) where i.year=? and status = 1 order by i.auctionItem asc");
$stmt = $mysqli->prepare("select i.id, i.timer, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId) where i.year=? and status = 1 order by i.auctionItem asc, b.bidAmount desc");
$stmt->bind_param("i",$year);
$result=$stmt->execute();
$stmt->bind_result( $id,$timer,$auctionItem,$itemName,$itemValue,$itemDescription,$donorName,$donorAd,$imageName,$bidderName,$maxBidAmount);
#print "<tr><th>Item #</th><th>Item Value</th><th>Current Bid</th><th>Current High Bidder</th><th>Item Name</th><th>Description</th><th>Donor</th>";
print "<tr><th>Item #</th><th>Item Value</th><th>Current Bid</th><th>Current High Bidder</th><th>Item Name</th><th>Donor</th>";
$lastId='';
while ( $result=$stmt->fetch()) {
	if ($lastId == $id) {
		continue;
	}
        if ($timer != '' && $timer != '0000-00-00 00:00:00') {
                $timerImg="<img src='images/timer.png' width='20'>&nbsp;";
        } else {
                $timerImg='';
        }
	if ($maxBidAmount == '') {
		$maxBidAmount = "<span class='alert'>No Bid</span>";
	} else {
		$maxBidAmount = '$'.$maxBidAmount;
	}
	$itemValue='$'.$itemValue;
	print "<tr><td>$auctionItem</td><td>$itemValue</td><td>$maxBidAmount</td><td>$bidderName</td><td>$timerImg";
	if ($imageName != '') {
		print "<a target='_new' href='images/items/$year/$imageName'><img  style='float: right; width: 20px; margin-right: 6px;' src='images/picture.png' border='0'></a>";
	}
	print "$itemName</td>";
	#print "<td>$itemDescription</td>";
	#print "<td><b>$donorName</b> - $donorAd</td></tr>";
	print "<td><b>$donorName</b></td></tr>";
	$lastId=$id;
}


?>
</table>
</center>
</body>
</html>