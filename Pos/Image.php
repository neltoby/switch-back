<?php
require_once ('C:/xampp/htdocs/oop_solutions/Pos/MysqlDB.php');
class Pos_Image{
    protected $_file;
    //protected $_name;
    protected $_inputType;
    protected $_properties;
    protected $_dbh;
    protected $_id;
    protected $_image;
    protected $_query;
    protected $_error;
    protected $_dir;
    
    public function __construct($file=array(),$id){
        if(!isset($file)){
            throw new Exception("input type=file name='' Field name was not specified!
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }
        
        if(!is_array($file)){
            throw new Exception("Parameter 1 must be a string");
        }

        if(!isset($id) || !is_numeric($id)){
            throw new Exception("Cookies id should be set and must be an integer.
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id)."); 
        }
        //$this->_file=$file;
        $this->_file=$file;
        $this->_id=$id;   
        $this->_properties=array();
        //$this->setType($type);
        
    }
    /*
    public function setType($type){
        switch($type){
            case 'post':
                $this->_inputType=$_POST;
                break;
            case 'get':
                $this->_inputType=$_GET;
                break;
        }
                foreach($this->_inputType as $key => $value){
                    if($key==$this->_name){
                        $this->_upload=$key;
                    }
                }        
    }
    */
    public function imgProperties(){
        if($this->_file["name"]=="" || $this->_file["size"]==0){
            throw new Exception("Upload can not be empty!");
        }
        
        if($this->_file["error"] != UPLOAD_ERR_OK){
            switch($this->_file["error"]){
                case UPLOAD_ERR_NO_FILE:
                    $this->_error="No file was uploaded";
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $this->_error="The uploaded file exceeded the upload_max_size directive";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->_error="The uploaded file exceeds the MAX_FILE_SIZE directive that
                    was specified in the HTML form.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->_error="File upload stopped by extension.";
                    break;
                } 
        }
        
        if($this->_error){
            throw new Exception($this->_error);
        }
        
        list($width,$height,$type,$attr)=getimagesize($this->_file["tmp_name"]);
        
                switch($type){
            case IMAGETYPE_GIF:
                $this->_image=imagecreatefromgif($this->_file["tmp_name"]);
                $ext=".gif";
                if(!$this->_image){
                    $this->_error="The file you uploaded was not a supported gif filetype.";
                }
                break;
            case IMAGETYPE_JPEG:
                $this->_image=imagecreatefromjpeg($this->_file["tmp_name"]);
                $ext=".jpeg";
                if(!$this->_image){
                    $this->_error="The file you uploaded was not a supported jpg filetype.";
                }
                break;
            case IMAGETYPE_PNG:
                $this->_image=imagecreatefrompng($this->_file["tmp_name"]);
                $ext=".png";
                if(!$this->_image){
                    $this->_error="The file you uploaded was not a supported png filetype.";
                }
                break;
            default:
                $this->_error="The file you uploaded was not a supported filetype.";
        }
        
        if($this->_error){
            throw new Exception($this->_error);
        }        
        
        $this->_properties["Width"]=$width;
        $this->_properties["Height"]=$height;
        $this->_properties["Type"]=$type;
        $this->_properties["Attr"]=$attr;
        $this->_properties["Ext"]=$ext;
        //$this->_image=$image;
        return $this->_properties;
        
    }
    
    public function setQuery($query){
        if(!isset($query)){
            throw new Exception("setQuery expects 1 parameter, none given.");
        }
        $this->_query=$query;
        if(!$this->_query){
            throw new Exception("Query not successfully set");
        }
        
    }
    
    public function process($host,$user,$password,$database,$dir){
        if(!isset($host) || !isset($user) || !isset($password) || !isset($database) || !isset($dir)){
            throw new Exception("process function expects 5 parameters in the order
                                Host,User,Password,Database,Directory.");
        }
        $this->_dbh=new Pos_MysqlDB($host,$user,$password,$database);
        //$this->_dbh=mysqli_connect($host,$user,$password);
        $this->_dir=$dir;
        /*
        if(!$this->_dbh){
            throw new Exception("Unable to connect to server");
        }
        
        if(!mysqli_select_db($this->_dbh,$database)){
            throw new Exception("Connection to database failed");
        }
        */
          $container=$this->imgProperties();
          $pos=$this->_id."_".$this->_file["name"];
          $this->_dbh->connect();
          $this->_dbh->execute($this->_query);
         /*
          if(!mysqli_query($this->_dbh,$this->_query)){
              throw new Exception("Query was not successful");
          }
          */
          switch($container["Type"]){
            case IMAGETYPE_GIF:
                imagegif($this->_image,$this->_dir.$pos);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($this->_image,$this->_dir.$pos,100);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->_image,$this->_dir.$pos);
                break;
          }
          imagedestroy($this->_image);
          return $pos;
        
    }
}


?>