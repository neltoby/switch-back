<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

	if(filter_has_var(INPUT_POST, 'id') && filter_has_var(INPUT_POST, 'likes')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Likes::$_host=$host;
            Pos_Likes::$_user=$user;
            Pos_Likes::$_pass=$pass;
            Pos_Likes::$_db=$database;
            echo Pos_Likes::userLikes($_POST['id'], $_POST['likes']);           
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}
?>