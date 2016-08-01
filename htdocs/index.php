<?PHP

$title = "Gallery";
include("header.php");

define("A_MONTH", 60*60*24*30);

$conf = parse_ini_file("../dbconfig.ini");
$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);

include("photographers.php"); # Array of photography credits

?>

<div id="content">

<p class="intro">Being a concise and possibly even accurate collection of residents of the world beyond the Maelfroth.</p>
<p>

<?PHP

if(!$file = file_get_contents('http://api.larp.me/cabal/maelfroth/')){
	die("Can't open data file");
}

$people = json_decode($file);


foreach($people->members as $person){


	if($person->card->image){
		$photocredit = $person->card->image->credit;
	} else {
		$photocredit = false;
	}

	if (isset($photographers[$photocredit])){
		$photocredit = sprintf('<a href="%1$s" title="Photo taken by %2$s">%2$s</a>', $photographers[$photocredit]['url'], $photographers[$photocredit]['name']);
	}

	$imageencoded = $person->card->image ? urlencode($person->card->image->url) : '';
	$image = $person->card->image ? $person->card->image->url : '';

	$displayname = preg_replace('/\W/', ' ', strtr($person->name, "_-", "  "));	
	$username    = preg_replace('/\W/', ' ', strtr($person->name, "_-", "  "));	

	$char_head = ucwords($person->card->type);
	if($person->card->type == 'character'){
		$character   = $person->card->name;
	} else {
		$character   = $person->card->title;
	}
	$character = "<a href=\"https://larp.me".$person->card->url."\">".$character."</a>";
	
	$tagline     = 'at <a href="https://larp.me'.$person->card->system->url.'">'.$person->card->system->name."</a>";
	$physrep     = $username;

	$card =  '
<div class="contactcard" style="background-image: url(\'nameback.php?url='.$imageencoded.'\'); position: relative;" id="'.$username.'">
	<a href="'.$image.'" class="gallerylink">&nbsp;</a>
	<h2 class="scripted"><a href="#'.$username.'">'.$displayname.'</a></h2>
	<dl>
		<dt>Character</dt>
		<dd>'.$character.' <br/><i>'.$tagline.'</i></dd>

		';
if($photocredit){
	$card .= '<dt class="photocredit">Photo Credit</dt>
		<dd class="photocredit">'.$photocredit.'</dd>';
}

$card .='	</dl>
</div>';

	$delta = 0;

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

</div>
