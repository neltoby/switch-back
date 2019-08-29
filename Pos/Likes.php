<?php
class Pos_Likes {
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
    public static function strings( $text){
        $insert_text = filter_var($text, FILTER_SANITIZE_STRING);
        if(preg_match("/^[a-zA-Z-_#'\s]+$/", $insert_text)){
            return $insert_text;
        }else{
            return false;
        }
    }
    public static function userLikes($id,$likes=array()){
        if(!is_array($likes) || count($likes) < 1){
            return (array('state' => false, 'error' => 'You have not liked any thing'));
        }
        $check = true;
        foreach ($likes as $value) {
            $verified = self::strings($value);
            if(!$verified){
                $check = false;
                return (array('state' => false, 'error' => $value.' should be only strings')) ;
            }
        }
        if($check){
            self::check();
            foreach($likes as $value){
                $state = false;
                $needle = '#';
                $values=substr_count($value,$needle);
                if($values > 0){
                    $value =str_replace('#', '', $value);
                }
                if(self::$_con->select_where('interest',array('Id'),array('User_id'=>$id,'Interest'=>$value)) < 1){
                    $field = array('User_id','Interest','Date_time');
                    $values = array($id,$value,date('Y-m-d h:i:m'));
                    if(!self::$_con->insert('interest',$field,$values)){
                        return (array('state' => false, 'error' => ' Server Error '));
                    }                  
                }
                
            }
            return (array('state' => true));
            
        }
    }
    public static function deleteLikes($user,$name){
        if(!$name || !$user){
            return (array('state' => false, 'error' => 'Could not resolve your request'));
        }
        $text = self::strings($name);
        if(!$text){
            return (array('state' => false, 'error' => $name.' is an invalid string type'));
        }
        self::check();
        if(self::$_con->delete('interest',array('User_id' =>$user, 'Interest' => $name))){
            return (array('state' => true));
        }
        return (array('state' => false, 'error' => ' Request could not be processed'));
    }
    public static function returnUserLikes($user){
        if(!$user || !is_numeric($user)){
            return (array('state' => false, 'error' => 'Could not resolve your request'));
        }
        self::check();
        $likes = array();
        if(self::$_con->select_where('interest',array('Interest'),array('User_id' => $user)) > 0){
            while($row = self::$_con->fetch_select_where()){
                array_push($likes,$row['Interest']);
            }
            return (array('state' => true, 'likes' => $likes)); 
        }
        return (array('state' => false, 'error' => 'Not picked up an interest yet'));
    }
    public static function getAlreadyInterest($text){
        if(!$text){
            return (array('state' => false, 'error' => 'Could not resolve your request'));
        }
        self::check();
        $likes = array();
        if(self::$_con->select_where_like('interest',array('Interest'),array('Interest' => "$text%"),1) > 0){
            while($row = self::$_con->fetch_select_where_like()){
                array_push($likes,$row['Interest']);
            }
            return (array('state' => true, 'likes' => $likes)); 
        }
        return (array('state' => false, 'error' => 'Process Failed'));
    }
    public static function likePost($uid, $pid){
        self::check();
        if(self::$_con->select_where('post_likes',array('Id'),array('Post_Id'=>$pid,'User_Id'=>$uid)) < 1){
            self::$_con->insert('post_likes',array('Post_Id','User_Id','DateTime'),array($pid,$uid,date('Y-m-d h:i:s')));
            return (array('status'=>true));
        }
        return (array('status'=>false));
    }
    public static function unlikePost($uid, $pid){
        self::check();
        if(self::$_con->delete('post_likes',array('Post_Id'=>$pid,'User_id'=>$uid))){
            return (array('status'=>true));
        }
        return (array('status'=>false));
    }
    public static function likeComment($uid, $cid){
        self::check();
        if(self::$_con->select_where('comment_likes',array('Id'),array('Comment_Id'=>$cid,'User_Id'=>$uid)) < 1){
            self::$_con->insert('comment_likes',array('Comment_Id','User_Id','Date_Time'),array($cid,$uid,date('Y-m-d h:i:s')));
            return (array('status'=>true));
        }
        return (array('status'=>false));
    }
    public static function unlikeComment($uid, $cid){
        self::check();
        if(self::$_con->delete('comment_likes',array('Comment_Id'=>$cid,'User_id'=>$uid))){
            return (array('status'=>true));
        }
        return (array('status'=>false));
    }
    public static function confirmInterest($uid, $interest){
        if($int = self::strings($interest)){
            self::check();
            if(self::$_con->select_where('interest',array('Id'),array('User_id'=>$uid,'Interest'=>$int)) > 0){
                return (array('state'=>true));
            }else{
                return (array('state'=>false));
            }
        }
        return (array('state'=>false));
    }
    public static function likeInterestWall($uid, $interest){
        if($int = self::strings($interest)){
            self::check();
            if(self::$_con->select_where('interest',array('Id'),array('User_id'=>$uid,'Interest'=>$int)) < 1){
                if(self::$_con->insert('interest',array('User_id','Interest','Date_time'),array($uid,$int,date('Y-m-d h:i:s')))){
                    return (array('state'=>true));
                }else{
                    return (array('state'=>false));
                }               
            }else{
                return (array('state'=>true));
            }
        }
        return (array('state'=>false));
    }

}