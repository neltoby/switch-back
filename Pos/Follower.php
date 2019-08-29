<?php
class Pos_Follower {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_con;
    private static $follower;
    private static $following;

     private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
        self::$follower = array();
        self::$following = array();
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
    public static function follow($fid,$uid){
        self::check();
        if(self::$_con->select_where('followers',array('Id'),array('Followee'=>$fid,'Follower'=>$uid)) < 1){
            if(self::$_con->insert('followers',array('Followee','Follower','Date_Time'),array($fid,$uid,date('Y-m-d h:i:s')))){
                return (array('status'=>true)); 
            }
            return (array('status'=>false,'res'=>'Operation failed'));
        }
        return (array('status'=>true)); 
    }
    public static function unfollow($fid,$uid){
        self::check();
        if(self::$_con->delete('followers',array('Followee'=>$fid,'Follower'=>$uid))){
            return (array('status'=>true));
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function getAllFollowers_country($interest,$country){
        self::check();
        $table = array('user','interest');
        $field = array('user.Id');
        $on = array('user.Id' => 'interest.User_id');
        $where = array('user.Country'=>$country,'interest.Interest'=>$interest);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function getAllFollowers_state($interest,$country,$state){
        self::check();
        $table = array('user','interest');
        $field = array('user.Id');
        $on = array('user.Id' => 'interest.User_id');
        $where = array('user.Country'=>$country,'user.State'=>$state,'interest.Interest'=>$interest);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function getAllFollowers_local($interest,$country,$state,$local){
        self::check();
        $table = array('user','interest');
        $field = array('user.Id');
        $on = array('user.Id' => 'interest.User_id');
        $where = array('user.Country'=>$country,'user.State'=>$state,'user.Municipal'=>$local,'interest.Interest'=>$interest);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function allFollowers($id){
        self::check();
        $num = self::$_con->select_where('followers',array('Id'),array('Followee' => $id));
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed')); 
    }
    public static function interestFollowers($id, $interest){
        self::check();
        $table = array('followers','interest');
        $field = array('followers.Id');
        $on = array('followers.Follower' => 'interest.User_id');
        $where = array('followers.Followee'=>$id,'interest.Interest'=>$interest);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function interestLocation_country($id,$interest,$country){
        self::check();
        $table = array('followers','interest','user');
        $field = array('followers.Id');
        $on = array('followers.Follower'=>'interest.User_id' , 'interest.User_id'=>'user.Id');
        $where = array('followers.Followee'=>$id,'interest.Interest'=>$interest,'user.Country'=>$country);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function interestLocation_state($id,$interest,$country,$state){
        self::check();
        $table = array('followers','interest','user');
        $field = array('followers.Id');
        $on = array('followers.Follower'=>'interest.User_id' , 'interest.User_id'=>'user.Id');
        $where = array('followers.Followee'=>$id,'interest.Interest'=>$interest,'user.Country'=>$country,'user.State'=>$state);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function interestLocation_local($id,$interest,$country,$state,$local){
        self::check();
        $table = array('followers','interest','user');
        $field = array('followers.Id');
        $on = array('followers.Follower'=>'interest.User_id' , 'interest.User_id'=>'user.Id');
        $where = array('followers.Followee'=>$id,'interest.Interest'=>$interest,'user.Country'=>$country,
            'user.State'=>$state,'user.Municipal'=>$local);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function locationFollowers_country($id,$country){
        self::check();
        $table = array('followers','user');
        $field = array('followers.Id');
        $on = array('followers.Follower' => 'user.Id');
        $where = array('followers.Followee'=>$id,'user.Country'=>$country);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function locationFollowers_state($id, $country, $state){
        self::check();
        $table = array('followers','user');
        $field = array('followers.Id');
        $on = array('followers.Follower' => 'user.Id');
        $where = array('followers.Followee'=>$id,'user.Country'=>$country,'user.State'=>$state);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function locationFollowers_local($id, $country, $state, $local){
        self::check();
        $table = array('followers','user');
        $field = array('followers.Id');
        $on = array('followers.Follower' => 'user.Id');
        $where = array('followers.Followee'=>$id,'user.Country'=>$country,'user.State'=>$state,'user.Municipal'=>$local);
        $num = self::$_con->join($table,$field,$on,$where,0,0);
        if($num == 0 || $num > 0){
            return (array('status'=>true,'count'=>$num)); 
        }
        return (array('status'=>false,'res'=>'Operation failed'));
    }
    public static function getPeople($table, $field, $on, $where, $order, $limit, $id, $interest){
        self::check();
        if(self::$_con->join_distinct($table, $field, $on, $where, $order, $limit, 1) > 0){
            $all_pple= array();
            while($row = self::$_con->fetch_join_distinct()){
                $followable = $row['Id'] != $id ? true : false ;
                $follow = self::$_con->select_where('followers',array('Id'),array('Followee'=>$row['Id'],'Follower'=>$id)) > 0 ? 
                true : false ;
                $loc_int = self::$_con->select_where('share_location_interest',array('Id'),
                    array('User_Id'=>$row['Id'], 'Interest'=>$interest));
                $int = self::$_con->select_where('share_interest',array('Id'),
                    array('User_Id'=>$row['Id'], 'Interest'=>$interest));
                $share = $loc_int + $int;
                $intpost = self::$_con->select_where('posts',array('Id'),array('UserId'=>$row['Id'],'Interest'=>$interest));
                $post = self::$_con->select_where('posts',array('Id'),array('UserId'=>$row['Id']));
                if($followable && !$follow){
                    array_push($all_pple, array('uid'=>$row['Id'],'uname'=>$row['User_Name'],'upix'=>$row['Pix'],'follow'=>$follow,
                        'intPost'=>$intpost,'totPost'=>$post,'sharedPost'=>$share));
                }
            }
            
            return array('state'=>true,'category'=>'people','all'=>$all_pple);
        }
        return array('state'=>false,'category'=>'people');
    }
    public static function getCountryPeople($id, $interest, $country){
        $table = array('user','interest');
        $field = array('user.Id','user.User_Name','user.Pix');
        $on = array('user.Id'=>'interest.User_id');
        $where = array('interest.Interest'=>$interest,'user.Country'=>$country);
        $start = 0;
        $lim = 50; 
        $limit=array($start,$lim); 
        return (self::getPeople($table, $field, $on, $where, 0, $limit, $id, $interest));     
    }
    public static function getStatePeople($id, $interest, $country, $state){
        $table = array('user','interest');
        $field = array('user.Id','user.User_Name','user.Pix');
        $on = array('user.Id'=>'interest.User_id');
        $where = array('interest.Interest'=>$interest,'user.Country'=>$country,'user.State'=>$state);
        $start = 0;
        $lim = 50; 
        $limit=array($start,$lim); 
        return (self::getPeople($table, $field, $on, $where, 0, $limit, $id, $interest)); 
    }
    public static function getLocalPeople($id, $interest, $country, $state, $municipal){
        $table = array('user','interest');
        $field = array('user.Id','user.User_Name','user.Pix');
        $on = array('user.Id'=>'interest.User_id');
        $where = array('interest.Interest'=>$interest,'user.Country'=>$country,'user.State'=>$state,'user.Municipal'=>$municipal);
        $start = 0;
        $lim = 50;
        $limit=array($start,$lim); 
        return (self::getPeople($table, $field, $on, $where, 0, $limit, $id, $interest)); 
    }
    public static function writeWallStatus($id, $status, $interest){
       if(self::text($status)){
            $text = addslashes($status);
            self::check();
            if(self::$_con->select_where('write_status',array('Status'),array('User_Id'=>$id, 'Interest'=>$interest)) < 1){
                if(self::$_con->insert('write_status',array('User_Id','Status','Interest'),array($id,$text,$interest))){
                    return (array('state'=>true,'status'=>$status,'res' => 'insert was succesful'));
                }else{
                    return (array('state'=>false));
                }
            }else{
                // self::$_con->update_where('write_status',array('Status'=>$text),array('User_Id'=>$id,'Interest'=>$interest))   
                if(!self::$_con->update_where('write_status',array('Status'=>$text),array('User_Id'=>$id,'Interest'=>$interest))){
                    return (array('state'=>false));                    
                }else{
                    return (array('state'=>true,'status'=>$text,'res' => 'update was succesful'));
                }
            }
       }
       return (array('state'=>false));
        
    }
    public static function checkStatus($id, $interest){ 
        if(self::text($interest)){
            self::check();
            if(self::$_con->select_where('write_status',array('Status'),array('User_Id'=>$id, 'Interest'=>$interest)) > 0){
                $row = self::$_con->fetch_select_where();
                return (array('state'=>true,'status'=>$row['Status']));
            }else{
                return (array('state'=>false));
            }
        }else {
            return (array('state'=>false));
        }
    }
    public static function getTurnOff($id, $interest){
        if(self::text($interest)){
            self::check();
            $table = array('user','interest_turnoff');
            $field = array('user.Id','user.Fullname','user.User_Name','user.Pix');
            $on = array('user.Id'=>'interest_turnoff.Turn_Off');
            $where = array('interest_turnoff.User_Id'=>$id,'interest_turnoff.Interest'=>$interest);
            if(self::$_con->join($table,$field,$on,$where,0,0) > 0){
                $all = array();
                while($row = self::$_con->fetch_join()){
                    array_push($all, array('uid'=>$row['id'],'fname'=>$row['Fullname'],'uname'=>$row['User_Name'],
                        'upix'=>$row['Pix'],'turnoff'=> true));
                }
                return (array('state'=>true,'res'=>$all));
            }
            return (array('state'=>false,'res'=>[]));
        }
    }
    public static function getBiodata($id, $interest, $userid){
        if(self::text($interest)){
            self::check();
            self::$_con->select_where('user',array('*'),array('Id'=>$id));
            $row = self::$_con->fetch_select_where();
            $intr = array();
            if(self::$_con->select_where('interest',array('Interest'),array('User_id'=>$id)) > 0){
                while($int = self::$_con->fetch_select_where()){
                    array_push($intr, $int['Interest']);
                }
            }
            if($userid == $id ){
                $follow = 'You';
            }else{
                $follow = self::$_con->select_where('followers',array('Id'),array('Followee'=>$id,'Follower'=>$userid)) > 0 ? true : false;
            }
            $userIntr = array();
            if(self::$_con->select_where('interest',array('Interest'),array('User_id'=>$userid)) > 0){
                while($int = self::$_con->fetch_select_where()){
                    array_push($userIntr, $int['Interest']);
                }
            }
            $similar = array_values(array_intersect($intr,$userIntr));
            $similar = count($similar) > 0? $similar : array();
            self::$_con->select_where('write_status',array('Status'),array('Interest'=>$interest,'User_Id'=>$id));
            $status = self::$_con->fetch_select_where();
            $inum = self::$_con->select_where('posts',array('Id'),array('UserId'=>$id,'Interest'=>$interest));
            $num = self::$_con->select_where('posts',array('Id'),array('UserId'=>$id));
            $follower = self::$_con->select_where('followers',array('Id'),array('Followee'=>$id));
            $following = self::$_con->select_where('followers',array('Id'),array('Follower'=>$id));
            $data = array('uref'=>$row['Id'],'fullname'=>$row['Fullname'],'username'=>$row['User_Name'],'pix'=>$row['Pix'],
                'post'=>$num,'similar'=>$similar,
                'profession'=>$row['Profession'],'status'=>$status['Status'],'interest'=>$intr,'ipost'=>$inum,
                'follower'=>$follower,'following'=>$following,'follow'=>$follow);
            return (array('state'=>true,'res'=>$data));
        }
    }
    public static function getFollower($table,$field,$on,$where,$order,$limit,$id,$follow){
        self::check();
        if(self::$_con->join($table,$field,$on,$where,0,$limit) > 0){
            while($row = self::$_con->fetch_join()){
                $chk = self::$_con->select_where('followers',array('Id'),array('Followee'=>$row['Id'],'Follower'=>$id)) > 0 ? true : false ;
                if($follow == 'follower'){
                    array_push(self::$follower, array('uid'=>$row['Id'],'fullname'=>$row['Fullname'],'username'=>$row['User_Name'],
                        'pix'=>$row['Pix'],'following'=>$chk));
                }else{
                    array_push(self::$following, array('uid'=>$row['Id'],'fullname'=>$row['Fullname'],'username'=>$row['User_Name'],
                        'pix'=>$row['Pix'],'following'=>$chk));
                }
            }
        }
        
    }
    public static function getCountryFollower($id, $interest, $country){
        if(!$id){
            return (array('state'=>false, 'error'=>'Operation failed'));
        }
        $table = array('user','followers','interest');
        $field = array('user.Id','user.Fullname','user.User_Name','user.Pix');
        $fron = array('user.Id'=>'followers.Follower','interest.User_id'=>'followers.Follower');
        $frwhere = array('followers.Followee'=>$id,'user.Country'=>$country,'interest.Interest'=>$interest);
        $fgon = array('user.Id'=>'followers.Followee','interest.User_id'=>'followers.Followee');
        $fgwhere = array('followers.Follower'=>$id,'user.Country'=>$country,'interest.Interest'=>$interest);
        $start = 0;
        $lim = 50;
        $limit=array($start,$lim); 
        self::getFollower($table,$field,$fron,$frwhere,0,$limit,'follower');
        self::getFollower($table,$field,$fgon,$fgwhere,0,$limit,'following');
        return (array('state'=>true,'follower'=>self::$follower,'following'=>self::$following));
    }
    public static function getStateFollower($id, $interest, $country, $state){
        if(!$id){
            return (array('state'=>false, 'error'=>'Operation failed'));
        }
        $table = array('user','followers','interest');
        $field = array('user.Id','user.Fullname','user.User_Name','user.Pix');
        $fron = array('user.Id'=>'followers.Follower','interest.User_id'=>'followers.Follower');
        $frwhere = array('followers.Followee'=>$id,'user.Country'=>$country,'user.State'=>$state,'interest.Interest'=>$interest);
        $fgon = array('user.Id'=>'followers.Followee','interest.User_id'=>'followers.Followee');
        $fgwhere = array('followers.Follower'=>$id,'user.Country'=>$country,'user.State'=>$state,'interest.Interest'=>$interest);
        $start = 0;
        $lim = 50;
        $limit=array($start,$lim); 
        self::getFollower($table,$field,$fron,$frwhere,0,$limit,'follower');
        self::getFollower($table,$field,$fgon,$fgwhere,0,$limit,'following');
        return (array('state'=>true,'follower'=>self::$follower,'following'=>self::$following));
    }
    public static function getLocalFollower($id, $interest, $country, $state, $local){
        if(!$id){
            return (array('state'=>false, 'error'=>'Operation failed'));
        }
        $table = array('user','followers','interest');
        $field = array('user.Id','user.Fullname','user.User_Name','user.Pix');
        $fron = array('user.Id'=>'followers.Follower','interest.User_id'=>'followers.Follower');
        $frwhere = array('followers.Followee'=>$id,'user.Country'=>$country,'user.State'=>$state,'user.Municipal'=>$local,
            'interest.Interest'=>$interest);
        $fgon = array('user.Id'=>'followers.Followee','interest.User_id'=>'followers.Followee');
        $fgwhere = array('followers.Follower'=>$id,'user.Country'=>$country,'user.State'=>$state,'user.Municipal'=>$local,
            'interest.Interest'=>$interest);
        $start = 0;
        $lim = 50;
        $limit=array($start,$lim); 
        self::getFollower($table,$field,$fron,$frwhere,0,$limit,$id,'follower');
        self::getFollower($table,$field,$fgon,$fgwhere,0,$limit,$id,'following');
        return (array('state'=>true,'follower'=>self::$follower,'following'=>self::$following));
    }
}