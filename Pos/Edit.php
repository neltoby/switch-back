<?php
class Pos_Edit {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;

    protected static $_con;
    protected static $_file;

    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }

    public static function editPhoto($file=array(), $uid) 
    {
     	self::$_file = $file;

     	if(!isset(self::$_file))
        {
            throw new Exception("input type=file name='' Field name was not specified!
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }

        if(!is_array(self::$_file))
        {
            throw new Exception("Parameter 1 must be an array");
        }

        if(!self::$_con)
        {
            self::connect();
        }

        $folder=dirname('.',1).DIRECTORY_SEPARATOR.'images';
        $validextensions = array("jpeg", "jpg", "png", "gif");
        $temporary = explode(".",self::$_file['name']);
        $file_extension = end($temporary);
        if(((self::$_file["type"]=="image/png") || (self::$_file["type"]=="image/jpeg") ||
           (self::$_file["type"]=="image/jpg") || (self::$_file["type"]=="image/gif"))
           && (self::$_file["size"] < 100000000000000000000) && in_array($file_extension, $validextensions))
        {
        	 if(self::$_file["error"] > 0)
            {
                throw new Exception("Error code:".self::$_file["error"]);
            }else
            {
            	$pix = '';
            	if(self::$_con->select_where('user',array('Pix'),array('Id'=>$uid)) > 0){
					$pix = self::$_con->fetch_select_where();
            	} 
            	// if($pix = 'anonymous-user-icon.jpg'){}
            	list($width,$height,$type,$attr)=getimagesize(self::$_file["tmp_name"]);
                $pic=$uid."_".date('d-m-Y-H-i-s').self::$_file["name"];
                // array_push($pix, $pic);
                // array_push($pix_height, $height);       
                $source = self::$_file["tmp_name"];
                $target = $folder. DIRECTORY_SEPARATOR.$pic;
                if($pix['Pix'] == 'anonymous-user-icon.jpg'){
	                if(!move_uploaded_file($source,$target)){
	                    throw new Exception("File upload wasn't successful");
	                }
	            }else{
	            	unlink($folder. DIRECTORY_SEPARATOR.$pix['Pix']);
	            	if(!move_uploaded_file($source,$target)){
	                    throw new Exception("File upload wasn't successful");
	                }
	            }
	            $field = array('Pix' => $pic, 'Pix_height' => $height);
	            $pointer = array('Id' => $uid);
	            if(self::$_con->update_where('user', $field, $pointer)){
	            	return $pic;
	            }else{
	            	return 'Error: Not successful';
	            }
            }
        }
    }

    public static function names($uid, $value, $name){
    	if(!self::$_con)
        {
            self::connect();
        }
        $row = ''; 
        if($name == 'fname'){
        	$row = 'FirstName';
        }elseif($name == 'lname'){
        	$row = 'LastName';
        }else{
        	$row = 'Profession';
        }
        $insert_value = filter_var($value, FILTER_SANITIZE_STRING);
		if(preg_match("/^[a-zA-Z-_'\s]+$/", $insert_value)){
			$field = array($row => $insert_value);
			$pointer =  array('Id' => $uid);
			if(self::$_con->update_where('user', $field, $pointer)){
				return json_encode(array('update' => 'yes', 'name' => $insert_value));
			}else{
				return json_encode(array('update' => 'no', 'name' => $value, 'error' => 'Server update failed'));
			}
		}else{
			return json_encode(array('update' => 'no', 'name' => $value, 'error' => 'Letters, underscores , and dash required'));
		}
    }

	public static function mobile($uid, $value, $name){
    	if(!self::$_con)
        {
            self::connect();
        }
		if(preg_match('/^[0-9]{10,15}+$/', $value)){
			$field = array('Mobile' => $value);
			$pointer =  array('Id' => $uid);
			$update = self::$_con->update_where('user', $field, $pointer);
			if($update === true){
				return json_encode(array('update' => 'yes', 'name' => $value));
			}else{
				return json_encode(array('update' => 'no', 'name' => $value, 'error' => 'Server update failed: mobile likely exist'));
			}
		}else{
				return json_encode(array('update' => 'no', 'name' => $value, 'error' => 'Numbers require'));
			}
    }

    public static function email($uid, $value, $name){
    	if(!self::$_con)
        {
            self::connect();
        }
        if(preg_match('^[_a-z0-9]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$^', $value)){
        	$s_value = filter_var($value, FILTER_SANITIZE_EMAIL);
        	if(filter_var($s_value, FILTER_VALIDATE_EMAIL)){
        		$field = array('Email' => $s_value);
				$pointer =  array('Id' => $uid);
				if(self::$_con->update_where('user', $field, $pointer) === true){
					return json_encode(array('update' => 'yes', 'name' => $value));
				}else{
					return json_encode(array('update' => 'no', 'name' => $value, 'error' => 'Server update failed.Email probably exist'));
				}
        	}else{
				return json_encode(array('update' => 'no', 'name' => $value, 'error' => 'Invalid email address'));
			}
        }else{
			return json_encode(array('update' => 'no', 'name' => $value, 'error' => 'Invalid email address'));
		}
    }

    public static function getCountries(){
    	if(!self::$_con)
        {
            self::connect();
        }
		$countries = array();
        if(self::$_con->select('countries') > 0){
        	while($row =self::$_con->fetch_select()){
				array_push($countries, $row['Name']);
        	}
        	return json_encode($countries);
        }
    }

    public static function getStates($country){
		if(!self::$_con)
        {
            self::connect();
        }
        $states = array();
        $table = array('countries','states');
		$field = array('states.State');
		$on = array('countries.id' => 'states.Country_id');
		$where = array('countries.Name' => $country);
        if(self::$_con->join($table, $field, $on, $where, 0, 0) > 0){
        	while($row = self::$_con->fetch_join()){
				array_push($states, $row['State']);
        	}
        	return json_encode($states);
        }
    }

    public static function getLocal($country, $state){
    	if(!self::$_con)
        {
            self::connect();
        }
        $locals = array();
        $table = array('countries','states','municipals');
		$field = array('municipals.Municipal');
		$on = array('countries.id' => 'states.Country_id','states.id' => 'municipals.State_id');
		$where = array('countries.Name' => $country, 'states.State' => $state);
		if(self::$_con->join($table, $field, $on, $where, 0, 0) > 0){
        	while($row = self::$_con->fetch_join()){
				array_push($locals, $row['Municipal']);
        	}
        	return json_encode($locals);
        }
    }

    public static function updateLocation($id, $country, $state, $local){
        if(!self::$_con)
        {
            self::connect();
        }
        $country_value = filter_var($country, FILTER_SANITIZE_STRING);
        $state_value = filter_var($state, FILTER_SANITIZE_STRING);
        $local_value = filter_var($local, FILTER_SANITIZE_STRING);
        $all = array($country_value, $state_value, $local_value);
        $preg = preg_grep("/^[a-zA-Z-_'\s]+$/", $all, PREG_GREP_INVERT);
        if(count($preg) == 0){
            $field = array('Country' => $country, 'State' => $state, 'Municipal' => $local);
            $pointer = array('Id' => $id);
            if(self::$_con->update_where('user', $field, $pointer) === true){
                return json_encode(array('update' => 'yes'));
            }else{
                return json_encode(array('update' => 'no', 'error' => 'Server update failed'));
            }
        }else{
            return json_encode(array('update' => 'no', 'error' => 'Letters, underscores , and dash required'));
        }
    }
}
?>