<?PHP

$title = "Quotes";
include("../header.php");

?>


<style type="text/css">

.quote pre {
	white-space: pre-wrap;
}

.quote {
	border: 2px solid black;
	margin: 1em;
	padding: .5em;
	background: rgba(255,255,255,.3);
}

.inline {
	display: inline;
}

</style>
<?PHP
define("A_MONTH", 60*60*24*30);

$conf = parse_ini_file("../../dbconfig.ini");
$mysqli = new mysqli("localhost", $conf['username'], $conf['password'], $conf['database']);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    die();
}
?>

<div id="content">
<div>
[ <a href="add.php">Add a new quote</a> ]
&mdash;
<form class="inline" method="GET" action="/quotes">
	Search 
	<input type="search" name="query" value="<?PHP echo isset($_GET['query']) ? $_GET['query'] : '' ?>">
	<button>X</button>
</form>

</div>


<?PHP

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perpage = 50;
$offset  = ($page - 1) * $perpage;

$search = false;
$title = "Recent Quotes";

$where = 'approved = 1';
if(isset($_GET['query'])){
	$where .= " and body like ?";
	$search = "%".$_GET['query']."%";
	$title = "Search for <q>".$_GET['query'].'</q>';
}

$sql = "select count(*) as count from chirpy.mf_quotes where $where ";

if(isset($_GET['random'])){
	$sql .= 'order by RAND()';
} else {
	$sql .= 'order by submitted desc';
}

$stmt = $mysqli->prepare($sql) or die("Prepare1 failed: (" . $mysqli->errno . ") " . $mysqli->error.'<pre>'.$sql.'</pre>');
if($search){
	$stmt->bind_param("s", $search);
}
$stmt->execute() or die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result() or die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);


$row = $result->fetch_assoc();

$count = $row['count'];
$maxpages = intval($count/$perpage) + 1;

$sql = "select * from chirpy.mf_quotes where $where order by submitted desc limit 50 offset ?";
$stmt = $mysqli->prepare($sql) or die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
if($search){
	$stmt->bind_param("si", $search, $offset);
} else {
	$stmt->bind_param("i", $offset);
}
$stmt->execute() or die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result() or die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);


echo '<h2>'. $title .'</h2>';
if ($offset){
	echo '<p>Page '. $page .' of '.$maxpages.'</p>';
} 
echo "<dl>";

while($row = $result->fetch_assoc()){
	echo "<div class='quote'>";
	echo '<pre>'.htmlentities($row['body']).'</pre>';
	echo "<cite>".$row['notes'].' - '.$row['submitted'].'</cite>';
	echo "</div>";
}

?></dl><?php

$prefix = "index.php?";

if($search){
	$prefix .= "query=".urlencode($_GET['query'])."&amp;page=";
} else {
	$prefix .= "page=";
}

?>
<p>
<?PHP if($page != 1){ ?>
<a href="<?PHP echo $prefix."1" ?>">&lt;&lt; First</a>
<?PHP } 
if($page > 2 ){ ?>
	<a href="<?PHP echo $prefix.($page-1); ?>">&lt; Back</a> 
<?PHP } ?>

Page <?PHP echo $page ?> of <?PHP echo intval($count/$perpage)+1;

if (($page+1) < $maxpages) { ?>
	<a href="<?PHP echo $prefix.($page+1); ?>">Next &gt;</a>
<?PHP } 
if($page < $maxpages && $maxpages > 1){ ?> 
 <a href="<?PHP echo $prefix.($maxpages); ?>">Last &gt;&gt;</a>
<?PHP } ?>
</p>

</div>
