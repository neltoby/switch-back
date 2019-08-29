<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
$header = apache_request_headers();
if($header['Authorization']){
    require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
    require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    $tok = new Pos_Token($host,$user,$pass,$database);
    $token = $tok->checkJWt('sha256', $header['Authorization'], $key);
    $id = $token['ref'];
    if($id){
        if(filter_has_var(INPUT_POST, 'pid') && filter_has_var(INPUT_POST, 'uid')){
            try{
                require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
                Pos_Comments::$_host=$host;
                Pos_Comments::$_user=$user;
                Pos_Comments::$_pass=$pass;
                Pos_Comments::$_db=$database;
                $details = Pos_Comments::getAllRepliers($_POST['pid'],$_POST['uid']); 
                $details['token'] = $token['token'];  
                echo json_encode($details);                 
            }catch(Exception $e){
                    echo $e->getMessage();
            }
        }else {
            echo json_encode(array('state' => false, 'error' => 'Operation failed'));   
        }
    }else{
        echo json_encode(array('state' => false, 'error' => 'Invalid token')); 
    }
}
?>