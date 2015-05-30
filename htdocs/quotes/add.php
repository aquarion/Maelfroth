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

.failure {
	border: 1px solid red;
	color: red;
	padding: 1em;
	width: 15em;
}

.indented {
}

.captchabox {
	margin-left: 5em;
	 height: 5em; 
}

textarea {
	background-color: rgba(255,255,255, .1);
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


function recaptcha(){
	global $conf;
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$fields = array(
		'secret' => $conf['recaptcha_secret'],
		'response' => $_POST['g-recaptcha-response']
	);


	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,True);


	//execute the post
	$result = curl_exec($ch);

	//close the connection
	curl_close($ch);

	return json_decode($result);
}

$form = True;

if(isset($_POST['submit'])){

	$recaptcha = recaptcha();

	if(!isset($_POST['quote']) or !$_POST['quote']){
		print '
			<div class="failure">
			<p>Please try that again</p>
			<p>To say nothing is wisdom</p>
			<p>But not quotable</p>
			</div>
			';
	} elseif(!$recaptcha->success){
		print '
			<div class="failure">
			<p>You have failed my test</p>
			<p>That judges humanity</p>
			<p>Try again, toaster</p>
			</div>
			';
	} else {
	
		// SQL
		$sql = "INSERT INTO chirpy.mf_quotes (body, notes) VALUES (?, ?)";

		$stmt = $mysqli->prepare($sql) or die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
		
		$stmt->bind_param("ss", $_POST['quote'], $_POST['comment']);
		
		$stmt->execute() or die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
		
		$form = False;
		print '
			<div class="success">
			<p>Thank you for your quote.</p>
			<p>It has been appended to</p>
			<p>the queue for judgement</p>
			</div>
			<p>[<a href="index.php">Return to Quotes</a>]</p>
			';
	}
} 

if ($form) {


?>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<form method="POST" action="add.php">
	<ul>
		<li>
			<label for="quote">Quote</label>
			<textarea cols="100" rows="12" id="quote" name="quote" required="true"><?PHP 
				echo isset($_POST['quote']) ? $_POST['quote'] : '' 
			?></textarea>
		</li>
		<li>
			<label for="comment">Comment</label>
			<input name="comment" id="comment" type="text" value="<?PHP echo isset($_POST['comment']) ? $_POST['comment'] : '' ?>"/>
		</li>
		<li style="clear: both;">
			<label for="recapture">Humanity</label>
			<div class="captchabox">
				<div class="g-recaptcha" data-sitekey="<?PHP echo $conf['recaptcha_key']; ?>"></div>
			</div>
		</li>
		<li style="clear: both;">
			<label for="submit">&nbsp;</label>
			<input name="submit" id="submit" type="submit" value="Submit for Judgement" />
		</li>
	</ul>
	</form>

<?PHP } ?>
</div>

</body></html>