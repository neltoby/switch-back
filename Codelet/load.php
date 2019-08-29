<?php
spl_autoload_register(function($class){
    $path=explode("_",$class);
    $part=implode(DIRECTORY_SEPARATOR,$path);
    $cn_dir = '..'.DIRECTORY_SEPARATOR.'Pos';
    $last= explode(DIRECTORY_SEPARATOR,$cn_dir);
    if(end($last)=="Pos")
    {
        $file = $cn_dir . DIRECTORY_SEPARATOR.$path[1].".php";
    }else{
        $file = $cn_dir . DIRECTORY_SEPARATOR.$part.".php";
    }
    
    if(file_exists($file))
    {
        require $file;
    }else{
        echo 0;
    }
    });
?>