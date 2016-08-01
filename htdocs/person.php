<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
<html> 
 <head> 
 <title> [Maelfroth] Gallery </title> 
 
<script type="text/javascript"> 
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script> 
<script type="text/javascript"> 
var pageTracker = _gat._getTracker("UA-68374-4");
pageTracker._initData();
pageTracker._trackPageview();
</script> 
 
<style style="text/css"> 
        @import url('/style.css');
</style> 
</head>
<body>
<?PHP


$conf = parse_ini_file("../dbconfig.ini");
$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);

?>

<div id="content">

<?PHP

if(!$file = file_get_contents('gallery.tsv')){
	die("Can't open data file");
}



include("photographers.php"); # Array of photography credits

$people = explode("\n", $file);

natcasesort($people);

define("DAYS", 60*60*24);

$longlost = array();

foreach($people as $person){


	if($person[0] == '#' || empty($person)){
		continue;
	} 
	$data = explode("\t", $person);
	
	if (strtolower($data[0]) != strtolower($_SERVER['QUERY_STRING'])){
		continue;
	}

	$displayname = preg_replace('/\W/', '', strtr($data[0], "_-", "  "));	
	$filename    = strtolower(preg_replace('/\W/', '', $data[0]));

	$username    = $data[0];
	$character   = $data[1];
	$tagline     = $data[2];
	$physrep     = $data[3];
	$photocredit = $data[4];
	

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
		$tfmt ='D \a\t H:i';
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

	echo '
<div class="contactcard" style="background-image: url(\'nameback.php?name='.$username.'\'); position: relative;">
	<a href="/gallery/'.$thumbnail.'.jpg" class="gallerylink">&nbsp;</a>
	<h2 class="scripted">'.$displayname.'</h2>
	<dl>
		<dt>Character</dt>
		<dd>'.$character.' <br/><i>'.$tagline.'</i></dd>

		<dt>Physrep</dt>
		<dd>'.$physrep.'</dd>

		<dt>Alias on #maelfroth</dt>
		<dd><a href="quotes/index.cgi?action=search&query='.$filename.'" title="Quotes in the database for this person">'.$username.'</a></dd>
		
		<dt>Last Seen</dt>
		<dd>'.$lastseen.'</dd>

		<dt class="photocredit">Photo Credit</dt>
		<dd class="photocredit">'.$photocredit.'</dd>

	</dl>
</div>';


}

?>

</div>
