<?PHP

$title = "Quotes";
include("../header.php");

define("A_MONTH", 60*60*24*30);

$conf = parse_ini_file("../../dbconfig.ini");
$mysqli = new mysqli("localhost", $conf['username'], $conf['password'], $conf['database']);

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    die();
}
?>
<style type="text/css">

form li {
	list-style: none;
	line-height: 2em;
}

form label {
	display: table;
	width: 5em;
	float: left;
}

.success {
	border: 1px solid darkgreen;
	color: darkgreen;
	padding: 1em;
	width: 15em;
}

.indented {
	margin-left: 7.5em;
}

</style>

<div id="content">

<h2>Add new quote</h2>

<?PHP

/*
+-----------+---------------------+------+-----+-------------------+----------------+
| Field     | Type                | Null | Key | Default           | Extra          |
+-----------+---------------------+------+-----+-------------------+----------------+
| id        | int(10) unsigned    | NO   | PRI | NULL              | auto_increment |
| body      | text                | NO   |     | NULL              |                |
| notes     | text                | YES  |     | NULL              |                |
| rating    | int(11)             | NO   |     | 0                 |                |
| votes     | int(10) unsigned    | NO   |     | 0                 |                |
| submitted | timestamp           | NO   |     | CURRENT_TIMESTAMP |                |
| approved  | tinyint(1) unsigned | NO   |     | 0                 |                |
| flagged   | tinyint(1) unsigned | NO   |     | 0                 |                |
| score     | double unsigned     | NO   |     | 1                 |                |
+-----------+---------------------+------+-----+-------------------+----------------+
*/
if(isset($_POST['submit'])){
	
	$sql = "INSERT INTO chirpy.mf_quotes (body, notes) VALUES (?, ?)";

	$stmt = $mysqli->prepare($sql) or die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
	
	$stmt->bind_param("ss", $_POST['quote'], $_POST['comment']);
	
	$stmt->execute() or die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
	
	print '
		<div class="success">
		<p>Thank you for your quote.</p>
		<p>It has been appended to</p>
		<p>the queue for judgement</p>
		</div>
		<p>[<a href="index.php">Return to Quotes</a>]</p>
		';
} else {


?>

	<form method="POST" action="add.php">
	<ul>
		<li>
			<label for="quote">Quote</label>
			<textarea cols="100" rows="12" id="quote" name="quote" required="true"></textarea>
		</li>
		<li>
			<label for="comment">Comment</label>
			<input name="comment" id="comment" type="text"/>
		</li>
		<li>
			<label for="submit">&nbsp;</label>
			<input name="submit" id="submit" type="submit" value="Submit for Judgement" />
		</li>
	</ul>
	</form>

<?PHP } ?>
</div>

</body></html>