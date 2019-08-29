<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

	if(filter_has_var(INPUT_POST, 'email') && filter_has_var(INPUT_POST, 'password')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Sign::$_host=$host;
            Pos_Sign::$_user=$user;
            Pos_Sign::$_pass=$pass;
            Pos_Sign::$_db=$database;
            echo Pos_Sign::login($_POST['email'], $_POST['password']);           
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}
?>
