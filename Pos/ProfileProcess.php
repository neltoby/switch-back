<?php
//require_once('C:\xampp\htdocs\oop_solutions\TGLN\load.php');
class Pos_ProfileProcess
{
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_con;
    protected static $_con_ii;

    private static function connection()
    {
        self::$_con=new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
        self::$_con_ii= new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    
    public static function profile_pix($id)
    {
        if(!self::$_con)
        {
            self::connection();
        }
        if(!isset($id)){
            throw new Exception("No data was supplied");
        }
            if(!is_numeric($id))
            {
                $id=(int)$id;
            }
            if(is_numeric($id))
            {
                $table="tgln_users";
                $point=array("Id"=>$id);
                $prof=self::$_con->select_all_where($table,$point);
                $profile=self::$_con->fetch_select_all_where();
                echo'<head>
                <style>
                @media screen and (min-width: 450px){
                    .name{
                        font-size: 13px;
                    }
                }
                </style>
                </head>
                <div class="row" style="margin-">
                <div class="row">
                <div class="col-xs-6 text-right name" style="background-color: 1a53ff; color: #fff;">
                <div style="padding: 15px 10px;">
                <h3 style="text-shadow: 2px 2px 25px #ffffff; padding: 0px; margin: 0px;">
                '.$profile[1].
                '</h3>
                </div>
                </div>
                <div class="col-xs-6 text-left name" style="background-color: #d9d9f2; color: #131339;">
                <div style="padding: 15px 10px;">
                <h3 style="text-shadow: 2px 2px 25px darkblue; padding: 0px; margin: 0px;">'.
                $profile[2].'
                </h3>
                </div>
                </div>
                <div id="upic">
                <img id="vpic" src="user_img/'.$profile[5].'" width="100%" height="400">
                </div>
                <div style="border: 1px solid #ddd; padding: 7px; font-weight: 500; color: #4080bf">
                <div class="row"">';
                echo'
                <script>
                $("#cpic").change(function(){
                    $("#spic").trigger("click");
                });
                
            $("#fpic").on("submit",(function(e){
                e.preventDefault();
                $.ajax({
                    url: "pp_upload.php",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    beforeSend: function(){
                    },
                    complete: function(){                      
                    },                    
                    success: function(data){
                    $("#vpic").attr("src","user_img/"+data);
                    $("#img").attr("src","user_img/"+data);
                    },
                    error: function(){
                    }
                });
                }));                
                
                
                </script>
                ';
                
                echo'
                <div class="col-sm-3 col-xs-3" style="padding-left: 25px;">
                <form action="pp_upload.php" name="fpic" id="fpic" enctype="multipart/form-data">
                <label for="cpic">
                <span id="cpix" class="glyphicon glyphicon-camera" style="font-size: 16px;">
                </label>
                <input type="file" id="cpic" name="cpic" style="display: none;">
                <input type="submit" id="spic" name="spic" style="display: none;">
                </form>
                </div>
                <div class="col-sm-6 col-xs-9 text-center" style="text-shadow: 2px 2px 15px #fff;">'.$profile[9].'   '.'<span style="font-size: 0.85em;">-</span>'.
                '   '.$profile[10].'
                </div>
                <div class="col-sm-3 col-xs-12">
                </div>
                </div>
                </div>
                </div>
                </div>
                <div class="row" style="margin: 20px;">
                <div class="">
                <div class="form-group has-feedback">
                <div>
                <input type="search" class="form-control" id="yseach" placeholder="Search your Post,Follower" style="border-radius: 20px;">
                <span class="glyphicon glyphicon-search form-control-feedback" style="cursor: pointer; color: #003d99;"></span>
                </div>
                </div>
                </div>
                </div>
                <div class="row" style="margin: 0px 20px 0px 20px;">
                <div class="col-xs-offset-4 col-xs-4 text-center" style="font-size: 16px; color: #4080bf; font-weight: 700;">
                ABOUT
                </div>
                </div>
                <div class="row">
                <div class="col-xs-offset-1 col-xs-11" style="font-weight: 600; font-size: 12px; border-bottom: 2px solid #4285f4; margin-bottom: 10px;">
                Personal
                </div>
                </div>
                <div class="row">
                <div class="col-xs-offset-1 col-xs-11">
                ';
                if($profile[7])
                {
                    echo'<script>
                    $("#gendsel").hide();
                    $("#gendc").hide();
                    $("#gendit").click(function(){
                  
                        $("#gendit").slideUp();
                        $("#gendans").slideUp();
                        $("#gendsel").slideDown();
                        $("#gendc").slideDown();
                     
                    });
                    
                    $("#gendchs").change(function(){
                    var gend = $("#gendchs").val();
                    
        $.ajax({
            url: "gender.php",
            type: "POST",
            data: { gend: gend, state: 1},
            beforeSend: function(){
                $("#gendsel").slideUp();
                $("#gendans").text("Updating").css("font-size","10px").slideDown();
                },
            complete: function(){
                },    
            success: function(data){
                $("#gendans").text(data).css("font-size","12px");
                },
            error: function(){
                
                }    
        });
                    
                        $("#gendit").slideDown();
                        $("#gendc").slideUp();
                                             
                    });
                    
                    $("#gendc").click(function(){
                        $("#gendit").text("EDIT");
                        $("#gendans").slideDown();
                        $("#gendsel").slideUp();
                        $("#gendc").slideUp();
                    });
                    </script>';
                    echo'<div class="row" style="margin-bottom: 0px; border-bottom: 1px solid #ddd;">
                    <div class="col-xs-2 text-right">
                    <span class="glyphicon glyphicon-user" style="color: #4080bf;font-size: 12px;"></span>
                    </div>
                    <div class="col-xs-5" id="gend">
                    <div id="gendans" style="font-size: 12px;">'.
                    $profile[7].
                    '
                    </div>
                    <div id="gendsel" style="display: none;">
                    <select class="form-control input-sm" id="gendchs" style="font-size: 12px;">
                    <option selected disabled>Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    </select>
                    </div>
                    </div>
                    <div class="col-xs-5">
                    <span class="btn btn-link" style="font-size: 10px; font-weight: 600;" id="gendit">EDIT</span>
                    <p><span class="btn btn-link" style="font-size: 10px; font-weight: 600; display: none;" id="gendc">CANCEL</span></p>
                    </div>
                    </div>';
                }
                if(empty($profile[8]))
                {
                    echo'<script>
                    $("#wst").keyup(function(){
                    if(!$.trim($("#wst").val()))
                    {
                     $("#stadit").text("EDIT");  
                    }else{
                        $("#stadit").text("SEND");
                    }  
                    });
                    
                    $("#stadit").click(function(){
                        if($("#stadit").text()=="SEND")
                        {
                        var txt=$("#wst").val();
        $.ajax({
            url: "write_status.php",
            type: "POST",
            data: { txt: txt, state: 1},
            beforeSend: function(){
                },
            complete: function(){
                },    
            success: function(data){
                $("#sta_con").replaceWith(data);
                },
            error: function(){
                
                }    
        });                            
                        }
                    });
                    </script>';
                    echo'<div class="row" style="margin-bottom: 0px; padding-top: 8px;">
                    <div class="col-xs-2 text-right" style="font-size: 12px; font-weight: 600; color: #4080bf;">
                    Status
                    </div>
                    <div id="sta_con" class="col-xs-9">
                    <div class="row">
                    <div class="col-xs-10">
                    <textarea id="wst" style="font-size: 12px;" placeholder="Write a status" class="form-control"></textarea>
                    </div>
                    <div class="col-xs-2">
                    <span class="btn btn-link" style="font-size: 10px; font-weight: 600;" id="stadit">EDIT</span>
                    </div>
                    </div>
                    </div>';
                }else{
                    echo'<script>
                    $("#ust").keyup(function(){
                    if(!$.trim($("#ust").val()))
                    {
                     $("#staup").text("EDIT");  
                    }else{
                        $("#staup").text("SEND");
                    }  
                    });
                    
                $("#staup").click(function(){
                if($("#staup").text()=="SEND")
                {
                    var txt=$("#ust").val();                
                
        $.ajax({
            url: "write_status.php",
            type: "POST",
            data: { txt: txt, state: 2},
            beforeSend: function(){
                },
            complete: function(){
                },    
            success: function(data){
                $("#stats").html(data);
                $("#ust").val("");
                },
            error: function(){
                
                }    
        });
                }
                });                    
                    </script>';
                    echo'<div class="row" style="margin-bottom: 15px; padding-top: 8px;">
                    <div class="col-xs-2 text-right" style="font-size: 12px; font-weight: 600; color: #4080bf;">
                    Status
                    </div>
                    <div id="sta_con" class="col-xs-9" style="font-size: 13px; border: 1px solid #ddd; box-shadow: 10px 10px 5px grey;
                    background-color: #4285f4; color: #fff; padding-top: 10px; padding-bottom: 10px; font-weight: 600;">
                    <div id="stats"><i>'.
                    $profile[8].'</i>
                    </div>
                    <div class="row" style="margin-top: 15px;">
                    <div class="col-xs-10">
                    <textarea id="ust" style="font-size: 12px;" placeholder="Update status" class="form-control"></textarea>
                    </div>
                    <div class="col-xs-2">
                    <span class="btn btn-link text-left" style="color: #fff; font-size: 10px; font-weight: 600;" id="staup">EDIT</span>
                    </div>
                    </div>
                    </div>
                    </div>';
                    
                }
                echo'</div>
                </div>
                <div class="row">
                <div class="col-xs-offset-1 col-xs-11" style="font-weight: 600; font-size: 12px; border-bottom: 2px solid #4285f4;
                margin-top: 20px; margin-bottom: 10px;">
                Ministry
                </div>
                </div>
                <div class="row" style="padding: 10px 0px;">
                <div class="col-xs-3 text-right" style="font-size: 12px; font-weight: 600; color: #4080bf;">
                Chapter Country
                </div>';
                if(empty($profile[12]))
                {
                    echo'
                    <script>
                    $("#chp_con").change(function(){
                    var chpt= $("#chp_con").val();
                        var txt="Successful Chapter Country Update";
                        var cnt=\'<div id="cnt" style="background-color: #0066ff; color: #fff; border-radius: 5px; font-weight: 600;"><div style="padding: 10px 5px;">\'+txt+\'</div></div>\';                    
                    
                    if($("#cpc_val").text()==$("#chp_con").val()){
                        $("#cpc_val").slideDown();
                        $("#cpc_cy").append(cnt);
                        setTimeout(function(){
                           $("#cnt").remove();
                        },5000);
                    }else{
                    
        $.ajax({
            url: "chpt_country.php",
            type: "POST",
            data: { chpt: chpt, state: 1},
            beforeSend: function(){
                $("#chp_con").slideUp();
                $("#cpc_val").text("Updating").css("font-size","10px").slideDown();
                },
            complete: function(){
                },    
            success: function(data){
                $("#cpc_val").text(data).css({"font-size":"12px","font-weight":"500"}).slideDown();
                $("#cpc_chg").slideDown();
                $("#cpc_cy").append(cnt);
                setTimeout(function(){
                    $("#cnt").remove();
                },5000);

                },
            error: function(){
                
                }    
        });                                        
               }
                    });
                    
                    $("#cpc_chg").click(function(){
                        $("#cpc_val").slideUp();
                        $("#chp_con").slideDown();
                        $("#cpc_chg").slideUp();
                    });
                    
                    </script>
                    ';
                    echo'
                    <div id="cpc_cy" class="col-xs-5">
                    <div id="cpc_val"style="font-size: 12px; font-weight: 600; diplay: none;">
                    </div>
                    <select id="chp_con" class="form-control input-sm">
                    <option disabled selected>Select Chapter Country</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="USA">USA</option>
                    <option value="UK">UK</option>
                    </select>
                    </div>
                    <div id="cpc_chg" class="col-sm-4" style="font-size: 10px; font-weight: 600; display: none; cursor: pointer">
                    CHANGE
                    </div>';
                }else
                {
                    echo'<script>
                    $("#cpc_sel").hide();
                    $("#cpc_cn").hide();
                    $("#cpc_edt").click(function(){
                        $("#cpc_edt").slideUp();
                        $("#cpc_det").slideUp();
                        $("#cpc_sel").slideDown();
                        $("#cpc_cn").slideDown();
                       
                    });
                    
                    $("#chp_con").change(function(){
                    var chpt= $("#chp_con").val();
        $.ajax({
            url: "chpt_country.php",
            type: "POST",
            data: { chpt: chpt, state: 1},
            beforeSend: function(){
                $("#cpc_sel").slideUp();
                $("#cpc_det").text("Updating").css("font-size","10px").slideDown();
                },
            complete: function(){
                },    
            success: function(data){
                $("#cpc_det").text(data).css({"font-size":"12px","font-weight":"500"});
                $("#cpc_cn").slideUp();
                $("#cpc_edt").slideDown();
                },
            error: function(){
                
                }    
        });                    

                    
                    });
                    
                    $("#cpc_cn").click(function(){
                        $("#cpc_edt").slideDown();
                        $("#cpc_det").slideDown();
                        $("#cpc_sel").slideUp();
                        $("#cpc_cn").slideUp();
                    });
                    </script>';
                    echo '<div class="col-xs-5">
                    <div id="cpc_det" class="text-left"  style="font-size: 12px;">
                    '.$profile[12].
                    '</div>
                    <div id="cpc_sel" style="display: none;">
                    <select id="chp_con" class="form-control input-sm">
                    <option disabled selected>Select Chapter Country</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="USA">USA</option>
                    <option value="UK">UK</option>
                    </select>
                    </div>
                    </div>
                    <div class="col-xs-4 text-left">
                    <span class="btn btn-link text-center" style="font-size: 10px; font-weight: 600;" id="cpc_edt">EDIT</span>
                    <p><span class="btn btn-link text-center" style="font-size: 10px; font-weight: 600; display: none;" id="cpc_cn">CANCEL</span></p>
                    </div>';
                }
                echo'
                </div>
                <div class="row" style="padding: 10px 0px;">
                <div class="col-xs-3 text-right" style="font-size: 12px; font-weight: 600; color: #4080bf;">
                Chapter State
                </div>';
                if(empty($profile[13]))
                {
                    echo'
                    <script>
                    $("#chp_sta").change(function(){
                    var chpt= $("#chp_sta").val();
                        var txt="Successful Chapter State Update";
                        var cnt=\'<div id="cnt" style="background-color: #0066ff; color: #fff; border-radius: 5px; font-weight: 600;"><div style="padding: 10px 5px;">\'+txt+\'</div></div>\';                    
                    
                    if($("#cps_val").text()==$("#chp_sta").val()){
                        $("#cps_val").slideDown();
                        $("#cps_cy").append(cnt);
                        setTimeout(function(){
                           $("#cnt").remove();
                        },5000);
                    }else{
                    
        $.ajax({
            url: "chpt_country.php",
            type: "POST",
            data: { chpt: chpt, state: 2},
            beforeSend: function(){
                $("#chp_sta").slideUp();
                $("#cps_val").text("Updating").css("font-size","10px").slideDown();
                },
            complete: function(){
                },    
            success: function(data){
                $("#cps_val").text(data).css({"font-size":"12px","font-weight":"500"}).slideDown();
                $("#cps_chg").slideDown();
                $("#cps_cy").append(cnt);
                setTimeout(function(){
                    $("#cnt").remove();
                },5000);

                },
            error: function(){
                
                }    
        });                                        
               }
                    });
                    
                    $("#cps_chg").click(function(){
                        $("#cps_val").slideUp();
                        $("#chp_sta").slideDown();
                        $("#cps_chg").slideUp();
                    });
                    
                    </script>';
                    echo'
                    <div id="cps_cy" class="col-xs-5">
                    <div id="cps_val"style="diplay: none;">
                    </div>
                    <select id="chp_sta" class="form-control input-sm">
                    <option disabled selected>Select Chapter State</option>
                    <option value="Anambra">Anambra</option>
                    <option value="Lagos">Lagos</option>
                    <option value="Port Harcort">Port Harcort</option>
                    </select>
                    </div>
                    <div id="cps_chg" class="col-sm-4" style="font-size: 10px; font-weight: 600; display: none;">
                    CHANGE
                    </div>';
                }else
                {
                    echo'<script>
                    $("#cps_sel").hide();
                    $("#cps_cn").hide();
                    $("#cps_edt").click(function(){
                        $("#cps_edt").slideUp();
                        $("#cps_det").slideUp();
                        $("#cps_sel").slideDown();
                        $("#cps_cn").slideDown();
                       
                    });
                    
                    $("#chp_sta").change(function(){
                    var chpt= $("#chp_sta").val();
        $.ajax({
            url: "chpt_country.php",
            type: "POST",
            data: { chpt: chpt, state: 2},
            beforeSend: function(){
                $("#cps_sel").slideUp();
                $("#cps_det").text("Updating").css("font-size","10px").slideDown();
                },
            complete: function(){
                },    
            success: function(data){
                $("#cps_det").text(data).css("font-size","12px");
                $("#cps_cn").slideUp();
                $("#cps_edt").slideDown();
                },
            error: function(){
                
                }    
        });                    

                    
                    });
                    
                    $("#cps_cn").click(function(){
                        $("#cps_edt").slideDown();
                        $("#cps_det").slideDown();
                        $("#cps_sel").slideUp();
                        $("#cps_cn").slideUp();
                    });
                    </script>';
                    echo '<div class="col-xs-5">
                    <div id="cps_det" class="text-left" style="font-size: 12px;">
                    '.$profile[13].
                    '</div>
                    <div id="cps_sel" style="display: none;">
                    <select id="chp_sta" class="form-control input-sm">
                    <option disabled selected>Select Chapter State</option>
                    <option value="Anambra">Anambra</option>
                    <option value="Lagos">Lagos</option>
                    <option value="Port Harcort">Port Harcort</option>
                    </select>
                    </div>
                    </div>
                    <div class="col-xs-4 text-left">
                    <span class="btn btn-link text-center" style="font-size: 10px; font-weight: 600;" id="cps_edt">EDIT</span>
                    <p><span class="btn btn-link text-center" style="font-size: 10px; font-weight: 600;
                    display: none;" id="cps_cn">CANCEL</span></p>
                    </div>';
                }
                echo'
                </div>
                <div class="row" style="padding: 10px 0px;">
                <div class="col-xs-3 text-right" style="font-size: 12px; font-weight: 600; color: #4080bf;">
                Chapter
                </div>';
                if(empty($profile[14]))
                {
                    echo'
                    <script>
                    $("#chp").change(function(){
                    var chpt= $("#chp").val();
                        var txt="Successful Chapter Update";
                        var cnt=\'<div id="cnt" style="background-color: #0066ff; color: #fff; border-radius: 5px; font-weight: 600;"><div style="padding: 10px 5px;">\'+txt+\'</div></div>\';                    
                    
                    if($("#chp_val").text()==$("#chp").val()){
                        $("#chp_val").slideDown();
                        $("#chp_cy").append(cnt);
                        setTimeout(function(){
                           $("#cnt").remove();
                        },5000);
                    }else{
                    
        $.ajax({
            url: "chpt_country.php",
            type: "POST",
            data: { chpt: chpt, state: 3},
            beforeSend: function(){
                $("#chp").slideUp();
                $("#chp_val").text("Updating").css("font-size","10px").slideDown();
                },
            complete: function(){
                },    
            success: function(data){
                $("#chp_val").text(data).css({"font-size":"12px","font-weight":"500"}).slideDown();
                $("#chp_chg").slideDown();
                $("#chp_cy").append(cnt);
                setTimeout(function(){
                    $("#cnt").remove();
                },3000);

                },
            error: function(){
                
                }    
        });                                        
               }
                    });
                    
                    $("#chp_chg").click(function(){
                        $("#chp_val").slideUp();
                        $("#chp").slideDown();
                        $("#chp_chg").slideUp();
                    });
                    
                    </script>';
                    echo'
                    <div id="chp_cy" class="col-xs-5">
                    <div id="chp_val"style="diplay: none;">
                    </div>
                    <select id="chp" class="form-control input-sm">
                    <option disabled selected>Select Chapter</option>
                    <option value="Ire-Akari">Ire-Akari</option>
                    <option value="Ojodu">Ojodu</option>
                    </select>
                    </div>
                    <div id="chp_chg" class="col-sm-4" style="font-size: 10px; font-weight: 600; display: none;">
                    CHANGE
                    </div>';
                }else
                {
                    echo'<script>
                    $("#cp_sel").hide();
                    $("#cp_cn").hide();
                    $("#cp_edt").click(function(){
                        $("#cp_edt").slideUp();
                        $("#cp_det").slideUp();
                        $("#cp_sel").slideDown();
                        $("#cp_cn").slideDown();
                       
                    });
                    
                    $("#chp").change(function(){
                    var chpt= $("#chp").val();
        $.ajax({
            url: "chpt_country.php",
            type: "POST",
            data: { chpt: chpt, state: 3},
            beforeSend: function(){
                $("#cp_sel").slideUp();
                $("#cp_det").text("Updating").css("font-size","10px").slideDown();
                },
            complete: function(){
                },    
            success: function(data){
                $("#cp_det").text(data).css("font-size","12px");
                $("#cp_cn").slideUp();
                $("#cp_edt").slideDown();
                },
            error: function(){
                
                }    
        });                    

                    
                    });
                    
                    $("#cp_cn").click(function(){
                        $("#cp_edt").slideDown();
                        $("#cp_det").slideDown();
                        $("#cp_sel").slideUp();
                        $("#cp_cn").slideUp();
                    });
                    </script>';                    
                    echo '<div class="col-xs-5">
                    <div id="cp_det" class="text-left" style="font-size: 12px;">
                    '.$profile[14].
                    '</div>
                    <div id="cp_sel" style="display: none;">
                    <select id="chp" class="form-control input-sm">
                    <option disabled selected>Select Chapter</option>
                    <option value="Ire-Akari">Ire-Akari</option>
                    <option value="Ojodu">Ojodu</option>
                    </select>
                    </div>
                    </div>
                    <div class="col-xs-4 text-left">
                    <span class="btn btn-link text-center" style="font-size: 10px; font-weight: 600;" id="cp_edt">EDIT</span>
                    <p><span class="btn btn-link text-center" style="font-size: 10px; font-weight: 600;
                    display: none;" id="cp_cn">CANCEL</span></p>
                    </div>';
                }

        $table=array("groups","grp_users") ;
        $field=array("groups.Id","groups.Name","groups.Pix","groups.Purpose");
        $on=array("groups.Id"=>"grp_users.Grp_id");
        $where=array("grp_users.User_id"=>$id);
        //$limit=array($start,$lim);
        //$order=array("rpn_tgln_comment_replies.Date_time"=>"DESC");                
                
                $grp=self::$_con->join($table,$field,$on,$where,0,0);
                echo'</div>
                <div class="row" style="margin-top: 15px;">
                <div class="col-xs-offset-4 col-xs-4 text-center" style="font-size: 16px; color: #4080bf; font-weight: 700; margin-bottom: 15px;">
                GROUP';
                if($grp > 0)
                {
                    if($grp > 9)
                    {
                        echo'<sup><span style="background-color: red; font-weight: 600; color: #fff; padding: 2px 2.5px; margin-left: 2px;" class="text-center">9+</span><sup>';
                    }else
                    {
                        echo'<sup><span style="font-weight: 700; color: red; padding: 2px; font-size: 11px;" class="text-center">'.$grp.'
                        </span><sup>';
                    }
                }
                echo'
                </div> 
                </div>';
                while($d_grp=self::$_con->fetch_join())
                {
                    echo'<script>
                    $("#grp'.$d_grp[0].'").click(function(){
                    var id=this.id;
        $.ajax({
            url: "grp_display.php",
            type: "POST",
            data: { id: id, info: "info"},
            beforeSend: function(){
                $("#vis").append("<div class=\'row\' id=\'gnotify\'><div id=\'grs\' style=\'margin: 20px;font-size: 11px;\'>Loading Group...</div></div>");
                },
            complete: function(){
            $("#gnotify").remove();
                },    
            success: function(data){
            $(".opt").slideUp();
                $("#vis").empty();
                $("#vis").html(data);
                },
            error: function(){
                
                }    
        });                        
                    });
                    </script>';
                    echo'<div class="row">
                         <div class="col-xs-2">
                         <img src="grp_prf'. DIRECTORY_SEPARATOR.$d_grp[2].'" width="40" height="40">
                         </div>
                         <div class="col-xs-10 text-left">
                         <h5 id="grp'.$d_grp[0].'" style="color: #4285f4;; font-weight: 600;">'.$d_grp[1].'</h5>
                         </div>
                         </div>
                         <br>';
                }
                                
                
 
            }else
            {
                throw new Exception("profile_pix method expects a parameter");
            }
    
    }    
}
?>