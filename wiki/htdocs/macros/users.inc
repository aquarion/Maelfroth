<?PHP

class users extends AqWikiMacro {




	function modify(){
		global $_EXTRAS;
		global $dataSource;
		
		$fields = array("firstname", "lastname", "email", "url", "password");
		
		$out = '';
		
		$user = $dataSource->userExists($this->settings['user_page']);
		
		if (
			(strtolower($this->settings['me']) != strtolower($this->settings['user_page'])) 
			&& (!in_array($this->settings['me'], $this->settings['admins']))	
		){
			panic("Maelfroth User System", "You have no permission to do that, citizen");
		}
		
		if(isset($_POST['submit'])){
			
			
			if (isset($_POST['password']) && !$_POST['password']){
				unset($_POST['password']);
			}
			if ($_POST['password'] != $_POST['password2']){
				$out .= '<div class="badthing">Passwords didn\'t match</div>';
				
			} else {
				$try = $dataSource->updateUser($this->settings['user_page'], $_POST);
				
				if($try === true){
					return '<div class="goodthing">Those changes have been applied</div>';
				} else {
					$out .= '<div class="badthing">Something went wrong: '.$try.' :(</div>';
				
				}
			}
		}
		
		
		$fid = uniqid();
		
		$_SESSION['formid'] = $fid;
				
		$form ='
		<form  method=post action="'.$_SERVER['REQUEST_URI'].'">
		<h2>Update User Information</h2>
		<table>
		<tr><th>Username		</th><td>'.$user['username'].'</td></tr>
		<tr><th>Display Name	</th><td><input type="text" name="real_name" value="'.$user['real_name'].'"></td></tr>
		<tr><th>e-Mail		</th><td><input type="text" name="email" value="'.$user['email'].'"></td></tr>
		<tr><td colspan="2">Leave these blank to keep the existing password:</td></tr>
		<tr><th>Password		</th><td><input type="password" name="password"></td></tr>
		<tr><th>Repeat Password</th><td><input type="password" name="password2"></td></th>
		<tr><td colspan="2"><input type="submit" name="submit" value="Update User" /></td></tr>
		</table>
		<input type="hidden" name="val" value="'.$fid.'"/>
		</form>';
		
		$id=uniqid();
		
		$_EXTRAS[$id] = $out.$form;
	
		return '[[RAWVAR|'.$id.']]';
	}
}
