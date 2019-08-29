<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

	if(filter_has_var(INPUT_POST, 'email')){
		try{
		// require_once dirname('.',1).DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Login::$_host=$host;
            Pos_Login::$_user=$user;
            Pos_Login::$_pass=$pass;
            Pos_Login::$_db=$database;
            echo Pos_Login::emailConfirmation($_POST['email']);           
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}
?>