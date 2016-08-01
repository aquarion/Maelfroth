<?php

$name = isset($_GET['name']) ? $_GET['name'] : "Aquarion";
$imagename = strtolower(preg_replace('/\W/', '', $name));
$imagefile = 'gallery/'.$imagename.'.jpg';

if (!file_exists($imagefile)){
	$imagefile = 'gallery/empty.jpg';
}

$imagefile = 'http:'.$_GET['url'];
#$imagefile = str_replace(" ", "%20", $imagefile);

$size = 80;
$font = 'font/renaissance.otf';
#$font = "/var/www/hosts/archive/racer.aqxs.net/trunk/tests/Zend/Pdf/_fonts/Vera.ttf";

$name = preg_replace('/\W/', '', $name);

define('CACHEDIR', '/tmp');
define("ALPHAMAX", 127);
define("ALPHASTART", 50);

$regen = false;

if (isset($_GET['regen'])){
	$regen = true;
	header('X-Reflector-Regenerated: Regen Requested');
}

$cachefilename = '/maelfroth.org-card-'.md5($imagefile);

if (file_exists(CACHEDIR.$cachefilename) && file_exists($imagefile)){

	# If image file is newer than cache, update the image
	if(filectime($imagefile) > filectime(CACHEDIR.$cachefilename)){
		$regen = true;
		header('X-Reflector-Regenerated: New Gallery Photo');
	}
}








header('X-Reflector-Filename: '.$cachefilename);

if(defined("CACHEDIR") && ! $regen){
    if(!is_readable(CACHEDIR)){
        error_log("[AQ-TRANSLUCENATOR] - Cannot read cachedir ".CACHEDIR);
    } elseif(!$regen) {
        if(file_exists(CACHEDIR.$cachefilename)){
            header("Content-Type: image/jpeg");
		header('X-Reflector-Cache: Used Cache');
            readfile(CACHEDIR.$cachefilename);
            die();
        }
    }
}


$baseline = 77; // Baseline of the name

$image = imagecreatefromjpeg('images/cardback.jpg');
$card_width = imagesx ($image);
$card_height = imagesy ($image);

$gallery_max_h = 400;
$gallery_max_w = 300;


$gallery = imagecreatefromjpeg($imagefile);
$orig_gal_x = imagesx ($gallery);
$orig_gal_y = imagesy ($gallery);

if(!$gallery){
	die("Couldn't load image: ".$imagefile);
}

$gal_y = $orig_gal_y;
$gal_x = $orig_gal_x;

if ($gal_x < $gallery_max_w && $gal_y < $gallery_max_h){
	$gal_x = $gal_x * 2;
	$gal_y = $gal_y * 2;
}

if ($gal_x > $gallery_max_w){
	$multiplier = $gallery_max_w/$orig_gal_x;
	$gal_x = $gallery_max_w;
	$gal_y = $orig_gal_y * $multiplier;

} 

if ($gal_y > $gallery_max_h){
	$multiplier = $gallery_max_h/$orig_gal_y;
	$gal_y = $gallery_max_h;
	$gal_x = $orig_gal_x * $multiplier;
}

$gal_x = round($gal_x);
$gal_y = round($gal_y);

//echo "Resizing from $orig_gal_y * $orig_gal_x to $gal_y * $gal_x ($multiplier)";


$in = 5 + (($gallery_max_w-$gal_x)/2);
//$down = $baseline+5;
$down = $baseline+5 + (($gallery_max_h-$gal_y)/2);

//imagecopyresampled ( $dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w,      $dst_h,      $src_w, $src_h )
imagecopyresampled   ($image     , $gallery  , $in   , $down ,      0,      0, $gal_x, $gal_y, $orig_gal_x, $orig_gal_y);


$fancyname = strtr($name, '_', ' ');

$white = imagecolorallocate($image, 255, 255, 255);

#$bb = imagettftext($image, $size, 0, abs($bbox[0]), $baseline, $white, $font, $fancyname);

// Reflector time:

// Starting from the bottom of the image, loop though half the image going up:

$fadeh = $gal_y/2;

$dest_y = $down+$gal_y-2;

$jump = (ALPHAMAX-ALPHASTART)/$fadeh;
$alpha = ALPHASTART;

$log = 0;

for ($src_y = $down+$gal_y; $src_y > $down+$fadeh; $src_y--){
	//echo '<br/>'.$src_y.':'.$dest_y;	
	$alpha+=$jump;
	for($x = $in; $x <= ($in+$gal_x); $x++){
		//echo $x.', ';

		$rgb = imagecolorat($image, $x, $src_y);
		$currentalpha = ($rgb & 0x7F000000) >> 24;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		if($currentalpha > $alpha){
			$newalpha = $currentalpha;
		} else {
			$newalpha = $alpha;
		}
		$newcolor = imagecolorallocatealpha($image, $r, $g, $b, $newalpha);
		//$newcolor = ();

		imagesetpixel($image, $x, $dest_y, $newcolor);
	}
	$dest_y++;	
}

$final = imagecreatetruecolor(400, 400);

imagecopyresampled($final, $image, 0, 0, 0, 0, $card_width/2, $card_height/2, $card_width, $card_height);
 

if (headers_sent()){
    echo "<hr />Not sending image, headers already sent.";
} else {
    header("Content-Type: image/jpeg");
    header('X-Reflector-Cache: No Cache');

	if(isset($_GET['comp'])){
		$comp = $_GET['comp'];
	} else {
		$comp = 60;
	}

    $newimage = $final;

    #imagestring($final, 1, 0, 0, "Regen");

    imagejpeg($final, null, $comp);

    //header("Content-Type: image/png");
    //imagepng($image, null, 7);

    if(defined("CACHEDIR")){
        if(is_writable(CACHEDIR)){
            //imagepng($image, CACHEDIR.$cachefilename, 9);
            imagejpeg($final, CACHEDIR.$cachefilename, $comp);
        } else {
            error_log("[AQ-TRANSLUCENATOR] - Couldn't write cache ".CACHEDIR.$cachefilename.' for '.$file);
        }
    }
}
 
