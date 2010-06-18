<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
 <title> [Maelfroth] Wiki - [[TITLE]] </title>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-68374-4");
pageTracker._initData();
pageTracker._trackPageview();
</script>

<style style="text/css">
        @import url('http://www.maelfroth.org/style.css');
	@import url("http://imperial.istic.net/static/style/wiki.css");
</style>

[[RAWVAR|head]]
</head>

<script type="text/javascript" src="http://imperial.istic.net/static/js/prototype.js"></script>
<script type="text/javascript" src="http://imperial.istic.net/static/js/firebugx.js"></script>

<script type="text/javascript">
var expand   = '<img src="http://imperial.istic.net/static/icons/silk/book.png" class="icon" title="Show Revisions"/>';
var collapse = '<img src="http://imperial.istic.net/static/icons/silk/book_open.png" class="icon" title="Hide Revisions"/>';


var AqWiki = {
	
	toggleVersions : function(){
		
		thing = document.getElementById('revisionsList');
		if (thing.style.display == 'none'){
			thing.style.display = '';
			this.innerHTML = collapse+" "+this.content;
		} else {
			thing.style.display = 'none';
			this.innerHTML = expand+" "+this.content;
		}
	},
	
	versionsInit : function(){
		thing = document.getElementById("revisionsList");
		content = thing.innerHTML;
		content = content.replace(/^\s+/, '');
		content = content.replace(/\s+$/, '');
		if (content == ""){
			 document.getElementById("revisions").style.display = "none";
		} else {
			header = document.getElementById("versionsTitle");
			header.onclick = AqWiki.toggleVersions;
			header.content = header.innerHTML
				thing.style.display = "none";
				header.innerHTML = expand+" "+header.content;
			}
		}
}


Event.observe(window, 'load', function() {
    AqWiki.versionsInit()
});
</script>
<link rel="alternate" type="application/x-wiki" title="Edit" href="[[URL]]?action=edit" />
<link rel="alternate" type="application/rss+xml" href="http://altru.istic.net/subscribeme/?rss=[[URL]]%3Foutput=rss">
</head>
<body id="wikipage[[TITLE]]" class="tundra">
<div class="bannersurround">
<div id="banner">
	<ul class="breadcrumbs">
		<li>Logged in as [[USER]]</li>
		<li><A HREF="/">Front Page</A></li>
		<li><A HREF="/recent">Recent</A></li>
		<li><A HREF="/contents">Index</A></li>
		<li><A HREF="/help">Help</A></li>
		<li>
			<form class="inline" method="get" action="/search"><input type="text" name="q" size="7" class="search"><button><img src="http://imperial.istic.net/static/icons/silk/magnifier.png"/></button></form>
		</li>
	</ul>
</div>
</div>


<ul id="navigation">
        <li><a class="section" href="http://www.maelfroth.org/">Gallery</a></li>
        <li><a class="section" href="http://www.maelfroth.org/events.php" title="Events">Events</a></li>
        <li><a class="section" href="http://www.maelfroth.org/quotes" title="Quotes">Quotes</a></li>
        <li><a class="section" href="http://www.maelfroth.org/links.php" title="Links">Links</a></li>
        <li><a class="section" href="http://wiki.maelfroth.org" title="Wiki">Wiki</a></li>
        <li><a class="section" href="http://www.maelfroth.org/lampstand.php" title="Lampstand">Lampstand</a></li>
        <li><a class="section" href="http://www.maelfroth.org/cgiirc/" title="Join the Channel">Join In</a></li>
</ul>

	</div>
</div>
<div id="content">

[[CONTENT]]
<p><a href="[[URL]]?action=edit">Edit this page</a></p>

<div class="boxed" id="pageinfo"><img src="http://imperial.istic.net/static/icons/silk/information.png" class="icon" title="Information"/> Revised by [[AUTHOR]] @ [[DATE]]</div>
<div id="revisions" class="boxed">
<h3 id="versionsTitle">[[VERSIONCOUNT]] Version[[PLURAL|[[VERSIONCOUNT]]]]</h3>

<div id="revisionsList">

[[VERSIONS]]

</div>
</div>
<div class="boxed" id="userinfo"><img src="http://imperial.istic.net/static/icons/silk/user_gray.png" class="icon" title="Information"/> You Are: [[USER]] (identified by [[AUTH]]) 
[[IfLoggedIn|<a href=\"[[URL]]?action=edit\"><img src="http://imperial.istic.net/static/icons/silk/note_edit.png" class="icon" title="Edit This Page"/></a>|<a href="[[URL]]?action=relogin"><img src="http://imperial.istic.net/static/icons/silk/user_go.png" class="icon" title="Login"/>Login</a> (<a href="[[URL]]?action=newUser"><img src="http://imperial.istic.net/static/icons/silk/user_add.png" class="icon" title="New User"/>New User</a>) ]]</div>
<form class="boxed"  id="search" method="get" action="[[BASE]]/search">
<img src="http://imperial.istic.net/static/icons/silk/find.png" class="icon" title="Search"/> <input type="text" name="q"> <input type="submit" value="search"></body>
</html>
