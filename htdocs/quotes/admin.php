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

if (!$authed) {
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


.quote_first {
	border-color: red;
}
.quote_win {
	border-color: green;
}

</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
removeIt = function(data){
	$('#quote-'+data.id).hide("fast", function(){ $('#quote-'+data.id).remove(); runUpdates() });
}
broken = function(data){
	console.log(data);
	result = jQuery.parseJSON(data.responseText)
	window.alert(result.return);
}
runUpdates = function (){
	console.log('Hi');
	updateCount();
	console.log($('.quote')[0]);
	$($('.quote')[0]).addClass('quote_first');
}
updateCount = function(){
	$('#count').html($('.quote:visible').length);
}
keyboardShortcuts = function(e){
	if (e.which == 121){ // y
		id = $('.quote_first a.approve').attr('rel')
		$('.quote_first a.approve').click();
		console.log("Yes to "+id);
	} else if (e.which == 110){ // y
		id = $('.quote_first a.deny').attr('rel')
		$('.quote_first a.deny').click();
		console.log("No to "+id);
	} else {
		console.log('Caught '+e.which);
	}
}
$(document).ready(function(){
	$(".approve").click(function(){
		$.post("review_ajax.php", { id: $(this).attr("rel"), action : "approve" }, removeIt, "json").fail(broken);
		return false;
	});
	$(".deny").click(function(){
		$.post("review_ajax.php", { id: $(this).attr("rel"), action : "remove"}, removeIt, "json").fail(broken);
		return false;
	});
	$('body').keypress(keyboardShortcuts);
	runUpdates();
});
</script>

<div id="count">

</div>
<?PHP

$sql = "select * from chirpy.mf_quotes where approved = 0 order by submitted ";
$stmt = $mysqli->prepare($sql) or die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
$stmt->execute() or die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
$result = $stmt->get_result() or die("Getting result set failed: (" . $stmt->errno . ") " . $stmt->error);

echo '<form action="admin.php" method="POST"><dl>';

while($row = $result->fetch_assoc()){
	printf("<div class='quote' id=\"quote-%s\">", $row['id']);
	echo '<pre>'.htmlentities($row['body']).'</pre>';
	echo "<cite>".$row['notes'].' - '.$row['submitted'].'</cite>';
	printf('<div>
		<a href="#" class="approve" rel="%s">Approve</a> 
		| <a href="#" class="deny" rel="%s">Delete</a></div>', $row['id'], $row['id']);
	echo "</div>";
}

echo "</form>";