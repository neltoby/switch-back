<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';

	if(filter_has_var(INPUT_POST, 'interest') && filter_has_var(INPUT_POST, 'current')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Follower::$_host=$host;
            Pos_Follower::$_user=$user;
            Pos_Follower::$_pass=$pass;
            Pos_Follower::$_db=$database;
            if($_POST['current'] == 'country'){
                echo json_encode(Pos_Follower::getAllFollowers_country($_POST['interest'],$_POST['country']));       
            }elseif($_POST['current'] == 'state'){
                echo json_encode(Pos_Follower::getAllFollowers_state($_POST['interest'],$_POST['country'],$_POST['state']));
            }elseif($_POST['current'] == 'local'){
                echo json_encode(Pos_Follower::getAllFollowers_local($_POST['interest'],$_POST['country'],
                                    $_POST['state'],$_POST['local']));
            }       
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}else{
        echo json_encode(array('status'=>false,'res'=>'No action recieved'));
    }
?>