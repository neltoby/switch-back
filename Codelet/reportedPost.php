<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

    if(filter_has_var(INPUT_POST, 'id') && filter_has_var(INPUT_POST, 'pid') 
        && filter_has_var(INPUT_POST, 'detail') && filter_has_var(INPUT_POST, 'suggest')){
        try{
            require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
            Pos_Post::$_host=$host;
            Pos_Post::$_user=$user;
            Pos_Post::$_pass=$pass;
            Pos_Post::$_db=$database;
            echo Pos_Post::reportedPost($_POST['id'],$_POST['pid'],$_POST['detail'],$_POST['suggest']);                    
        }catch(Exception $e){
                echo $e->getMessage();
        }
    }else {
        echo json_encode(array('state' => false, 'error' => 'Operation failed'));   
    }

?>