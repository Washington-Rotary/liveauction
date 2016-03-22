function beginAreYouSure() {
	return confirm('Are you sure you want to begin this auction?');
}
function timerAreYouSure() {
	return confirm('Are you sure you want to start the timer on this auction?');
}
function endAreYouSure() {
	return confirm('Are you sure you want to end this auction?');
}

baseWindowLocation=window.location;
function jumpToItem() {
	var jtf=document.forms["jumpToForm"];
    	id=jtf["id"].value;
	//window.location=baseWindowLocation+"#item"+id;
window.location="http://washingtonrotary.com/liveauction/admin/manage.php#item"+id;
	$('.itemRow').removeClass("highlightItemRow");
	$('.itemRow'+id).addClass("highlightItemRow");
	return false;
}

function lookupByPhone() {
	var bf=document.forms["bid"];
    	var bidderPhone=bf["bidderPhone"].value;
	var bidderName=$.get("http://washingtonrotary.com/liveauction/lookup.php?bidderPhone="+bidderPhone, function( my_var ) {
	if (my_var !='') {
		bf["bidderName"].value=my_var;
	} else {
		alert('Phone number not found, please enter bidder name.');
	}
});
}