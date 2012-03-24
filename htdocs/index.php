<?PHP

$title = "Gallery";
include("header.php");

define("A_MONTH", 60*60*24*30);

$conf = parse_ini_file("../dbconfig.ini");
$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);

?>

<div id="content">

<p class="intro">Being a concise and possibly even accurate collection of residents of the world beyond the Maelfroth. For inclusion, supply a name, physrep identifier, character and associated graphical representation by metamagical transfer to the address "<B>gallery</B>" care of the <B>Maelfroth</B> <B>org</B>anisation. This is also the place to send replacement photos and suggestions for taglines for characters (Not physreps). Taglines are approved and modified based on what the archivist finds most amusing. He has a twisted sense of humour. Comments and objections to the address above.
<p>

<?PHP

if(!$file = file_get_contents('gallery.tsv')){
	die("Can't open data file");
}


include("photographers.php"); # Array of photography credits

$people = explode("\n", $file);

natcasesort($people);

define("DAYS", 60*60*24);

$longlost = array();
$edgeoftheworld = array();
$lastshown = "";

echo "<h1>Operators</h1>";
$saidop = false;

foreach($people as $person){
	if($person[0] == '#' || empty($person)){
		continue;
	} 
	$data = explode("\t", $person);

	$displayname = preg_replace('/\W/', ' ', strtr($data[0], "_-", "  "));	
	$filename    = strtolower(preg_replace('/\W/', '', $data[0]));

	$username    = $data[0];
	$character   = $data[1];
	$tagline     = $data[2];
	$physrep     = $data[3];

	if(isset($data[4])){
	$photocredit = $data[4];
	} else {
	$photocredit = false;
	}
	if($username[0] != "@" && !$saidop){
		echo "<h1 style=\"clear: both; padding-top: 1em;\">Frothians</h1>";
		$saidop = true;
	}

	/// Populate LastSeen:

	$q = "select * from lastseen where username = \"%s\" or username LIKE \"%s\" order by last_seen desc limit 1";
	
	$sql = sprintf($q, $filename,$filename."|%");
	
	$result = $db->query($sql);
	if(!$result){
		print_r($db->errorInfo());
	}

	$row = $result->fetch(PDO::FETCH_ASSOC);

	$lastseen = $row['last_seen'];
	
	$delta = time() - $lastseen;
	if ($delta < 7*DAYS){
		$tfmt ='l \a\t H:i';
	} elseif ($delta < 30*DAYS){
		$tfmt = 'j\<\s\u\p\>S\<\/\s\u\p\> M \a\t H:i';
	} else {
		$tfmt = 'j\<\s\u\p\>S\<\/\s\u\p\> M Y';
	}
	
	$lastseen = date($tfmt, $lastseen);
	if (strtolower($row['username']) != $filename){
		$lastseen .= "<br/> as ".$row['username'];
	}

	/*if($delta > 60*60*24*30*6){ // Six months
		$longlost[] = $person;
		continue;			
	}*/

	if (isset($photographers[$photocredit])){
		$photocredit = sprintf('<a href="%1$s" title="Photo taken by %2$s">%2$s</a>', $photographers[$photocredit]['url'], $photographers[$photocredit]['name']);
	}

	$card =  '
<div class="contactcard" style="background-image: url(\'nameback.php?name='.$username.'\'); position: relative;" id="'.$username.'">
	<a href="/gallery/'.$filename.'.jpg" class="gallerylink">&nbsp;</a>
	<h2 class="scripted"><a href="#'.$username.'">'.$displayname.'</a></h2>
	<dl>
		<dt>Character</dt>
		<dd>'.$character.' <br/><i>'.$tagline.'</i></dd>

		<dt>Physrep</dt>
		<dd>'.$physrep.'</dd>

		<dt>Alias on #maelfroth</dt>
		<dd><a href="quotes/index.cgi?action=search&query='.$filename.'" title="Quotes in the database for this person">'.$username.'</a></dd>
		
		<dt>Last Seen</dt>
		<dd>'.$lastseen.'</dd>

		';
if($photocredit){
	$card .= '<dt class="photocredit">Photo Credit</dt>
		<dd class="photocredit">'.$photocredit.'</dd>';
}

$card .='	</dl>
</div>';


	if($delta > A_MONTH * 12){
		$edgeoftheworld[] = $displayname;
	} elseif ($delta > A_MONTH * 6) { // Six months
		$longlost[] = $card;
	} else {
		echo $card;
		$lastshown = $displayname;
	}


}

?>

<!--
-->


<div class="contactcard" style="background-image: url('nameback.php?name=Your%20Photo%20Here');">
	<h2 class="scripted">Your Photo Here</h2>

<div class="words">
	<p>(If it's after <?PHP echo $lastshown?> in the alphabet, which seems unlikely)</p>

	<p>To appear in this gallery: (a) Be on #maelfroth, (b) Send a photo and details to gallery@maelfroth.org</p>
</div>
</div>

<hr style="clear: both;">

<h1>Folks we haven't seen in a while:</h1>

<?PHP

foreach($longlost as $card){
	echo $card;
}
?>

<hr style="clear: both;">

<h2>The Lost</h2>

<?PHP 
	$last = array_pop($edgeoftheworld);
	echo implode(", ", $edgeoftheworld)." &amp; ".$last;
?>

</div>
