<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();
	if(filter_has_var(INPUT_POST, 'username') && filter_has_var(INPUT_POST, 'email') && filter_has_var(INPUT_POST, 'password') &&
		filter_has_var(INPUT_POST, 'country') && filter_has_var(INPUT_POST, 'state') && filter_has_var(INPUT_POST, 'municipal') &&
		$_FILES['file']['name'] != '')
	{
		
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Sign::$_host=$host;
            Pos_Sign::$_user=$user;
            Pos_Sign::$_pass=$pass;
            Pos_Sign::$_db=$database;

            $res = Pos_Sign::createUser($_POST['username'],$_POST['email'],$_POST['password'],$_FILES['file'],$_POST['country'],
            	$_POST['state'],$_POST['municipal']); 
            	if($res['state'] === true){
					$_SESSION['set_id'] = $res['session'];
					// $new = array('state' => $res['state']);
					echo json_encode($res);
            	}else{
            		echo json_encode($res);
            	}          
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}else{
    	echo 'successd';
    }
?>