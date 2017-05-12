<<!DOCTYPE html>
<html lang="en-GB">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>What have I forgotten to pack?</title>
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Chonburi|Gloria+Hallelujah|Inknut+Antiqua|Kaushan+Script|Righteous" rel="stylesheet">
		<style type="text/css">
		
		body { 
			background: url(' http://www.aquarionics.com/files/2015/04/2015-04-01-20.01.00.jpg ') fixed; 
			background-position: center;
			background-repeat: no-repeat; 
			background-size: cover; 
			background-image: url("https://art.istic.net/maelfroth/forgotten.jpg");
			font-family: 'Inknut Antiqua', serif;
			/*font-family: 'Gloria Hallelujah', cursive;
			font-family: 'Chonburi', cursive;
			font-family: 'Righteous', cursive;
			font-family: 'Kaushan Script', cursive;*/
		}

		.vertical-align {
		    display: flex;
		    align-items: center;
		}

		.row {
			height: 100%;
		}

		.thing {
			font-size: 72pt;
		}

		.boxothing {
			background: rgba(255,255,255,.7);
			border-radius: 1em;
			 box-shadow: 5px 5px 5px rgba(0,0,0,.3);
		}

		.boxothing h1, .boxothing .thing {
			text-shadow: 2px 2px 3px rgba(0,0,0,.3);
		}
		</style>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="row  vertical-align">
			<div class="col-xs-8 col-sm-10 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2 col-sm-offset-1 text-center boxothing">
				<h1 class="text-center">You have forgotten</h1>
				<p class="thing">
				<?PHP
				$datecache = '';
				$conf = parse_ini_file("../dbconfig.ini");
				$db = new PDO('mysql:dbname='.$conf['database'], $conf['username'], $conf['password']);
				$query = "select item, author from item ORDER BY RAND() limit 1";
				$result = $db->query($query)->fetch(PDO::FETCH_ASSOC);
				echo ucfirst($result['item']);
				?>
				</p>
			</div>	
		</div>
		
		<!-- jQuery -->
		<script src="//code.jquery.com/jquery.js"></script>
		<!-- Bootstrap JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<nav class="navbar navbar-inverse navbar-default navbar-fixed-bottom" role="navigation">
			<div class="container-fluid">
				<p class="navbar-text text-center">
				Suggestions by the denizens of Maelfroth (This one by <?PHP echo $result['author'] ?>), Actually useful suggestions at <a href="http://larphacks.tumblr.com/post/122682366224/kit-tip-generic-larp-weekend-packing-list">LARPHacks</a> &amp; <a href="http://wiki.maelfroth.org/CommonlyForgottenItemsforLARPtrips">our wiki</a>
				</p>

			</div>
		</nav>
	</body>
	<bs3-tem
</html>