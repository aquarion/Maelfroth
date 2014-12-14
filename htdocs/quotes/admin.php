<?PHP


$conf = parse_ini_file("../../dbconfig.ini");
$mysqli = new mysqli("localhost", $conf['username'], $conf['password'], $conf['database']);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    die();
}


$authed = false;

if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){
	$authed = false;
} else {
	$u = $_SERVER['PHP_AUTH_USER'];
	$p = $_SERVER['PHP_AUTH_PW'];
	if($u == $conf['admin_user'] && $p = $conf['admin_password']){
		$authed = true;
	} else {
		$authed = false;
	}
}

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Review"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}


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

</style><?PHP

if(isset($_POST['approve'])){
	$approve = array_keys($_POST['approve']);
	$sql = "UPDATE chirpy.mf_quotes set approved = 1 where id in (".implode(",", $approve).")";
	$mysqli->query($sql) or die("Query failed: (" . $mysqli->errno . ") " . $mysqli->error);
}

if(isset($_POST['delete'])){
	$delete = array_keys($_POST['delete']);
	$sql = "DELETE from chirpy.mf_quotes where id in (".implode(",", $delete).")";
	$mysqli->query($sql) or die("Query failed: (" . $mysqli->errno . ") " . $mysqli->error);
}



$sql = "select * from chirpy.mf_quotes where approved = 0 order by submitted ";
$stmt = $mysqli->prepare($sql) or die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
$stmt->execute() or die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result() or die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);

echo '<form action="admin.php" method="POST"><dl>';

while($row = $result->fetch_assoc()){
	echo "<div class='quote'>";
	echo '<pre>'.htmlentities($row['body']).'</pre>';
	echo "<cite>".$row['notes'].' - '.$row['submitted'].'</cite>';
	echo '<br/><input type="checkbox" name="approve['.$row['id'].']" checked="checked"/> Approve';
	echo '<br/><input type="checkbox" name="delete['.$row['id'].']" /> Delete <br/>';
	echo "</div>";
}

echo "<input type=submit></form>";