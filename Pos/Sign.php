<?php
class Pos_Sign {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_file;
    protected static $_con;

    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    private static function check(){
        if(!self::$_con)
        {
            self::connect();
        }
    }
    public static function createUser($username,$email,$password,$file,$country,$state,$local){
    	self::check();
    	$progress = true;
    	$name = self::fullname($username);
    	$checkName = self::username($name);
    	$mail = self::email($email);
    	$pass = self::password($password);
		$cont = self::location($country);
		$stat = self::location($state);
		$loc = self::location($local);
		if(!$name){
			$progress = false;
			return array('state' => false, 'option' => 'Username', 'error' => 'Incompatible Username');
		}
		if(!$checkName){
			$progress = false;
			return array('state' => false, 'option' => 'Email', 'error' => 'Username already exist');
		}
		if(!$mail){
			$progress = false;
			return array('state' => false, 'option' => 'Email','error' => 'Incompatible Email');
		}
		if(!$pass){
			$progress = false;
			return array('state' => false, 'option' => 'Password','error' => 'Incompatible Password');
		}
		if(!$cont){
			$progress = false;
			return array('state' => false, 'option' => 'Country','error' => 'Incompatible Country option');
		}
		if(!$stat){
			$progress = false;
			return array('state' => false, 'option' => 'State','error' => 'Incompatible State option');
		}
		if(!$loc){
			$progress = false;
			return array('state' => false, 'option' => 'Municipal','error' => 'Incompatible Municipal option');
		}

		if($progress === true){
            $hash = password_hash($pass, PASSWORD_DEFAULT);
			$field = array("User_Name","Email","Password","Country","State","Municipal");
			$value = array($checkName,$mail,$hash,$cont,$stat,$loc);
			if($session = self::$_con->insert("user",$field,$value)){
                require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
                $current = time();
                $nbf = $current + 5;
                $exp = $current + 3605;
                $header = ["alg"=>"HS256","typ"=>"JWT"];
                $payload = ["iat"=>$current,"jti"=>$jti,"iss"=>$domain,"nbf"=>$nbf,"exp"=>$exp,"ref"=>$session,
                "username"=>$checkName];
                $token = new Pos_Token(self::$_host,self::$_user,self::$_pass,self::$_db);
                $tokens = $token->JWT_token('sha256',$header,$payload,$key);
                if(self::$_con->update_where('user',array('Token'=>$tokens),array('Id'=>$session))){
                    return json_encode(array('state' => true,'token' => $tokens));
                }
            }
            return json_encode(array('state' => false, 'option' => 'Username', 'error' => 'Failed Operation'));

        	// return array('state' => true, 'session'=> $session);
        	// return self::file($file,$session,$checkName);
		}

    }
    public static function file($file=array(), $uid,$name){
		self::$_file = $file;
		self::check();
		$output ='';

     	if(!isset(self::$_file))
        {
            throw new Exception("input type=file name='' Field name was not specified!
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }

        if(!is_array(self::$_file))
        {
            throw new Exception("Parameter 1 must be an array");
        }
        $folder='..'.DIRECTORY_SEPARATOR.'Profile';
        $validextensions = array("jpeg", "jpg", "png", "gif");
        $temporary = explode(".",self::$_file['name']);
        $file_extension = end($temporary);
        if(((self::$_file["type"]=="image/png") || (self::$_file["type"]=="image/jpeg") ||
           (self::$_file["type"]=="image/jpg") || (self::$_file["type"]=="image/gif"))
           && (self::$_file["size"] < 100000000000000000000) && in_array($file_extension, $validextensions))
        {
        	if(self::$_file["error"] > 0)
            {
            	$output = array('state' => false, 'option' => 'File','error' => self::$_file["error"]);
            	return $output;             
            }else
            {
            	$pix = '';
            	if(self::$_con->select_where('user',array('Pix'),array('Id'=>$uid)) > 0){
					$pix = self::$_con->fetch_select_where();
            	}
            	list($width,$height,$type,$attr)=getimagesize(self::$_file["tmp_name"]);
                $pic=$uid."_".date('d-m-Y-H-i-s').self::$_file["name"];
                $source = self::$_file["tmp_name"];
                $target = $folder. DIRECTORY_SEPARATOR.$pic;
                if(!move_uploaded_file($source,$target)){
                    $output = array('state' => false, 'option' => 'File','error' => "File could not be moved");
                    return $output;
                }else{
                	$field = array('Pix' => $pic, 'Pix_height' => $height);
		            $pointer = array('Id' => $uid);
		            if(self::$_con->update_where('user', $field, $pointer)){
		            	return(array('state' => true, 'session'=> $uid, 'set_name' => $name));
		            	// return $output;
		            }else{
		            	return array('state' => false, 'option' => 'File','error' => "File couldn't update");
		            	// return $output;
		            }
		            
                }

                
            }
        }
    }
    public static function username( $username){
        self::check();
        $field = array('Id');
        $pointer = array ('User_Name' => $username);
        if(self::$_con->select_where('user', $field, $pointer) > 0){
			return false;
        }else{
			  return $username;
        }
    }
    public static function fullname( $fullname){
        $insert_fullname = filter_var($fullname, FILTER_SANITIZE_STRING);
		if(preg_match("/^[a-zA-Z0-9-_'\s]+$/", $insert_fullname)){
			return $insert_fullname;
		}else{
			return false;
		}
    }
    public static function email($email){
        if(preg_match('^[_a-z0-9]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$^', $email)){
        	$s_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        	if(filter_var($s_email, FILTER_VALIDATE_EMAIL)){
        		return  $s_email;
        	}else{
				return false;
			}
        }else{
			return false;
		}
    }
    public static function password( $password){
        $insert_password = ctype_alnum($password) ? $password : false;
        if($insert_password){
        	if(preg_match("/^[a-zA-Z0-9]+$/", $insert_password)){
				return $insert_password;
			}else{
				return false;
			}
        }else{
        	return false;
        }
		
    }
    public static function location($location){
		$insert_location = filter_var($location, FILTER_SANITIZE_STRING);
		if(preg_match("/^[a-zA-Z0-9-_'\s]+$/", $insert_location)){
			return $insert_location;
		}else{
			return false;
		}
    }
    public static function login($email, $password){
    	$progress = true;
    	if($email && $password){
			$mail = self::email($email);
			$pass = self::password($password); 
			if(!$mail){
				$progress = false;
				return json_encode(array('state' => false,'error' => 'Incompatible Email'));
			}
			if(!$pass){
				$progress = false;
				return json_encode(array('state' => false,'error' => 'Incompatible Password'));
			}
			if($progress){
				self::check();
				$field = array('Id','Password','User_Name');
				$pointer = array('Email' => $mail);
				if(self::$_con->select_where('user',$field,$pointer) > 0){
					$row = self::$_con->fetch_select_where();
                    if(password_verify($pass, $row['Password'])){
                        require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
                        $current = time();
                        $nbf = $current + 5;
                        $exp = $current + 3605;
                        $header = ["alg"=>"HS256","typ"=>"JWT"];
                        $payload = ["iat"=>$current,"jti"=>$jti,"iss"=>$domain,"nbf"=>$nbf,"exp"=>$exp,"ref"=>$row['Id'],
                        "username"=>$row['User_Name']];
                        $token = new Pos_Token(self::$_host,self::$_user,self::$_pass,self::$_db);
                        $tokens = $token->JWT_token('sha256',$header,$payload,$key);
                        if(self::$_con->update_where('user',array('Token'=>$tokens),array('Id'=>$row['Id']))){
                            return json_encode(array('state' => true,'token' => $tokens));
                        }
                    }
                    return json_encode(array('state' => false, 'error' => 'No Email-Password match found1'));
					
				}else{
					return json_encode(array('state' => false, 'error' => 'No Email-Password match found2'));
				}
			}

    	}else{
			return json_encode(array('state' => false, 'error' => 'No Email-Password match found3'));
		}
    }

}
?>