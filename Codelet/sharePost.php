<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
$header = apache_request_headers();
if($header['Authorization']){
    require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
    require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    $tok = new Pos_Token($host,$user,$pass,$database);
    $token = $tok->checkJWt('sha256', $header['Authorization'], $key);
    $id = (int)$token['ref'];
    	if($id && filter_has_var(INPUT_POST, 'pid') && filter_has_var(INPUT_POST, 'num')){
    		try{
    			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    			Pos_Share::$_host=$host;
                Pos_Share::$_user=$user;
                Pos_Share::$_pass=$pass;
                Pos_Share::$_db=$database;
                if($_POST['num'] == 1){
                    $details = Pos_Share::all($id,$_POST['pid'],$_POST['text']); 
                    $details['token'] = $token['token'];  
                    echo json_encode($details);  
                }elseif($_POST['num'] == 2){
                    $details = Pos_Share::interest($id,$_POST['pid'],$_POST['interest'],$_POST['text']);
                    $details['token'] = $token['token'];  
                    echo json_encode($details); 
                }elseif($_POST['num'] == 3){
                    $details = Pos_Share::locationInterest($id, $_POST['pid'], $_POST['interest'], $_POST['current'], 
                        $_POST['country'], $_POST['state'], $_POST['local'],$_POST['text']);
                    $details['token'] = $token['token'];  
                    echo json_encode($details); 
                }elseif ($_POST['num'] == 4) {
                    $details = Pos_Share::location($id, $_POST['pid'], $_POST['current'], 
                        $_POST['country'], $_POST['state'], $_POST['local'],$_POST['text']);
                    $details['token'] = $token['token'];  
                    echo json_encode($details); 
                }   
            }catch(Exception $e){
                    echo $e->getMessage();
            }
    	}
        // else{
        //     echo 'nothing';
        // }
}
?>