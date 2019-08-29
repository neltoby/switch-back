<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

	if(filter_has_var(INPUT_POST, 'country')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Edit::$_host=$host;
            Pos_Edit::$_user=$user;
            Pos_Edit::$_pass=$pass;
            Pos_Edit::$_db=$database;
            echo Pos_Edit::getCountries();           
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}
?>