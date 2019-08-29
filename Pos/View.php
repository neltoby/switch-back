<?php
class Pos_View
{
	protected static $_con;
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;

    private static function connection()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }

    public static function image_view($uid, $pid, $image)
    {
    	if(!self::$_con)
        {
            self::connection();
        }
		$column = array('Id');
        $pointer = array('User_id' => $uid, 'Post_id' => $pid, 'Image' => $image);
        $view = self::$_con->select_where('multi_image_view', $column, $pointer);
        if($view < 1)
        {
			$date = date('Y-m-d h:i:s');
	        $field = array('User_id','Post_id','Image','Date_time');
	        $value = array($uid, $pid, $image, $date);
	        self::$_con->insert('multi_image_view', $field, $value);
        }
        
    }

    public static function image_view_count($pid, $image)
    {
		if(!self::$_con)
        {
            self::connection();
        }
        $column = array('Id');
        $points = array('Post_id' => $pid, 'Image' => $image);
        return self::$_con->select_where('multi_image_view', $column, $points);
    }
}
?>