<?php
//require_once('C:\xampp\htdocs\oop_solutions\TGLN\Pos\MysqlDB.php');
class Pos_Process extends Pos_MysqlDB
{
    // protected $_fetched;
    // protected $_fetch;
    // protected $_fetch_like;
    // protected $_join_search_fetch;
    // protected $_join_search_fetch_i;
    // protected $_fetch_in;
    // protected $_all_fetch;
    // protected $_join_fetch;
    // protected $_join_like_fetch;
    // protected $_join_fetch_ii;
    // protected $_join_fetch_distinct;
    // protected $_total_join;
    // protected $_total_join_like;
    // protected $_total_join_ii;
    // protected $_total_join_search;
    // protected $_total_join_search_i;
    // protected $_total_join_distinct;
    protected $_sql2;
    protected $_sqlLike;
    protected $_sql2_ii;
    protected $_sqlSearch;
    protected $_sqlSearch_i;
    protected $_stmtSelect;
    protected $_stmtSelectWhere;
    protected $_stmtSelectWhereLike;
    protected $_stmtSelectWhereIn;
    protected $_stmtSelectAllWhere;
    protected $_stmtJoin;
    protected $_stmtJoinII;
    protected $_stmtJoinLike;
    protected $_stmtJoinSearch;
    protected $_stmtJoinSearchI;
    protected $_stmtJoinDistinct;
    public function __construct($host, $user, $pass, $db)
    {
        parent::__construct($host,$user,$pass,$db);
        parent::connect();

    }
    
    
    public function update_where($table, $fields=array(), $pointer=array(),$bind=false)
    {
        if(!$this->_dbh){
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        if(!is_null($fields) && !is_array($fields))
        {
            throw new Exception("The second parameter must be field(s) name and must be an associative array,
                                even if only one field is required.");
        }
        if(!isset($pointer))
        {
            throw new Exception("The third parameter can not be empty!");
        }
        if(!is_null($pointer) && !is_array($pointer))
        {
            throw new Exception("The third parameter must be an array!");
        }
        $num=count($fields);
        $sql="UPDATE $table SET ";
        $i=0;
        foreach($fields as $key => $value)
        {
           $i++;
           if($i==$num){
            $sql.=$key."= ? ";
           }else{
            $sql.=$key."= ? ,";
           }
           
        }
        $sql.=" WHERE ";
        $num_pt=count($pointer);
        $ii=0;
        foreach($pointer as $keys => $values)
        {
            $ii++;
            if($ii==$num_pt){
                $sql.=$keys."= ? ";
            }else
            {
                $sql.=$keys."= ? AND ";
            }
        }
        $tot = $i + $ii;
        if(!$bind){
            for ($i = 0; $i < $tot; $i++) {
                $bind.='s';
            }
        }
        $fvalues = array_values($fields);
        $pvalues = array_values($pointer);
        // for ($i = 0; $i < count($fd); $i++) {
        //     $val.= $fd[$i].',';
        // }
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$fvalues, ...$pvalues);
        if($stmt->execute())
        {
            $stmt->close();
            return true;
        }else{
            $stmt->close();
            return("Query failed :".mysqli_error($this->_dbh));
        }
    }
    
    public function insert($table,$fields=array(),$values=array(),$bind=false)
    {
        if(!$this->_dbh){
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        if(!is_null($fields) && !is_array($fields))
        {
            throw new Exception("The second parameter must be field(s) name and must be an array,
                                even if only one field is required.");
        }
        if(!isset($values) || !is_array($values)){
            throw new Exception("The third parameter must be field values and must be an array");
        }
        
        $num=count($fields);
        $val=count($values);
        if($num != $val)
        {
            if($num > $val)
            {
                throw new Exception("Number of fields is greater than the values to be inserted!");
            }else{
                throw new Exception("Number of values to be inserted is greater than the fields!");
            }
            
        }
        
        $sql="INSERT INTO $table(";
        for($i=0; $i<$num; $i++){
            if($i==($num-1))
            {
                $sql.=$fields[$i];
            }else{
                $sql.=$fields[$i].",";
            }     
        }
        $sql.=")VALUES(";
        for($i=0; $i<$val; $i++)
        {
            if($i==($val-1))
            {
                $sql.=" ? ";
            }else
            {
                $sql.=" ?, ";
            }
        }
        
        $sql.=")";
        if(!$bind){
            for ($i = 0; $i < $val; $i++) {
                $bind.='s';
            }
        }
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        if($stmt->execute())
        {
            return $this->_dbh->insert_id;
        }else{
            throw new Exception("Query failed .$this->_dbh->connect_error");
        }
        
    }
    
    public function select($table,$limit=false,$order=false)
    {
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        $sql="SELECT * FROM $table";
        
        if($limit)
        {
            if(!is_array($limit))
            {
                throw new Exception("Parameter two must be an array and
                                    can have only two elements in the array");
            }
            //$limit=array();
            $numLimit=count($limit);
            if($numLimit != 2){
                throw new Exception("Parameter two must have only two elements");
            }else
            {
                if(!is_numeric($limit[0]) || !is_numeric($limit[1]))
                {
                    throw new Exception("Elements of parameter two must be integers");
                }
                
                $sql.=" LIMIT ".$limit[0].",".$limit[1];
            }
            
 
        }
        
        if($order){
            if(!is_array($order)){
                throw new Exception("Parameter three must be an associative array");
            }else
            {
                $sql.=" ORDER BY ";
                $numOrder=count($order);
                $i=0;
                foreach($order as $key=>$value)
                {
                    $i++;
                    $values=strtolower($value);
                    if($values == "desc" || $values == "asc")
                    {
                        if($i==$numOrder)
                        {
                            $sql.=$key." ".$values;
                        }else
                        {
                            $sql.=$key." ".$values.",";
                        }
                    }
                }
                
            }
        }
        $stmt = $this->_dbh->prepare($sql);
        // $this->_stmtSelect->prepare($sql);
        if($stmt->execute())
        {
            $this->_stmtSelect = $stmt->get_result();
            return $this->_stmtSelect->num_rows;
        }else{
             throw new Exception("Query failed .$this->_dbh->connect_error");
        }
    }
    
    public function fetch_select()
    {
        if(!$this->_stmtSelect)
        {
            throw new Exception("select method has not been called");
        }
        return $this->_stmtSelect->fetch_assoc();
    }
    
    public function select_where($table,$fields=array(),$pointer=array(),$bind=false,$distinct=false)
    {
        if(!$this->_dbh){
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        if(!is_null($fields) && !is_array($fields))
        {
            throw new Exception("The second parameter must be field(s) name and must be an array,
                                even if only one field is required.");
        }
        if(!isset($pointer) || !is_array($pointer)){
            throw new Exception("The third parameter must be field values and must be an associative array");
        }
        
        $num=count($fields);
        $numPointer=count($pointer);
        $dist = $distinct ? 'DISTINCT ' : '' ;
        $sql="SELECT $dist";
        for($i=0; $i<$num; $i++)
        {
            if($i==$num-1){
                $sql.=$fields[$i];
            }else{
                $sql.=$fields[$i].",";
            }
        }
        $sql.=" FROM $table WHERE ";
        $ii=0;
        foreach($pointer as $key => $val)
        {
            $ii++;
            if($ii==$numPointer)
            {
                $sql.=$key."= ?";
            }else
            {
                $sql.=$key."= ? AND ";
            }
        }
        if(!$bind){
            for ($i = 0; $i < $numPointer; $i++) {
                $bind.='s';
            }
        }
        $values = array_values($pointer);
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        if($stmt->execute())
        {
            // $stmt->store_result();
            $this->_stmtSelectWhere = $stmt->get_result();
            // echo '  '. $this->_stmtSelectWhere->num_rows.' number of rows affected' ;
            return $this->_stmtSelectWhere->num_rows;
        }else{
             throw new Exception("Query failed .$this->_dbh->connect_error");
        }
        
    }

    public function fetch_select_where()
    {
        if($this->_stmtSelectWhere){
            return $this->_stmtSelectWhere->fetch_assoc();           
        }else{
            throw new Exception("select_where method has not been called");
        }
        
        
    }

    public function select_where_like($table,$fields=array(),$pointer=array(),$distinct=false,$bind=false)
    {
        if(!$this->_dbh){
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        if(!is_null($fields) && !is_array($fields))
        {
            throw new Exception("The second parameter must be field(s) name and must be an array,
                                even if only one field is required.");
        }
        if(!isset($pointer) || !is_array($pointer)){
            throw new Exception("The third parameter must be field values and must be an associative array");
        }
        
        $num=count($fields);
        $numPointer=count($pointer);
        $sql="SELECT ";
        if($distinct){
            $sql.="DISTINCT ";
        }
        for($i=0; $i<$num; $i++)
        {
            if($i==$num-1){
                $sql.=$fields[$i];
            }else{
                $sql.=$fields[$i].",";
            }
        }
        $sql.=" FROM $table WHERE ";
        $ii=0;
        foreach($pointer as $key => $value)
        {
            $ii++;
            if($ii==$numPointer)
            {
                $sql.=$key." LIKE ?";
            }else
            {
                $sql.=$key." LIKE ? AND ";
            }
        }
        // echo $sql;
        if(!$bind){
            for ($i = 0; $i < $numPointer; $i++) {
                $bind.='s';
            }
        }
        $values = array_values($pointer);
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        // $stmt->prepare($sql);
        if($stmt->execute())
        {
            $this->_stmtSelectWhereLike = $stmt->get_result();
            return $this->_stmtSelectWhereLike->num_rows;
        }else{
             throw new Exception("Query failed .$this->_dbh->connect_error");
        }
        
    }

    public function fetch_select_where_like()
    {
        if($this->_stmtSelectWhereLike){
            return $this->_stmtSelectWhereLike->fetch_assoc();           
        }else{
            throw new Exception("select_where method has not been called");
        }
               
    }

    public function select_where_in($table,$fields=array(),$column,$point=array(),$pointer=false,$in=true,$bind=false)
    {
        if(!$this->_dbh){
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        if(!is_null($fields) && !is_array($fields))
        {
            throw new Exception("The second parameter must be field(s) name and must be an array,
                                even if only one field is required.");
        }
        if(!isset($column) || !is_string($column))
        {
            throw new Exception("the third parameter must be a column name");       
        }
        if(!isset($point) || !is_array($point)){
            throw new Exception("The fourth parameter must be field values and must be an associative array");
        }
        if($pointer){
            if(!is_array($pointer)){
                throw new Exception("The fifth parameter must be field values and must be an associative array");
            }
        }
        
        $num=count($fields);
        $numPointer=count($pointer);
        $numPoint=count($point);
        $sql="SELECT ";       
        $operator = !$in ?  " NOT IN " : " IN ";       
        for($i=0; $i<$num; $i++)
        {
            if($i==$num-1){
                $sql.=$fields[$i];
            }else{
                $sql.=$fields[$i].",";
            }
        }
        $sql.=" FROM $table WHERE $column $operator (";
        $ii=0;
        foreach($point as $value)
        {
            $ii++;
            if($ii==$numPoint)
            {
                $sql.=" ? )";
            }else
            {
                $sql.=" ?, ";
            }
        }
        if($pointer){
            $i=0;
            $sql.=' AND ';
            foreach($pointer as $key => $value)
            {
                $i++;
                if($i==$numPointer)
                {
                    $sql.=$key." = ?";
                }else
                {
                    $sql.=$key." = ? AND ";
                }
            }
        }
        if(!$bind){
            for ($i = 0; $i < $numPoint; $i++) {
                $bind.='s';
            }
            for ($i = 0; $i < $numPointer; $i++) {
                $bind.='s';
            }
        }
        $values =isset($pointer) && is_array($pointer) ? array_values($pointer): [];
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$point, ...$values);
        if($stmt->execute())
        {
            $this->_stmtSelectWhereIn = $stmt->get_result();
            return $this->_stmtSelectWhereIn->num_rows;
        }else{
             throw new Exception("Query failed :".$this->_dbh->connect_error);
        }
        
    }

    public function fetch_select_where_in()
    {
        if(!$this->_stmtSelectWhereIn){
            throw new Exception("select_where method has not been called");
        }
        return $this->_stmtSelectWhereIn->fetch_assoc();
        
    }
    
    public function select_all_where($table,$pointer=array(),$order=false,$limit=false,$bind){
        if(!$this->_dbh){
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        if(!isset($pointer) || !is_array($pointer)){
            throw new Exception("The third parameter must be field values and must be an associative array");
        }
        if($limit)  
        {
            if(!is_array($limit))
            {
                throw new Exception("The fourth parameter must be an array of numbers");
            }
        $numLimit=count($limit);
        }
        if($limit && $numLimit > 2)
        {
            throw new Exception("Limit can only accept 2 element in its array");
        }
        if($order && !is_array($order))
        {
            throw new Exception("The third parameter must be field name and value  and must be an array");
        }
        $sql="SELECT * FROM $table WHERE ";
        $num=count($pointer);
        $i=0;
        foreach($pointer as $key=>$value){
            $i++;
            if($i==$num){
                $needle = '!';
                $values=substr_count($key,$needle);
                if($values > 0){
                    $keys =str_replace('!', '', $key);
                    $sql.=$keys."!= ?";
                }else{
                    $sql.=$key."= ?";
                }           
            }else{
                $needle = '!';
                $values=substr_count($key,$needle);
                if($values > 0){
                    $keys =str_replace('!', '', $key);
                    $sql.=$keys."!= ? AND ";
                }else{
                    $sql.=$key."= ? AND ";
                }            
            }
        }

        if($order)
        {
            $numOrder=count($order);
            $sql.=" ORDER BY ";
            $i=0;
            foreach($order as $key=>$value)
            {
                $i++;
                $values=strtolower($value);
                
                if($values == "desc" || $values == "asc")
                {
                    if($i==$numOrder)
                    {
                        $sql.=$key." ".$values;
                    }else
                    {
                        $sql.=$key." ".$values.",";
                    }
                    
                }
                /*else
                {
                    throw new Exception("Incorrect element.Element must be either 'DESC' OR 'ASC'");
                }
                */
                
            }
            
        }

        if($limit)
        {
            $sql.=" LIMIT ".$limit[0].", ".$limit[1];
        } 
        if(!$bind){
            for ($i = 0; $i < $num; $i++) {
                $bind.='s';
            }
        }
        $values = array_values($pointer);
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
    
        if($stmt->execute())
        {
            $this->_stmtSelectAllWhere = $stmt->get_result();
            return $this->_stmtSelectAllWhere->num_rows;
        }else{
             throw new Exception("Query failed .$this->_dbh->connect_error");
        }
        
    }
    
    public function fetch_select_all_where()
    {
        if($this->_stmtSelectAllWhere){
            return $this->_stmtSelectAllWhere->fetch_assoc();            
        }else{
            throw new Exception("select_all_where method has not been called");
        }
               
    }

    public function join_like($tables=array(),$fields=array(),$on=array(),$where=false,$order=false,$limit=false,$bind=false)
    {
        $numLimit;
        
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!$tables || !is_array($tables))
        {
            throw new Exception("The first parameter must be table names and must be an array");
        }
        if(!isset($fields) || !is_array($fields))
        {
            throw new Exception("The second parameter must be field names and must be an associative array");
        }
        if(!isset($on) || !is_array($on))
        {
            throw new Exception("The third parameter must be field name  and must be an array");
        }
        if($where && !is_array($where))
        {
            throw new Exception("The fourth parameter must be field name  and must be an array");
        }
        if($limit)  
        {
            if(!is_array($limit))
            {
                throw new Exception("The fifth parameter must be an array of numbers");
            }
        $numLimit=count($limit);
        }
        if($limit && $numLimit > 2)
        {
            throw new Exception("Limit can only accept 2 element in its array");
        }
        if($order && !is_array($order))
        {
            throw new Exception("The fifth parameter must be field name and value  and must be an array");
        }
        /*
        if($limit)
        {
            for($i=0; $i<$numLimit; $i++)
            {
                if(!is_double($limit[$i]))
                {
                    if($i==0)
                    {
                        throw new Exception("First limit elements must be an integer");
                    }else{
                        throw new Exception("Second imit elements must be an integer");
                    }
                    
                }
            }
        }
        */
 
        $numTable=count($tables);
        $numField=count($fields);
        $numOn=count($on);
        $numWhere=count($where);
        
        for($i=0; $i<$numTable; $i++)
        {
            if(!is_string($tables[$i]))
            {
                throw new Exception("Table names must be a string");
            }
        }
    
        for($i=0; $i<$numField; $i++)
        {
            if(!is_string($fields[$i]))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        
        foreach($on as $key=>$value)
        {
            if(!is_string($key) || !is_string($value))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        $sql="SELECT SQL_CALC_FOUND_ROWS ";
        $i=0;
        for($i=0; $i<$numField; $i++)
        {
            if($i==$numField-1){
                $sql.=$fields[$i];
            }else
            {
                $sql.=$fields[$i].",";
            }
            
        }
        $sql.=" FROM ";
        $i=0;
        for($i=0; $i<$numTable; $i++)
        {
            if($i==0)
            {
                $sql.=$tables[$i];
            }else
            {
                $x=($i-1);
                $a=array();
                $b=array();
                    foreach($on as $key=>$value)
                    {
                        $a[]=$key;
                        $b[]=$value;
                    }
                    $sql.=" JOIN ".$tables[$i]." ON ".$a[$x]." = ".$b[$x]." ";
                    
            }
        }
        
        if($where)
        {
            //$where=array();
            $sql.="WHERE ";
            $i=0;
            foreach($where as $key=>$value)
            {
                $i++;
                if($i==$numWhere){
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                            $needle = '(';
                            $values=substr_count($keys,$needle);
                            if($values > 0){
                                $keys =str_replace('(', '', $keys);
                                $sql.=$keys." NOT LIKE ? ";
                            }else {
                                $keys =str_replace('(', '', $keys);
                                $sql.=$keys." != ? ";
                            }
                    }else{
                        $needle = '(';
                        $values=substr_count($key,$needle);
                        if($values > 0){
                             $keys =str_replace('(', '', $key);
                            $sql.=$keys." LIKE ? ";
                        }else{
                             $keys =str_replace('(', '', $key);
                            $sql.=$keys." = ? ";
                        }
                        
                    }
                }else
                {
                    $needle = '[]';
                    $values = substr_count($key,$needle);
                    if($values > 0){
                        $key = str_replace('[]', '', $key);
                        $andor = 'OR';
                    }else{
                        $andor = 'AND';
                    }
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                            $needle = '(';
                            $values=substr_count($keys,$needle);
                            if($values > 0){
                                $keys =str_replace('(', '', $keys);
                                $sql.=$keys." NOT LIKE ? ";                                
                            }else {
                                $keys =str_replace('(', '', $keys);
                                $sql.=$keys." != ? ";
                            }
                    }else{
                        $needle = '(';
                        $values=substr_count($key,$needle);
                        if($values > 0){
                             $keys =str_replace('(', '', $key);
                            $sql.=$keys." LIKE ? ".$andor." ";
                        }else{
                             $keys =str_replace('(', '', $key);
                            $sql.=$keys." = ? ".$andor." ";
                        }
                        
                    }
                }
            }
        }
        

        
        if($order)
        {
            $numOrder=count($order);
            $sql.=" ORDER BY ";
            $i=0;
            foreach($order as $key=>$value)
            {
                $i++;
                $values=strtolower($value);
                
                if($values == "desc" || $values == "asc")
                {
                    if($i==$numOrder)
                    {
                        $sql.=$key." ".$values;
                    }else
                    {
                        $sql.=$key." ".$values.",";
                    }
                    
                }/*else
                {
                    throw new Exception("Incorrect element.Element must be either 'DESC' OR 'ASC'");
                }
                */
                
            }
            
        }
        
        if($limit)
        {
            $sql.=" LIMIT ".$limit[0].", ".$limit[1];
        }
        if(!$bind){
            for ($i = 0; $i < $numWhere; $i++) {
                $bind.='s';
            }
        }
        $values = array_values($where);         
        $sql2="SELECT FOUND_ROWS();";
        // $this->_total_join_like =mysqli_query($this->_dbh,$sql2);
        // echo $sql.'<br/>'.$numOn;
        // $this->_sqlLike=$sql2;
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        if($stmt->execute())
        {
            $this->_stmtJoinLike = $stmt->get_result();
            return $this->_stmtJoinLike->num_rows;
        }else{
             throw new Exception("Query failed : ".$this->_dbh->connect_error);
        }
            
    }
    
    public function join($tables=array(),$fields=array(),$on=array(),$where=false,$order=false,$limit=false,$bind=false)
    {
        $numLimit;
        
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!$tables || !is_array($tables))
        {
            throw new Exception("The first parameter must be table names and must be an array");
        }
        if(!isset($fields) || !is_array($fields))
        {
            throw new Exception("The second parameter must be field names and must be an associative array");
        }
        if(!isset($on) || !is_array($on))
        {
            throw new Exception("The third parameter must be field name  and must be an array");
        }
        if($where && !is_array($where))
        {
            throw new Exception("The fourth parameter must be field name  and must be an array");
        }
        if($limit)  
        {
            if(!is_array($limit))
            {
                throw new Exception("The fifth parameter must be an array of numbers");
            }
        $numLimit=count($limit);
        }
        if($limit && $numLimit > 2)
        {
            throw new Exception("Limit can only accept 2 element in its array");
        }
        if($order && !is_array($order))
        {
            throw new Exception("The fifth parameter must be field name and value  and must be an array");
        }
        /*
        if($limit)
        {
            for($i=0; $i<$numLimit; $i++)
            {
                if(!is_double($limit[$i]))
                {
                    if($i==0)
                    {
                        throw new Exception("First limit elements must be an integer");
                    }else{
                        throw new Exception("Second imit elements must be an integer");
                    }
                    
                }
            }
        }
        */
 
        $numTable=count($tables);
        $numField=count($fields);
        $numOn=count($on);
        $numWhere=count($where);
        
        for($i=0; $i<$numTable; $i++)
        {
            if(!is_string($tables[$i]))
            {
                throw new Exception("Table names must be a string");
            }
        }
    
        for($i=0; $i<$numField; $i++)
        {
            if(!is_string($fields[$i]))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        
        foreach($on as $key=>$value)
        {
            if(!is_string($key) || !is_string($value))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        $sql="SELECT SQL_CALC_FOUND_ROWS ";
        $i=0;
        for($i=0; $i<$numField; $i++)
        {
            if($i==$numField-1){
                $sql.=$fields[$i];
            }else
            {
                $sql.=$fields[$i].",";
            }
            
        }
        $sql.=" FROM ";
        $i=0;
        for($i=0; $i<$numTable; $i++)
        {
            if($i==0)
            {
                $sql.=$tables[$i];
            }else
            {
                $x=($i-1);
                $a=array();
                $b=array();
                    foreach($on as $key=>$value)
                    {
                        $a[]=$key;
                        $b[]=$value;
                    }
                    $sql.=" JOIN ".$tables[$i]." ON ".$a[$x]." = ".$b[$x]." ";
                    
            }
        }
        
        if($where)
        {
            //$where=array();
            $sql.="WHERE ";
            $i=0;
            foreach($where as $key=>$value)
            {
                $i++;
                if($i==$numWhere){
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys." !=? ";
                    }else{
                        $sql.=$key." =? ";
                    }
                    // $sql.=$key."='".$value."'";
                }else
                {
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys." !=? AND ";
                    }else{
                        $sql.=$key." =? AND ";
                    } 
                    // $sql.=$key."='".$value."' AND ";
                }
            }
        }
        

        
        if($order)
        {
            $numOrder=count($order);
            $sql.=" ORDER BY ";
            $i=0;
            foreach($order as $key=>$value)
            {
                $i++;
                $values=strtolower($value);
                
                if($values == "desc" || $values == "asc")
                {
                    if($i==$numOrder)
                    {
                        $sql.=$key." ".$values;
                    }else
                    {
                        $sql.=$key." ".$values.",";
                    }
                    
                }/*else
                {
                    throw new Exception("Incorrect element.Element must be either 'DESC' OR 'ASC'");
                }
                */
                
            }
            
        }
        
        if($limit)
        {
            $sql.=" LIMIT ".$limit[0].", ".$limit[1];
        }        
   
        if(!$bind){
            for ($i = 0; $i < $numWhere; $i++) {
                $bind.='s';
            }
        }
        $values = array_values($where);
        $sql2="SELECT FOUND_ROWS();";
        // $this->_total_join_like =mysqli_query($this->_dbh,$sql2);
        // echo $sql.'<br/>'.$numOn;
        // $this->_sqlLike=$sql2;
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        if($stmt->execute())
        {
            $this->_stmtJoin = $stmt->get_result();
            // echo $this->_stmtJoin->num_rows.' from join statement';
            return $this->_stmtJoin->num_rows;
        }else{
             throw new Exception("Query failed : ".$this->_dbh->connect_error);
        }
            
    }
    
    public function join_ii($tables=array(),$fields=array(),$on=array(),$where=false,$order=false,$limit=false,$bind=false)
    {
        $numLimit;
        
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!$tables || !is_array($tables))
        {
            throw new Exception("The first parameter must be table names and must be an array");
        }
        if(!isset($fields) || !is_array($fields))
        {
            throw new Exception("The second parameter must be field names and must be an associative array");
        }
        if(!isset($on) || !is_array($on))
        {
            throw new Exception("The third parameter must be field name  and must be an array");
        }
        if($where && !is_array($where))
        {
            throw new Exception("The fourth parameter must be field name  and must be an array");
        }
        if($limit)  
        {
            if(!is_array($limit))
            {
                throw new Exception("The fifth parameter must be an array of numbers");
            }
        $numLimit=count($limit);
        }
        if($limit && $numLimit > 2)
        {
            throw new Exception("Limit can only accept 2 element in its array");
        }
        if($order && !is_array($order))
        {
            throw new Exception("The six parameter must be field name and value  and must be an array");
        }
        /*
        if($limit)
        {
            for($i=0; $i<$numLimit; $i++)
            {
                if(!is_double($limit[$i]))
                {
                    if($i==0)
                    {
                        throw new Exception("First limit elements must be an integer");
                    }else{
                        throw new Exception("Second imit elements must be an integer");
                    }
                    
                }
            }
        }
        */
 
        $numTable=count($tables);
        $numField=count($fields);
        $numOn=count($on);
        $numWhere=count($where);
        
        for($i=0; $i<$numTable; $i++)
        {
            if(!is_string($tables[$i]))
            {
                throw new Exception("Table names must be a string");
            }
        }
    
        for($i=0; $i<$numField; $i++)
        {
            if(!is_string($fields[$i]))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        
        foreach($on as $key=>$value)
        {
            if(!is_string($key) || !is_string($value))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        $sql="SELECT SQL_CALC_FOUND_ROWS ";
        $i=0;
        for($i=0; $i<$numField; $i++)
        {
            if($i==$numField-1){
                $sql.=$fields[$i];
            }else
            {
                $sql.=$fields[$i].",";
            }
            
        }
        $sql.=" FROM ";
        $i=0;
        for($i=0; $i<$numTable; $i++)
        {
            if($i==0)
            {
                $sql.=$tables[$i];
            }else
            {
                $x=($i-1);
                $a=array();
                $b=array();
                    foreach($on as $key=>$value)
                    {
                        $a[]=$key;
                        $b[]=$value;
                    }
                    $sql.=" JOIN ".$tables[$i]." ON ".$a[$x]."=".$b[$x]." ";
                    
            }
        }
        
        if($where)
        {
            //$where=array();
            $sql.="WHERE ";
            $i=0;
            foreach($where as $key=>$value)
            {
                $i++;
                if($i==$numWhere){
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys."!= ? ";
                    }else{
                        $sql.=$key."= ? ";
                    }
                    // $sql.=$key."='".$value."'";
                }else
                {
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys."!= ? AND ";
                    }else{
                        $sql.=$key."= ? AND ";
                    }
                    // $sql.=$key."='".$value."' AND ";
                }
            }
        }
        

        
        if($order)
        {
            $numOrder=count($order);
            $sql.=" ORDER BY ";
            $i=0;
            foreach($order as $key=>$value)
            {
                $i++;
                $values=strtolower($value);
                
                if($values == "desc" || $values == "asc")
                {
                    if($i==$numOrder)
                    {
                        $sql.=$key." ".$values;
                    }else
                    {
                        $sql.=$key." ".$values.",";
                    }
                    
                }/*else
                {
                    throw new Exception("Incorrect element.Element must be either 'DESC' OR 'ASC'");
                }
                */
                
            }
            
        }
        
        if($limit)
        {
            $sql.=" LIMIT ".$limit[0].", ".$limit[1];
        }        
   
        if(!$bind){
            for ($i = 0; $i < $numWhere; $i++) {
                $bind.='s';
            }
        }
        $values = array_values($where);         
        $sql2="SELECT FOUND_ROWS();";
        // $this->_total_join_like =mysqli_query($this->_dbh,$sql2);
        // echo $sql.'<br/>'.$numOn;
        // $this->_sqlLike=$sql2;
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        if($stmt->execute())
        {
            $this->_stmtJoinII = $stmt->get_result();
            return $this->_stmtJoinII->num_rows;
        }else{
             throw new Exception("Query failed .$this->_dbh->connect_error");
        }
        
        
    }  

    public function join_search($tables=array(),$fields=array(),$match=array(),$seed,$on=array(),$where=false,$order=false,$limit=false)
    {
        $numLimit;
        
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!$tables || !is_array($tables))
        {
            throw new Exception("The first parameter must be table names and must be an array");
        }
        if(!isset($fields) || !is_array($fields))
        {
            throw new Exception("The second parameter must be field names and must be an associative array");
        }
        if(!isset($match) || !is_array($match))
        {
            throw new Exception("The third parameter must be field names and must be an array");
        }
        if(!isset($seed) || !is_string($seed))
        {
            throw new Exception("The fourth parameter must be strings");
        }
        if(!isset($on) || !is_array($on))
        {
            throw new Exception("The fifth parameter must be field name  and must be an array");
        }
        if($where && !is_array($where))
        {
            throw new Exception("The sixth parameter must be field name  and must be an array");
        }
        if($limit)  
        {
            if(!is_array($limit))
            {
                throw new Exception("The fifth parameter must be an array of numbers");
            }
        $numLimit=count($limit);
        }
        if($limit && $numLimit > 2)
        {
            throw new Exception("Limit can only accept 2 element in its array");
        }
        if($order && !is_array($order))
        {
            throw new Exception("The six parameter must be field name and value  and must be an array");
        }
 
        $numTable=count($tables);
        $numField=count($fields);
        $numMatch = count($match);
        $numOn=count($on);
        $numWhere=count($where);
        
        for($i=0; $i<$numTable; $i++)
        {
            if(!is_string($tables[$i]))
            {
                throw new Exception("Table names must be a string");
            }
        }
    
        for($i=0; $i<$numField; $i++)
        {
            if(!is_string($fields[$i]))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        
        foreach($on as $key=>$value)
        {
            if(!is_string($key) || !is_string($value))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        $sql="SELECT SQL_CALC_FOUND_ROWS ";
        $i=0;
        for($i=0; $i<$numField; $i++)
        {
            // if($i==$numField-1){
            //     $sql.=$fields[$i];
            // }else
            // {
                $sql.=$fields[$i].",";
            // }
            
        }
        $sql.=" MATCH (";
        for($i=0; $i<$numMatch; $i++)
        {
            if($i==$numMatch-1){
                $sql.=$match[$i].") AGAINST ( ? ) AS score ";
            }else
            {
                $sql.=$match[$i].",";
            }
            
        }
        $sql.=" FROM ";
        $i=0;
        for($i=0; $i<$numTable; $i++)
        {
            if($i==0)
            {
                $sql.=$tables[$i];
            }else
            {
                $x=($i-1);
                $a=array();
                $b=array();
                    foreach($on as $key=>$value)
                    {
                        $a[]=$key;
                        $b[]=$value;
                    }
                    $sql.=" JOIN ".$tables[$i]." ON ".$a[$x]."=".$b[$x]." ";
                    
            }
        }
        
        if($where)
        {
            $bind = 'ss';
            $value = $seed.','.$seed;
            $values = array_values($where);
            for ($i = 0; $i < count($where); $i++) {
                $bind.='s';
            }
            //$where=array();
            $sql.="WHERE MATCH (";
            for($i=0; $i<$numMatch; $i++)
            {
                if($i==$numMatch-1){
                    $sql.=$match[$i].") AGAINST ( ? ) AND ";
                }else
                {
                    $sql.=$match[$i].",";
                }
                
            }
            $i=0;
            foreach($where as $key=>$value)
            {
                $i++;
                if($i==$numWhere){
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys."!= ? ";
                    }else{
                        $sql.=$key."= ? ";
                    }
                    // $sql.=$key."='".$value."'";
                }else
                {
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys."!= ? AND ";
                    }else{
                        $sql.=$key."= ? AND ";
                    }
                    // $sql.=$key."='".$value."' AND ";
                }
            }
        }else{
            $bind ='s';
            $value = $seed;
            $values = [];
        }
        

        
        if($order)
        {
            $numOrder=count($order);
            $sql.=" ORDER BY ";
            $i=0;
            foreach($order as $key=>$value)
            {
                $i++;
                $values=strtolower($value);
                
                if($values == "desc" || $values == "asc")
                {
                    if($i==$numOrder)
                    {
                        $sql.=$key." ".$values;
                    }else
                    {
                        $sql.=$key." ".$values.",";
                    }
                    
                }/*else
                {
                    throw new Exception("Incorrect element.Element must be either 'DESC' OR 'ASC'");
                }
                */
                
            }
            
        }
        
        if($limit)
        {
            $sql.=" LIMIT ".$limit[0].", ".$limit[1];
        }        
   
        // $sql2="SELECT FOUND_ROWS();";
        //$this->_total_join_search=mysqli_query($this->_dbh,$sql2);
        //echo $sql;
        // $this->_sqlSearch=$sql2;
        $sql2="SELECT FOUND_ROWS();";
        // $this->_total_join_like =mysqli_query($this->_dbh,$sql2);
        // echo $sql.'<br/>'.$numOn;
        // $this->_sqlLike=$sql2;
        $this->_stmtJoinSearch = $this->_dbh->prepare($sql);
        // $this->_stmtJoinSearch->prepare($sql);
        $this->_stmtJoinSearch->bind_param($bind,$value,...$values);
        if($this->_stmtJoinSearch->execute())
        {
            $this->_stmtJoinSearch->store_result();
            return $this->_stmtJoinSearch->num_rows;
        }else{
             throw new Exception("Query failed .$this->_dbh->connect_error");
        }
        
        
    }
    public function join_search_i($tables=array(),$fields=array(),$match=array(),$seed,$on=array(),$where=false,$order=false,$limit=false)
    {
        $numLimit;
        
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!$tables || !is_array($tables))
        {
            throw new Exception("The first parameter must be table names and must be an array");
        }
        if(!isset($fields) || !is_array($fields))
        {
            throw new Exception("The second parameter must be field names and must be an associative array");
        }
        if(!isset($match) || !is_array($match))
        {
            throw new Exception("The third parameter must be field names and must be an array");
        }
        if(!isset($seed) || !is_string($seed))
        {
            throw new Exception("The fourth parameter must be strings");
        }
        if(!isset($on) || !is_array($on))
        {
            throw new Exception("The fifth parameter must be field name  and must be an array");
        }
        if($where && !is_array($where))
        {
            throw new Exception("The sixth parameter must be field name  and must be an array");
        }
        if($limit)  
        {
            if(!is_array($limit))
            {
                throw new Exception("The fifth parameter must be an array of numbers");
            }
        $numLimit=count($limit);
        }
        if($limit && $numLimit > 2)
        {
            throw new Exception("Limit can only accept 2 element in its array");
        }
        if($order && !is_array($order))
        {
            throw new Exception("The six parameter must be field name and value  and must be an array");
        }
 
        $numTable=count($tables);
        $numField=count($fields);
        $numMatch = count($match);
        $numOn=count($on);
        $numWhere=count($where);
        
        for($i=0; $i<$numTable; $i++)
        {
            if(!is_string($tables[$i]))
            {
                throw new Exception("Table names must be a string");
            }
        }
    
        for($i=0; $i<$numField; $i++)
        {
            if(!is_string($fields[$i]))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        
        foreach($on as $key=>$value)
        {
            if(!is_string($key) || !is_string($value))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        $sql="SELECT SQL_CALC_FOUND_ROWS ";
        $i=0;
        for($i=0; $i<$numField; $i++)
        {
            // if($i==$numField-1){
            //     $sql.=$fields[$i];
            // }else
            // {
                $sql.=$fields[$i].",";
            // }
            
        }
        $sql.=" MATCH (";
        for($i=0; $i<$numMatch; $i++)
        {
            if($i==$numMatch-1){
                $sql.=$match[$i].") AGAINST ( ? ) AS score ";
            }else
            {
                $sql.=$match[$i].",";
            }
            
        }
        $sql.=" FROM ";
        $i=0;
        for($i=0; $i<$numTable; $i++)
        {
            if($i==0)
            {
                $sql.=$tables[$i];
            }else
            {
                $x=($i-1);
                $a=array();
                $b=array();
                    foreach($on as $key=>$value)
                    {
                        $a[]=$key;
                        $b[]=$value;
                    }
                    $sql.=" JOIN ".$tables[$i]." ON ".$a[$x]."=".$b[$x]." ";
                    
            }
        }
        
        if($where)
        {
            $bind = 'ss';
            $value = $seed.','.$seed;
            $values = array_values($where);
            for ($i = 0; $i < count($where); $i++) {
                $bind.='s';
            }
            //$where=array();
            $sql.="WHERE MATCH (";
            for($i=0; $i<$numMatch; $i++)
            {
                if($i==$numMatch-1){
                    $sql.=$match[$i].") AGAINST ( ? ) AND ";
                }else
                {
                    $sql.=$match[$i].",";
                }
                
            }
            $i=0;
            foreach($where as $key=>$value)
            {
                $i++;
                if($i==$numWhere){
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys."!= ?";
                    }else{
                        $sql.=$key."=' ? ";
                    }
                    // $sql.=$key."='".$value."'";
                }else
                {
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys."!= ? AND ";
                    }else{
                        $sql.=$key."= ? AND ";
                    }
                    // $sql.=$key."='".$value."' AND ";
                }
            }
        }else{
            $bind = 's';
            $value = $seed; 
            $values = [];
        }
        

        
        if($order)
        {
            $numOrder=count($order);
            $sql.=" ORDER BY ";
            $i=0;
            foreach($order as $key=>$value)
            {
                $i++;
                $values=strtolower($value);
                
                if($values == "desc" || $values == "asc")
                {
                    if($i==$numOrder)
                    {
                        $sql.=$key." ".$values;
                    }else
                    {
                        $sql.=$key." ".$values.",";
                    }
                    
                }/*else
                {
                    throw new Exception("Incorrect element.Element must be either 'DESC' OR 'ASC'");
                }
                */
                
            }
            
        }
        
        if($limit)
        {
            $sql.=" LIMIT ".$limit[0].", ".$limit[1];
        }        
   
        // $sql2_i="SELECT FOUND_ROWS();";
        //$this->_total_join_search_i=mysqli_query($this->_dbh,$sql2_i);
        //echo $sql;
        // $this->_sqlSearch_i=$sql2_i;
         $this->_stmtJoinSearchI = $this->_dbh->prepare($sql);
        // $this->_stmtJoinSearchI->prepare($sql);
        $this->_stmtJoinSearch->bind_param($bind,$value,...$values);
        if($this->_stmtJoinSearchI->execute())
        {
            $this->_stmtJoinSearchI->store_result();
            return $this->_stmtJoinSearchI->num_rows;
        }else{
             throw new Exception("Query failed .$this->_dbh->connect_error");
        }
        
        
    }  

    public function join_distinct($tables=array(),$fields=array(),$on=array(),$where=false,$order=false,$limit=false,$distinct=false,$bind=false)
    {
        $numLimit;
        
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!$tables || !is_array($tables))
        {
            throw new Exception("The first parameter must be table names and must be an array");
        }
        if(!isset($fields) || !is_array($fields))
        {
            throw new Exception("The second parameter must be field names and must be an associative array");
        }
        if(!isset($on) || !is_array($on))
        {
            throw new Exception("The third parameter must be field name  and must be an array");
        }
        if($where && !is_array($where))
        {
            throw new Exception("The fourth parameter must be field name  and must be an array");
        }
        if($limit)  
        {
            if(!is_array($limit))
            {
                throw new Exception("The fifth parameter must be an array of numbers");
            }
        $numLimit=count($limit);
        }
        if($limit && $numLimit > 2)
        {
            throw new Exception("Limit can only accept 2 element in its array");
        }
        if($order && !is_array($order))
        {
            throw new Exception("The six parameter must be field name and value  and must be an array");
        }
        /*
        if($limit)
        {
            for($i=0; $i<$numLimit; $i++)
            {
                if(!is_double($limit[$i]))
                {
                    if($i==0)
                    {
                        throw new Exception("First limit elements must be an integer");
                    }else{
                        throw new Exception("Second imit elements must be an integer");
                    }
                    
                }
            }
        }
        */
 
        $numTable=count($tables);
        $numField=count($fields);
        $numOn=count($on);
        $numWhere=count($where);
        
        for($i=0; $i<$numTable; $i++)
        {
            if(!is_string($tables[$i]))
            {
                throw new Exception("Table names must be a string");
            }
        }
    
        for($i=0; $i<$numField; $i++)
        {
            if(!is_string($fields[$i]))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        
        foreach($on as $key=>$value)
        {
            if(!is_string($key) || !is_string($value))
            {
                throw new Exception("Table and field pair must be strings");
            }
        }
        $sql="SELECT ";
        if($distinct){
            $sql.="DISTINCT ";
        }
        $i=0;
        for($i=0; $i<$numField; $i++)
        {
            if($i==$numField-1){
                $sql.=$fields[$i];
            }else
            {
                $sql.=$fields[$i].",";
            }
            
        }
        $sql.=" FROM ";
        $i=0;
        for($i=0; $i<$numTable; $i++)
        {
            if($i==0)
            {
                $sql.=$tables[$i];
            }else
            {
                $x=($i-1);
                $a=array();
                $b=array();
                    foreach($on as $key=>$value)
                    {
                        $a[]=$key;
                        $b[]=$value;
                    }
                    $sql.=" JOIN ".$tables[$i]." ON ".$a[$x]."=".$b[$x]." ";
                    
            }
        }
        
        if($where)
        {
            //$where=array();
            $sql.="WHERE ";
            $i=0;
            foreach($where as $key=>$value)
            {
                $i++;
                if($i==$numWhere){
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys." != ? ";
                    }else{
                        $sql.=$key." = ? ";
                    }
                    // $sql.=$key."='".$value."'";
                }else
                {
                    $needle = '!';
                    $values=substr_count($key,$needle);
                    if($values > 0){
                        $keys =str_replace('!', '', $key);
                        $sql.=$keys." != ? AND ";
                    }else{
                        $sql.=$key." = ? AND ";
                    }
                    // $sql.=$key."='".$value."' AND ";
                }
            }
        }
        

        
        if($order)
        {
            $numOrder=count($order);
            $sql.=" ORDER BY ";
            $i=0;
            foreach($order as $key=>$value)
            {
                $i++;
                $values=strtolower($value);
                
                if($values == "desc" || $values == "asc")
                {
                    if($i==$numOrder)
                    {
                        $sql.=$key." ".$values;
                    }else
                    {
                        $sql.=$key." ".$values.",";
                    }
                    
                }/*else
                {
                    throw new Exception("Incorrect element.Element must be either 'DESC' OR 'ASC'");
                }
                */
                
            }
            
        }
        
        if($limit)
        {
            $sql.=" LIMIT ".$limit[0].", ".$limit[1];
        }        
   
        // $sql2ii="SELECT FOUND_ROWS();";
        //$this->_total_join=mysqli_query($this->_dbh,$sql2);
        //echo $sql;
        // $this->_sql2_ii=$sql2ii;
        if(!$bind){
            for ($i = 0; $i < $numWhere; $i++) {
                $bind.='s';
            }
        }
        $values = array_values($where);          
        $sql2="SELECT FOUND_ROWS();";
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        if($stmt->execute())
        {
            $this->_stmtJoinDistinct = $stmt->get_result();
            return $this->_stmtJoinDistinct->num_rows;
        }else{
             throw new Exception("Query failed : ".$this->_dbh->connect_error);
        }
        
        
    }  
    
    public function join_total()
    {
        $this->_total_join=mysqli_query($this->_dbh,$this->_sql2);
        if(!$this->_total_join){
            throw new Exception("join method has not been called");
        }
        //echo  num_rows($this->_total_join); 
        return mysqli_fetch_array($this->_total_join);        
        
    }
    
    public function join_total_ii()
    {
        $this->_total_join_ii=mysqli_query($this->_dbh,$this->_sql2_ii);
        if(!$this->_total_join_ii){
            throw new Exception("join method has not been called");
        }
        //echo  num_rows($this->_total_join); 
        return mysqli_fetch_array($this->_total_join_ii);        
        
    }

    public function fetch_join_like(){
        
        if(!$this->_stmtJoinLike){
            throw new Exception("join method has not been called");
        }
        //echo mysqli_num_rows($this->_join_fetch); 
        return $this->_stmtJoinLike->fetch_assoc();
    
    }
    
    public function fetch_join(){
        
        if(!$this->_stmtJoin){
            throw new Exception("join method has not been called");
        }
        //echo mysqli_num_rows($this->_join_fetch); 
        return $this->_stmtJoin->fetch_assoc();
    
    }
    
    public function fetch_join_ii(){
        
        if(!$this->_stmtJoinII){
            throw new Exception("join method has not been called");
        }
        //echo mysqli_num_rows($this->_join_fetch); 
        return $this->_stmtJoinII->fetch_assoc();
    
    }  

    public function fetch_join_distinct(){
        
        if(!$this->_stmtJoinDistinct){
            throw new Exception("join_distinct method has not been called");
        }
        //echo mysqli_num_rows($this->_join_fetch); 
        return $this->_stmtJoinDistinct->fetch_assoc();
    
    }
    public function fetch_join_search(){
        
        if(!$this->_stmtJoinSearch){
            throw new Exception("join_search method has not been called");
        }
        //echo mysqli_num_rows($this->_join_fetch); 
        return $this->_stmtJoinSearch->fetch_assoc();
    
    } 
    public function fetch_join_search_i(){
        
        if(!$this->_stmtJoinSearchI){
            throw new Exception("join_search method has not been called");
        }
        //echo mysqli_num_rows($this->_join_fetch); 
        return $this->_stmtJoinSearchI->fetch_assoc();
    
    }  
    
    public function pagination($sql,$pageNum)
    {
        
    }
    
    public function delete($table,$value,$bind=false)
    {
        if(!$this->_dbh)
        {
            throw new Exception("Class Pos_Process must be instantiated first");
        }
        if(!isset($table) || !is_string($table))
        {
            throw new Exception("the first parameter must be a table name");
        }
        if(!isset($value) || !is_array($value))
        {
            throw new Exception("The third parameter must be field values and must be an associative array");
        }
        
        $sql="DELETE FROM $table WHERE ";
        $num = count($value);
        $i=0;
        foreach($value as $keys => $values)
        {
            $i++;
            if($i==$num)
            {
                $sql.=$keys."= ? ";
            }else
            {
                $sql.=$keys."= ? AND ";
            }
        }
        // $val = '';
        $values = array_values($value);
        if(!$bind){
            for ($i = 0; $i < $num; $i++) {
                $bind.='s';
            }
        }
        $stmt = $this->_dbh->prepare($sql);
        $stmt->bind_param($bind, ...$values);
        if($stmt->execute())
        {
            $stmt->close();
            return true;
        }else{
            $stmt->close();
            return("Query failed .$this->_dbh->connect_error");
        }
        
    }
 
}
?>