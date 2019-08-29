<?php
class Pos_EditProfile
{
    public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_con;
    
    private static function connection()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    
    public static function editProfile($table,$fields,$pointer)
    {
        if(!self::$_con)
        {
            self::connection();
        }
        self::$_con->update_where($table,$fields,$pointer);
        
    }
    
    public static function writeStatus($table,$fields,$pointer,$text)
    {
        self::editProfile($table,$fields,$pointer);
        echo'
            <script>
                $("#ust").keyup(function(){
                if(!$.trim($("#ust").val()))
                {
                    $("#staup").text("EDIT");  
                }else
                {
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
        echo'
            <div id="sta_con" class="col-xs-9" style="font-size: 13px; border: 1px solid #ddd; box-shadow: 10px 10px 5px grey;
            background-color: #4285f4; color: #fff; padding-top: 10px; padding-bottom: 10px; font-weight: 600;">
            <div id="stats"><i>'.
            $text.'</i>
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
        ';
        
    }
    
    public static function editStatus($table,$fields,$pointer,$text)
    {
        self::editProfile($table,$fields,$pointer);
        echo $text;
    }
    
}
?>