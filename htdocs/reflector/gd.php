<?PHP

if(isset($_GET['url'])){
	$file = urldecode($_GET['url']);
} else {
	$file = $_SERVER['QUERY_STRING'];
}
$filename = '/reflectedImage-'.md5($file);

header('X-Reflector-Filename: '.$filename);


define("ALPHAMAX", 127);
define("ALPHASTART", 50);
define("CACHEDIR", "/tmp/");
//define("CACHEDIR", '');

if(defined("CACHEDIR")){
	if(!is_readable(CACHEDIR)){
		error_log("[AQ-TRANSLUCENATOR] - Cannot read cachedir ".CACHEDIR);
	} elseif(!isset($_GET['regen'])) {
		if(file_exists(CACHEDIR.$filename)){
			header("Content-Type: image/jpeg");
			readfile(CACHEDIR.$filename);
			die();
		}
	}
}


if(isset($_GET['type'])){
	$type = $_GET['type'];
} else {
	$type = substr($file, -3, 3);
}

if (isset($_GET['gravatar_id'])){
	$type = "jpg";
}

/*switch($type){

	case "jpg":
		$original = imagecreatefromjpeg ($file) || passThoughImage($file);
		break;


	case "png":
		$original = imagecreatefrompng ($file);
		break;

	default:
		error_log("[AQ-TRANSLUCENATOR] - Couldn't open file ".CACHEDIR.$filename);
		die("What filetype?");
		header("location: ".$file);
		die();
}*/

$file = file_get_contents($file);
$original = imagecreatefromstring($file);


$w = imagesx($original);
$h = imagesy($original)-3;

function passThoughImage($file){
		header("location: ".$file);
		die();
	
}


////////////////////////////////// Build the fade

$fadeh = $h/2;

$fade = imagecreatetruecolor($w, $fadeh);

//ImageAlphaBlending($fade, false); // Uncomment this to break IE5 and blend to alpha, rather than to black.

imagesavealpha($fade, true);
$clear = imagecolorallocatealpha($fade, 0, 0, 0, 255);
imagefill($fade, 0, 0, $clear);

/*for ($y = 0; $y < $fadeh; $y++) {
		imagecopy($fade, $image, 0, $y, 0, $h - $y - 1, $w, 1);
}*/

//$color = imagecolorallocatealpha();



$jump = (ALPHAMAX-ALPHASTART)/$fadeh;
$alpha = ALPHASTART;

for ($y = 0; $y < $fadeh; $y++) {
		$alpha+=$jump;
		//echo $alpha."<br/>";
	for ($x = 0; $x < $w; $x++){

		$rgb = imagecolorat($original, $x, ($h-$y));
		$currentalpha = ($rgb & 0x7F000000) >> 24;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		if($currentalpha > $alpha){
			$newalpha = $currentalpha;
		} else {
			$newalpha = $alpha;
		}
		$newcolor = imagecolorallocatealpha($fade, $r, $g, $b, $newalpha);
		//$newcolor = ();
		imagesetpixel($fade, $x, $y, $newcolor);
	}
}

////////////////////////////////////////////////////////////////// Build image+reflection;



$image = imagecreatetruecolor($w, $h+$fadeh);
ImageAlphaBlending($image, false);
imagesavealpha($image, true);


$clear = imagecolorallocatealpha($fade, 0, 0, 0, 127);


imagefill($image, 0, 0, $clear);

imagecopy ($image, $original, 0, 0,  0, 0, $w, $h     );
imagecopy ($image, $fade,     0, $h, 0, 0, $w, $fadeh );

//$img = imageflip($img);

if (headers_sent()){
	echo "<hr />Not sending image, headers already sent.";
} else {
	header("Content-Type: image/jpeg");
	imagejpeg($image, null, 100);

	//header("Content-Type: image/png");
	//imagepng($image, null, 7);

	if(defined("CACHEDIR")){
		if(is_writable(CACHEDIR)){
			//imagepng($image, CACHEDIR.$filename, 9);
			imagejpeg($image, CACHEDIR.$filename, 100);
		} else {
			error_log("[AQ-TRANSLUCENATOR] - Couldn't write cache ".CACHEDIR.$filename.' for '.$file);
		}
	}
}
?>
