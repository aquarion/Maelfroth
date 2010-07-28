<?php


$title = "Advice Mokosh";

$defaultTop = isset($_GET['top']) ? $_GET['top'] : "Kill Wemics";
$defaultBottom = isset($_GET['bottom']) ? $_GET['bottom'] : "Make Sausages";
#$defaultSize = isset($_GET['size']) ? $_GET['size'] : "30";
$defaultSizeTop = isset($_GET['sizetop']) ? $_GET['sizetop'] : "30";
$defaultSizeBottom = isset($_GET['sizebottom']) ? $_GET['sizebottom'] : "30";

$form = <<< EOW

<div id="content">

<p>[ <a href="http://www.maelfroth.org/mokosh_gallery/">Gallery of previous Mokosh</a> ]</p> 

<table>

<form action="advice_mokosh.php" method="GET">
<input type="hidden" name="view" value="true"/>
<tr>
	<td></td><td>Text</td><td>Font size</td>
</tr>
<tr>
	<th>Top    </th>
	<td> <input name="top" value="$defaultTop"/></td>
	<td><input name="sizetop" value="$defaultSizeTop" /></td>
</tr>
<tr>
	<th>Bottom </th>
	<td> <input name="bottom" value="$defaultBottom"></td>
	<td><input name="sizebottom" value="$defaultSizeBottom" /></td>
</tr>
<tr><td colspan="2"><input type="submit" value="It make advices"/></td></tr>
</form>

</table>

EOW;

if(isset($_GET['view'])) {
	include("header.php");
	echo $form;
	$query = $_GET;
	unset($query['view']);
	$url = "http://www.maelfroth.org/advice_mokosh.php?".http_build_query($query);

	$html = sprintf('<img src="%s" />', $url);

	echo "<p>$html</p>";


	printf("HTML Code: <blockquote><pre>%s</pre></blockquote>", htmlentities($html));
	printf("IFCode (For use on Rule7): <blockquote><pre>[img]%s[/img]</pre></blockquote>", htmlentities($url));
	
	echo "</div></body></html>";
	die();
	
} elseif(!isset($_GET['top'])){
	include("header.php");
	echo $form;
	echo "</div></body></html>";
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
 
