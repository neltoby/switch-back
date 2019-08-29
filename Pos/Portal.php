<?php

class Pos_Portal{
	protected static $_con;
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    public static $_file;

    private static function connection()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }

	public static function createPortal($uid, $name, $about, $category, $coverage,$country=false,$state= false,$municipal=false){
		if(!self::$_con)
        {
            self::connection();
        }
        $err = '';
		$insert_name = $insert_about = $insert_category = $insert_coverage = $insert_country = $insert_state = $insert_municipal = ''; 
        if($name){
	        $insert_name = filter_var($name, FILTER_SANITIZE_STRING);
	        if(!preg_match("/^[a-zA-Z0-9-_'\s]+$/", $insert_name)){
				$err = 'Portal name must alpha-numeric, underscore, dash';
	        }
	    }else{
	    	$err = 'Portal name is empty';
	    }

	    if($about){
			$insert_about = filter_var($about, FILTER_SANITIZE_STRING);
	    }else{
	    	$err = 'Please tell us about your portal';
	    }
	    if($category){
			$insert_category = filter_var($category, FILTER_SANITIZE_STRING);
	    }else{
	    	$err = 'Please choose a niche';
	    }
	    if($coverage){
			$insert_coverage = filter_var($coverage, FILTER_SANITIZE_STRING);
	    }else{
	    	$err = 'Please select range portal should cover';
	    }
	    if($country){
			$insert_country = filter_var($country, FILTER_SANITIZE_STRING);
	    }
	    if($state){
			$insert_state = filter_var($state, FILTER_SANITIZE_STRING);
	    }
	    if($municipal){
			$insert_municipal = filter_var($municipal, FILTER_SANITIZE_STRING);
	    }

	    if(!$err || $err == ''){
	    	$fields = array('User_id','Name','About','Category','Coverage','Date_time');
	    	$values = array($uid, $insert_name, $insert_about, $insert_category, $insert_coverage, date('Y-m-d h:i:s'));
			$pid = self::$_con->insert('portal', $fields, $values);
			if($coverage == 'national'){
				$field = array('Portal_id', 'Country');
				$value = array($pid, $insert_country);
				self::$_con->insert('portal_country', $field, $value);
			}elseif($coverage == 'state'){
				$field = array('Portal_id', 'Country', 'State');
				$value = array($pid, $insert_country, $insert_state);
				self::$_con->insert('portal_state', $field, $value);
			}elseif($coverage == 'municipal'){
				$field = array('Portal_id', 'Country', 'State', 'Municipal');
				$value = array($pid, $insert_country, $insert_state, $insert_municipal);
				self::$_con->insert('portal_municipal', $field, $value);
			}
			return array('name' => $insert_name, 'pid' => $pid, 'coverage' => $insert_coverage, 'category' => $insert_category);
	    }else{
	    	return json_encode(array('success'=>false, 'error'=>$err));
	    }

	} 

	public static function insertPortalPix($pid, $file=array(),$name=false,$coverage=false,$category=false) {
		self::$_file = $file;

     	if(!isset(self::$_file))
        {
            throw new Exception("input type=file name='' Field name was not specified!
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }

        if(!is_array(self::$_file))
        {
            throw new Exception("Parameter 12 must be an array");
        }

        if(!self::$_con)
        {
            self::connection();
        }

        $folder=dirname('.',1).DIRECTORY_SEPARATOR.'portal_profile';
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
            	if(self::$_con->select_where('portal',array('Pix'),array('Id'=>$pid)) > 0){
					$pix = self::$_con->fetch_select_where();
            	} 
            	// if($pix = 'anonymous-user-icon.jpg'){}
            	list($width,$height,$type,$attr)=getimagesize(self::$_file["tmp_name"]);
                $pic=$pid."_".date('d-m-Y-H-i-s').self::$_file["name"];
                // array_push($pix, $pic);
                // array_push($pix_height, $height);       
                $source = self::$_file["tmp_name"];
                $target = $folder. DIRECTORY_SEPARATOR.$pic;
                if($pix['Pix'] == 'anonymous-user-icon.jpg'){
	                if(!move_uploaded_file($source,$target)){
	                    throw new Exception("File upload wasn't successful");
	                }
	            }else{
	            	if(!empty($pix['Pix'])){
		            	if(file_exists($folder. DIRECTORY_SEPARATOR.$pix['Pix'])){
		            		unlink($folder. DIRECTORY_SEPARATOR.$pix['Pix']);
		            	}
		            }
	            	
	            	if(!move_uploaded_file($source,$target)){
	                    throw new Exception("File upload wasn't successful");
	                }else{
	                	$field = array('Pix' => $pic);
			            $pointer = array('Id' => $pid);
			            if(self::$_con->update_where('portal', $field, $pointer)){
			            	if($name && $coverage && $category){
			            		return json_encode(array('success' => true, 'error' => false, 'pid' => $pid, 'pic' => $pic,
			            			'name' => $name, 'coverage' => $coverage, 'category' => $category));
			            	}else{
				            	return json_encode(array('success' => true, 'error' => false));
				            }
			            }else{
			            	return json_encode(array('success' => false, 'error' => 'Profile display picture upload failed'));
			            }
	                }
	            }
	            
            }
        }
	}

	public static function nowPortal($uid, $pid){
		if(!self::$_con)
        {
            self::connection();
        }
        // $sent = array();
        if(is_numeric($uid) && is_numeric($pid)){
        	$query = self::$_con->select_where('portal',array('*'),array('User_id' => $uid, 'Id' => $pid));
        	$portal = self::$_con->fetch_select_where();
        	if($portal['Coverage'] == 'national'){
        		$loc = self::$_con->select_where('portal_country', array('Country'), array('Portal_id' => $pid));
        		$location = self::$_con->fetch_select_where();
				$all = array('name' => $portal['Name'], 'about' => $portal['About'], 'category' => $portal['Category'], 'pix' => $portal['Pix'], 'coverage' => $portal['Coverage'], 'country' => $location['Country']);
				return json_encode($all);
        	}elseif($portal['Coverage'] == 'state'){
        		$loc = self::$_con->select_where('portal_state', array('Country', 'State'), array('Portal_id' => $pid));
        		$location = self::$_con->fetch_select_where();
        		$all = array('name' => $portal['Name'],'about' => $portal['About'],'category' => $portal['Category'],'pix' => $portal['Pix'], 'coverage' => $portal['Coverage'], 'country' => $location['Country'], 'state' => $location['State']);
        		return json_encode($all);
        	}elseif($portal['Coverage'] == 'municipal'){
        		$loc = self::$_con->select_where('portal_municipal', array('Country','State','Municipal'), array('Portal_id' => $pid));
        		$location = self::$_con->fetch_select_where();
				$all = array('name' => $portal['Name'],'about' => $portal['About'],'category' => $portal['Category'],'pix' => $portal['Pix'],'coverage' => $portal['Coverage'],'country' => $location['Country'],'state' => $location['State'],
					'municipal' => $location['Municipal']);
				return json_encode($all);
        	}else{
        		$all = array('name' => $portal['Name'], 'about' => $portal['About'], 'category' => $portal['Category'], 'pix' => $portal['Pix'], 'coverage' => $portal['Coverage']);
        		return json_encode($all);
        	}
        }else{
        	if(is_numeric($uid) && !is_numeric($pid)){
        		// $check = self::$_con->select_where('portal', array('*'),)
        		if(self::$_con->select('portal',array(0,1),array('Date_time'=>'DESC')) > 0){
        			$portal = self::$_con->fetch_select();
        			if($portal['Coverage'] == 'national'){
		        		$loc = self::$_con->select_where('portal_country', array('Country'), array('Portal_id' => $pid));
		        		$location = self::$_con->fetch_select_where();
						$all = array('name' => $portal['Name'], 'about' => $portal['About'], 'category' => $portal['Category'], 'pix' => $portal['Pix'], 'coverage' => $portal['Coverage'], 'country' => $location['Country']);
						return json_encode($all);
		        	}elseif($portal['Coverage'] == 'state'){
		        		$loc = self::$_con->select_where('portal_state', array('Country', 'State'), array('Portal_id' => $pid));
		        		$location = self::$_con->fetch_select_where();
		        		$all = array('name' => $portal['Name'],'about' => $portal['About'],'category' => $portal['Category'],'pix' => $portal['Pix'], 'coverage' => $portal['Coverage'], 'country' => $location['Country'], 'state' => $location['State']);
		        		return json_encode($all);
		        	}elseif($portal['Coverage'] == 'municipal'){
		        		$loc = self::$_con->select_where('portal_municipal', array('Country','State','Municipal'), array('Portal_id' => $pid));
		        		$location = self::$_con->fetch_select_where();
						$all = array('name' => $portal['Name'],'about' => $portal['About'],'category' => $portal['Category'],'pix' => $portal['Pix'],'coverage' => $portal['Coverage'],'country' => $location['Country'],'state' => $location['State'],
							'municipal' => $location['Municipal']);
						return json_encode($all);
		        	}else{
		        		$all = array('name' => $portal['Name'], 'about' => $portal['About'], 'category' => $portal['Category'], 'pix' => $portal['Pix'], 'coverage' => $portal['Coverage']);
		        		return json_encode($all);
		        	}
        		}
        	}
        }
	}

	public static function otherPortal($uid){
		if(!self::$_con)
        {
            self::connection();
        }
        $all = array();
		if(self::$_con->select_all_where('portal',array('User_id' => $uid),0,0) > 0){
			while($portal = self::$_con->fetch_select_all_where()){
				if($portal['Coverage'] == 'global'){
					$new = array('pid' => $portal['Id'], 'name'=>$portal['Name'], 'about'=>$portal['About'],'category'=>$portal['Category'],'pic'=>$portal['Pix'],'coverage'=>$portal['Coverage'] );
					array_push($all, $new);
				}elseif($portal['Coverage'] == 'national'){
					$loc = self::$_con->select_where('portal_country', array('Country'), array('Portal_id' => $portal['Id'])); 
					$location = self::$_con->fetch_select_where();
					$new = array('pid' => $portal['Id'], 'name'=>$portal['Name'], 'about'=>$portal['About'],'category'=>$portal['Category'],'pic'=>$portal['Pix'],'coverage'=>$portal['Coverage'],'country'=>$location['Country']);
					array_push($all, $new);
				}elseif($portal['Coverage'] == 'state'){
					$loc = self::$_con->select_where('portal_state', array('Country','State'), array('Portal_id' => $portal['Id'])); 
					$location = self::$_con->fetch_select_where();
					$new = array('pid' => $portal['Id'], 'name'=>$portal['Name'], 'about'=>$portal['About'],'category'=>$portal['Category'],'pic'=>$portal['Pix'],'coverage'=>$portal['Coverage'],'country'=>$location['Country'],'state'=>$location['State']);
					array_push($all, $new);
				}else{
					$loc = self::$_con->select_where('portal_municipal', array('Country','State','Municipal'), array('Portal_id' => $portal['Id'])); 
					$location = self::$_con->fetch_select_where();
					$new = array('pid' => $portal['Id'], 'name'=>$portal['Name'], 'about'=>$portal['About'],'category'=>$portal['Category'],'pic'=>$portal['Pix'],'coverage'=>$portal['Coverage'],'country'=>$location['Country'],'state'=>$location['State'],'municipal'=>$location['Municipal']);
					array_push($all, $new);
				}
			}
		}
		return json_encode($all);
	}
}
?>