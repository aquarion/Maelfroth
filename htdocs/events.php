<?PHP

$title = "Events";
include("header.php");

$datecache = '';
$filterquery = "";
if(isset($_GET['filter'])){
	$filter = explode("|",$_GET['filter']);
	foreach ($filter as $index => $value){
		$filter[$index] = addslashes($value);
	}

	$filterquery = " AND class in (\"". implode($filter, '", "')."\")";
}

?>


<div id="content">

<h1>Events</h1>

<p class="intro">This is the list of events Lampstand currently knows about. If you want another thing to be on this list, email Aquarion (aquarion at maelfroth fullstop org) with the details. To ask Lampstand how long until something is, say "Lampstand: How long until (event title)?" (Event title is the bit in <i>italics</i>). If you ask "How long until (event type)?" (The bit in bold) it will give you the next event of that class, so "How long until Maelstrom?" will give you the time until the next event.
</p>

<p>Lampstand pulls events from <a href="http://larp.me/events">Larp.me</a>'s masterlist, including player events.</p>

<p>You can subscribe to this as a calendar in <a href="http://www.google.com/calendar/render?cid=apn6om2v7p1d77prbsj2i3aia9gq1f84%40import.calendar.google.com">Google Calendar</a>, or in your own <a href="http://larp.me/events/ical">Calendar software</a> if you have any installed.</p>



</div>

</body>
</html>
