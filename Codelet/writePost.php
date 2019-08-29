<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

if(filter_has_var(INPUT_POST, 'current') && filter_has_var(INPUT_POST, 'pid')){
	try{
			$count=0;
			if(count($_FILES['file']['name']) > 0){
				foreach($_FILES['file']['name'] as $file){
					if(!empty($file)){
						$count++;
					}
				}
			}
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Post::$_host=$host;
            Pos_Post::$_user=$user;
            Pos_Post::$_pass=$pass;
            Pos_Post::$_db=$database;
            $pid = $_POST['pid'];
            $post = $_POST['post'];
            $tag = $_POST['tag'];
            // $thread = $_POST['thread'];
            if($count > 0){
            	echo Pos_Post::insertPost($pid,$_POST['tag'],$_POST['post'],$_POST['interest'],$_POST['thread'],$_POST['share'],
            		$_FILES['file'],$_POST['current'],$_POST['country'],$_POST['state'],$_POST['municipal']);
            }else{
            	echo Pos_Post::insertPost($pid,$_POST['tag'],$_POST['post'],$_POST['interest'],$_POST['thread'],$_POST['share'],
            		false,$_POST['current'],$_POST['country'],$_POST['state'],$_POST['municipal']);
            }

            // $username = ;  
            // if(!$username){
            //     echo json_encode(array('state' => false, 'error' => 'Username already exist'));
            // }else{
            //     echo json_encode(array('state' => true));
            // }        
        }catch(Exception $e){
                echo $e->getMessage();
        }
	
}else{
	echo json_encode(array('state'=>false,'error'=>'Missed an option'));
}
?>