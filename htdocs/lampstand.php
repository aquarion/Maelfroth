<?PHP

header("location: http://wiki.maelfroth.org/lampstandDocs");

$title = "Lampstand Manual";
include("header.php");
?>

<div id="content">

<?PHP

readfile ('http://hol.istic.net/lampstand?output=fragment');

?>


</div>

</body>
</html>
