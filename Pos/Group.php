<?php
class Pos_Group
{
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_con;
    
    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    
    public static function createGroup($required=array(),$id)
    {
        if(!self::$_con)
        {
            self::connect();
        }
        if(!$id)
        {
            throw new Exception("createGroup method accepts 2 parameter one was given.");
        }
        if(!is_numeric($id))
        {
            throw new Exception("second parameter must be an integer.");   
        }
        $val = new Pos_Validator($required);
            $val->checkTextLength('grp_name', 2);
            $val->checkTextLength('grp_pp', 20);
            $val->removeTags('grp_name');
            $val->removeTags('grp_pp');
        $filtered = $val->validateInput();
        $missing = $val->getMissing();
        $errors = $val->getErrors();
        if(!$missing && !$errors)
        {
            $field=array("Name","Grp_type","Purpose","Created_by");
            $value=array($filtered["grp_name"],"Closed",$filtered["grp_pp"],$_SESSION["user"]);
            $session=self::$_con->insert("groups",$field,$value);
            $field1=array("Grp_id","User_id","Added_by","Position","Status");
            $value1=array($session,$id,$id,"Admin","Confirmed");
            self::$_con->insert("grp_users",$field1,$value1);
            $ary=array($session,$filtered["grp_name"]);
            return $ary;
        //self::upload($filtered["grp_name"]);
        }else
        {
            if(isset($errors['grp_name']))
            {
                echo'Miminum length of string for defining <b>Name</b> must be 2.';
                throw new Exception("Oooops!");
            }
            if(isset($errors['grp_pp']))
            {
                echo'Miminum length of string for defining <b>Purpose</b> must be 20.';
                throw new Exception("Oooops!");
            }
            
        }
    }
    
    public static function upload($name)
    {
        if(!is_string($name))
        {
            throw new Exception("Group name must be string");
        }
        echo'
        <script>
        
        
        </script>
        ';
        echo'

        ';
    }
    
    public static function addUsers($table,$field,$value)
    {
        if(!self::$_con)
        {
            self::connect();
        }
        
        self::$_con->insert($table,$field,$value);   
    }
    
     public static function display($id,$uid)
     {
        if(!self::$_con)
        {
            self::connect();
        }
        $date=date("Y-m-d h:i:s");
        $tab="grp_users";
        $fd=array("Last_seen"=>$date);
        $ptr=array("Grp_id"=>$id,"User_id"=>$uid);
        self::$_con->update_where($tab,$fd,$ptr);
        $pointer=array("Id"=>$id);
        self::$_con->select_all_where("groups",$pointer);
        $det=self::$_con->fetch_select_all_where();
         echo'
         <script>
         $("#prof").click(function(){
            $(".opt").slideDown();
        $.ajax({
            url: "profile.php",
            type: "POST",
            data: { info: "info", state: "1"},
            beforeSend: function(){
                $("#vis").replaceWith("<div class=\'row\' id=\'vis\'><div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Loading Notification...</div></div>");
                },
            complete: function(){
                $("#notify").remove();
                },    
            success: function(data){
                $("#vis").empty();
                    $("#vis").html(data);        
                },
            error: function(){
                alert("error");
                }    
        });             
         });
         
         $("#gus'.$det[0].'").click(function(){
         var id=this.id;
        $.ajax({
            url: "grp_user.php",
            type: "POST",
            data: { info: "info", state: id},
            beforeSend: function(){
                $("#grp_cnt").replaceWith("<div class=\'row\' id=\'grp_cnt\'><div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Loading Group Users...</div></div>");
                },
            complete: function(){
                $("#notify").remove();
                },    
            success: function(data){
                $("#grp_cnt").empty();
                    $("#grp_cnt").html(data);        
                },
            error: function(){
                alert("error");
                }    
        });                      
         });
         
         $("#gps'.$det[0].'").click(function(){
         var id=this.id;
        $.ajax({
            url: "grp_post.php",
            type: "POST",
            data: { info: "info", state: id},
            beforeSend: function(){
                $("#grp_cnt").replaceWith("<div class=\'row\' id=\'grp_cnt\'><div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Loading Group Post...</div></div>");
                },
            complete: function(){
                $("#notify").remove();
                },    
            success: function(data){
                $("#grp_cnt").empty();
                    $("#grp_cnt").html(data);        
                },
            error: function(){
                alert("error");
                }    
        });                      
         });
         
         $("#gnt'.$det[0].'").click(function(){
         var id=this.id;
        $.ajax({
            url: "grp_notify.php",
            type: "POST",
            data: { info: "info", state: id},
            beforeSend: function(){
                $("#grp_cnt").replaceWith("<div class=\'row\' id=\'grp_cnt\'><div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Loading Group Notification...</div></div>");
                },
            complete: function(){
                $("#notify").remove();
                },    
            success: function(data){
                $("#grp_cnt").empty();
                    $("#grp_cnt").html(data);        
                },
            error: function(){
                alert("error");
                }    
        });                      
         });
         
         $("#gad'.$det[0].'").click(function(){
         var id=this.id;
        $.ajax({
            url: "grp_admin.php",
            type: "POST",
            data: { info: "info", state: id},
            beforeSend: function(){
                $("#grp_cnt").replaceWith("<div class=\'row\' id=\'grp_cnt\'><div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Loading Group Admins...</div></div>");
                },
            complete: function(){
                $("#notify").remove();
                },    
            success: function(data){
                $("#grp_cnt").empty();
                    $("#grp_cnt").html(data);        
                },
            error: function(){
                alert("error");
                }    
        });                      
         });         
         </script>
         <form id="grpsrc" action="grp_search.php" method="post" style="margin-bottom: 0px;">
         <div class="row" style="background-color: #2c3345; color: #fff; padding: 20px 10px 10;">
         <div class="col-sm-2 col-xs-2">
         <span class="glyphicon glyphicon-arrow-left" id="prof" style="font-size: 20px; font-weight: 400;"></span>
         </div>
         <div class="col-sm-8 col-xs-8">
         <div class="form-group">
         <input type="search" name="srcgrp" style="background-color: #2c3345; color: #fff; width: 100%; font-size: 14px; border: 0px; border-bottom: 1px solid #fff;" placeholder="Search '.$det[1].'">
         </div>
         </div>
         <div class="col-sm-2 col-xs-2">
         <label for="srcsb" style="font-size: 16px;"><span class="glyphicon glyphicon-search"></span></label>
         <input type="submit" id="srcsb" value="Search" style="display:none" name="srcsb">
         </div>
         </div>
         </form>
         <div class="row" style="position: relative;">
         <div>
         <img id="grp_img" src="grp_prf'. DIRECTORY_SEPARATOR.$det[2].'" width="100%" height="300">
         </div>
         <div class="col-sm-10 col-xs-10" style="position: absolute; left: 20px; bottom: 10px; color: #fff; font-size: 20px; font-weight: 700;">'.
         $det[1].' [Group]
         </div>
         </div>
         <div class="row" style="padding-top: 10px">
         <div class="col-xs-3 col-sm-3">
         <button id="gus'.$det[0].'" class="form-control btn btn-primary style="color: #ccc;;">
         <span class="glyphicon glyphicon-user"></span><span style="font-size: 12px; color: #ccc; padding-left: 4px;">Users</span></button>
         </div>
         <div class="col-xs-3 col-sm-3">
         <button id="gps'.$det[0].'" class="form-control btn btn-link style="color: #4285f4;">
         <span class="glyphicon glyphicon-pencil"></span> <span style="font-size: 12px; color: #4285f4;">Post</span></button>
         </div>
         <div class="col-xs-3 col-sm-3">
         <button id="gnt'.$det[0].'" class="form-control btn btn-link style="color: #4285f4;">
         <span class="glyphicon glyphicon-globe"></span><span style="font-size: 12px; color: #4285f4; padding-left: 4px;">Notification</span></button>
         </div>
         <div class="col-xs-3 col-sm-3">
         <button id="gad'.$det[0].'" class="form-control btn btn-link" style="color: #4285f4; font-size: 12px;">
         Admins
         </button>
         </div>
         </div>
         <div id="grp_cnt">
         </div>';
     }
     
     public static function users($gid,$uid,$target)
     {
        if(!self::$_con)
        {
            self::connect();
        }
        $table=array("grp_users","tgln_users");
        $field=array("tgln_users.Id","tgln_users.First_name","tgln_users.Last_name","tgln_users.Pix","grp_users.Position");
        $on=array("tgln_users.Id"=>"grp_users.User_id");
        $where=array("grp_users.Grp_id"=>$gid);
        $limit=array(0,4);
        $users=self::$_con->join($table,$field,$on,$where,0,$limit);
        if($users > 0)
        {
            echo'<div class="row">
            <div class="col-xs-offset-2 col-xs-8 text-center btn btn-primary" style="font-size: 17px; padding: 15px 5px; color: fff;
            font-weight: 600; margin-top: 20px; margin-bottom: 20px;">
            Active Group Users
            </div>
            </div>
            <div class="row">
            ';
            while($dis=self::$_con->fetch_join())
            {
                echo'<script>
                $("#gun'.$dis[0].'").click(function(){
                var id=this.id;
                $.ajax({
            url: "gru_post.php",
            type: "POST",
            data: { info: "info", state: id},
            beforeSend: function(){
                $("#grp_cnt").replaceWith("<div class=\'row\' id=\'grp_cnt\'><div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Loading User\'s Post ...</div></div>");
                },
            complete: function(){
                $("#notify").remove();
                },    
            success: function(data){
                $("#grp_cnt").empty();
                    $("#grp_cnt").html(data);        
                },
            error: function(){
                alert("error");
                }    
        });        
                });
                </script>';
                
                echo'<div class="col-xs-3">
                <div style="border: solid 2px #337ab7; border-radius: 50%; padding: 3px;">
                <img src="'.$target. DIRECTORY_SEPARATOR.$dis[3].'" class="img-circle" width="100%" height="150">
                
                </div>
                <div id="gun'.$dis[0].'" class="text-center" style="font-size: 12px; padding: 5px 2px;">
                '.$dis[1].'     '.$dis[2].'
                </div>';
                if($uid==$dis[0])
                {
                    echo'<div class="text-center">
                    <button class="form-control btn btn-primary" style="font-size: 14px; font-weight: 600;">'.$dis[4].'</button>
                    </div>';
                }else
                {
                    $dir="follow";
                    $pt=array("Lead_id"=>$dis[0],"Follower_id"=>$uid);
                    if(self::$_con->select_all_where($dir,$pt) > 0)
                    {
                        echo'<div class="text-center">
                        <button class="form-control btn btn-primary" style="font-size: 14px; font-weight: 600;">'.$dis[4].'</button>
                        </div>';
                    }else
                    {
                        echo'<div class="col-xs-6 text-center">
                        <button class="form-control btn btn-primary" style="font-size: 14px; font-weight: 600;">'.$dis[4].'</button>
                        </div>
                        <div class="col-xs-6 text-center">
                        <button class="form-control btn btn-primary" style="font-size: 14px; font-weight: 600;">Follow</button>
                        </div>';
                    }
                    
                }
                echo'
                </div>';
                
            }
            echo'</div>';
        }else
        {
            echo'nothing';
        }
     }
     
     public static function post($gid,$uid,$target)
     {
        if(!self::$_con)
        {
            self::connect();
        }
        echo'<div class="row" style="margin-bottom: 20px;">
        <div class="col-sm-offset-2 col-sm-8 col-xs-12" style="margin-top: 20px;">
        <div class="row">
        <div class="col-xs-12 btn btn-primary text-center"
        style="padding-top: 20px; padding-bottom: 20px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
        Post
        </div>
        </div>
        <div class="row" style="background-color: #2c3345; border: 1px solid #2c3345; border-radius: 5px; padding: 30px 15px;">
        <form id="grpost" action="gru_post.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
        <div class="col-xs-offset-1 col-xs-9">
        <textarea name="grpst" class="" placeholder="Write Post"
        style="width: 100%; border: 0px; border-bottom: 2px solid #fff; background-color: #2c3345; color: #fff; font-size: 14px;"></textarea>
        </div>
        <div class="col-xs-1">
        <label for="grpfile">
        <span class="glyphicon glyphicon-camera" style="cursor: pointer; color: #fff;"></span>
        </label>
        <input type="file" id="grpfile" name="grpfile" style="display: none;">
        </div>
        <div class="col-xs-1">
        <label for="grpsnd">
        <span id="grpsd" class="glyphicon glyphicon-send" style="cursor: pointer; color: #fff;"></span>
        </label>
        </div>   
        </div>
        <div class="">
        <input type="submit" name="grpsnd" id="grpsnd" value="Post" style="display: none;">
        </div>
        </form>
        </div>
        </div>
        </div>
        <div id="grp_post">
        </div>';
     }
    
    
}

?>