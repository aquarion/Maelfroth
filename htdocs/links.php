<?PHP

$title = "Links";
include("header.php");

if(isset($_GET['channel'])){
	$channel = addslashes($_GET['channel']);
} else {
	$channel = '#maelfroth';
}

$datecache = '';

?>


<div id="content">

<h1>Linkamatic</h1>

<p class="intro">A list of links recently mentioned on <?PHP echo $channel ?>. <b>Some of these may not be work-safe</b>, Caveat Clicktor.
<p>

<p>Change the channel: [
	<a href="links.php?channel=%23maelfroth">Maelfroth</a>
	| <a href="links.php?channel=%23empirefroth">Empirefroth</a>
	]
</p>
<?PHP


$tr = '<li><NOBR>%s</NOBR> [%s] %s</li>';

$conf = parse_ini_file("../dbconfig.ini");
$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);

if (isset($_GET['month']) && isset($_GET['year'])){
	$from = intval($_GET['year']).'-'.intval($_GET['month']).'-01T00:00';
	$to = strtotime($from.' +1 Month');
	$from = strtotime($from);
	$query = "select * from urllist where time between ".$from." and  ".$to." and channel = '".$channel."' order by time";	

} else {
	$query = "select * from urllist where channel = '".$channel."' order by time desc limit 60";
}

if (isset($_GET['user'])){
	$query = "select * from urllist where username = \"".addslashes($_GET['user'])."\" and channel = '".$channel."' order by time desc";
}


$result = $db->query($query);

while ($row = $result->fetch(PDO::FETCH_ASSOC)){

	$date = date("D j\<\s\u\p\>S\<\/\s\u\p\> F", $row['time']);
	if ($date != $datecache){
		if ($datecache != ''){
			print "</ul>\n\n";
		}
		
		print "<h2>".$date."</h2>\n\n<ul>";
		$datecache = $date;
		
	}
	
	$time = date("H:i", $row['time']);
	$user = $row['username'];
		
	if($row['checked_status'] != 0 && $row['checked_status'] != 200){
		$time = "<strike>".$time;
		$message = $row['message']."</strike> (Not working (". $row['checked_status']."), last checked ".$row['checked_date'].")";
		printf($tr, $time, $user, $message);
		continue;
	} 
	
	$time = date("H:i", $row['time']);
	$user = $row['username'];
	if ($row['shorturl']){
		$id = uniqid();
		$message = nl2br(ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"$id\" rel=\"nofollow\">\\0</a>", $row['message']));
		$message = str_replace($id, $row['shorturl'], $message);
	} else {
		$message = nl2br(ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" rel=\"nofollow\">\\0</a>", $row['message']));
	}
	printf($tr, $time, $user, $message);
}

?>

</ul>


<h2>Archives:</h2>

<p><?PHP

$year  = 2008;
$month = 3;

$yearcache = '';

$epoch = mktime(0,0,0,$month, 1, $year);


while ($epoch < time()){
	$year = date('Y', $epoch);
	$month = date('m', $epoch);

	if ($year != $yearcache){
		if ($yearcache != ''){
			print "<br />\n";
		}
				
		print "<b>".$year."</b>: ";
		$yearcache = $year;
		
	}
	printf('<a href="links.php?month=%02d&year=%04d&channel=%s">%s</a> ', $month, $year, urlencode($channel), date('F',$epoch));


	//$epoch += 60*60*24*30;

	$epoch = mktime(0,0,0,$month+1, 1, $year);
}

?>
[<a href="links.php">Latest</a>]</p>

</div>

</body>
</html>
