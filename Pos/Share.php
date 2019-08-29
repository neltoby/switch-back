<?php
class Pos_Share {
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
    public static function all($id, $pid,$text=false){
        self::check();
        if(!$text){
            if(self::$_con->select_where('share_all',array('Id'),array('User_Id'=>$id,'Post_Id'=>$pid)) < 1){
                if(self::$_con->insert('share_all',array('User_Id','Post_Id','Date_Time'),array($id,$pid,date('Y-m-d h:i:s')))){
                    return (array('status'=>true,'res'=>'Post shared was successful'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }
            return (array('status'=>false,'res'=>'You already used this option'));
        }else{
            if(self::text($text)){
                if(self::$_con->select_where('share_all',array('Id'),array('User_Id'=>$id,'Post_Id'=>$pid)) < 1){
                    if(self::$_con->insert('share_all',array('User_Id','Post_Id','Comment','Date_Time'),array($id,$pid,
                        addslashes($text),date('Y-m-d h:i:s')))
                    ){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'You already used this option'));
            }
            return (array('status'=>false,'res'=>'Comment could not be resolved'));
        }
    }
    public static function interest($id, $pid, $interest,$text=false){
        self::check();
        if(self::$_con->select_where('share_interest',array('Id'),array('User_Id'=>$id,'Post_Id'=>$pid)) < 1){
            if(!$text){
                if(self::$_con->insert('share_interest',array('User_Id','Post_Id','Interest','Date_Time'),
                                array($id,$pid,$interest,date('Y-m-d h:i:s')))){
                    return (array('status'=>true,'res'=>'Post shared was successful'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }else{
                if(self::text($text)){
                    if(self::$_con->insert('share_interest',array('User_Id','Post_Id','Interest','Comment','Date_Time'),
                                    array($id,$pid,$interest,addslashes($text),date('Y-m-d h:i:s')))){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'Comment could not be resolved'));
            }
        }
        return (array('status'=>false,'res'=>'You already used this option'));
    }
    public static function location($id, $pid, $location, $country, $state, $local, $text=false){
        self::check();
        if($location == 'country'){
            $table = array('share_location','country_share');
            $field = array('share_location.Id');
            $on = array('share_location.Id' => 'country_share.Share_Id');
            $where = array('share_location.User_Id'=>$id,'share_location.Post_Id'=>$pid,'country_share.Country'=>$country);
            if(self::$_con->join($table,$field,$on,$where,0,0) < 1){
                if(!$text){
                    $field = array('User_Id','Post_Id','Location','Date_Time');
                    $value = array($id,$pid,$location,date('Y-m-d h:i:s'));
                }else{
                    if(self::text($text)){
                        $field = array('User_Id','Post_Id','Location','Comment','Date_Time');
                        $value = array($id,$pid,$location,addslashes($text),date('Y-m-d h:i:s'));
                    }else{
                        return (array('status'=>false,'res'=>'Comment could not be resolved'));
                    }
                }
                if($num=self::$_con->insert('share_location',$field,$value)){
                    if(self::$_con->insert('country_share',array('Share_Id','Country'),array($num,$country))){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }
            return (array('status'=>false,'res'=>'You already used this option'));
        }elseif($location == 'state'){
            $table = array('share_location','state_share');
            $field = array('share_location.Id');
            $on = array('share_location.Id' => 'state_share.Share_Id');
            $where = array('share_location.User_Id'=>$id,'share_location.Post_Id'=>$pid,
                'state_share.Country'=>$country,'state_share.State'=>$state);
            if(self::$_con->join($table,$field,$on,$where,0,0) < 1){
                if(!$text){
                    $field = array('User_Id','Post_Id','Location','Date_Time');
                    $value = array($id,$pid,$location,date('Y-m-d h:i:s'));
                }else{
                    if(self::text($text)){
                        $field = array('User_Id','Post_Id','Location','Comment','Date_Time');
                        $value = array($id,$pid,$location,addslashes($text),date('Y-m-d h:i:s'));
                    }else{
                        return (array('status'=>false,'res'=>'Comment could not be resolved'));
                    }
                }
                if($num=self::$_con->insert('share_location',$field,$value)){
                    if(self::$_con->insert('state_share',array('Share_Id','Country','State'),array($num,$country,$state))){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }
            return (array('status'=>false,'res'=>'You already used this option'));
        }else{
            $table = array('share_location','local_share');
            $field = array('share_location.Id');
            $on = array('share_location.Id' => 'local_share.Share_Id');
            $where = array('share_location.User_Id'=>$id,'share_location.Post_Id'=>$pid,
                'local_share.Country'=>$country,'local_share.State'=>$state,'local_share.Municipal'=>$local);
            if(self::$_con->join($table,$field,$on,$where,0,0) < 1){
                if(!$text){
                    $field = array('User_Id','Post_Id','Location','Date_Time');
                    $value = array($id,$pid,$location,date('Y-m-d h:i:s'));
                }else{
                    if(self::text($text)){
                        $field = array('User_Id','Post_Id','Location','Comment','Date_Time');
                        $value = array($id,$pid,$location,addslashes($text),date('Y-m-d h:i:s'));
                    }else{
                        return (array('status'=>false,'res'=>'Comment could not be resolved'));
                    }
                }
                if($num=self::$_con->insert('share_location',$field,$value)){
                    if(self::$_con->insert('local_share',array('Share_Id','Country','State','Municipal'),
                                            array($num,$country,$state,$local))){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }
            return (array('status'=>false,'res'=>'You already used this option'));
        }

        // if(self::$_con->select_where('share_location',array('Id'),array('User_Id'=>$id,'Post_Id'=>$pid)) < 1){}
    }
    public static function locationInterest($id, $pid, $interest, $location, $country, $state, $local, $text=false){
        self::check();
        if($location == 'country'){
            $table = array('share_location_interest','country_share_interest');
            $field = array('share_location_interest.Id');
            $on = array('share_location_interest.Id' => 'country_share_interest.Share_Id');
            $where = array('share_location_interest.User_Id'=>$id,'share_location_interest.Post_Id'=>$pid,
                'share_location_interest.Interest'=>$interest,'country_share_interest.Country'=>$country);
            if(self::$_con->join($table,$field,$on,$where,0,0) < 1){
                if(!$text){
                    $field = array('Post_Id','User_Id','Location','Interest','Date_Time');
                    $value = array($pid,$id,$location,$interest,date('Y-m-d h:i:s'));
                }else{
                    if(self::text($text)){
                        $field = array('Post_Id','User_Id','Location','Interest','Comment','Date_Time');
                        $value = array($pid,$id,$location,$interest,addslashes($text),date('Y-m-d h:i:s'));
                    }else{
                        return (array('status'=>false,'res'=>'Comment could not be resolved'));
                    }
                }
                if($num=self::$_con->insert('share_location_interest',$field, $value)){
                    if(self::$_con->insert('country_share_interest',array('Share_Id','Country'),array($num,$country))){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }
            return (array('status'=>false,'res'=>'You already used this option'));
        }elseif ($location == 'state') {
            $table = array('share_location_interest','state_share_interest');
            $field = array('share_location_interest.Id');
            $on = array('share_location_interest.Id' => 'state_share_interest.Share_Id');
            $where = array('share_location_interest.User_Id'=>$id,'share_location_interest.Post_Id'=>$pid,
                'share_location_interest.Interest'=>$interest,'state_share_interest.Country'=>$country,
                'state_share_interest.State'=>$state);
            if(self::$_con->join($table,$field,$on,$where,0,0) < 1){
                if(!$text){
                    $field = array('Post_Id','User_Id','Location','Interest','Date_Time');
                    $value = array($pid,$id,$location,$interest,date('Y-m-d h:i:s'));
                }else{
                    if(self::text($text)){
                        $field = array('Post_Id','User_Id','Location','Interest','Comment','Date_Time');
                        $value = array($pid,$id,$location,$interest,addslashes($text),date('Y-m-d h:i:s'));
                    }else{
                        return (array('status'=>false,'res'=>'Comment could not be resolved'));
                    }
                }
                if($num=self::$_con->insert('share_location_interest',$field,$value)){
                    if(self::$_con->insert('state_share_interest',array('Share_Id','Country','State'),array($num,$country,$state))){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }
            return (array('status'=>false,'res'=>'You already used this option'));
        }else{
            $table = array('share_location_interest','local_share_interest');
            $field = array('share_location_interest.Id');
            $on = array('share_location_interest.Id' => 'local_share_interest.Share_Id');
            $where = array('share_location_interest.User_Id'=>$id,'share_location_interest.Post_Id'=>$pid,
                'share_location_interest.Interest'=>$interest,'local_share_interest.Country'=>$country,
                'local_share_interest.State'=>$state,'local_share_interest.Municipal'=>$local);
            if(self::$_con->join($table,$field,$on,$where,0,0) < 1){
                if(!$text){
                    $field = array('Post_Id','User_Id','Location','Interest','Date_Time');
                    $value = array($pid,$id,$location,$interest,date('Y-m-d h:i:s'));
                }else{
                    if(self::text($text)){
                        $field = array('Post_Id','User_Id','Location','Interest','Comment','Date_Time');
                        $value = array($pid,$id,$location,$interest,addslashes($text),date('Y-m-d h:i:s'));
                    }else{
                        return (array('status'=>false,'res'=>'Comment could not be resolved'));
                    }
                }
                if($num=self::$_con->insert('share_location_interest',$field,$value)){
                    if(self::$_con->insert('local_share_interest',array('Share_Id','Country','State','Municipal'),array($num,$country,$state,$local))){
                        return (array('status'=>true,'res'=>'Post shared was successful'));
                    }
                    return (array('status'=>false,'res'=>'Could not share post'));
                }
                return (array('status'=>false,'res'=>'Could not share post'));
            }
            return (array('status'=>false,'res'=>'You already used this option'));
        }
    }
}