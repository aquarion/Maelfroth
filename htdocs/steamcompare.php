<?PHP

$title = "Links";

$header = <<<EOW
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> 
<link type="text/css" href="http://akquinet.github.com/jquery-toastmessage-plugin/demo/css/jquery.toastmessage-min.css" rel="stylesheet"/> 
<script type="text/javascript" src="http://akquinet.github.com/jquery-toastmessage-plugin/demo/jquery.toastmessage-min.js"></script> 


<script type="text/javascript">

Array.prototype.clone = function () {
var arr1 = new Array(); 
for (var property in this) {
arr1[property] = typeof (this[property]) == 'object' ? this[property].clone() : this[property]
}
return arr1;
}

var people = []

function updateLink(){
        
    if (people.length > 1){
        link = 'steamcompare_action.php?'+people.join(',');
        peoplecopy = people.clone();
        console.log(people)
        last = peoplecopy.pop();
        text = "Compare "+peoplecopy.join(', ')+" & "+last;
        $('#compare').text(text);
        $('#compare').attr("href",link);
        
    } else {
        $('#compare').text(people[0]);
        $('#compare').attr("href",'#');
    }
}

doAddPerson = function(){
    value = $('#addperson').attr('value')
    if(value){
        if (-1 == jQuery.inArray(value, people)){
            people.push(value);
            updateLink()
        } else {
             $().toastmessage('showErrorToast', value+" is already listed");
        }
    } else {
         $().toastmessage('showErrorToast', "That shouldn't be empty");
    }
    $('#addperson').attr('value', '')
    return false;
}

$(document).ready(function()
{
    $('#addbutton').click(doAddPerson);
    $('#addform').submit(doAddPerson);
    
});

</script>
EOW;

include("header.php");

?>

<h1>Steam Games in common</h1>

<p>Steam games in common will tell you which games you have in common with another person.

<p>To make it go, you need to put the steam profile IDs of the people you want to compare with. For anyone who's set their profile name, this is easy. For everyone else, it's a string of unreadable numbers. To find out the profile ID, go to <a href="http://steamcommunity.com">Steam Community</a>, then click on your friend's names, then look at the URL of the page.</p>

<p>If it looks like <code>http://steamcommunity.com/id/<strong>[username]</strong></code> then you should put <strong><code>[username]</code></strong> in the box below.</p>

<p>If it looks like <code>http://steamcommunity.com/profiles/<strong>[lots of numbers]</strong></code> you should put <strong><code>[lots of numbers]</code></strong> in the box below.</p>

<p>When you've finished adding people, click the link at the end.</p>

<form id="addform">
    <input id="addperson" type="text"/>
    <a href="#" id="addbutton">
        <img src="http://art.istic.net/iconography/silk/add.png">
    </a>
</form>

<p><a href="" id="compare"></a></p>

</body>
</html>
