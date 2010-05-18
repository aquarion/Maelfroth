<?PHP

$title = "Gallery";
include("header.php");


$db = new PDO('mysql:dbname=maelfroth', 'lampstand', 'glados');

?>

<div id="content">

<p class="intro">Being a concise and possibly even accurate collection of residents of the world beyond the Maelfroth. For inclusion, supply a name, physrep identifier, character and associated graphical representation by metamagical transfer to the address "<B>gallery</B>" care of the <B>Maelfroth</B> <B>org</B>anisation. This is also the place to send replacement photos and suggestions for taglines for characters (Not physreps). Taglines are approved and modified based on what the archivist finds most amusing. He has a twisted sense of humour. Comments and objections to the address above.
<p>

<?PHP

if(!$file = file_get_contents('gallery.tsv')){
	die("Can't open data file");
}


# 1 - IRC, 2 - Character, 3 - Title, 4 - Physrep, 5 - Image


$template = '

<div class="contactcard" style="background-image: url(\'nameback.php?name=%1$s\'); position: relative;">
	<a href="/gallery/%5$s.jpg" class="gallerylink">&nbsp;</a>
	<h2 class="scripted">%7$s</h2>
	<dl>
		<dt>Character</dt>
		<dd>%2$s <br/><i>%3$s</i></dd>

		<dt>Physrep</dt>
		<dd>%4$s</dd>

		<dt>Alias on #maelfroth</dt>
		<dd><a href="quotes/index.cgi?action=search&query=%5$s" title="Quotes in the database for this person">%1$s</a></dd>
		
		<dt>Last Seen</dt>
		<dd>%6$s</dd>

	</dl>
</div>
';

$people = explode("\n", $file);

natcasesort($people);

define("DAYS", 60*60*24);

$longlost = array();

foreach($people as $person){
	if($person[0] == '#' || empty($person)){
		continue;
	} 
	$person = explode("\t", $person);
	$image = $thumbnail = strtolower(preg_replace('/\W/', '', $person[0]));
	

	$person[7] = preg_replace('/\W/', '', $person[0]);	
	$person[7] = strtr($person[7], "_-", "  ");	
	
	#                 1 - IRC   , 2 - Character, 3 - Title , 4 - Physrep, 5 - Image 
	
	$q = "select * from lastseen where username = \"%s\" or username LIKE \"%s\" order by last_seen desc limit 1";
	
	$sql = sprintf($q, $image,$image."|%");
	
	$result = $db->query($sql);
	if(!$result){
		print_r($db->errorInfo());
	}
	while ($row = $result->fetch(PDO::FETCH_ASSOC)){
		$lastseen = $row['last_seen'];
		
		$delta = time() - $lastseen;
		if ($delta < 7*DAYS){
			$tfmt ='D \a\t H:i';
		} elseif ($delta < 30*DAYS){
			$tfmt = 'j\<\s\u\p\>S\<\/\s\u\p\> M \a\t H:i';
		} else {
			$tfmt = 'j\<\s\u\p\>S\<\/\s\u\p\> M Y';
		}
		
		$lastseen = date($tfmt, $lastseen);
		if (strtolower($row['username']) != $image){
			$lastseen .= "<br/> as ".$row['username'];
		}
		#$lastseen .= "<br/>".date("r", $row['last_seen']);
	}


	if($delta > 60*60*24*30*6){ // Six months
		$person[4] = $lastseen;
		$longlost[] = $person;
		continue;			
	}


	printf($template, $person[0], $person[1]   , $person[2], $person[3], $image, $lastseen, $person[7]);

}

?>

<!--
-->


<div class="contactcard" style="background-image: url('nameback.php?name=Your%20Photo%20Here');">

<div class="words">
	<p>(If it's after Zeke in the alphabet, which seems unlikely)</p>

	<p>To appear in this gallery: (a) Be on #maelfroth, (b) Send a photo and details to gallery@maelfroth.org</p>
</div>
</div>

<hr style="clear: both;">

<h1>Folks we haven't seen in a while:</h1>

<?PHP

foreach($longlost as $person){
	$image = strtolower(preg_replace('/\W/', '', $person[0]));
	#printf($template, $person[0], $person[1]   , $person[2], $person[3] , $image, $lastseen);
	printf($template, $person[0], $person[1]   , $person[2], $person[3],  $image, $person[4], $person[7]);
}
?>

</div>
