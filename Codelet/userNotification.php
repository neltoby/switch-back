<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
$header = apache_request_headers();
if($header['Authorization']){
    require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
    require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    $tok = new Pos_Token($host,$user,$pass,$database);
    $token = $tok->checkJWt('sha256', $header['Authorization'], $key);
    	if($token['ref'] && filter_has_var(INPUT_POST, 'interest')){
    		try{
    			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    			Pos_Notification::$_host=$host;
                Pos_Notification::$_user=$user;
                Pos_Notification::$_pass=$pass;
                Pos_Notification::$_db=$database;
                echo Pos_Notification::userNotification($token['ref'],$_POST['interest']);      
            }catch(Exception $e){
                    echo $e->getMessage();
            }
    	}else{
            echo json_encode(array('state'=>false, 'error'=>'Incomplete options'));
        }
}
?>