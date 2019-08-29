<?php
require_once __DIR__. DIRECTORY_SEPARATOR.'load.php';
session_start();

	if(filter_has_var(INPUT_POST, 'id') && filter_has_var(INPUT_POST, 'interest') && filter_has_var(INPUT_POST, 'search') 
        && filter_has_var(INPUT_POST, 'current') && filter_has_var(INPUT_POST, 'category')){
		try{
			require_once '..'.DIRECTORY_SEPARATOR.'Defy'.DIRECTORY_SEPARATOR.'store.php';
			Pos_Search::$_host=$host;
            Pos_Search::$_user=$user;
            Pos_Search::$_pass=$pass;
            Pos_Search::$_db=$database;
            if($_POST['current'] == 'country'){
                if($_POST['category'] == 'People'){
                    echo Pos_Search::getCountryPeople($_POST['id'],$_POST['interest'],$_POST['country'],$_POST['search']);
                }elseif($_POST['category'] == 'Post'){
                    echo Pos_Search::getCountryPost($_POST['id'],$_POST['interest'],$_POST['country'],$_POST['search']);
                }elseif($_POST['category'] == 'Group'){

                }elseif ($_POST['category'] == 'Portal') {
                    
                }
            }elseif($_POST['current'] == 'state'){
                if($_POST['category'] == 'People'){
                    echo Pos_Search::getLocalPeople($_POST['id'],$_POST['interest'],$_POST['country'],
                        $_POST['state'],$_POST['search']);
                }elseif($_POST['category'] == 'Post'){
                    echo Pos_Search::getStatePost($_POST['id'],$_POST['interest'],$_POST['country'],
                        $_POST['state'],$_POST['search']);
                }elseif($_POST['category'] == 'Group'){

                }elseif ($_POST['category'] == 'Portal') {
                    
                }
            }elseif($_POST['current'] == 'local'){
                if($_POST['category'] == 'People'){
                    echo Pos_Search::getLocalPeople($_POST['id'],$_POST['interest'],$_POST['country'],
                        $_POST['state'],$_POST['local'],$_POST['search']);
                }elseif($_POST['category'] == 'Post'){
                    echo Pos_Search::getLocalPost($_POST['id'],$_POST['interest'],$_POST['country'],
                        $_POST['state'],$_POST['local'],$_POST['search']);
                }elseif($_POST['category'] == 'Group'){

                }elseif ($_POST['category'] == 'Portal') {
                    
                }
            }                     
        }catch(Exception $e){
                echo $e->getMessage();
        }
	}else {
        echo json_encode(array('status' => false, 'error' => 'Operation failed'));   
    }

?>