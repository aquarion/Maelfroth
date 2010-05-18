<?php

if(!isset($_GET['top'])){
echo <<< EOW
<form action="advice_mokosh.php" method="GET">
Top <input name="top" value="kill wemics"/><br/>
Bottom <input name="bottom" value="make sausages"><br/>
Size <input name="size" value="30"/><br/>
<input type="submit" value="It make advices"/>
</form>
EOW;
die();
}

$top = stripslashes($_GET['top']); 
$bottom = stripslashes($_GET['bottom']);
$size = $sizey = (isset($_GET['size']) ? $_GET['size'] : 30);

$sizetop = (isset($_GET['sizetop']) ? $_GET['sizetop'] : $size);
$sizebottom = (isset($_GET['sizebottom']) ? $_GET['sizebottom'] : $size);

$imagefile = 'images/Advicemokosh_template.jpg';

$font = 'font/impact.ttf';

$name = preg_replace('/\W/', '', $name);

define('CACHEDIR', '/var/www/hosts/www.maelfroth.org/htdocs/mokosh_gallery');
define("ALPHAMAX", 127);
define("ALPHASTART", 50);

$regen = false;

if (isset($_GET['regen'])){
	$regen = true;
}

$cachefilename = '/maelfroth.org-advice-'.md5($_SERVER['QUERY_STRING']);

header('X-Reflector-Filename: '.$cachefilename);

if(defined("CACHEDIR") && ! $regen){
    if(!is_readable(CACHEDIR)){
        error_log("[AQ-TRANSLUCENATOR] - Cannot read cachedir ".CACHEDIR);
    } elseif(!isset($_GET['regen'])) {
        if(file_exists(CACHEDIR.$cachefilename)){
            header("Content-Type: image/jpeg");
            readfile(CACHEDIR.$cachefilename);
            die();
        }
    }
}


 	 function imagettftextoutline (& $im ,$size ,$angle ,$x ,$y ,& $col ,& $outlinecol ,$fontfile ,$text ,$width )
  	 {
  	     // For every X pixel to the left and the right
  	     for ( $xc =$x -abs ($width ); $xc <= $x +abs ($width ); $xc ++)
  	     {
  	         // For every Y pixel to the top and the bottom	

		 for ( $yc =$y -abs ($width ); $yc <= $y +abs ($width ); $yc ++)
  	         {
  	             // Draw the text in the outline color
  	             $text1 = imagettftext ($im ,$size ,$angle ,$xc ,$yc ,$outlinecol ,$fontfile ,$text );
  	         }
  	     }
  	     // Draw the main text
 	     $text2 = imagettftext ($im ,$size ,$angle ,$x ,$y ,$col ,$fontfile ,$text );
  	 }


$baseline = 55; // Baseline of the name

$image = imagecreatefromjpeg($imagefile);
$card_width = imagesx ($image);
$card_height = imagesy ($image);

$white = imagecolorallocate($image, 255, 255, 255);

$topbox = imagettfbbox  ($sizetop , 0 , $font , $top );
$topwidth = $topbox[0]+$topbox[2];
$topx = ($card_width - $topwidth)/2;

$baseline = (($card_height/3) + ($topbox[1]+$topbox[7]));

$bottombox = imagettfbbox  ($sizebottom , 0 , $font , $bottom );
$bottomwidth = $bottombox[0]+$bottombox[2];
$bottomx = ($card_width - $bottomwidth)/2;

imagettftextoutline ($image, $sizetop , 0, $topx , $baseline , $white , $black ,$font ,$top, 2 );
imagettftextoutline ($image, $sizebottom , 0, $bottomx , 255 , $white , $black ,$font ,$bottom, 2 );


#
#imagettftext($image, $size, 0, $topx,      $baseline, $white, $font, $top);
#imagettftext($image, $size, 0, $bottomx, 255, $white, $font, $bottom);

// Reflector time:


if (headers_sent()){
    echo "<hr />Not sending image, headers already sent.";
} else {
    header("Content-Type: image/jpeg");

	if(isset($_GET['comp'])){
		$comp = $_GET['comp'];
	} else {
		$comp = 60;
	}

    imagejpeg($image, null, $comp);
    //header("Content-Type: image/png");
    //imagepng($image, null, 7);

    if(defined("CACHEDIR")){
        if(is_writable(CACHEDIR)){
            //imagepng($image, CACHEDIR.$cachefilename, 9);
            imagejpeg($image, CACHEDIR.$cachefilename, $comp);
		error_log("Cached at ".CACHEDIR.$cachefilename);
        } else {
            error_log("[AQ-TRANSLUCENATOR] - Couldn't write cache ".CACHEDIR.$cachefilename.' for '.$file);
        }
    } else {
	error_log('Not cached, no cachedir');
    }
}
 
