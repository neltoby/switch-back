<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
$header = apache_request_headers();
if($header['Authorization']){
    require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
    require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    $tok = new Pos_Token($host,$user,$pass,$database);
    $token = $tok->checkJWt('sha256', $header['Authorization'], $key);
    $id = (int)$token['ref'];
	if($id && filter_has_var(INPUT_POST, 'interest') && filter_has_var(INPUT_POST, 'status')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Follower::$_host=$host;
            Pos_Follower::$_user=$user;
            Pos_Follower::$_pass=$pass;
            Pos_Follower::$_db=$database;
                $details = Pos_Follower::writeWallStatus($id,$_POST['status'],$_POST['interest']);    
                $details['token'] = $token['token'];  
                echo json_encode($details);   
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}
    else{
        if($token){
            echo json_encode(array('state'=>false, 'error'=>'Incomplete parameters','token'=>$token['token']));
        }else{
            echo json_encode(array('state'=>false, 'error'=>'Incomplete parameters','token'=>$header['Authorization']));
        }
    }
}
?>