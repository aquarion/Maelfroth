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
[ <a href="/quotes">Recent Quotes</a> | <a href="/quotes?random=true">Random Quotes</a> | <a href="add.php">Add a new quote</a> ]
&mdash;
<form class="inline" method="GET" action="/quotes">
	Search 
	<input type="search" name="query" value="<?PHP echo isset($_GET['query']) ? $_GET['query'] : '' ?>">
	<button>X</button>
</form>

</div>


<?PHP

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perpage = 1;
$offset  = ($page - 1) * $perpage;

$search = false;

$where = 'approved = 1 and id = ?';

$sql = "select count(*) as count from chirpy.mf_quotes where $where ";

$stmt = $mysqli->prepare($sql) or die("Prepare1 failed: (" . $mysqli->errno . ") " . $mysqli->error.'<pre>'.$sql.'</pre>');

$stmt->bind_param("d", $_GET['id']);

$stmt->execute() or die("Execute1 failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result() or die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);


$row = $result->fetch_assoc();

$count = $row['count'];
$maxpages = intval($count/$perpage) + 1;

$sql = "select * from chirpy.mf_quotes where $where ";
$stmt = $mysqli->prepare($sql) or die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
$stmt->bind_param("d", $_GET['id']);
$stmt->execute() or die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result() or die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);


echo "<dl>";

while($row = $result->fetch_assoc()){
	echo "<div class='quote'>";
	echo '<pre>'.htmlentities($row['body']).'</pre>';
	echo "<cite>".$row['notes'].' - <a href="/quotes/quote.php?id='.$row['id'].'">'.$row['submitted'].'</a></cite>';
	echo "</div>";
}

?></dl>
</div>
