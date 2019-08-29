<?php
// require_once ('C:\xampp\htdocs\oop_solutions\Pos\MysqlStatement.php');
class Pos_MysqlDB{
    protected $_dbh;
    protected $_database;
    protected $_result;
    protected $_user;
    protected $_host;
    protected $_password;
    protected $_count;
     
     public function __construct($host,$user,$password,$database){
        $this->_user=$user;
        $this->_host=$host;
        $this->_password=$password;
        $this->_database=$database;
     }
     public function connect(){
        $this->_dbh= new mysqli($this->_host,$this->_user,$this->_password,$this->_database);
            if($this->_dbh->connect_error){
               throw new Exception("Connection to database failed: ".mysqli_error($this->_dbh)); 
            } 
             $this->_dbh->set_charset('utf8mb4')
             ;  
     }
     
     public function execute($query){
        if(!$this->_dbh){
            $this->connect(); 
        }

            $ret = mysqli_query($this->_dbh,$query);
            $this->_result = $ret;
            if(!$ret){
                throw new Exception("Query not executed");                
            }else{
             $chk=explode(" ",$query);
             if(strtolower($chk[0]) == "insert"){
                return mysqli_insert_id($this->_dbh);
             }                
                }            
            /*
            if(!$ret){
                throw new Exception("Query not executed");
            }
            elseif(!is_resource($ret)){
                return TRUE;
            }else{
                $stmt = new MysqlStatement($this->_dbh,$query);
                $stmt->result = $ret;
                return $stmt;
            }
            */
     }
     
     public function paginate(){
        $query="SELECT FOUND_ROWS();";
        if(!$this->_dbh){
            $this->connect(); 
        }
        $this->_count=mysqli_query($this->_dbh,$query);
        if(!$this->_count){
            throw new Exception("Query not executed"); 
        }
     }
     
     public function fetch_array(){
        if(!$this->_result){
            throw new Exception("Execute the main query first");
        }        
        return mysqli_fetch_array($this->_result);
    }
    
    public function pag_fetch_array(){
        if(!$this->_count){
            throw new Exception("Execute the number of rows query first");
        }
        return mysqli_fetch_array($this->_count);
    }

     public function num_rows(){
        if(!$this->_result){
            throw new Exception("Execute the query first");
        }        
        return mysqli_num_rows($this->_result);        
     }     
}
?>