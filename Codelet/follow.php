<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
$header = apache_request_headers();
require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    $tok = new Pos_Token($host,$user,$pass,$database);
    $token = $tok->checkJWt('sha256', $header['Authorization'], $key);
    $id = (int)$token['ref'];
	if($id && filter_has_var(INPUT_POST, 'fid') && filter_has_var(INPUT_POST, 'num')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Follower::$_host=$host;
            Pos_Follower::$_user=$user;
            Pos_Follower::$_pass=$pass;
            Pos_Follower::$_db=$database;
                if($_POST['num'] == 1){
                    $details = Pos_Follower::follow($_POST['fid'],$id); 
                    $details['token'] = $token['token'];  
                    echo json_encode($details); 
                }else{
                    $details = Pos_Follower::unfollow($_POST['fid'],$id); 
                    $details['token'] = $token['token'];  
                    echo json_encode($details);
                }
                      
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}else{
        echo json_encode(array('status'=>false,'res'=>'No action recieved'));
    }
?>