<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

	if(filter_has_var(INPUT_POST, 'username')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Sign::$_host=$host;
            Pos_Sign::$_user=$user;
            Pos_Sign::$_pass=$pass;
            Pos_Sign::$_db=$database;
            $username = Pos_Sign::username($_POST['username']);  
            if(!$username){
                echo json_encode(array('state' => false, 'error' => 'Username already exist'));
            }else{
                echo json_encode(array('state' => true));
            }        
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}
?>