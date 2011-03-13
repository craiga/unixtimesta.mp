
var Timestamp = Timestamp || {};

Timestamp.getTimestamp = function() {
	return parseInt($("span#timestamp").text(), 10);
};

Timestamp.injectResult = function(result) {
	$("p#utctime").after("<p id=\"localtime\" class=\"time\" style=\"display: none;\">That's <span class=\"date\">" + result + "</span> in your local time zone.</p>");
	$("p#localtime").show("slow");
};

$(function() {
	var timestamp = Timestamp.getTimestamp();
	var date = new Date(timestamp * 1000);
	Timestamp.injectResult(date.toString("dddd d MMMM yyyy h:mm:ss tt"));
});
