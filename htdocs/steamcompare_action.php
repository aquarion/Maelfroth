<html>

<head>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> 
<link type="text/css" href="http://akquinet.github.com/jquery-toastmessage-plugin/demo/css/jquery.toastmessage-min.css" rel="stylesheet"/> 
<script type="text/javascript" src="http://akquinet.github.com/jquery-toastmessage-plugin/demo/jquery.toastmessage-min.js"></script> 

<script type="text/javascript">

lookingfor = []

var people = []

var names = []

var all_the_people = lookingfor.length;

function log(message){
    $('#output').append("<li>"+message+"</il>");
}

function updateLists(){
    var all  = []
    var some = []
    var one  = []
    
    
    everyone = names.length;
        
    $(people).each(function(peopleindex){
        person = this[0]
        games  = this[1]
                
        log("PERSON is "+person);
                
        $(games).each(function(index, element){
            game = String(this)
            log("GAME is "+game);
            folks = [person]
            $(people).each(function(folkindex,folkelement){
                if(this[0]!=person){
                     log(" - Looking at "+this[0]);
                    
                    gameindex = jQuery.inArray(game, this[1]);
                    if (gameindex != -1){
                        log(" - "+this[0]+" has this game");
                        folks.push(this[0])
                        people[folkindex][1].splice(gameindex,1);
                    }
                }  
            });
                        
            if ( folks.length == everyone ){
                all.push(game)
                log(" - Everyone has "+game);
            } else if ( folks.length == 1){
                one.push(game+" ("+folks[0]+")");
                log(" - One has "+game);
            } else {
                some.push(game+" ("+folks.join()+")");
                log(" - "+folks.join()+" have "+game);
            }
        })
    });
    
    all  = all.sort()
    some = some.sort()
    one  = one.sort()
    
    $(all).each(function(){
        $('#all').append("<li>"+this+"</li>");        
    });
    $(some).each(function(){
        $('#some').append("<li>"+this+"</li>");        
    });
    $(one).each(function(){
        $('#one').append("<li>"+this+"</li>");        
    });
}


function parseXml(xml)
{
  person = $(xml).find("steamID").text()

  var games = []
  person = $(xml).find("steamID").text()
  error = $(xml).find("error");
  if(error.length){
      failureXML(xml, error.text())
      //all_the_people = all_the_people -1;
  } else {      
      names.push(person)
      $().toastmessage('showSuccessToast', person+" found");
  }

  
      
  namesnow = []
  $(names).each(function(index,item){namesnow.push(item)})
  last = namesnow.pop()
  $('#everyone').text(namesnow.join(', ')+" & "+last+" have");
  
  
  var games = []
  $(xml).find("game").each(function()
  {
    $(this).find("name").each(function(){
        games.push($(this).text())
    });
    
  });
   
  
  games = games.sort()
  people.push([person, games])
  if (people.length == all_the_people){
    updateLists();
  }
}

function failureXML(xml,texterror, exception){
  person = $(xml).find("steamID").text()
  if(person){
      texterror = person+": "+texterror;
  }
  $().toastmessage('showErrorToast', texterror);
  
}

$(document).ready(function()
{
    
  
  lookingfor=window.location.search.substring(1).split(",")
  
  if (lookingfor.length < 2){
      window.location = "index.html";
      return;
  }
  
  all_the_people = lookingfor.length;
  
  $(lookingfor).each(function(){          
      user=String(this)
      
      $.ajax({
        type: "GET",
        url: "steam.php?u="+user,
        dataType: "xml",
        success: parseXml,
        error: failureXML
      });
  })
  
  
});


</script>

</head>
<body>

<h1>Steam Games in common</h1>
<div id="result"/>


<h2>Games <span id="everyone">Everyone Has</span></h2>
    <ul id="all">
    
    </ul>
<h2>Games Some Have</h2>
    <ul id="some">
    
    </ul>
<h2>Games One Person Has</h2>
    <ul id="one">
    
    </ul>
</div>

</html>