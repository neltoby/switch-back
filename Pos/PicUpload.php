<?php
class Pos_PicUpload
{
    protected $_host;
    protected $_user;
    protected $_pass;
    protected $_db;
    protected $_target;
    protected $_con;
    protected $_file;
    
    public function __construct($host,$user,$pass,$db,$target)
    {
        $this->_host=$host;
        $this->_user=$user;
        $this->_pass=$pass;
        $this->_db=$db;
        $this->_target=$target;
        $this->connect();
    }
    
    private function connect()
    {
        $this->_con = new Pos_Process($this->_host,$this->_user,$this->_pass,$this->_db);
    }
    
    public function upload($file=array(),$table,$pointer)
    {
        $this->_file=$file;
        if(!$this->_con)
        {
            $this->connect();
        }        
        if(!isset($this->_file))
        {
            throw new Exception("input type=file name='' Field name was not specified!
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }
        if(!is_array($this->_file))
        {
            throw new Exception("Parameter 1 must be an array");
        }
        if(!is_string($this->_target))
        {
            throw new Exception("Parameter 5 must be a string");
        }
        
        
            $validextensions = array("jpeg", "jpg", "png", "gif");
            $temporary = explode(".",$this->_file["name"]);
            $file_extension = end($temporary);
            if((($this->_file["type"]=="image/png") || ($this->_file["type"]=="image/jpeg") ||
               ($this->_file["type"]=="image/jpg") || ($this->_file["type"]=="image/gif"))
               && ($this->_file["size"] < 100000000000000000000) && in_array($file_extension, $validextensions))
            {
                if($this->_file["error"] > 0)
                {
                    throw new Exception("Error code:".$this->_file["error"]);
                }else
                {
                    list($width,$height,$type,$attr)=getimagesize($this->_file["tmp_name"]);
                    $id;
                    foreach($pointer as $key=>$value){
                        $low=strtolower($key);
                        if($low == "id")
                        {
                            $id=$value;
                        }
                    }
                    
                    $pix=array("Pix");
                    $this->_con->select_where($table,$pix,$pointer);
                    $chk_pix=$this->_con->fetch_select_where();
                    if(!empty($chk_pix[0]))
                    {
                        if(file_exists($this->_target. DIRECTORY_SEPARATOR.$chk_pix[0]))
                        {
                            unlink($this->_target. DIRECTORY_SEPARATOR.$chk_pix[0]);
                        }
                    }
                    $pic=$id."_".$this->_file["name"];
                    //$ext=".".$file_extension;
                    $field=array("Pix"=>$pic,"Pix_height"=>$height);
                    $this->_con->update_where($table,$field,$pointer);
                    $source = $this->_file["tmp_name"];
                    $target = $this->_target. DIRECTORY_SEPARATOR.$pic;
                    move_uploaded_file($source,$target);
                    return  $pic;
                }
            }
        
    }
    
}
    

?>