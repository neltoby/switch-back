<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

if(filter_has_var(INPUT_POST, 'current') && filter_has_var(INPUT_POST, 'id') 
    && filter_has_var(INPUT_POST, 'text') && filter_has_var(INPUT_POST, 'interest')){
	try{
		require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
		Pos_Threads::$_host=$host;
        Pos_Threads::$_user=$user;
        Pos_Threads::$_pass=$pass;
        Pos_Threads::$_db=$database;
    	echo Pos_Threads::tagThread($_POST['id'],$_POST['text'],$_POST['interest'],
    		$_POST['current'],$_POST['country'],$_POST['state'],$_POST['local']);    
        }catch(Exception $e){
                echo $e->getMessage();
        }
	
}else{
    echo 'nothing eas found';
}
?>