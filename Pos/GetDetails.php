<?php
class Pos_GetDetails {
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
    public static function getPix($id){
    	if(!$id){
    		return (array('state' => false, 'error' => 'No input'));
    	}
    	self::check();
    	$field = array('Pix');
    	$pointer = array('Id' => $id);
    	if(self::$_con->select_where('user',$field,$pointer) > 0 ){
    		$row = self::$_con->fetch_select_where();
    		return (array('state' => true, 'pix' => $row['Pix']));
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
    public static function getUserDetails( $name){
        if(!$name){
            return (array('state' => false, 'error' => 'Could not resolve your request'));
        }
        $username = self::fullname($name);
        if($username){
            self::check();
            if(self::$_con->select_where('user',array('*'),array('User_Name' => $username)) > 0){
                $row = self::$_con->fetch_select_where();
                $followers = self::$_con->select_where('followers',array('Id'),array('Followee'=>$row['Id']));
                $following = self::$_con->select_where('followers',array('Id'),array('Follower'=>$row['Id']));
                $post = self::$_con->select_where('posts',array('Id'),array('UserId'=>$row['Id']));
                if(!empty($row['Municipal'])){
                    $current = 'local';
                }elseif(empty($row['Municipal']) && !empty($row['State'])){
                    $current = 'state';
                }elseif(empty($row['Municipal']) && empty($row['State']) && !empty($row['Country'])){
                    $current = 'country';
                }
                $details = array('id'=>$row['Id'],'fname' => $row['Fullname'],'uname' => $row['User_Name'],'pic' => $row['Pix'],
                    'country' => $row['Country'],'state' => $row['State'],'municipal' => $row['Municipal'],
                    'followers'=>$followers,'following'=>$following,'post'=>$post,'current'=>$current);
                return array('state' => true, 'user' => $details);
            }
        }
    }
    public static function getUserInterest($id){
    	if(!$id || !ctype_digit($id)){
            return json_encode(array('state' => false, 'error' => 'Could not resolve your request'));
        }
    	$list = array('Politics','Business','Entertainment','Sport','Fashion','Beauty','Gossips',
    		'Jobs','Technology','Buy n Sell');
    	$interest = array();
    	self::check();
    	if(self::$_con->select_where_in('interest',array('Interest'),'Interest',$list,array('User_id' => $id),0) > 0){
			while($row = self::$_con->fetch_select_where_in()){
				array_push($interest, $row['Interest']);
			}
			return (array('state' => true, 'interests' => $interest));
    	}
        return (array('state' => false, 'error' => 'No result found'));
    }
}
?>