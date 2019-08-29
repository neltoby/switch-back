<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
$header = apache_request_headers();
if($header['Authorization']){
	require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
	require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
    $tok = new Pos_Token($host,$user,$pass,$database);
    $token = $tok->checkJWt('sha256', $header['Authorization'], $key);
	$id = (int)$token['ref'];
		if($id && filter_has_var(INPUT_POST, 'current')){
			try{
				require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
				Pos_Post::$_host=$host;
	            Pos_Post::$_user=$user;
	            Pos_Post::$_pass=$pass;
	            Pos_Post::$_db=$database;
	            if($_POST['current'] == 'country'){
	            	$details = Pos_Post::getCountryPost($id,$_POST['interest'],$_POST['country']);
	            	$details['token'] = $token['token'];  
	                echo json_encode($details); 
	            }elseif($_POST['current'] == 'state'){
	            	$details = Pos_Post::getStatePost($id,$_POST['interest'],$_POST['country'],$_POST['state']);
	            	$details['token'] = $token['token'];  
	                echo json_encode($details); 
	            }elseif ($_POST['current'] == 'local') {
	            	$details = Pos_Post::getLocalPost($id,$_POST['interest'],$_POST['country'],$_POST['state'],$_POST['local']);
	            	$details['token'] = $token['token'];  
	                echo json_encode($details); 
	            }      
	        }catch(Exception $e){
	                echo $e->getMessage();
	        }
		}
}
?>