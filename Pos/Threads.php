<?php
class Pos_Threads {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_con;

    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }
    private static function check(){
        if(!self::$_con)
        {
            self::connect();
        }
    }
    private static function text( $text){
        if(!filter_var($text, FILTER_SANITIZE_STRING) === false){
            return $text;
        }else{
            return false;
        }
    }
    private static function getTagThread($table,$field,$on,$where,$order,$limit){
        self::check();
        if(self::$_con->join_like($table,$field,$on,$where,$order,$limit) > 0){
            $all_res = array();
            while($row = self::$_con->fetch_join_like()){
                if(!empty($row['Post_Pix'])){
                    // $all_pix = array();
                    $needle = '*?';
                    $value=strpos($row['Post_Pix'],$needle);
                    if($value){
                        $pixes = explode('*?',$row['Post_Pix']);
                        $pix = $pixes[0];
                        $pixNum = count($pixes);
                    }else{
                        $pix=$row['Pix'];
                        $pixNum = 1;
                    }
                }
                $follower = self::$_con->select_where('followers',array('Id'),array('Followee'=>$row['UserId']));
                array_push($all_res,array('postId'=>$row['Id'],'tag'=>$row['Tag'],'post'=>$row['Post'],
                    'postpix'=>$pix,'uname'=>$row['User_Name'],'pix'=>$row['Pix'],'pixNum'=>$pixNum,
                    'followers'=>$follower));
            }
            return json_encode(array('status' => true, 'res' => $all_res));
        }
        return json_encode(array('status' => false, 'error' => 'Nothing was found'));
    }
    public static function getThread($pid){
        if(ctype_digit($pid)){
            self::check();
            $table = array('posts','thread');
            $field = array('posts.Id','posts.Post','posts.Tag','posts.Pix');
            $on = array('posts.Id' => 'thread.Post_Id');
            $where = array('thread.Thread' => $pid);
            $start = 0;
            $lim = 9;
            $limit=array($start,$lim);        
            $order=array('posts.DateTime'=>'DESC'); 
            $allPost = array();
            if(self::$_con->join($table,$field,$on,$where,$order,$limit) > 0){
                while($row = self::$_con->fetch_join()){
                    if(!empty($row['Pix'])){
                        // $all_pix = array();
                        $needle = '*?';
                        $value=strpos($row['Pix'],$needle);
                        if($value){
                            $pixes = explode('*?',$row['Pix']);
                            $pix = $pixes[0];
                            $pixNum = count($pixes);
                        }else{
                            $pix=$row['Pix'];
                            $pixNum = 1;
                        }
                    }
                    $com = self::$_con->select_where('post_comments',array('Id'),array('Post_id'=>$row['Id']));
                    $likes = self::$_con->select_where('post_likes',array('Id'),array('Post_id'=>$row['Id']));
                    $thread = self::$_con->select_where('thread',array('Id'),array('Thread'=>$row['Id']));
                    array_push($allPost, array('pid'=>$row['Id'],'post'=>$row['Post'],'tag'=>$row["Tag"],
                        'pix'=>$pix,'pixNum'=>$pixNum,'com'=>$com,'likes'=>$likes,'thread'=>$thread));
                }
                return json_encode(array('status' => true, 'res' => $allPost));
            }
        }
    }
    public static function tagThread($id,$tag,$interest,$current,$country,$state,$local){
        if(!$tag){
            return json_encode(array('status' => false, 'error' => 'No match found'));
        }
        if(self::text($tag)){
            $tag = '%'.addslashes($tag).'%';
            if($current == 'country'){
                $table = array('user','posts','country_post');
                $field = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',);
                $on = array('user.Id'=>'posts.UserId','posts.Id'=>'country_post.Post_id');
                $where = array('(posts.Tag'=>$tag,'posts.Interest'=>$interest,'posts.Location'=>'country',
                    'country_post.Country'=>$country,'!posts.UserId'=>$id);
                $order=array('posts.DateTime'=>'DESC'); 
                return self::getTagThread($table,$field,$on,$where,$order,0);
            }elseif ($current == 'state') {
                $table = array('user','posts','state_post');
                $field = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',);
                $on = array('user.Id'=>'posts.UserId','posts.Id'=>'state_post.Post_id');
                $where = array('(posts.Tag'=>$tag,'posts.Interest'=>$interest,'posts.Location'=>'state',
                    'state_post.Country'=>$country,'state_post.State'=>$state,'!posts.UserId'=>$id);
                $order=array('posts.DateTime'=>'DESC'); 
                return self::getTagThread($table,$field,$on,$where,$order,0);
            }elseif ($current == 'local') {
                $table = array('user','posts','local_post');
                $field = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',);
                $on = array('user.Id'=>'posts.UserId','posts.Id'=>'local_post.Post_id');
                $where = array('(posts.Tag'=>$tag,'posts.Interest'=>$interest,'posts.Location'=>'local',
                    'local_post.Country'=>$country,'local_post.State'=>$state,'local_post.Municipal'=>$local,'!posts.UserId'=>$id);
                $order=array('posts.DateTime'=>'DESC'); 
                return self::getTagThread($table,$field,$on,$where,$order,0);
            }
        }
        return json_encode(array('status' => false, 'error' => 'Your tag contained unwanted characters'));
    }
}