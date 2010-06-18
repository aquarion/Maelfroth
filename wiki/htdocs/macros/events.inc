<?PHP

class events extends AqWikiMacro {




	function userpage(){
		global $_EXTRAS;
		global $dataSource;
		
		$fields = array("datetime", "datetime_end", "description", "class");
		
		$out = '';
		
		$user = $dataSource->userExists($this->settings['user_page']);
		
		if (
			(strtolower($this->settings['me']) != strtolower($this->settings['user_page'])) 
			&& (!in_array($this->settings['me'], $this->settings['admins']))	
		){
			panic("Maelfroth Events System", "You have no permission to do that, citizen");
		}
		
		if(isset($_POST['submit']) && $_POST['submit'] == "Add Event" ){
			$event = $_POST;			
		
			$q = sprintf('insert into events (creator, datetime, description,class, datetime_end) values ("%s", "%s", "%s", "%s", "%s")', $user['username'], $event['datetime'], $event['description'], $event['class'], $event['datetime_end']);	


			$this->data->query($q);

			return "Event added";
		}
		
		
		$fid = uniqid();
		
		$_SESSION['formid'] = $fid;

		if(!isset($_POST['submit'])){
			$event = array(
				'id' => 0,
				'description' => "",
				'class' => "", 
				'datetime' => "",
				'datetime_end' => "",
				'creator' => $user->username
			);

			print_r($event);
		}

		$query = "select distinct class from events order by class";
		$res = $this->data->sql_as_array($query);
				
		$form ='
		<form  method=post action="'.$_SERVER['REQUEST_URI'].'">
		<h2>Add New Event</h2>

		<p>(<b>Do not play silly buggers with this system, or you will be banned from using it</b>)</p>
	
		<table>
		<tr><th>Creator		</th><td>'.$user['username'].'</td></tr>
		<tr><th>Event Name	</th><td><input type="text" name="description" value="'.stripslashes($event['description']).'"></td></tr>
		<tr><th>Event Type	</th><td><select name="class">';

		foreach($res as $row){

			$default = $row['class'] == $event['class'] ? " SELECTED=\"selected\"" : "";

			$form .= '<option '.$default.'>'.$row['class']."</option>\n";
		}

		$form .= '</select></td></tr>
		<tr><th>Date & Time, or Time In</th><td><input type="text" name="datetime" value="'.$event['datetime'].'"> (Format <tt>YYYY-MM-DD HH:MM</tt>)</td></th>
		<tr><th>Time Out</th><td><input type="text" name="datetime_end" value="'.$event['datetime_end'].'"> (Format <tt>YYYY-MM-DD HH:MM</tt>, leave this blank for instant event)</td></th>
		<tr><td colspan="2"><input type="submit" name="submit" value="Add Event" /></td></tr>
		</table>
		<input type="hidden" name="val" value="'.$fid.'"/>
		<input type="hidden" name="id" value="'.$event['id'].'"/>
		</form>';
		
		$id=uniqid();
		
		$_EXTRAS[$id] = $out.$form;
	
		return '[[RAWVAR|'.$id.']]';
	}
}
