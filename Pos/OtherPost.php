<?php
class Pos_OtherPost
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

    public static function getOtherPost($uid, $pid)
    {
    	if(!self::$_con)
        {
            self::connection();
        }

        if($pid){
        	$table = array('post','user');
			$field = array('post.User_id','user.User_Name','user.Pix');
			$on = array('post.User_id' => 'user.Id');
			$where = array('post.Id' => $pid);
			self::$_con->join($table, $field, $on ,$where, 0, 0);
			$row = self::$_con->fetch_join();
			$name = $row['User_Name'];
			$ppix = $row['Pix'];
			$userid = $row['User_id'];
			// $post = array('*');
        	// $limit = array(0, 10);
        	$statement=array();
        	$alls = array();
        	$tables = array('post','threads','user');
        	$fields = array('post.Id','post.Title','post.Post','post.Category','post.Post_pix','post.Date_time');
        	$ons = array('post.Id' => 'threads.Threaded_id','post.User_id' => 'user.Id');
        	$wheres = array('!post.Id' => $pid, 'post.User_id' =>$userid);
        	$start = 0;
        	$lim = 8;
        	$order=array('post.Date_time'=>'DESC');
        	$limit=array($start,$lim);
        	if(self::$_con->join_distinct($tables, $fields, $ons ,$wheres, $order, $limit, 1) > 0){
        		array_push($statement,array('sql' => 'join', 'name' => $name));
				while($rows = self::$_con->fetch_join_distinct()){
					$u_like = array('Like_id');
		        	$u_like_where = array('Post_id' => $rows['Id'],'User_id'=>$uid);
		        	$u_likes = self::$_con->select_where('post_like',$u_like,$u_like_where);
		        	if($u_likes > 0){
		        		$user_like = true;
		        	}else{
		        		$user_like = false;
		        	}
		        	$like = array('Like_id');
		        	$like_where = array('Post_id' => $rows['Id']);
		        	$likes = self::$_con->select_where('post_like',$like,$like_where);
		        	$comment = array('Comment_id');
		        	$comment_where = array('Post_id' => $rows['Id']);
		        	$comments = self::$_con->select_where('post_comments',$comment,$comment_where);
		        	$thread_id = array('Id'); 
		        	$thread_where = array('Threaded_id' => $rows['Id']);
		        	$threads = self::$_con->select_where('threads',$thread_id,$thread_where);
		        	if(!empty($rows['Post_pix'])){
		        		$needle = '*?';
			        	$value=strpos($rows['Post_pix'],$needle);
			        	$all=array();
			        	$all_pix = array();
			        	if($value){
							$pix = explode('*?',$rows['Post_pix']);
			        	}else{
			        		$pix=array($rows['Post_pix']);
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
			        	if(!empty($rows['Post'])){
			        		$result = array('category'=>$rows['Category'],'name'=>$name,'profile'=>$ppix,'pid'=>$rows['Id'],'title'=>$rows['Title'],'post'=>$rows['Post'],'date'=>$rows['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
			        	}else{
			        		$result = array('category'=>$rows['Category'],'name'=>$name,'profile'=>$ppix,'pid'=>$rows['Id'],'title'=>$rows['Title'],'date'=>$rows['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
			        	} 
			        	array_push($all, $result);
					    array_push($all, $all_pix);
			        	array_push($alls, $all);
		        	}else{
			        	$result = array('category'=>$rows['Category'],'name'=>$name,'profile'=>$ppix,'pid'=>$rows['Id'],'title'=>$rows['Title'],'post'=>$rows['Post'],'date'=>$rows['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
			        	array_push($statement, $result);
			        }
				}
				
        	}else{
        		$post_where = array('User_id' => $userid, '!Id' => $pid);
        		$start = 0;
	        	$lim = 8;
	        	$order=array('post.Date_time'=>'DESC');
	        	$limit=array($start,$lim);
	        	if(self::$_con->select_all_where('post',$post_where,$order,$limit) > 0){
	        		array_push($statement,array('sql' => 'all', 'name' => $name));
	        		while($rows = self::$_con->fetch_select_all_where()){
	        			$u_like = array('Like_id');
			        	$u_like_where = array('Post_id' => $rows['Id'],'User_id'=>$uid);
			        	$u_likes = self::$_con->select_where('post_like',$u_like,$u_like_where);
			        	if($u_likes > 0){
			        		$user_like = true;
			        	}else{
			        		$user_like = false;
			        	}
			        	$like = array('Like_id');
			        	$like_where = array('Post_id' => $rows['Id']);
			        	$likes = self::$_con->select_where('post_like',$like,$like_where);
			        	$comment = array('Comment_id');
			        	$comment_where = array('Post_id' => $rows['Id']);
			        	$comments = self::$_con->select_where('post_comments',$comment,$comment_where);
			        	$thread_id = array('Id'); 
			        	$thread_where = array('Threaded_id' => $rows['Id']);
			        	$threads = self::$_con->select_where('threads',$thread_id,$thread_where);
			        	if(!empty($rows['Post_pix'])){
			        		$needle = '*?';
				        	$value=strpos($rows['Post_pix'],$needle);
				        	$all=array();
				        	$all_pix = array();
				        	if($value){
								$pix = explode('*?',$rows['Post_pix']);
				        	}else{
				        		$pix=array($rows['Post_pix']);
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
				        	if(!empty($rows['Post'])){
				        		$result = array('id'=>$rows['User_id'],'name'=>$name,'profile'=>$ppix,'pid'=>$rows['Id'],'title'=>$rows['Title'],'post'=>$rows['Post'],'date'=>$rows['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
				        	}else{
				        		$result = array('id'=>$rows['User_id'],'name'=>$name,'profile'=>$ppix,'pid'=>$rows['Id'],'title'=>$rows['Title'],'date'=>$rows['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
				        	} 
				        	array_push($all, $result);
						    array_push($all, $all_pix);
						    array_push($alls, $all);
				        	// array_push($statement, $alls);
			        	}else{
				        	$result = array('id'=>$rows['User_id'],'name'=>$name,'profile'=>$ppix,'pid'=>$rows['Id'],'title'=>$rows['Title'],'post'=>$rows['Post'],'date'=>$rows['Date_time'],'likes'=>$likes,'comments'=>$comments,'like'=>$user_like,'thread'=>$threads);
				        	array_push($all, $result);
				        	// array_push($statement, $result);
				        }
	        		}
	        		
	        	}
	        }
	        array_push($statement, $alls);
	        return json_encode($statement);
        }
    }
}
?>