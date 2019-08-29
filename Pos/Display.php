<?php
require_once('C:\xampp\htdocs\oop_solutions\TGLN\Pos\Process.php');
require_once('C:\xampp\htdocs\oop_solutions\TGLN\Pos\Defy.php');
class Pos_Display
{
    protected static $_con;
    protected static $_con_ii;
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    
    public function __construct($callid,$pageNum)
    {
        self::connection(); 
    }
    
    private static function connection()
    {
        self::$_con=new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
        self::$_con_ii= new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    
    public static function comment($id,$user_id)
    {
        if(!self::$_con)
        {
            self::connection();
        }        
        $rid=array("Id"=>$id);
        $rfd=array("*");
        if(self::$_con->select_where("rpn_tgln",$rfd,$rid))
        {
        $read=self::$_con->fetch_select_where();
        $cid=array("Post_id"=>$id);
        $numCom=self::$_con->select_all_where("rpn_tgln_comment",$cid);
        $ltable=array("rpn_tgln_comment","follow");
        $lfield=array("rpn_tgln_comment.Comment_id","rpn_tgln_comment.Comment",
                      "rpn_tgln_comment.User_id","rpn_tgln_comment.Date_time");
        $lon=array("rpn_tgln_comment.User_id"=>"follow.Lead_id");
        $lwhere=array("follow.Follower_id"=>$user_id,"rpn_tgln_comment.Post_id"=>$id);
        $numLead=self::$_con->join($ltable,$lfield,$lon,$lwhere,0,0);
        $disL=self::$_con->fetch_join();
        $fon=array("rpn_tgln_comment.User_id"=>"follow.Follower_id");
        $fwhere=array("follow.Lead_id"=>$user_id,"rpn_tgln_comment.Post_id"=>$id);
        $numff=self::$_con->join($ltable,$lfield,$fon,$fwhere,0,0);
        $disF=self::$_con->fetch_join();
        $pic=array("Id"=>$user_id);
        $pfid=array("Pix");
        $use=self::$_con->select_where("tgln_users",$pfid,$pic);
        $users=self::$_con->fetch_select_where();
        $numlik=self::$_con->select_all_where("rpn_tgln_likes",$cid);
        $lk=array("Post_id"=>$id,"User_id"=>$user_id);
        $numchl=self::$_con->select_all_where("rpn_tgln_likes",$lk);
        
            if(!empty($users[0])){
                 $img="user_img/".$users[0];
            }else{
               $img="anonymous_img/icons8-customer-100.png";
            }
            if($numchl > 0){
                $srt="You already liked this";
                $colo="red";
            }else{
                $srt="You haven't liked this";
                $colo="#4285f4";
            }
            
        echo'
        <script>
        var quote;
        var pNum=1;
        $("#direct").click(function(){
        $("#nht_div").val("tgln");
        $("#vis").replaceWith("<div class=\'row\' id=\'vis\'><div id=\'chat_con\'></div></div>");
        $.ajax({
            url: "post_rpn.php",
            type: "POST",
            data: { info: "tgln", state: 1},
            beforeSend: function(){
                $("#vis").append("<div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Updating Post From RPN...</div>");
                },
            complete: function(){
                $("#notify").remove();
                $("#flas").remove();
                },    
            success: function(data){
                if(data==""){
                    ad_div=$("<button></button>").text("Finished...").attr({"id":"flas","class":"btn btn-info"});
                    action="active";
                }else{
                
                    $("#chat_con").before(data);
                                
                    action="inactive";
                }
                },
            error: function(){
                
                }    
        });            
        });
        
        $("#cm'.$id.'").hover(function(){';
                    
            echo'
                $("#rd'.$id.'").hide();
                $("#rw'.$id.'").show();
                $("#cr'.$id.'").fadeIn("slow");
  
        });
        
        
        $("#tx'.$id.'").focus(function(){
            $("#tx'.$id.'").css({"border-width":"0px 0px 2px","border-bottom":"2px solid #4285f4","width":"100%","border-radius":"180px"})
        });
        
        
        $("#c_send").click(function(){
            if(!$.trim($("#tx'.$id.'").val())){
            alert("not sending");
            var nth="Empty string";
            }else{
            quote += 1;
            var str=$.trim($("#tx'.$id.'").val());
            var id="tx'.$id.'";            
            $.ajax({
            url: "rpn_write_comment.php",
            type: "POST",
            data: { str: str, id: id, quote: quote},
            beforeSend: function(){
                $("#sp'.$id.'").after("<div id=\'notify\' style=\'margin: 20px;font-size: 11px;\'>Loading...</div>");
                },
            complete: function(){
                },    
            success: function(data){
            if(data != "No entry!"){
            items=data.split("<<br>><<div>>//::;;<>");
                setTimeout(function(){                    
                $("#notify").remove();
                $("#sp'.$id.'").after(items[0]);
                $("#tx'.$id.'").val("");
                $("#sac'.$id.'").text(items[1]).css({"color":"white","background-color":"red"});
                $("#clickSound").play();
                },1000);
            }
                },
            error: function(){
                
                }    
        });                
            }
        });
        

        </script>
        ';
        
        echo'<div class="content">
        <audio id="clickSound" style="display: none;">
        <source src="audio_4.wav"></source>
        <source src="audio_4.mp3"></source>
        <source src="audio_4.ogg"></source>
        </audio>
        </div>
        <div class="row" style="border: 1px solid #ddd; padding: 10px 0px;">
        <div class="col-sm-2 col-xs-2">
        <i class="glyphicon glyphicon-circle-arrow-left" style="font-size: 15px; color: #4285f4; cursor: pointer;" id="direct"></i>
        </div>
        <div class="col-sm-10 col-xs-10 text-center">
        <div class="row">
        <div class="col-sm-offset-2 col-xs-offset-2 col-sm-4 col-xs-4" style="color : #4285f4; font-weight: 700;">
         #RPN
        </div>
        <div class="col-sm-6 col-xs-6 text-center" style="font-size: 11px; color: #ddd;">'.$read[4].'
        </div>
        </div>
        </div>
        </div>
        <div class="row" style="padding-bottom: 20px;">
        ';
        echo !empty($read[2]) ? '<div><img src="rpn_post_img/'.$read[2].'" width="100%" height="100px"></div>' : "" ;
        echo'
        <div class="" style="font-size: 12px; background-color: #000033; color: #fff;">
        <div style="padding: 20px 10px;">
        '.$read[1].'
        </div>
        </div>
        </div>
        <div class="row" id="rd'.$id.'">
        <div class="col-sm-offset-4 col-xs-offset-4 col-sm-4 col-xs-4 text-center">';
        echo'<span class="glyphicon glyphicon-th" id="cm'.$id.'"></span>
        </div>
        <div class="col-sm-4 col-xs-4">
        </div>
        </div>
        
        
        <div class="row" style="margin-bottom: 0px; display: none; padding: 10px;" id="cr'.$id.'">
        <div class="col-sm-6 col-xs-6" style="font-size: 10px; color: '.$colo.';">'
        .$srt.
        '</div>
        <div class="col-sm-6 col-xs-6">
        <span class="text-center" style="font-size: 10px;">'.$numlik[0].
        '   '.'<span class="glyphicon glyphicon-heart" style="color:'.$colo.'; font-size: 13px;">
        </span>
        </span>
        </div>
        <hr>
        </div>
        
        <div id="rw'.$id.'" class="row" style="display: none;">
        <div class="col-sm-offset-1 col-xs-offset-1 col-sm-2 col-xs-2">
        <img src="'.$img.'" width="40" height="40">
        </div>
        <div class="col-sm-7 col-xs-7">
        <textarea id="tx'.$id.'"
        style="height: 40; border: 1px solid #ddd; border-radius: 180px;">
        </textarea>
        </div>
        <div class="col-sm-2 col-xs-2">
        <div id="c_send" style="border: 1px solid #ddd; width: 40px;
        height: 40px; border-radius: 50%; padding: 11px; background-color: #4285f4;">
        <span class="glyphicon glyphicon-send text-center" style="color: #fff;">
        </span>
        </div>
        </div>
        </div>
        <hr>
        
        <div class="row" id="sp'.$id.'" style="margin-bottom: 10px;">
        <div class="col-sm-4 col-xs-4 text-center">
        <button class="btn btn-link" id="al'.$id.'" style="padding: 5px 5px; font-size: 11px;">
         All Comment<sup><span style="background-color: red; color: white; margin-left: 3px; font-weight: 700;
         border: 1px solid #ddd; padding: 2px; border-radius: 3px" class="text-center sac" id="sac'.$id.'">'.$numCom.'
        </span></sup></button>
        </div>
        <div class="col-sm-4 col-xs-4 text-center">
        <button class="btn btn-link" id="ld'.$id.'" style="padding: 5px 5px; font-size: 11px; width: 100%;">
        Following<sup><span style="background-color: red; color: white; margin-left: 3px; font-weight: 700; border: 1px solid #ddd; padding: 2px; border-radius: 3px;" class="text-center">
        '.$numLead.'</span></sup></button>
        </div>
        <div class="col-sm-4 col-xs-4 text-center">
        <button class="btn btn-link" id="ff'.$id.'" style="padding: 5px 5px; font-size: 11px; width: 100%;">
        Follower<sup><span style="width: 8px; height: 4px; background-color: red; color: white;
        margin-left: 3px; font-weight: 700; border: 1px solid #ddd; padding: 2px; border-radius: 50%;"
        class="text-center">'.$numff.'
        </span></sup></button>
        </div>
        </div>
        '
        ;
        $table=array("tgln_users","rpn_tgln_comment") ;
        $field=array("rpn_tgln_comment.Comment_id","rpn_tgln_comment.Comment","rpn_tgln_comment.Pix",
                     "rpn_tgln_comment.User_id","rpn_tgln_comment.Date_time",
                     "tgln_users.First_name","tgln_users.Last_name","tgln_users.Pix",
                     );
        $on=array("rpn_tgln_comment.User_id"=>"tgln_users.Id");
        $where=array("rpn_tgln_comment.Post_id"=>$id);
        $limit=array(0,8);
        $order=array("rpn_tgln_comment.Date_time"=>"DESC");        
        
        self::tgln_add_Comment($table,$field,$on,$where,$order,$limit,$user_id);
        self::$_con_ii->join($table,$field,$on,$where,$order,$limit);
        $tjoin=self::$_con_ii->join_total();
        if($tjoin[0] > 8){
            echo'<script>
            $("#rot'.$id.'").click(function(){
            var id=this.id;
            var num=$("#pi'.$id.'").val();
            num++;
            $("#pi'.$id.'").val(num);
            $.ajax({
            url: "rpn_add_comment.php",
            type: "POST",
            data: { id: id, num: num},
            beforeSend: function(){
                $("#rot'.$id.'").before("<div id=\'add_com\' style=\'margin: 20px;font-size: 11px;\'>Loading  more comments...</div>");
                },
            complete: function(){
                },    
            success: function(data){
            $("#add_com").remove();
                 if(!$.trim(data)){
                    $("#rot'.$id.'").fadeOut();
                 }else{
                    $("#drot").before(data);
                 }
                },
            error: function(){
                
                }    
        });                  
            });
            </script>';
            echo '<div class="row text-center" id="drot">
            <div class="col-sm-offset-4 col-xs-offset-4 col-sm-4 col-xs-4">
            <span id="rot'.$id.'" class="btn btn-primary" style="font-size: 11px;">See more</span>
            </div>
            <div class="col-sm-4 col-xs-4">
            <input type="hidden" id="pi'.$id.'" value="1">
            </div>
            </div>';            
        }
        }
        
        
    }
    
    public static function tgln_add_Reply($table,$field,$on,$where,$order=false,$limit=false,$user_id)
    {
        if(!self::$_con)
        {
            self::connection();
        }
        if(self::$_con->join($table,$field,$on,$where,$order,$limit) > 0)
        {
            while($roll=self::$_con->fetch_join())
            {
                $str=explode(" ",$roll[1]);
                $texts="";
                $count=count($str);
                for($i=0; $i<$count; $i++)
                {
                    $texts.=$str[$i]." ";
                }
                    $strg="";
                    if($count > 12)
                    {
                        for($i=0; $i<12; $i++)
                        {
                            $strg.=$str[$i]." ";
                        }
                    }else
                    {
                        for($i=0; $i<$count; $i++)
                        {
                            $strg.=$str[$i]." ";
                        }
                    }
                     echo'<script>
                              $("#reply'.$roll[0].'").click(function(){
                                 $("#dcrep'.$roll[0].'").text("'.$roll[1].'").css("font-size","10px");
                              });
                          </script>';
                    echo'<div class="row" style="padding-top: 15px; font-size: 10px;">
                         <div class="col-sm-offset-2 col-sm-10 col-xs-12">
                         <div class="col-sm-2 col-xs-2">';
                         echo empty($roll[6]) ? '<img src="anonymous_img/icons8-customer-100.png" width="30"
                         height="30" class="img-circle">': '<img src="user_img/'.$roll[6].'"
                         width="30" height="30" class="img-circle">';
                    echo'
                         </div>
                         <div class="col-sm-10 col-xs-10">
                         <div class="">
                         <span style="font-size: 11px; font-weight: 700; color: #4080bf;"
                         class="btn btn-link" id="ns'.$roll[2].'">';
                    echo $roll[4].'    '.'    '.$roll[5].'
                         </span>
                         </div>
                         <div class="text-center" style="font-size: 8px; color: #adad85; font-weight: 600;">'.
                         $roll[3].'
                         </div>                         
                         <div  id="dcrep'.$roll[0].'" style="border: 1px solid #ddd; text-overflow: pre-wrap;
                         border-radius: 10px; padding: 10px; background-color: #0040ff; color: #fff;">'.$strg;
                    echo $count > 12 ? '...<span id="reply'.$roll[0].'"
                         style="font-size: 10px; color: #fff; font-weight: 600;">Continue</span>':'';
                    echo'
                         </div>';
                         $tab="rpn_tgln_comm_rep_likes";
                         $fild=array("Id");
                         $point=array("Reply_id"=>$roll[0]);
                         $ct_rlk=self::$_con->select_where($tab,$fild,$point);
                         
                    echo'<script>
                         $("#lre'.$roll[0].'").click(function(){
                            var gly="glyphicon glyphicon-heart-empty";
                            var id=this.id;
                            //var sp=id.slice(3);
                            var atri=$("#"+id).attr("class");
                            if(atri === gly){
                              $("#"+id).attr("class","glyphicon glyphicon-heart").css("color","rgb(255, 51, 51)");
                              $.post("rpn_com_rep_likes.php",{
                              start: "1",
                              identity: id
                              },function(data){
                              $("#drl'.$roll[0].'").text(data).css({"font-size":"9px","font-weight":"700","color":"#4285f4"});
                             });                    
                            }else{
                              $("#"+id).attr("class","glyphicon glyphicon-heart-empty").css("color","#4285f4");
                              $.post("rpn_com_rep_likes.php",{
                              start: "0",
                              identity: id
                              },function(data){
                              $("#drl'.$roll[0].'").text(data).css({"font-size":"9px","font-weight":"700","color":"#4285f4"});                      
                         
                              });                    
                    
                            }
                            
                         });
                         </script>';
                    echo'
                         <div class="row" style="margin-top: 5px;">
                         <div class="col-sm-2 col-xs-2 text-center">
                         <span id="lre'.$roll[0].'"';                         
                         
                         $filds=array("*");
                         $points=array("User_id"=>$user_id,"Reply_id"=>$roll[0]);
                         $ck_rep=self::$_con->select_where($tab,$filds,$points);
                    echo $ck_rep > 0 ? 'class="glyphicon glyphicon-heart"
                         style="font-size: 12px; color: #ff1a1a; font-weight: 700;">': 'class="glyphicon glyphicon-heart-empty"
                         style="font-size: 12px; color: #4d79ff; font-weight: 700;">';
                    echo'</span>
                         </div>
                         <div class="col-sm-6 col-xs-9" id="drl'.$roll[0].'">';
                         if($ct_rlk < 2){
                            if($ct_rlk < 1){
                                echo'<small>Be 1st to like</small>';
                            }else{
                                echo'<small>'. $ct_rlk.' Like</small>';
                            }
                         }else{
                            echo'<small>'. $ct_rlk.' Likes</small>';
                         }
                         echo'
                         </div>
                         </div>
                         </div>
                         </div>
                         </div>';                         
                    
                
            }
        }
        

        
    }
    
    public static function tgln_add_Comment($tables,$fields,$ons,$wheres,$orders=false,$limits=false,$user_id)
    {
        if(!self::$_con)
        {
            self::connection();
        }
        $tjoin="";
        $numjoin=self::$_con->join_ii($tables,$fields,$ons,$wheres,$orders,$limits);
        if($numjoin > 0)
        {
            //$cnt=0;
            ///$col=self::$_con->fetch_join_ii();
            while($coll=self::$_con->fetch_join_ii())
            {
                //$cnt++;
                $cdate=date("Y-m-d");
                $xdate=explode(" ",$coll[4]);
                if($xdate[0]==$cdate){
                    $tdate="Today";
                }else{
                    $tdate=$xdate[0];
                }
                echo '
                <script>
                $("#ylk'.$coll[0].'").click(function(){
                    var gly="glyphicon glyphicon-heart-empty";
                    var id=this.id;
                    var sp=id.slice(3);
                    var atri=$("#"+id).attr("class");
                    if(atri === gly){
                        $("#"+id).attr("class","glyphicon glyphicon-heart").css("color","rgb(255, 0, 0)");
                        $.post("rpn_likes_comment.php",{
                        start: "1",
                        identity: id
                        },function(data){
                           $("#dv'.$coll[0].'").text(data).css({"font-size":"9px","font-weight":"700","color":"#4285f4"});
                        });
                    }else{
                        $("#"+id).attr("class","glyphicon glyphicon-heart-empty").css("color","#4285f4");
                        $.post("rpn_likes_comment.php",{
                        start: "0",
                        identity: id
                        },function(data){
                           $("#dv'.$coll[0].'").text(data).css({"font-size":"9px","font-weight":"700","color":"#4285f4"});
                        });
                    }
                });
                
                var dictate;
                
                $("#edt'.$coll[0].'").click(function(){                
                    $("#ded'.$coll[0].'").fadeIn("slow");
                    $("#edt'.$coll[0].'").fadeOut("slow");
                    var ann = $("#edt'.$coll[0].'").text();
                    if(ann=="Edit"){
                        dictate="edit";
                    }else{
                    $("#ctx'.$coll[0].'").val("");
                         dictate="reply";               
                    }

                });
                
                $("#cmup'.$coll[0].'").click(function(){
                var id = this.id;
                var ann = $("#edt'.$coll[0].'").text();
                if(ann=="Edit"){
            if(!$.trim($("#ctx'.$coll[0].'").val())){
            alert("empty");   
            }else{ 
            var updt=$("#ctx'.$coll[0].'").val();
            $.ajax({
            url: "rpn_update_comment.php",
            type: "POST",
            data: { update: updt, id: id},
            beforeSend: function(){
                $("#ded'.$coll[0].'").after(\'<div class="row text-center" id="update" style="font-size: 10px;"><div>Updating your comment</div></div>\');
                },    
            success: function(data){
                if(data != "String not supported!"){
                items=data.split("<<br>><<div>>//::;;<>");
                $("#update").remove();
                $("#cmcn'.$coll[0].'").html("");
                $("#cmcn'.$coll[0].'").html(items[0]);
                $("#ctx'.$coll[0].'").val("");
                $("#tme'.$coll[0].'").html(items[1]);
                $("#ded'.$coll[0].'").fadeOut("slow");
                $("#edt'.$coll[0].'").fadeIn("slow");               
                }else{
                $("#ded'.$coll[3].'").after(\'<div class="row text-center" id="update"><div>\'+data+\'</div></div>\');
                }
                },
            error: function(){
                
                }    
        });
            }                   
                }
                if(ann=="Reply"){
            if(!$.trim($("#ctx'.$coll[0].'").val())){
            alert("empty");   
            }else{ 
            var updt=$("#ctx'.$coll[0].'").val();
            $.ajax({
            url: "rpn_reply_comment.php",
            type: "POST",
            data: { updates: updt, id: id},
            beforeSend: function(){
                $("#ded'.$coll[0].'").after(\'<div class="row text-center" id="updtes" style="font-size: 10px;"><div>Updating your comment with \'+updt+\' and \'+id+\' </div></div>\');
                },    
            success: function(data){
                $("#updtes").remove();
                if(data != "String not supported!"){
                items=data.split("<<br>><<div>>//::;;<>");
                $("#cmcn'.$coll[0].'").after(items[1]);
                $("#rcnt'.$coll[0].'").text(items[0]);
                $("#ded'.$coll[0].'").fadeOut("slow");
                $("#edt'.$coll[0].'").fadeIn("slow");               
                }else{
                $("#ded'.$coll[3].'").after(\'<div class="row text-center" id="update"><div>\'+data+\'</div></div>\');
                }
                },
            error: function(responseTxt, statusTxt, xhr){
            setTimeout(function(){
            $("#updtes").remove();
            },1000);
            alert("Error: " + xhr.status + ": " + xhr.statusText);
                
                }    
        });
            }                 
                

                }

                });
                
                </script>
                ';
                
            echo'<div class="row" style="margin-bottom: 20px;padding-bottom: -10px;">';
            echo $coll[3]==$_SESSION["user"] ?
            '<div class="col-sm-offset-1 col-xs-offset-1 col-sm-11 col-xs-11" style="border: 1px solid #ddd;">' : '
            <div class="col-sm-11 col-xs-11" style="border: 1px solid #ddd;">';
            echo'
            <div class="row" style="border-bottom: 1px solid #ddd; background-color: ';
            echo $coll[3]==$_SESSION["user"] ? '#4080bf;': ' #0040ff ;';
            echo'
            padding: 10px;">
            <div class="col-sm-2 col-xs-2" style="">';
            echo !empty($coll[7]) ? '<img src="user_img/'.$coll[7].'"
            width="30" height="30" class="img-circle">' :
            '<img src="anonymous_img/icons8-customer-100.png" width="30"
            height="30" class="img-circle">';
            echo'</div>
            <div class="col-sm-10 col-xs-10 text-center btn btn-link" style="font-weight: 700;padding: 5px;color: #fff;"
            id="dv'.$coll[0].'_'.$coll[3].'">
            ';
            echo $coll[3]==$_SESSION["user"] ? 'You' : $coll[5].'    '.'    '.$coll[6];
            echo'
            <br><span id="tme'.$coll[0].'" style="font-size: 10px; color: #fff; margin-top: -10px;">'.$tdate.'   '.
            $xdate[1].'</span>
            </div>
            </div>
            <div class="row" style="padding: 10px 5px; font-size: 12px; margin-bottom: -15px;" id="cmcn'.$coll[0].'">
            <div>'.$coll[1].'           
            </div>
            </div>';
            $cm_ide=array("Comment_id"=>$coll[0]);
            $reply=self::$_con_ii->select_all_where("rpn_tgln_comment_replies",$cm_ide);
        $table=array("tgln_users","rpn_tgln_comment_replies") ;
        $field=array("rpn_tgln_comment_replies.Reply_id","rpn_tgln_comment_replies.Reply",
                     "rpn_tgln_comment_replies.User_id","rpn_tgln_comment_replies.Date_time",
                     "tgln_users.First_name","tgln_users.Last_name","tgln_users.Pix",
                     );
        $on=array("rpn_tgln_comment_replies.User_id"=>"tgln_users.Id");
        $where=array("rpn_tgln_comment_replies.Comment_id"=>$coll[0]);
        $limit=array(0,4);
        $order=array("rpn_tgln_comment_replies.Date_time"=>"DESC");
        self::tgln_add_Reply($table, $field, $on, $where, $order,$limit, $user_id);
        //$rep=self::$_con_ii->join($table,$field,$on,$where,$order,$limit);

            //if($rep > 0)
            //{                
                         if($reply > 4){
                            echo'
                            <script>
                            $("#vre'.$coll[0].'").click(function(){
                            var id=this.id;
                            var hid=$("#hd'.$coll[0].'").val();
                            hid++;
                            $("#hd'.$coll[0].'").val(hid);
                            
                            $.ajax({
                                url: "rpn_addreply_comment.php",
                                type: "POST",
                                data: { hid: hid, id: id},
                                beforeSend: function(){
                                   //$("#vre'.$coll[0].'").css("color","red");
                                   $("#smr'.$coll[0].'").after(\'<div class="row text-center" id="updtng" style="font-size: 10px;"><div>Loading more replies</div></div>\');
                                },    
                                success: function(data){
                                   $("#updtng").remove();
                                   if(!$.trim(data)){
                                      $("#smr'.$coll[0].'").fadeOut();
                                      $("#updtng").remove();
                                   }else{
                                      $("#smr'.$coll[0].'").before(data);
                                   }
                               },
                               error: function(responseTxt, statusTxt, xhr){
                                  setTimeout(function(){
                                    $("#updtng").remove();
                                  },1000);
                                  alert("Error: " + xhr.status + ": " + xhr.statusText);
                
                              }
                           })
                             });
                                            
                            </script>
                            <div style="margin-top: 10px;" class="text-center" id="smr'.$coll[0].'">                            
                            <button class="btn btn-link" id="vre'.$coll[0].'"
                            style="font-size: 11px; font-weight: 500;">
                            See more replies
                            </button>
                            </div>
                            <input type="hidden" value="1" id="hd'.$coll[0].'">';
                                         
                
            }                
            //}
            
            echo'
            <hr>
            <div class="row" style="margin-top: -15px; margin-bottom: 10px;">
            <div class="col-sm-8 col-xs-8 text-left">
            <div class="row">
            <div class="col-sm-4 col-xs-4">';
            $cms_lk=array("Comment_id"=>$coll[0],"User_id"=>$user_id);
            $cm_lk=self::$_con->select_all_where("rpn_tgln_comment_likes",$cms_lk);
            echo $cm_lk < 1 ? '<span class="glyphicon glyphicon-heart-empty" id="ylk'.$coll[0].'"
            style="color: #4285f4; font-size: 15px;"></span>' : '<span class="glyphicon glyphicon-heart"
            id="ylk'.$coll[0].'" style="color: red; font-size: 15px;"></span>';
            echo'
            </div>
            <div class="col-sm-8 col-xs-8 btn btn-link" style="font-size: 10px; font-size-adjust: inherit;
            color: #4285f4; font-weight: 600;" id="dv'.$coll[0].'">';
            $cms_lks=array("Comment_id"=>$coll[0]);
            
            $cm_lks=self::$_con->select_all_where("rpn_tgln_comment_likes",$cms_lks);
            if($cm_lks < 2){
                if($cm_lks < 1){
                    //$cm=self::$_con->fetch_select_where();
                    echo $coll[3] == $user_id ? ' Like your comment' :
                    ' Like this comment';
                }else{
                    echo $cm_lks.' Like';
                }
            }else{
                echo $cm_lks.' Likes';
            }
            echo'
            </div>
            </div>
            </div>
            <div class="col-sm-4 col-xs-4 text-right" >
            ';
            echo $coll[3]==$user_id ? '<span class="btn btn-link"
            id="edt'.$coll[0].'" style="font-size: 10px; font-weight: 700;">Edit</span>':
            '<span class="btn btn-link" id="edt'.$coll[0].'"
            style="font-size: 10px; font-weight: 700;">Reply</span>';
            echo'
            </div>
            </div>
            
            <div class="text-center btn btn-link" id="rcnt'.$coll[0].'" style="font-size: 10px;
            color: #4285f4; margin-top: -20px; font-weight: 600;">';
            if($reply < 2){
                if($reply < 1){
                    echo 'No reply';
                }else{
                    echo $reply.' reply';
                }
            }else{
                echo $reply.' replies';
            }
            echo'
            </div>
            
            <div id="ded'.$coll[0].'" class="row" style="display: none;">
            <div class="row" style="padding: 3px 3px 10px; margin-top: 5px;">
            <div class="col-sm-offset-1 col-xs-offset-1 col-sm-8 col-xs-8">
            <textarea style="width: 100%; height: 30px; border-radius: 180px;" id="ctx'.$coll[0].'">'.
            $coll[1].'
            </textarea>
            </div>
            <div class="col-sm-3 col-xs-3 text-center">
            <div id="cmup'.$coll[0].'" style="color: #fff; border: 1px solid #ddd;
            background-color: #3f3e8e; width: 30px; height: 30px; border-radius: 50%;
            font-size: 9px; padding: 8px;">
            <span class="glyphicon glyphicon-send"></span>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            ';            
            
            }

            
        }
    }
    

}
?>