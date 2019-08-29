<?php

class Pos_Like{
	protected static $_con;
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;

    private static function connection()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    private static function check(){
        if(!self::$_con)
        {
            self::connection();
        }
    }

    public static function like($uid, $postid, $name, $status)
    {
    	self::check();
        $like = array('Id');
        $like_where = array('User_id' => $uid, 'Post_id' => $postid, 'Image' => $name);
        if($status == 1){
        	$u_like = self::$_con->select_where('multi_image_like', $like, $like_where);
        	if($u_like < 1){
				$field = array('User_id','Post_id','Image','Date_time');
        		$value = array($uid, $postid, $name, date('Y-m-d H:i:s'));
        		self::$_con->insert('multi_image_like', $field, $value);
        	}
        	
        }else{
			$u_like = self::$_con->delete('multi_image_like', $like_where);
        }
        $likes_where = array('Post_id' => $postid, 'Image' => $name);
		$likes = self::$_con->select_where('multi_image_like', $like, $likes_where);
		return $likes;

    }
	public static function checklike($postid, $name){
		if(!self::$_con)
        {
            self::connection();
        }
        $like = array('Id');
        $likes_where = array('Post_id' => $postid, 'Image' => $name);
        $likes = self::$_con->select_where('multi_image_like', $like, $likes_where);
		return $likes;
	}
    public static function likePost($uid, $pid,){
        self::check();
        if(self::$_con->select_where('post_likes',array('Id'),array('Post_Id'=>$pid,'User_Id'=>$uid)) < 1){
            self::$_con->insert('post_likes'array('Post_Id','User_Id','DateTime'),array($pid,$uid,date('Y-m-d h:i:s')));
            return json_encode(array('status'=>true));
        }
        return json_encode(array('status'=>false));
    }
    public static function unlikePost($uid, $pid){
        self::check();
        if(self::$_con->delete('post_likes',array('Post_Id'=>$pid,'User_id'=>$uid))){
            return json_encode(array('status'=>true));
        }
        return json_encode(array('status'=>false));
    }
    public static function likeComment($uid, $cid,){
        self::check();
        if(self::$_con->select_where('comment_likes',array('Id'),array('Comment_Id'=>$cid,'User_Id'=>$uid)) < 1){
            self::$_con->insert('comment_likes'array('Comment_Id','User_Id','DateTime'),array($cid,$uid,date('Y-m-d h:i:s')));
            return json_encode(array('status'=>true));
        }
        return json_encode(array('status'=>false));
    }
    public static function unlikeComment($uid, $cid){
        self::check();
        if(self::$_con->delete('comment_likes',array('Comment_Id'=>$cid,'User_id'=>$uid))){
            return json_encode(array('status'=>true));
        }
        return json_encode(array('status'=>false));
    }

}
?>