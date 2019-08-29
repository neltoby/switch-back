<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

if(filter_has_var(INPUT_POST, 'pid')){
	try{
		require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
		Pos_Threads::$_host=$host;
        Pos_Threads::$_user=$user;
        Pos_Threads::$_pass=$pass;
        Pos_Threads::$_db=$database;
    	echo Pos_Threads::getThread($_POST['pid']);    
        }catch(Exception $e){
                echo $e->getMessage();
        }
	
}else{
    echo 'nothing eas found';
}
?>