<?php
class Pos_Comments {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
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
    private static function text( $text){
        if(!filter_var($text, FILTER_SANITIZE_STRING) === false){
			return $text;
		}else{
			return false;
		}
    }
    public static function postComment(int $id,int $pid,string $comment, array $referred){
    	$text = self::text($comment);
    	if($text){
    		$date= date('Y-m-d h:i:s');
    		self::check();
    		$refer = array();
    		$cid = self::$_con->insert('post_comments',array('Post_id','User_id','Comment','DateTime'),array($pid,$id,
    			addslashes($text),$date));
    		foreach ($referred as $key) {
    			self::$_con->insert('post_comment_refer',array('Comment_Id','Referred_User'),array($cid,$key));
    			if(self::$_con->select_where('user',array('User_Name'),array('Id'=>$key))){
	    			$row = self::$_con->fetch_select_where(); 
					$refer[] = $row['User_Name'];
				}
    		}
    		self::$_con->select_where('user',array('User_Name','Pix'),array('Id'=>$id));
    		$row = self::$_con->fetch_select_where();
    		return (array('name'=>$row['User_Name'],'pix'=>$row['Pix'],'cid'=>$cid,'like'=>false,'comment'=>$text,'date'=>$date,
    			'refer'=>$refer));
    	}
    	
    }
    public static function getComment($id,$pid){
    	self::check();
    	// $num = self::$_con->select_where('post_comments',$array('Id'),array('Id'=>$pid));
    	$table = array('post_comments','user','posts');
    	$field = array('user.User_Name','user.Pix','post_comments.Id','post_comments.Comment','post_comments.DateTime');
    	$on = array('user.Id'=>'post_comments.User_id','posts.Id'=>'post_comments.Post_id');
    	$where = array('post_comments.User_id'=>$id,'post_comments.Post_id'=>$pid);
    	$start = 0;
    	$lim = 1;
    	$order=array('post_comments.DateTime'=>'DESC');
    	$limit=array($start,$lim);
    	if(self::$_con->join($table,$field,$on,$where,$order,$limit) > 0){
    		$row = self::$_con->fetch_join();
    		$like = self::$_con->select_where('comment_likes',array('Id'),array('Comment_Id'=>$row['Id'],'User_Id'=>$id)) > 0 ?
				true : false ;
    		return (array('name'=>$row['User_Name'],'pix'=>$row['Pix'],'cid'=>$row['Id'],'like'=>$like,'comment'=>$row['Comment'],'date'=>$row['DateTime']));
    	}
    }
    public static function getAllComment($id,$pid,$pgNum){
    	self::check();
    	$allComment = array();
    	// $num = self::$_con->select_where('post_comments',$array('Id'),array('Id'=>$pid));
    	$table = array('post_comments','user','posts');
    	$field = array('user.User_Name','user.Pix','post_comments.Id','post_comments.Comment','post_comments.DateTime');
    	$on = array('user.Id'=>'post_comments.User_id','posts.Id'=>'post_comments.Post_id');
    	$where = array('post_comments.Post_id'=>$pid);
    	// $start = 0;
    	$lim = 5;
    	$start = ($pgNum-1)*$lim;
    	$order=array('post_comments.DateTime'=>'DESC');
    	$limit=array($start,$lim);
    	$num = self::$_con->join($table,$field,$on,$where,$order,$limit);
    	if($num > 0){
    		while($row = self::$_con->fetch_join()){
    			$referred = array();
    			$rtab = array('user','post_comment_refer');
    			$rfield = array('User_Name','Referred_User');
    			$ron = array('user.Id' => 'post_comment_refer.Referred_User');
    			$rwhere = array('post_comment_refer.Comment_Id'=>$row['Id']);
    			if(self::$_con->join_ii($rtab,$rfield,$ron,$rwhere,0,0 > 0)){
    				while ($ref = self::$_con->fetch_join_ii()) {
    				    $referred[] = $ref['User_Name'];
    				}
    			}

    			$like = self::$_con->select_where('comment_likes',array('Id'),array('Comment_Id'=>$row['Id'],'User_Id'=>$id)) > 0 ?
					true : false ;
					array_push($allComment, array('name'=>$row['User_Name'],'pix'=>$row['Pix'],'cid'=>$row['Id'],'like'=>$like,'comment'=>$row['Comment'],'date'=>$row['DateTime'],'refer'=>$referred));

    		}
    		// array_push($allComment, $details);
    		return (array('status'=>true,'num'=>$num,'res'=>$allComment));
    	}
    	return (array('status'=>true,'num'=>$num));
    }
    public static function getAllRepliers($pid, $uid){
        if(!$pid || !ctype_digit($pid) || !$uid || !ctype_digit($uid)){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        } 
        self::check();
        $res = array();
        $table = array('post_comments','user');
        $field = array('user.Id','user.User_Name','user.Pix');
        $on = array('post_comments.User_id' =>'user.Id');
        $where = array('!post_comments.User_Id' => $uid,'post_comments.Post_id' => $pid);
        if (self::$_con->join_distinct($table,$field,$on,$where,0,0,1) > 0) {
            while($row = self::$_con->fetch_join_distinct()){
				$res[] = array('id'=>$row['Id'],'name'=>$row['User_Name'],'pix'=>$row['Pix']);
            }
            return array('state'=>true,'res'=>$res);
        }else{
        	return array('state'=>true,'res'=>$res);
        }
    }
    
}