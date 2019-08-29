<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

	if(filter_has_var(INPUT_POST, 'id') && filter_has_var(INPUT_POST, 'name')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Likes::$_host=$host;
            Pos_Likes::$_user=$user;
            Pos_Likes::$_pass=$pass;
            Pos_Likes::$_db=$database;
            echo Pos_Likes::deleteLikes($_POST['id'],$_POST['name']);           
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}else {
        echo json_encode(array('state' => false, 'error' => 'Operation failed'));   
    }

?>