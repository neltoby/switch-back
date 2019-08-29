<?php
class Pos_Notification {
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
    public static function userNotification($id,$interest){
        if(!$id || !ctype_digit($id)){
            return json_encode(array('state'=>false,'error'=>'Could not process request'));
        }
        self::check();
        $follower = self::$_con->select_where('followers',array('Id'),array('Followee'=>$id,'Status'=>'Unseen'));
        $turnoff = self::$_con->select_where('interest_turnoff',array('Id'),array('Turn_Off'=>$id));
        $total = $follower + $turnoff;
        return json_encode(array('state'=>true,'total'=>$total));
    }
}
?>