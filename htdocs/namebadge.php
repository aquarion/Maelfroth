<?php

$name = isset($_GET['name']) ? $_GET['name'] : "Aquarion";
$size = 80;
$font = 'font/renaissance.ttf';

$name = preg_replace('/\W/', '', $name);

define('CACHEDIR', '/tmp');

$filename = '/maelfroth.org-cardback--'.md5($name);

header('X-Reflector-Filename: '.$filename);


define("ALPHAMAX", 127);
define("ALPHASTART", 50);
define("CACHEDIR", "/tmp/");
//define("CACHEDIR", '');

if(defined("CACHEDIR") && ! isset($_GET['regen'])){
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




$bbox = imagettfbbox  ( $size, 0 , $font, $name);

$height = $bbox[1]+abs($bbox[5]);
$width = $bbox[2]+abs($bbox[0]*4);

$width = 380*2;
$height = 52*2;
$baseline = 77;

//print_r($bbox);
//echo "Width: $width          Height: $height    Baseline:". ($height-$bbox[1]);

    $png = imagecreatetruecolor($width, $height);
    //imagesavealpha($png, true);

    //imageantialias($png, true);
    //$trans_colour = imagecolorallocatealpha($png, 0, 0, 0, 127);
    $white = imagecolorallocate($png, 255, 255, 255);
    //imagefill($png, 0, 0, $trans_colour);
   
   imagettftext($png, $size, 0, abs($bbox[0]), $baseline, $white, $font, $name);


   $image = imagecreatetruecolor($width/2, $height/2);

   //$image = imagecreatefromjpeg('images/cardback.jpg');

   imagecopyresampled($image, $png, 0, 0, 0, 0, $width/2, $height/2, $width, $height);
 

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
  
