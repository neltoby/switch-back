<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
$sql = new Pos_Process($host,$user,$pass,$database);
$sql->select('user');
while($row = $sql->fetch_select()){
	try{
		$hash = password_hash($row['Password'], PASSWORD_DEFAULT);
		if($sql->update_where('user',array('Password'=>$hash),array('Id'=>$row['Id']))){
			echo'update was successfull : '.$hash;
		}		
	}catch(Exception $e){
        echo $e->getMessage();
    }
}
?>