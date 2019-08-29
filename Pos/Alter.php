<?php

class Pos_Alter
{
	protected static $_con;
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_file;

    private static function connection()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }

    public static function alterCountry($uid, $country, $category)
    {
    	if(!self::$_con)
        {
            self::connection();
        }

		$table = array('post','country_post','user');
		$field = array('post.Id','post.User_id','post.Title','post.Post','post.Post_pix','post.Pix_height','post.Date_time','user.User_Name','user.Pix');
		$on = array('post.Id' => 'country_post.Post_id','post.User_id' => 'user.Id');
		$where = array('post.Category' => $category, 'post.Location' =>'country', 'country_post.Country' => $country);
		$start = 0;
    	$lim = 50;
    	$order=array('post.Date_time'=>'DESC');
    	$limit=array($start,$lim);
        self::$_con->join($table, $field, $on ,$where, $order, $limit);
        $statement=array();
        // $images = array();
        while($row = self::$_con->fetch_join()){
        	$u_like = array('Like_id');
        	$u_like_where = array('Post_id' => $row['Id'],'User_id'=>$uid);
        	$u_likes = self::$_con->select_where('post_like',$u_like,$u_like_where);
        	if($u_likes > 0){
        		$user_like = true;
        	}else{
        		$user_like = false;
        	}
        	$like = array('Like_id');
        	$like_where = array('Post_id' => $row['Id']);
        	$likes = self::$_con->select_where('post_like',$like,$like_where);
        	$comment = array('Comment_id');
        	$comment_where = array('Post_id' => $row['Id']);
        	$comments = self::$_con->select_where('post_comments',$comment,$comment_where);
        	$thread_id = array('Id'); 
        	$thread_where = array('Threaded_id' => $row['Id']);
        	$threads = self::$_con->select_where('threads',$thread_id,$thread_where);
        	if(!empty($row['Post_pix'])){
	        	$needle = '*?';
	        	$value=strpos($row['Post_pix'],$needle);
	        	$all=array();
	        	$all_pix = array();
	        	if($value){
					$pix = explode('*?',$row['Post_pix']);
	        	}else{
	        		$pix=array($row['Post_pix']);
	        	}
	        	for ($i=0; $i < count($pix) ; $i++) { 
	        		$m_like = array('Id');
	        		$m_like_where = array('Image' => $pix[$i]);
	        		$m_likes_where = array('Image' => $pix[$i],'User_id' => $uid);
	        		$m_likes = self::$_con->select_where('multi_image_like',$m_like,$m_like_where);
	        		$m_true = self::$_con->select_where('multi_image_like',$m_like,$m_likes_where); 
	        		$final = $m_true > 0 ? true : false ;
	        		$m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
	        		$m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where); 
	    			array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'mcom' => $m_com,'views'=>$m_view));       	
	        	}	        	
	        	if(!empty($row['Post'])){
	        		$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
	        	}else{
	        		$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,
	        			'thread'=>$threads);
	        	} 
	        	array_push($all, $result);
			    array_push($all, $all_pix);
	        	array_push($statement, $all);
	        }else{
	        	$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,
	        		'like'=>$user_like,'thread'=>$threads);
	        	array_push($statement, $result);
	        }
        }
        return json_encode($statement);
    }

    public static function alterState($uid, $country, $state, $category)
    {
    	if(!self::$_con)
        {
            self::connection();
        }

		$table = array('post','state_post','user');
		$field = array('post.Id','post.User_id','post.Title','post.Post','post.Post_pix','post.Pix_height','post.Date_time','user.User_Name','user.Pix');
		$on = array('post.Id' => 'state_post.Post_id','post.User_id' => 'user.Id');
		$where = array('post.Category' => $category, 'state_post.Country' => $country, 'state_post.State' => $state);
		$start = 0;
    	$lim = 50;
    	$order=array('post.Date_time'=>'DESC');
    	$limit=array($start,$lim);
        self::$_con->join($table, $field, $on ,$where, $order, $limit);
        $statement=array();
        // $images = array();
        while($row = self::$_con->fetch_join()){
        	$u_like = array('Like_id');
        	$u_like_where = array('Post_id' => $row['Id'],'User_id'=>$uid);
        	$u_likes = self::$_con->select_where('post_like',$u_like,$u_like_where);
        	if($u_likes > 0){
        		$user_like = true;
        	}else{
        		$user_like = false;
        	}
        	$like = array('Like_id');
        	$like_where = array('Post_id' => $row['Id']);
        	$likes = self::$_con->select_where('post_like',$like,$like_where);
        	$comment = array('Comment_id');
        	$comment_where = array('Post_id' => $row['Id']);
        	$comments = self::$_con->select_where('post_comments',$comment,$comment_where);
        	$thread_id = array('Id'); 
        	$thread_where = array('Threaded_id' => $row['Id']);
        	$threads = self::$_con->select_where('threads',$thread_id,$thread_where);
        	if(!empty($row['Post_pix'])){
	        	$needle = '*?';
	        	$value=strpos($row['Post_pix'],$needle);
	        	$all=array();
	        	$all_pix = array();
	        	if($value){
					$pix = explode('*?',$row['Post_pix']);
	        	}else{
	        		$pix=array($row['Post_pix']);
	        	}
	        	for ($i=0; $i < count($pix) ; $i++) { 
	    			$m_like = array('Id');
	        		$m_like_where = array('Image' => $pix[$i]);
	        		$m_likes_where = array('Image' => $pix[$i],'User_id' => $uid);
	        		$m_likes = self::$_con->select_where('multi_image_like',$m_like,$m_like_where);
	        		$m_true = self::$_con->select_where('multi_image_like',$m_like,$m_likes_where); 
	        		$final = $m_true > 0 ? true : false ;
	        		$m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
	        		$m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where); 
	    			array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'mcom' => $m_com,'views'=>$m_view));      	
	        	}	        	
	        	if(!empty($row['Post'])){
	        		$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
	        	}else{
	        		$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,
	        			'thread'=>$threads);
	        	} 
	        	array_push($all, $result);
			    array_push($all, $all_pix);
	        	array_push($statement, $all);
	        }else{
	        	$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,
	        		'like'=>$user_like,'thread'=>$threads);
	        	array_push($statement, $result);
	        }
        }
        return json_encode($statement);
    }

    public static function alterLocal($uid, $country, $state, $local, $category)
    {
    	if(!self::$_con)
        {
            self::connection();
        }

		$table = array('post','local_post','user');
		$field = array('post.Id','post.User_id','post.Title','post.Post','post.Post_pix','post.Pix_height','post.Date_time','user.User_Name','user.Pix');
		$on = array('post.Id' => 'local_post.Post_id','post.User_id' => 'user.Id');
		$where = array('post.Category' => $category, 'local_post.Country' => $country, 'local_post.State' => $state, 
			'local_post.local' => $local);
        $start = 0;
    	$lim = 50;
    	$order=array('post.Date_time'=>'DESC');
    	$limit=array($start,$lim);
        self::$_con->join($table, $field, $on ,$where, $order, $limit);
        $statement=array();
        // $images = array();
        while($row = self::$_con->fetch_join()){
        	$u_like = array('Like_id');
        	$u_like_where = array('Post_id' => $row['Id'],'User_id'=>$uid);
        	$u_likes = self::$_con->select_where('post_like',$u_like,$u_like_where);
        	if($u_likes > 0){
        		$user_like = true;
        	}else{
        		$user_like = false;
        	}
        	$like = array('Like_id');
        	$like_where = array('Post_id' => $row['Id']);
        	$likes = self::$_con->select_where('post_like',$like,$like_where);
        	$comment = array('Comment_id');
        	$comment_where = array('Post_id' => $row['Id']);
        	$comments = self::$_con->select_where('post_comments',$comment,$comment_where);
        	$thread_id = array('Id'); 
        	$thread_where = array('Threaded_id' => $row['Id']);
        	$threads = self::$_con->select_where('threads',$thread_id,$thread_where);
        	if(!empty($row['Post_pix'])){
	        	$needle = '*?';
	        	$value=strpos($row['Post_pix'],$needle);
	        	$all=array();
	        	$all_pix = array();
	        	if($value){
					$pix = explode('*?',$row['Post_pix']);
	        	}else{
	        		$pix=array($row['Post_pix']);
	        	}
	        	for ($i=0; $i < count($pix) ; $i++) { 
	    			$m_like = array('Id');
	        		$m_like_where = array('Image' => $pix[$i]);
	        		$m_likes_where = array('Image' => $pix[$i],'User_id' => $uid);
	        		$m_likes = self::$_con->select_where('multi_image_like',$m_like,$m_like_where);
	        		$m_true = self::$_con->select_where('multi_image_like',$m_like,$m_likes_where); 
	        		$final = $m_true > 0 ? true : false ;
	        		$m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
	        		$m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where); 
	    			array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'mcom' => $m_com,'views'=>$m_view));      	
	        	}	        	
	        	if(!empty($row['Post'])){
	        		$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
	        	}else{
	        		$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,
	        			'thread'=>$threads);
	        	} 
	        	array_push($all, $result);
			    array_push($all, $all_pix);
	        	array_push($statement, $all);
	        }else{
	        	$result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],'pid'=>$row['Id'],'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$likes,'comments'=>$comments,
	        		'like'=>$user_like,'thread'=>$threads);
	        	array_push($statement, $result);
	        }
        }
        return json_encode($statement);
    }

    public static function upload($file=array(), $id)
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
            self::connection();
        }

        $folder=dirname('.',1).DIRECTORY_SEPARATOR.'postImages';
		$validextensions = array("jpeg", "jpg", "png", "gif");
		if(count(self::$_file['name']) > 0 )
		{
			$pix=array();
			$pix_height=array();
			for($i = 0; $i < count(self::$_file['name']); $i++)
			{
				$temporary = explode(".",self::$_file['name'][$i]);
				$file_extension = end($temporary);
				if(((self::$_file["type"][$i]=="image/png") || (self::$_file["type"][$i]=="image/jpeg") ||
	               (self::$_file["type"][$i]=="image/jpg") || (self::$_file["type"][$i]=="image/gif"))
	               && (self::$_file["size"][$i] < 100000000000000000000) && in_array($file_extension, $validextensions))
				{
					if(self::$_file["error"][$i] > 0)
					{
						throw new Exception("Error code:".self::$_file["error"][$i]);
					}else
					{
						list($width,$height,$type,$attr)=getimagesize(self::$_file["tmp_name"][$i]);
						$pic=$id."_".date('d-m-Y-H-i-s').self::$_file["name"][$i];
						array_push($pix, $pic);
						array_push($pix_height, $height);		
						$source = self::$_file["tmp_name"][$i];
						$target = $folder. DIRECTORY_SEPARATOR.$pic;
						if(!move_uploaded_file($source,$target)){
							throw new Exception("File upload wasn't successful");
						}
					}
				}
			}
			
			$im_pix = implode('*?',$pix);
			$im_pix_height = implode('*?',$pix_height);
			$field = array('Post_pix' => $im_pix, 'Pix_height' => $im_pix_height);
			$pointer = array('Id' => $id);
			self::$_con->update_where('post', $field, $pointer);
			$final=array();
			for($i = 0; $i < count($pix); $i++)
			{
				array_push($final, array("pic" => $pix[$i],"height" => $pix_height[$i]));
			}
				return $final;
		}
    }

    public static function post($uid, $title, $post=false, $file=false, $category, $current, $country, $state=false, $local=false)
    {
    	if(!self::$_con)
        {
            self::connection();
        }

		if($current == 'country')
		{
			if($post && $file)
			{
				
				$field=array('User_id','Title','Post','Category','Location','Date_time');
				$value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$images=self::upload($file, $id);
				$fild = array('Post_id','Country');
				$values = array($id, $country);
				self::$_con->insert('country_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['post'] = $post;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings,$images);
				return json_encode($info);
				
			}elseif($post && !$file)
			{
				$field=array('User_id','Title','Post','Category','Location','Date_time');
				$value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$fild = array('Post_id','Country');
				$values = array($id, $country);
				self::$_con->insert('country_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['post'] = $post;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings);
				return json_encode($info);

			}elseif(!$post && $file)
			{
				$field=array('User_id','Title','Category','Location','Date_time');
				$value=array($uid,$title,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$images=self::upload($file, $id);
				$fild = array('Post_id','Country');
				$values = array($id, $country);
				self::$_con->insert('country_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings,$images);
				return json_encode($info);
			}
		}elseif($current == 'state'){
			if($post && $file)
			{
				
				$field=array('User_id','Title','Post','Category','Location','Date_time');
				$value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$images=self::upload($file, $id);
				$fild = array('Post_id','Country','State');
				$values = array($id, $country, $state);
				self::$_con->insert('state_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['post'] = $post;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings,$images);
				return json_encode($info);
				
			}elseif($post && !$file)
			{
				$field=array('User_id','Title','Post','Category','Location','Date_time');
				$value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$fild = array('Post_id','Country','State');
				$values = array($id, $country,$state);
				self::$_con->insert('state_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['post'] = $post;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings);
				return json_encode($info);

			}elseif(!$post && $file)
			{
				$field=array('User_id','Title','Category','Location','Date_time');
				$value=array($uid,$title,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$images=self::upload($file, $id);
				$fild = array('Post_id','Country','State');
				$values = array($id, $country,$state);
				self::$_con->insert('state_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings,$images);
				return json_encode($info);

			}
		}else{
			if($post && $file)
			{				
				$field=array('User_id','Title','Post','Category','Location','Date_time');
				$value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$images=self::upload($file, $id);
				$fild = array('Post_id','Country','State','Local');
				$values = array($id, $country, $state, $local);
				self::$_con->insert('local_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['post'] = $post;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings,$images);
				return json_encode($info);
				
			}elseif($post && !$file)
			{
				$field=array('User_id','Title','Post','Category','Location','Date_time');
				$value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$fild = array('Post_id','Country','State','Local');
				$values = array($id, $country, $state, $local);
				self::$_con->insert('local_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['post'] = $post;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings);
				return json_encode($info);

			}elseif(!$post && $file)
			{
				$field=array('User_id','Title','Category','Location','Date_time');
				$value=array($uid,$title,$category,$current,date('Y-m-d H:i:s'));
				$id=self::$_con->insert('post', $field, $value);
				$images=self::upload($file, $id);
				$fild = array('Post_id','Country','State','Local');
				$values = array($id, $country, $state, $local);
				self::$_con->insert('local_post', $fild, $values);
				$column = array('User_Name','Pix');
				$pointer = array('Id' => $uid);
				self::$_con->select_where('user', $column, $pointer);
				$row=self::$_con->fetch_select_where();
				$strings = array();
				$strings['cid'] = $uid;
				$strings['id'] = $uid;
				$strings['name'] = $row['User_Name'];
				$strings['profile'] = $row['Pix'];
				$strings['pid'] = $id;
				$strings['title'] = $title;
				$strings['date'] = date('Y-m-d H:i:s');
				$info = array($strings,$images);
				return json_encode($info);

			}
		}
    }
}
?>