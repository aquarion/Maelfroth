<?PHP

$title = "Tenets";
include("header.php");

$datecache = '';
?>


<div id="content">

<h1>Tenets</h1>

<div class="content">

<p class="intro">Maelfroth Tenets, as defined by people using Lampstand.
<p>


<?PHP

$tr = '<li><NOBR>%s</NOBR> [%s] %s</li>';

$conf = parse_ini_file("../dbconfig.ini");
$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);

$query = "select * from define where word like \"%tenet of%\" or word like \"%tenant of%\" or word like \"%tent of%\" order by id";

$result = $db->query($query);

$tenets = array();


function ordinal($one,$two){
	$order = array("first","second","third","fourth","fifth","sixth","seventh","eighth","ninth","tenth");

	#echo "compare $one to $two";

	$one = strtolower($one);
	$two = strtolower($two);

	$one = array_search($one,$order);
	$two = array_search($two,$order);

	if (false === $one){
		$one = 1000;
	} elseif (false === $two){
		$two = 1000;
	}

	if ($one > $two){
		return 1;
	}

	if ($one < $two){
		return -1;
	}

	return 0;

}

while ($row = $result->fetch(PDO::FETCH_ASSOC)){

	$regex = "#^the (.*?) (ten\w*t) of (.*)#i";

	preg_match($regex, $row['word'], $matches);

	#if($matches[2] == "tenant"){
		$set = ucwords($matches[2])."s of ".ucwords($matches[3]);
	#} else {
	#	$set = ucwords($matches[3]);
	#}

	if(!$set){
		$set = "Others";
	} 


	$line = ucwords($matches[1]);
	if(!$line){
		$line = $row['word'];
	}

	$boom = explode(" ", $line, 2);


	if(count($boom) > 1){
		$line = $boom[0];
		$set .= " &ndash; ".$boom[1];
	}

	if(!isset($tenets[$set])){
		$tenets[$set] = array();
	}
	if(!isset($tenets[$set][$line])){
		$tenets[$set][$line] = array();
	}
	$tenets[$set][$line][] = $row;

}

foreach($tenets as $setname => $set){
	$hash = substr(sha1($setname),0,7);
	echo "<h2><a name=\"".$hash."\" href=\"#".$hash."\">#</a> $setname</h2>";
	echo "<dl>\n";


	uksort($set, "ordinal");

	foreach($set as $linename => $line){
		echo "\t<dt>$linename</dt>\n";
		foreach($line as $alternative){
			echo "\t\t".'<dd title="'.$alternative['author'].'">'.$alternative['definition']."</dd>\n";
		}
	}
	echo "</dl>\n\n";

	echo "<!-- ";
	#print_r($set);
	echo "-->";
}

?>

</ul>


</div>

</body>
</html>
