<?php
class Pos_PixUpload
{
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_con;
    protected static $_file;
    
    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    
    
    public static function upload($file=array(),$table,$pointer)
    {
        self::$_file=$file;
        if(!self::$_con)
        {
            self::connect();
        }        
        if(!isset(self::$_file))
        {
            throw new Exception("input type=file name='' Field name was not specified!
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }
        if(!is_array(self::$_file))
        {
            throw new Exception("Parameter 1 must be an array");
        }
        
        
            $validextensions = array("jpeg", "jpg", "png", "gif");
            $temporary = explode(".",self::$_file["name"]);
            $file_extension = end($temporary);
            if(((self::$_file["type"]=="image/png") || (self::$_file["type"]=="image/jpeg") ||
               (self::$_file["type"]=="image/jpg") || (self::$_file["type"]=="image/gif"))
               && (self::$_file["size"] < 100000000000000000000) && in_array($file_extension, $validextensions))
            {
                if(self::$_file["error"] > 0)
                {
                    throw new Exception("Error code:".self::$_file["error"]);
                }else
                {
                    list($width,$height,$type,$attr)=getimagesize(self::$_file["tmp_name"]);
                    $id;
                    foreach($pointer as $key=>$value){
                        $low=strtolower($key);
                        if($low == "id")
                        {
                            $id=$value;
                        }
                    }
                    $pic=$id."_".self::$_file["name"];
                    $field=array("Pix"=>$pic,"Pix_height"=>$height);
                    self::$_con->update_where($table,$field,$pointer);
                    $source = self::$_file["tmp_name"];
                    $target = "user_img/".$pic;
                    move_uploaded_file($source,$target);
                    echo $pic;
                }
            }
        
    }
    
}
?>