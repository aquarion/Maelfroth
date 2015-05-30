<?PHP

$conf = parse_ini_file("../../dbconfig.ini");
$mysqli = new mysqli("localhost", $conf['username'], $conf['password'], $conf['database']);

function return_response($id, $message, $code = 200){

	$response = array(
		'id' => $id,
		'status' => $code,
		'return' => $message
	);

	header("Content-Type: application/json");
    header('HTTP/1.1 '.$code);
	print json_encode($response);
	die();
}

if ($mysqli->connect_errno) {
    $error = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    return_response($id, $error, 500);
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
    return_response($id, $error, 401);
}


function approve($id){
	global $mysqli;
	$sql = "UPDATE chirpy.mf_quotes set approved = 1 where id = ? limit 1";
	$stmt = $mysqli->prepare($sql) or return_response($id, "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error, 500);
	$stmt->bind_param("i", $id) or return_response($id, "Bind failed: (" . $stmt->errno . ") " . $stmt->error, 500);
	$stmt->execute() or return_response($id, "Execute failed: (" . $stmt->errno . ") " . $stmt->error, 500);

	return_response($id, "Approved");
}

function remove($id){
	global $mysqli;
	$sql = "DELETE from chirpy.mf_quotes where id = ? limit 1";
	$stmt = $mysqli->prepare($sql) or return_response($id, "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error, 500);
	$stmt->bind_param("i", $id) or return_response($id, "Bind failed: (" . $stmt->errno . ") " . $stmt->error, 500);
	$stmt->execute() or return_response($id, "Execute failed: (" . $stmt->errno . ") " . $stmt->error, 500);
	return_response($id, "Denied");
}

if($_POST['action'] == "approve"){
	$id = intval($_POST['id']);
	approve($id);
}
if($_POST['action'] == "remove"){
	$id = intval($_POST['id']);
	remove($id);
}

return_response($id, "What? ".$_POST['action'], 400);