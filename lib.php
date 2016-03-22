<?php

function showAddView() {
global $year;
global $mysqli;
global $baseURL;

$stmt = $mysqli->prepare("select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName from item i where i.year=? and status = 0 order by i.auctionItem asc");
$stmt->bind_param("i",$year);
$result=$stmt->execute();
$stmt->bind_result( $id,$auctionItem,$itemName,$itemValue,$itemDescription,$donorName,$donorAd,$imageName);
print "<h2>Upcoming Items</h2>";
print "<form onSubmit='return jumpToItem();' name='jumpToForm'><input type='hidden' name='ai'>Auction Id: <input type='text' name='id' size='6' maxlength='4'><input type='submit' value='Jump To'></form>";
#print "<div style='height: 800px; background: #abcdef;'>filler</div>";
print "<table border='1' width='70%'>";
print "<tr><th>Action</th><th>Item #</th><th>Item Value</th><th>Item Name</th><th>Description</th><th>Donor</th>";
while ( $result=$stmt->fetch()) {
	print "<tr class='itemRow itemRow$auctionItem'><td><a name='item$auctionItem'></a><form method='post'><input type='hidden' name='f' value='ai'><input type='hidden' name='id' value='$id'><input type='submit' value='Go Live' onclick='return beginAreYouSure();'></form></td><td>$auctionItem</td><td>$itemValue</td><td>$itemName</td><td>";
	if ($imageName != '') {
		print "<a target='_new' href='".$baseURL."images/items/$year/$imageName'><img  style='float: left; width: 20px; margin-right: 6px;' src='".$baseURL."images/picture.png' border='0'></a>";
	}
	print "$itemDescription";
	print "</td><td><b>$donorName</b> - $donorAd</td></tr>\n";
}

}

function showCurrentView($title) {
global $year;
global $mysqli;
global $baseURL;

# item listing logic
#$stmt = $mysqli->prepare("select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId and b.bidAmount = (select max(b2.bidAmount) bAmt from bid b2 where b2.itemId=i.id group by b2.id order by bAmt desc limit 1)) where i.year=? and status = 1 order by i.auctionItem asc");
$stmt = $mysqli->prepare("select i.id, i.timer, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId) where i.year=? and status = 1 order by i.auctionItem asc, b.bidAmount desc");
$stmt->bind_param("i",$year);
$result=$stmt->execute();
$stmt->bind_result( $id,$timer,$auctionItem,$itemName,$itemValue,$itemDescription,$donorName,$donorAd,$imageName,$bidderName,$maxBidAmount);
print "<h2>$title</h2>";

print "<table border='1' width='70%'>";
print "<tr><th>Action</th><th>Item #</th><th>Item Value</th><th>Current Bid</th><th>Current High Bidder</th><th>Item Name</th><th>Description</th><th>Donor</th>";
$lastId='';
while ( $result=$stmt->fetch()) {

        if ($timer != '' && $timer != '0000-00-00 00:00:00') {
		$timerImg="<img src='".$baseURL."images/timer.png' width='20'>&nbsp;";
	} else {
		$timerImg='';
	}
	if ($lastId == $id) {
		continue;
	}
	if ($maxBidAmount == '') {
		$maxBidAmount = "<span class='alert'>No Bid</span>";
	}
	print "<tr><td><form method='post'><input type='hidden' name='f' value='ri'><input type='hidden' name='id' value='$id'><input type='submit' value='Close Auction' onclick='return endAreYouSure();'></form></td><td>$auctionItem</td><td>$itemValue</td><td>$maxBidAmount</td><td>$bidderName</td><td>$timerImg$itemName</td><td>";
	if ($imageName != '') {
		print "<a target='_new' href='".$baseURL."images/items/$year/$imageName'><img  style='float: left; width: 20px; margin-right: 6px;' src='".$baseURL."images/picture.png' border='0'></a>";
	}
	print "$itemDescription";
	print "</td><td><b>$donorName</b> - $donorAd</td></tr>\n";
	$lastId=$id;
}
print "</table>";
}

function showDailySummary($title) {
global $mysqli;
global $year;
$stmt = $mysqli->prepare("select date_format(timer,'%W') wd, count(*) from item where status = 2 and year = $year group by wd");
$result=$stmt->execute();
$stmt->bind_result( $day,$count);
print "<h2>$title</h2>";
print "<table border='1' width='200'>";
print "<tr><th>Day</th><th>Total</th></tr>";
$totalCount=0;
while ( $result=$stmt->fetch()) {
	if ($day == '') {
		$day='Unknown';
	}
	print "<tr><td>$day</td><td>$count</td></tr>\n";
	$totalCount=$totalCount+$count;
}
print "<tr><td>TOTAL</td><td>$totalCount</td></tr>";
print "</table>";

}

function showTimerView($title) {
global $year;
global $mysqli;
global $baseURL;

# item listing logic
#$stmt = $mysqli->prepare("select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId and b.bidAmount = (select max(b2.bidAmount) bAmt from bid b2 where b2.itemId=i.id group by b2.id order by bAmt desc limit 1)) where i.year=? and status = 1 order by i.auctionItem asc");
$stmt = $mysqli->prepare("select i.id, i.timer, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId) where i.year=? and status = 1 order by i.auctionItem asc, b.bidAmount desc");
$stmt->bind_param("i",$year);
$result=$stmt->execute();
$stmt->bind_result( $id,$timer,$auctionItem,$itemName,$itemValue,$itemDescription,$donorName,$donorAd,$imageName,$bidderName,$maxBidAmount);
print "<h2>$title</h2>";

print "<table border='1' width='70%'>";
print "<tr><th>Action</th><th>Item #</th><th>Item Value</th><th>Current Bid</th><th>Current High Bidder</th><th>Item Name</th><th>Description</th><th>Donor</th>";
$lastId='';
while ( $result=$stmt->fetch()) {
	if ($lastId == $id) {
		continue;
	}
        if ($timer != '' && $timer != '0000-00-00 00:00:00') {
		$timerImg="<img src='".$baseURL."images/timer.png' width='20'>&nbsp;";
	} else {
		$timerImg='';
	}
	if ($maxBidAmount == '') {
		$maxBidAmount = "<span class='alert'>No Bid</span>";
	}
	print "<tr><td>";
        if ($timer == '' or $timer == '0000-00-00 00:00:00') {
		print "<form method='post'><input type='hidden' name='f' value='st'><input type='hidden' name='id' value='$id'><input type='submit' value='Start Timer' onclick='return timerAreYouSure();'></form>";
	}
	print "</td><td>$auctionItem</td><td>$itemValue</td><td>$maxBidAmount</td><td>$bidderName</td><td>$timerImg$itemName</td><td>";
	if ($imageName != '') {
		print "<a target='_new' href='".$baseURL."images/items/$year/$imageName'><img  style='float: left; width: 20px; margin-right: 6px;' src='".$baseURL."images/picture.png' border='0'></a>";
	}
	print "$itemDescription";
	print "</td><td><b>$donorName</b> - $donorAd</td></tr>\n";
	$lastId=$id;
}
}

function showBidView() {
global $year;
global $mysqli;
global $baseURL;

# item listing logic
$stmt = $mysqli->prepare("select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId and b.bidAmount = (select max(b2.bidAmount) bAmt from bid b2 where b2.itemId=b.itemId group by b2.id order by bAmt desc limit 1)) where i.year=? and status = 1 order by i.auctionItem asc");
$stmt = $mysqli->prepare("select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount, i.timer from item i left join (bid b) on (i.id = b.itemId) where i.year=? and status = 1 order by i.auctionItem asc, b.bidAmount desc");
$stmt->bind_param("i",$year);
$result=$stmt->execute();
$stmt->bind_result( $id,$auctionItem,$itemName,$itemValue,$itemDescription,$donorName,$donorAd,$imageName,$bidderName,$maxBidAmount, $timer);
print "<h2>Place Bid</h2>";

$lastId='';
$itemCount=1;
print "<table><tr><td width='50%' valign='top'>";
print "<table border='1' width='100%'>";
print "<tr><th>Item #</th><th>Action</th><th>Item Value</th><th>Current Bid</th><th>Current High Bidder</th><th>Item Name</th></tr>";
while ( $result=$stmt->fetch()) {
	if ($lastId==$id) {
		continue;
	}

	if ($timer != '' && $timer != '0000-00-00 00:00:00') {
		$timerImg="<img src='".$baseURL."images/timer.png' width='20'>&nbsp;";
	} else {
		$timerImg='';
	}

	if ($itemCount == 9) {
		print "</table></td><td width='50%' valign='top'>";
		print "<table border='1' width='100%'>";
		print "<tr><th>Item #</th><th>Action</th><th>Item Value</th><th>Current Bid</th><th>Current High Bidder</th><th>Item Name</th></tr>";
	}

	if (strlen($itemName) > 100) {
		$itemName=substr($itemName,0,100)."...";
	}
	if ($maxBidAmount == '') {
		$maxBidAmount = "<span class='alert'>No Bid</span>";
	}
	print "<tr><td>$timerImg$auctionItem</td><td><form method='post'><input type='hidden' name='f' value='bi'><input type='hidden' name='id' value='$id'><input type='submit' value='Place Bid'></form></td><td>$itemValue</td><td>$maxBidAmount</td><td>$bidderName</td><td>$itemName";
	if ($imageName != '') {
		print "<a target='_new' href='".$baseURL."images/items/$year/$imageName'><img  style='float: right; width: 20px; margin-right: 6px;' src='".$baseURL."images/picture.png' border='0'></a>";
	}
	print "</td></tr>\n";
	$lastId=$id;
	$itemCount++;
}

print "</td></tr></table>";
print "</td></tr></table>";


}

function showBidItemView() {
global $year;
global $mysqli;
global $baseURL;

$biId=$_POST['id'];
if ($biId == '') {
$biId=$_GET['id'];
}

#print "biId = $biId; year= $year";

#$sql="select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId and b.bidAmount = (select max(b2.bidAmount) bAmt from bid b2 where b2.itemId=i.id group by b2.id order by bAmt desc limit 1)) where i.year=? and i.id = ? and status = 1 order by i.auctionItem asc";

$sql="select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId) where i.year=? and i.id= ? and status = 1 order by i.auctionItem asc, b.bidAmount desc limit 1";

#print "<br>SQL: $sql";
# item listing logic
$stmt = $mysqli->prepare($sql);
#"select i.id, i.auctionItem, i.itemName, i.itemValue, i.itemDescription, i.donorName, i.donorAd, i.imageName, b.bidderName, b.bidAmount from item i left join (bid b) on (i.id = b.itemId and b.bidAmount = (select max(b2.bidAmount) bAmt from bid b2 where b2.itemId=i.id group by b2.id order by bAmt desc limit 1)) where i.year=? and i.id = ? and status = 1 order by i.auctionItem asc");
$stmt->bind_param("ii",$year,$biId);
$result=$stmt->execute();
$stmt->bind_result( $id,$auctionItem,$itemName,$itemValue,$itemDescription,$donorName,$donorAd,$imageName,$bidderName,$maxBidAmount);
print "<h2>Place Bid</h2>";

while ( $result=$stmt->fetch()) {
	if ($bidderName == '') {
		$bidderName = "<span class='alert'>No Bid</span>";
	}
	if ($maxBidAmount == '') {
		$maxBidAmount = "<span class='alert'>No Bid</span>";
	}
	print "<form name='bid' method='post'><input type='hidden' name='f' value='pb'>";
	print "<table>";
	print "<tr><td><b>Item #:</b></td><td>$auctionItem</td></tr>\n";
	print "<tr><td><b>Item Name:</b></td><td>$itemName</td></tr>\n";
	print "<tr><td><b>Item Value:</b></td><td>$itemValue</td></tr>\n";
	print "<tr><td><b>Item Description:</b></td><td>";
	if ($imageName != '') {
		print "<a target='_new' href='".$baseURL."images/items/$year/$imageName'><img  style='float: left; width: 20px; margin-right: 6px;' src='".$baseURL."images/picture.png' border='0'></a>";
	}
	print "$itemDescription";
	print "</td></tr>\n";
	print "<tr><td><b>Current High Bidder:</b></td><td>$bidderName</td></tr>\n";
	print "<tr><td><b>Current Bid:</b></td><td>$maxBidAmount</td></tr>\n";
	print "<tr><td colspan='2'><hr></td></tr>\n";
	print "<tr><td><b>Bidder Phone:</b></td><td><input type='text' name='bidderPhone' size='15' maxlength='10'><input type='button' value='Look Up' onClick='lookupByPhone();'></td></tr>\n";
	print "<tr><td><b>Bidder Name:</b></td><td><input type='text' name='bidderName' size='50' maxlength='40'></td></tr>\n";
	print "<tr><td><b>New Bid:</b></td><td><input type='text' name='bidAmount' size='14' maxlength='10'></td></tr>\n";


	print "</table>";

	print "<input type='hidden' name='id' value='$id'><input type='submit' value='Place Bid'></form>";
}
}

function nameLookup() {
	$bidderPhone=$_GET['bidderPhone'];
	global $mysqli;
	$stmt = $mysqli->prepare("select b.bidderName from bid b where bidderPhone=? order by bidDate desc limit 1");
	$stmt->bind_param("s",$bidderPhone);
	$result=$stmt->execute();
	$stmt->bind_result( $bidderName);
	while ( $result=$stmt->fetch()) {
		print "$bidderName";
	}
}

function showBanner() {
	global $year;
	global $mysqli;
	global $baseURL;
	$stmt = $mysqli->prepare("select b.id, b.imageName, b.link from banner b order by views asc limit 1");
	$result=$stmt->execute();
	$stmt->bind_result( $id,$imageName, $link);
	while ( $result=$stmt->fetch()) {
		if ($link != '') {
			print "<a target='new$id'  href='$link'>";
		}
		print "<img src='".$baseURL."images/banners/$imageName' height='100'>";
		if ($link != '') {
			print "</a>";
		}
	}
	$hour=date('H');
	if ($hour > 15 && $hour < 21) {
		$stmt = $mysqli->prepare("update banner set views=views+1 where id = ?");
		$stmt->bind_param("i",$id);
		$result=$stmt->execute();
	}
}

function showMenu() {
print "<table><tr>";
print "<td><form method='post'><input type='hidden' name='f' value=''><input type='submit' value='Show Live Items'></form></td>";
print "<td><form method='post'><input type='hidden' name='f' value='av'><input type='submit' value='Make Items Live'></form></td>";
print "</tr></table>";
}
function showOpMenu() {
print "<table><tr>";
#print "<td><form method='post'><input type='hidden' name='f' value='bv'><input type='submit' value='Place Bid'></form></td>";
print "</tr></table>";
}
function showTimerMenu() {
}