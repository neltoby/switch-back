<?php
class MysqlStatement{
    public $result;
    protected $_query;
    protected $_dbh;
    
    public function __construct($dbh,$query){
        $this->_dbh = $dbh;
        $this->_query = $query;
        if(!is_resource($dbh)){
            throw new Exception("Not a valid database handle");
        }
    }
    
    public function fetch_row(){
        if(!$this->result){
            throw new Exception("Query not executed");
        }
        return mysqli_fetch_row($this->result);
    }
    
    public function fetch_assoc(){
        return mysqli_fetch_assoc($this->result);
    }
    
    public function fetch_array(){
        return mysqli_fetch_array($this->result);
    }
    
    public function fetchall_assoc(){
        $retval = array();
        while($row = $this->fetch_assoc()){
            $retval[] = $row;
        }
        return $fetch;
     }    
}
?>