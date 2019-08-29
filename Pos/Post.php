<?php
class Pos_Post {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_file;
    protected static $_con;
    protected static $_all;

    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
        self::$_all = array();
    }
    private static function check(){
        if(!self::$_con)
        {
            self::connect();
        }
    }
    public static function extract($id){
        $newStr = substr($id,2);
        $latest = substr($newStr,0,-2);
        return $latest;
    }
    private static function text( $text){
        if(!filter_var($text, FILTER_SANITIZE_STRING) === false){
            return $text;
        }else{
            return false;
        }
    }
    //SELECT DISTINCT Post, count(*) as Count FROM posts GROUP by Post ORDER BY Count DESC
    public static function editPost($id,$post,$tag){
        if($post){
            self::check();
            if($tag){
                $tpost = self::text($post);
                $ttag = self::text($tag);
                if($tpost){
                    if($ttag){
                        if(self::$_con->update_where('posts',array('Post'=>addslashes($tpost),'Tag'=>addslashes($ttag)),array('Id'=>$id))){
                            return (array('state' => true));
                        }  
                        return (array('state' => false, 'error' => 'Failed operation'));                     
                    } 
                    return (array('state' => false, 'error' => 'Unwanted character in tag'));
                }
                return (array('state' => false, 'error' => 'Unwanted character in post'));
            }else{
                $tpost = self::text($post);
                if($tpost){
                    if(self::$_con->update_where('posts',array('Post'=>addslashes($tpost),'Tag'=>$tag),array('Id'=>$id))){
                        return (array('state' => true));
                    }
                }
                return (array('state' => false, 'error' => 'Unwanted character in post'));
            }   
        }
        return (array('state' => false, 'error' => 'Edited post should not be empty'));
        
    }
    public static function upload($file=array(), $id)
    {
        self::$_file = $file;

        if(!isset(self::$_file))
        {
            return  array('state' => false, 'option' => 'File','error' => 
                'Constuctor expects atleast 2 parameters in the order (Field,Cookies_id)');
            // throw new Exception("input type=file name='' Field name was not specified!
            //                     Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }

        if(!is_array(self::$_file))
        {
            return array('state' => false, 'option' => 'File','error' => 'Parameter 1 must be an array');
            // throw new Exception("Parameter 1 must be an array");
        }
        self::check();
        $folder='..'.DIRECTORY_SEPARATOR.'PostImages';
        $validextensions = array("jpeg", "jpg", "png", "gif");
        if(count(self::$_file['name']) > 0 )
        {
            $pix=array();
            $pix_height=array();
            for($i = 0; $i < count(self::$_file['name']); $i++)
            {
                $temporary = explode(".",self::$_file['name'][$i]);
                $file_extension = end($temporary);
                if(((self::$_file["type"][$i]=="image/png") || (self::$_file["type"][$i]=="image/jpeg") ||
                   (self::$_file["type"][$i]=="image/jpg") || (self::$_file["type"][$i]=="image/gif"))
                   && (self::$_file["size"][$i] < 100000000000000000000) && in_array($file_extension, $validextensions))
                {
                    if(self::$_file["error"][$i] > 0)
                    {
                        return  array('state' => false, 'option' => 'File','error' => self::$_file["error"]);
                    }else
                    {
                        list($width,$height,$type,$attr)=getimagesize(self::$_file["tmp_name"][$i]);
                        $pic=$id."_".date('d-m-Y-H-i-s').self::$_file["name"][$i];
                        array_push($pix, $pic);
                        array_push($pix_height, $height);       
                        $source = self::$_file["tmp_name"][$i];
                        $target = $folder. DIRECTORY_SEPARATOR.$pic;
                        if(!move_uploaded_file($source,$target)){
                            return array('state' => false, 'option' => 'File','error' => "File could not be moved");
                        }
                    }
                }
            }
            
            $im_pix = implode('*?',$pix);
            $im_pix_height = implode('*?',$pix_height);
            $field = array('Pix' => $im_pix, 'Pix_height' => $im_pix_height);
            $pointer = array('Id' => $id);
            self::$_con->update_where('posts', $field, $pointer);
            $final=array('state' => true);
            for($i = 0; $i < count($pix); $i++)
            {
                array_push($final, array("pic" => $pix[$i],"height" => $pix_height[$i], "picnt" => 0, "imageLike" => false, "mcom" => 0, "views" => 0));
            }
                return $final;
        }
    }
    public static function insertHashtags($id, $interest, $hashtag=array()){
        if(!$id){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        }
        if(!is_array($hashtag)){
            return (array('state' => false, 'error' => 'No words found'));
        }
        self::check();
        if($hashtag){
            $existed = array();
            // $hash = explode(' ', $hashtag);
            $nhash = array();
            foreach ($hashtag as $value) {
                $needle = '#';
                // echo $value;
                $key=substr_count($value,$needle);
                if($key){
                    $nvalue=str_replace($needle, '', $value);
                    $nvalue = trim($nvalue);
                    if(self::$_con->select_where('interest_hashtag',array('Id'),array('User_Id'=>$id,'Interest'=>$interest,
                        'Hashtag'=>$nvalue)) < 1)
                    {
                        self::$_con->insert('interest_hashtag',array('User_Id','Interest','Hashtag'),array($id,$interest,$nvalue));
                        $nhash[] = $nvalue;
                    }else{
                        $existed[] = '#'.$nvalue;
                    }
                }else{
                    return (array('state'=>false,'error'=>'Inconsistent input format '.$value));
                }
            }
            return (array('state'=>true,'hash'=>$nhash,'existed'=>$existed));
        }
    }
    public static function deleteHashTags($id, $interest, $hashtag){
        if(!$id){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        }
        self::check();
        if(self::$_con->delete('interest_hashtag',array('User_Id'=>$id,'Interest'=>$interest,'Hashtag'=>$hashtag))){
            return (array('state'=>true));
        }else{
            return (array('state'=>false));
        }
    }
    public static function getHashtags($id, $interest){
        if(!$id){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        }
        self::check();
        if(self::$_con->select_where('interest_hashtag',array('Hashtag'),array('User_Id'=>$id,'Interest'=>$interest)) > 0){
            $hash = array();
            while($row = self::$_con->fetch_select_where()){
                $hash[] = $row['Hashtag'];
            }
            return array('state'=>true,'hash'=>$hash);
        }
    }
    public static function insertPost($uid,string $tag=null, string $post=null,string $interest,string $thread,string $share, $pix=false,string $loc, string $country,string $state, string $local){
        if(!$uid){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        }
        self::check();
        $realPost='';
        $realTag = '';
        if($post){
            $textPost = self::text($post);
            if(!$textPost){
                return (array('state' => false, 'error' => 'Your post contained unwanted characters'));
            }else{
                $post = $textPost;
            }
        }
        if($tag){
            $textTag = self::text($tag);
            if(!$textTag){
                return (array('state' => false, 'error' => 'Your tag contained unwanted characters'));
            }else{
                $tag = $textTag;
            }
        }
        $field = array('UserId','Tag','Post','Interest','Status','Location','DateTime');
        $value = array($uid,addslashes($tag),addslashes($post),$interest,$share,$loc,date('Y-m-d h:i:s'));
        $pid = self::$_con->insert('posts',$field,$value);
        if($realPost){
            $needle = '#';
            $value=strpos($realPost,$needle);
            if($value){
                preg_match_all('/#(\w+)/', $realPost, $matches);
                foreach ($matches[1] as $match) {
                    self::$_con->insert('hashtags',array('Post_id','Hashtags','Date_Time'),array($pid,$match,date('Y-m-d h:i:s')));
                }
            }
        }
        if($thread && ctype_digit($thread)){
            self::$_con->insert('thread',array('Post_Id','Thread'),array($pid,$thread));
        }
        if($loc == 'country'){
            if(!self::$_con->insert('country_post',array('Post_id','Country'),array($pid,$country))){
                return (array('state' => false, 'error' => 'Server error.Transaction incomplete.c'));
            }
        }elseif($loc == 'state'){
            if(!self::$_con->insert('state_post',array('Post_id','Country','State'),array($pid,$country,$state))){
                return (array('state' => false, 'error' => 'Server error.Transaction incomplete.s'));
            }
        }elseif($loc == 'local'){
            if(!self::$_con->insert('local_post',array('Post_id','Country','State','Municipal'),array($pid,$country,$state,$local))){
                return (array('state' => false, 'error' => 'Server error.Transaction incomplete.l'));
            }
        }
        $pix = self::$_con->select_where('user',array('User_Name','Pix'),array('Id'=>$uid));
        $row = self::$_con->fetch_select_where();
        if($pix){
            $photo = self::upload($pix,$pid);
            if($photo['state']){
                array_shift($photo);
                // $collection = $photo;              
                return (array('state' => true,'pid'=>$pid,'tag'=>$tag,'post'=>$post,'follow'=>false,'uid'=>$uid,'followable'=>false,'likes'=>0,'like'=>false,'comment'=>0,'thread'=>0,'share'=>$share,'userpix'=>$row['Pix'],
                    'uname'=>$row['User_Name'],'upix'=>$row['Pix'],'postpix'=>$photo));
            }
        }else{
            return (array('state' => true,'pid'=>$pid,'tag'=>$tag,'post'=>$post,'follow'=>false,'uid'=>$uid,'followable'=>false,'likes'=>0,'like'=>false,'comment'=>0,'thread'=>0,'share'=>$share,'userpix'=>$row['Pix'],
                    'uname'=>$row['User_Name'],'upix'=>$row['Pix']));
        }        
        // return (array('state' => true,'tag'=>$tag,'post'=>$post,'interest'=>$interest,'loc'=>$loc,'country'=>$country,
        //             'state'=>$state,'local'=>$local));
    }
    private static function getdetails($table, $field, $on, $where, $order, $limit, $uid, $interest){
        self::check();
        $all = array();
        if(self::$_con->join($table, $field, $on ,$where, $order, $limit)){
            self::$_con->select_where('user',array('Pix'),array('Id' => $uid));
            $userpix = self::$_con->fetch_select_where();            
            while($row = self::$_con->fetch_join()) {
                $like_f= array('Id');
                $like_p = array('Post_id' => $row['Id']);
                $likes = self::$_con->select_where('post_likes', $like_f, $like_p);
                $com_f= array('Id');
                $thread = self::$_con->select_where('thread',array('Id'),array('Thread' => $row['Id']));
                $followable = $uid == $row['uid'] ? false : true ;
                if($followable){
                    $follow = self::$_con->select_where('followers',array('Id'),
                        array('Followee'=>$row['uid'],'Follower'=>$uid)) > 0 ? true : false ;
                }else{
                    $follow = false;
                }
                $com_p = array('Post_id' => $row['Id']);
                $coms = self::$_con->select_where('post_comments', $com_f, $com_p);
                if(self::$_con->select_where('post_likes',$like_f,array('Post_id'=>$row['Id'],'User_id'=>$uid)) > 0){
                    $like = true;
                }else{
                    $like = false;
                }
                $saved = self::$_con->select_where('saved_post',array('Id'),array('Post_Id'=>$row['Id'],'User_Id'=>$uid)) > 0 ? true : false ;
                $report = self::$_con->select_where('reported_post',array('Id'),array('Post_Id'=>$row['Id'],'User_Id'=>$uid)) > 0 ?true : false ;                   
                // $num = self::$_con->select_where('post_comments',array('Id'),array('Id'=>$pid));
                $turnoff = self::$_con->select_where('interest_turnoff',array('Id'),array('User_Id'=>$uid,'Turn_Off'=>$row['uid'],
                    'Interest'=>$interest)) > 0 ? true : false ;
                if(!empty($row['Pix'])){
                    $all_pix = array();
                    $needle = '*?';
                    $value=strpos($row['Pix'],$needle);
                    if($value)
                    {
                        $pix = explode('*?',$row['Pix']);
                    }else{
                        $pix=array($row['Pix']);
                    }
                    if(count($pix) > 1)
                    {
                        for ($i = 0; $i < count($pix) ; $i++) {
                            $m_like = array('Id');
                            $m_like_where = array('Image' => $pix[$i]);
                            $m_likes_where = array('Image' => $pix[$i],'User_id' => $uid);
                            $m_likes = self::$_con->select_where('multi_image_like',$m_like,$m_like_where);
                            $m_true = self::$_con->select_where('multi_image_like',$m_like,$m_likes_where); 
                            $final = $m_true > 0 ? true : false ;
                            // $m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
                            $m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where);
                            array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'views'=>$m_view));
                        }
                    }else{
                        array_push($all_pix, array('pic' => $pix[0],'height' => $row['Pix_height']));
                    }
                    $res = array('uid'=>$row['uid'],'fname'=>$row['Fullname'],'uname'=>$row['User_Name'],'upix'=>$row['uPix'],
                        'pid'=>$row['Id'],'tag'=>$row['Tag'],
                        'post'=>$row['Post'],'postpix'=>$all_pix,'userpix'=>$userpix['Pix'],'comment'=>$coms,'likes'=>$likes,
                        'like' =>$like,'followable'=>$followable,'follow'=>$follow,'thread'=>$thread,'share'=>$row['Status'],
                        'saved'=>$saved,'report'=>$report,'date'=>$row['DateTime'],'editerr'=>false,'errText'=>false,
                        'type'=>'post','turnoff'=>$turnoff);
                     array_push(self::$_all, $res); 
                }
                
                          
            } 
            // self::$_all = $all;
            // return array('state'=>true,'category'=>'post','all'=>self::$_all);
        }
        // return array('state'=>false);
    }
    public static function getCommentdetails($table, $field, $on, $where, $order, $limit, $uid, $interest){
        self::check();
        if(self::$_con->join($table, $field, $on, $where, $order, $limit) > 0){
            while($row = self::$_con->fetch_join()){
                $tab = array('user','posts');
                $fld = array('user.Id','user.User_Name','user.Pix as opix','posts.Tag','posts.Status','posts.Post','posts.Pix',
                    'posts.Pix_height','posts.DateTime');
                $lon = array('user.Id'=>'posts.UserId');
                $whr = array('posts.Id'=>$row['Post_id']);
                self::$_con->join_ii($tab,$fld,$lon,$whr,0,0);
                $rows = self::$_con->fetch_join_ii();
                $like_f= array('Id');
                $like_p = array('Post_id' => $row['Post_id']);
                $likes = self::$_con->select_where('post_likes', $like_f, $like_p);
                $com_f= array('Id');
                $thread = self::$_con->select_where('thread',array('Id'),array('Thread' => $row['Post_id']));
                $followable = $uid == $rows['Id'] ? false : true ;
                if($followable){
                    $follow = self::$_con->select_where('followers',array('Id'),
                        array('Followee'=>$rows['Id'],'Follower'=>$uid)) > 0 ? true : false ;
                }else{
                    $follow = false;
                }
                $com_p = array('Post_id' => $row['Post_id']);
                $coms = self::$_con->select_where('post_comments', $com_f, $com_p);
                if(self::$_con->select_where('post_likes',$like_f,array('Post_id'=>$row['Post_id'],'User_id'=>$uid)) > 0){
                    $like = true;
                }else{
                    $like = false;
                }
                $saved = self::$_con->select_where('saved_post',array('Id'),array('Post_Id'=>$row['Post_id'],'User_Id'=>$uid)) > 0 ? true : false ;
                $report = self::$_con->select_where('reported_post',array('Id'),array('Post_Id'=>$row['Post_id'],'User_Id'=>$uid)) > 0 ?true : false ;
                $turnoff = self::$_con->select_where('interest_turnoff',array('Id'),array('User_Id'=>$uid,'Turn_Off'=>$row['uid'],
                    'Interest'=>$interest)) > 0 ? true : false ;
                if(!empty($rows['Pix'])){
                    $all_pix = array();
                    $needle = '*?';
                    $value=strpos($rows['Pix'],$needle);
                    if($value)
                    {
                        $pix = explode('*?',$rows['Pix']);
                    }else{
                        $pix=array($rows['Pix']);
                    }
                    if(count($pix) > 1)
                    {
                        for ($i = 0; $i < count($pix) ; $i++) {
                            $m_like = array('Id');
                            $m_like_where = array('Image' => $pix[$i]);
                            $m_likes_where = array('Image' => $pix[$i],'User_id' => $uid);
                            $m_likes = self::$_con->select_where('multi_image_like',$m_like,$m_like_where);
                            $m_true = self::$_con->select_where('multi_image_like',$m_like,$m_likes_where); 
                            $final = $m_true > 0 ? true : false ;
                            // $m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
                            $m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where);
                            array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'views'=>$m_view));
                        }
                    }else{
                        array_push($all_pix, array('pic' => $pix[0],'height' => $rows['Pix_height']));
                    }
                    // if(self::$_con->select_where('viewed_post',array('Id'),array('Post_Id'=>$row['Post_Id'],'User_Id'=>$row['uid'])) < 1){
                    //     $type = 'share';
                    // }else{
                    //     $type = 'nshare';
                    // }
                    if($row['uid'] != $rows['Id']){
                        $res = array('uid'=>$row['uid'],'uname'=>$row['User_Name'],'upix'=>$row['uPix'],'pid'=>$row['Post_id'],'tag'=>
                            $rows['Tag'],
                            'post'=>$rows['Post'],'postpix'=>$all_pix,'ownerId'=>$rows['Id'],'comment'=>$coms,'likes'=>$likes,
                            'like' =>$like,'ownerName'=>$rows['User_Name'],'ownerPix'=>$rows['opix'],'thread'=>$thread,'share'=>$rows['Status'],'pcid'=>$row['pcid'],'followable'=>$followable,'follow'=>$follow,
                            'saved'=>$saved,'report'=>$report,'postdate'=>$rows['DateTime'],'editerr'=>false,'errText'=>false,
                            'date'=>$row['DateTime'],'type'=>'comment');
                         array_push(self::$_all, $res); 
                    }
                }  
            }
        }
    }
    public static function getlikedetails($table, $field, $on, $where, $order, $limit, $uid, $interest){
        self::check();
        if(self::$_con->join($table, $field, $on, $where, $order, $limit) > 0){
            while($row = self::$_con->fetch_join()){
                $tab = array('user','posts');
                $fld = array('user.Id','user.User_Name','user.Pix as opix','posts.Tag','posts.Status','posts.Post','posts.Pix',
                    'posts.Pix_height','posts.DateTime');
                $lon = array('user.Id'=>'posts.UserId');
                $whr = array('posts.Id'=>$row['Post_Id']);
                self::$_con->join_ii($tab,$fld,$lon,$whr,0,0);
                $rows = self::$_con->fetch_join_ii();
                $like_f= array('Id');
                $like_p = array('Post_id' => $row['Post_Id']);
                $likes = self::$_con->select_where('post_likes', $like_f, $like_p);
                $com_f= array('Id');
                $thread = self::$_con->select_where('thread',array('Id'),array('Thread' => $row['Post_Id']));
                $followable = $uid == $rows['Id'] ? false : true ;
                if($followable){
                    $follow = self::$_con->select_where('followers',array('Id'),
                        array('Followee'=>$rows['Id'],'Follower'=>$uid)) > 0 ? true : false ;
                }else{
                    $follow = false;
                }
                $com_p = array('Post_id' => $row['Post_Id']);
                $coms = self::$_con->select_where('post_comments', $com_f, $com_p);
                if(self::$_con->select_where('post_likes',$like_f,array('Post_id'=>$row['Post_Id'],'User_id'=>$uid)) > 0){
                    $like = true;
                }else{
                    $like = false;
                }
                $saved = self::$_con->select_where('saved_post',array('Id'),array('Post_Id'=>$row['Post_Id'],'User_Id'=>$uid)) > 0 ? true : false ;
                $report = self::$_con->select_where('reported_post',array('Id'),array('Post_Id'=>$row['Post_Id'],'User_Id'=>$uid)) > 0 ?true : false ;                   
                // $num = self::$_con->select_where('post_comments',array('Id'),array('Id'=>$pid));
                $turnoff = self::$_con->select_where('interest_turnoff',array('Id'),array('User_Id'=>$uid,'Turn_Off'=>$row['uid'],
                    'Interest'=>$interest)) > 0 ? true : false ;
                if(!empty($rows['Pix'])){
                    $all_pix = array();
                    $needle = '*?';
                    $value=strpos($rows['Pix'],$needle);
                    if($value)
                    {
                        $pix = explode('*?',$rows['Pix']);
                    }else{
                        $pix=array($rows['Pix']);
                    }
                    if(count($pix) > 1)
                    {
                        for ($i = 0; $i < count($pix) ; $i++) {
                            $m_like = array('Id');
                            $m_like_where = array('Image' => $pix[$i]);
                            $m_likes_where = array('Image' => $pix[$i],'User_id' => $uid);
                            $m_likes = self::$_con->select_where('multi_image_like',$m_like,$m_like_where);
                            $m_true = self::$_con->select_where('multi_image_like',$m_like,$m_likes_where); 
                            $final = $m_true > 0 ? true : false ;
                            // $m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
                            $m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where);
                            array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'views'=>$m_view));
                        }
                    }else{
                        array_push($all_pix, array('pic' => $pix[0],'height' => $rows['Pix_height']));
                    }
                    // if(self::$_con->select_where('viewed_post',array('Id'),array('Post_Id'=>$row['Post_Id'],'User_Id'=>$row['uid'])) < 1){
                    //     $type = 'share';
                    // }else{
                    //     $type = 'nshare';
                    // }
                    if($row['uid'] != $rows['Id']){
                        $res = array('uid'=>$row['uid'],'uname'=>$row['User_Name'],'upix'=>$row['uPix'],'pid'=>$row['Post_Id'],'tag'=>
                            $rows['Tag'],
                            'post'=>$rows['Post'],'postpix'=>$all_pix,'ownerId'=>$rows['Id'],'comment'=>$coms,'likes'=>$likes,
                            'like' =>$like,'ownerName'=>$rows['User_Name'],'ownerPix'=>$rows['opix'],'thread'=>$thread,'share'=>$rows['Status'],'plid'=>$row['plid'],'followable'=>$followable,'follow'=>$follow,
                            'saved'=>$saved,'report'=>$report,'postdate'=>$rows['DateTime'],'editerr'=>false,'errText'=>false,
                            'date'=>$row['DateTime'],'type'=>'like');
                         array_push(self::$_all, $res); 
                    }
                } 

            }
        }
        
    } 
    public static function getCountryPost($id,$interest,$country){
        if(!$id){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        }
        $table = array('user','posts','country_post','followers');
        $field = array('user.Id AS uid','user.Fullname','user.User_Name','user.Pix as uPix','posts.Id','posts.Tag','posts.Status','posts.Post',
            'posts.Pix','posts.Pix_height','posts.DateTime');
        $on = array('user.Id' => 'posts.UserId','posts.Id' => 'country_post.Post_id','followers.Followee'=>'user.Id');
        $where = array('posts.Interest' => $interest, 'country_post.Country' => $country,'followers.Follower'=>$id);
        $start = 0;
        $lim = 10;
        $order=array('posts.DateTime'=>'DESC'); 
        $limit=array($start,$lim);  
        $sTable = array('user','followers','share_location_interest','country_post');
        $sField = array('user.Id AS uid','user.User_Name','user.Pix as uPix','share_location_interest.Post_Id',
            'share_location_interest.Comment','share_location_interest.Date_Time');
        $sOn = array('user.Id' => 'followers.Followee','share_location_interest.User_Id'=>'followers.Followee',
            'share_location_interest.Post_Id'=>'country_post.Post_id');
        $sWhere = array('followers.Follower'=>$id,'country_post.Country'=>$country);
        self::getdetails($table, $field, $on, $where, $order, $limit, $id, $interest);
        self::getSharedetails($sTable, $sField, $sOn, $sWhere, 0, $limit, $id);
        if(count(self::$_all) > 0){
            $pid = array_column(self::$_all, 'pid');
            $view = array_column(self::$_all, 'view');
            array_multisort($pid,SORT_DESC,SORT_NUMERIC,$view,SORT_DESC,SORT_NUMERIC,self::$_all);
            return (array('state'=>true,'category'=>'post','all'=>self::$_all));
        }else{
            return (array('state'=>false));
        }

    }
    public static function getSharedetails($table, $field, $on, $where, $order, $limit, $uid){
        self::check();
        $all = array();
        if(self::$_con->join($table, $field, $on ,$where, $order, $limit)){
            while($row = self::$_con->fetch_join()) {
                self::$_con->select_where('posts',array('UserId','Tag','Status','Post','Pix',
                    'Pix_height','DateTime'),array('Id'=>$row['Post_Id']));
                $rows = self::$_con->fetch_select_where();
                // if($rows['UserId'] != $uid){}
                $like_f= array('Id');
                $like_p = array('Post_id' => $row['Post_Id']);
                $likes = self::$_con->select_where('post_likes', $like_f, $like_p);
                $com_f= array('Id');
                $thread = self::$_con->select_where('thread',array('Id'),array('Thread' => $row['Post_Id']));
                $followable = $uid == $rows['UserId'] ? false : true ;
                if($followable){
                    $follow = self::$_con->select_where('followers',array('Id'),
                        array('Followee'=>$rows['UserId'],'Follower'=>$uid)) > 0 ? true : false ;
                }else{
                    $follow = false;
                }
                $com_p = array('Post_id' => $row['Post_Id']);
                $coms = self::$_con->select_where('post_comments', $com_f, $com_p);
                self::$_con->select_where('user',array('User_Name','Pix'),array('Id' => $rows['UserId']));
                $ans = self::$_con->fetch_select_where();
                if(self::$_con->select_where('post_likes',$like_f,array('Post_id'=>$row['Post_Id'],'User_id'=>$uid)) > 0){
                    $like = true;
                }else{
                    $like = false;
                }
                if(!empty($rows['Pix'])){
                    $all_pix = array();
                    $needle = '*?';
                    $value=strpos($rows['Pix'],$needle);
                    if($value)
                    {
                        $pix = explode('*?',$rows['Pix']);
                    }else{
                        $pix=array($rows['Pix']);
                    }
                    if(count($pix) > 1)
                    {
                        for ($i = 0; $i < count($pix) ; $i++) {
                            $m_like = array('Id');
                            $m_like_where = array('Image' => $pix[$i]);
                            $m_likes_where = array('Image' => $pix[$i],'User_id' => $uid);
                            $m_likes = self::$_con->select_where('multi_image_like',$m_like,$m_like_where);
                            $m_true = self::$_con->select_where('multi_image_like',$m_like,$m_likes_where); 
                            $final = $m_true > 0 ? true : false ;
                            // $m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
                            $m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where);
                            array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'views'=>$m_view));
                        }
                    }else{
                        array_push($all_pix, array('pic' => $pix[0],'height' => $rows['Pix_height']));
                    }
                    if(self::$_con->select_where('viewed_post',array('Id'),array('Post_Id'=>$row['Post_Id'],'User_Id'=>$row['uid'])) < 1){
                        $type = 'share';
                    }else{
                        $type = 'nshare';
                    }
                    $res = array('uid'=>$row['uid'],'uname'=>$row['User_Name'],'upix'=>$row['uPix'],'pid'=>$row['Post_Id'],'tag'=>
                        $rows['Tag'],
                        'post'=>$rows['Post'],'scomment'=>$row['Comment'],'postpix'=>$all_pix,'ownerId'=>$rows['UserId'],'comment'=>$coms,'likes'=>$likes,'followable'=>$followable,'follow'=>$follow,
                        'like' =>$like,'ownerName'=>$ans['User_Name'],'ownerPix'=>$ans['Pix'],'follow'=>'null','thread'=>$thread,'share'=>$rows['Status'],'date'=>$row['Date_Time'],
                        'saved'=>'null','report'=>'null','postdate'=>$rows['DateTime'],'editerr'=>false,'errText'=>false,
                        'type'=>$type);
                     array_push(self::$_all, $res); 
                }                
            }
        }
    }
    public static function getStatePost($id,$interest,$country,$state){
        if(!$id){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        }
        $table = array('user','posts','state_post','followers');
        $field = array('user.Id AS uid','user.Fullname','user.User_Name','user.Pix as uPix','posts.Id','posts.Tag','posts.Status',
            'posts.Post', 'posts.Pix','posts.Pix_height','posts.DateTime');
        $on = array('user.Id' => 'posts.UserId','posts.Id' => 'state_post.Post_id','followers.Followee'=>'user.Id');
        $where = array('posts.Interest' => $interest, 'state_post.Country' => $country, 'state_post.State' => $state,
            'followers.Follower'=>$id);
        $start = 0;
        $lim = 10;
        $order=array('posts.DateTime'=>'DESC'); 
        $limit=array($start,$lim); 
        $sTable = array('user','followers','share_location_interest','state_post');
        $sField = array('user.Id AS uid','user.User_Name','user.Pix as uPix','share_location_interest.Post_Id',
            'share_location_interest.Comment','share_location_interest.Date_Time');
        $sOn = array('user.Id' => 'followers.Followee','share_location_interest.User_Id'=>'followers.Followee',
            'share_location_interest.Post_Id'=>'state_post.Post_id');
        $sWhere = array('followers.Follower'=>$id,'state_post.Country'=>$country,'state_post.State'=>$state);      
        self::getdetails($table, $field, $on, $where, $order, $limit, $id, $interest);
        self::getSharedetails($sTable, $sField, $sOn, $sWhere, 0, $limit, $id);
        if(count(self::$_all) > 0){
            $pid = array_column(self::$_all, 'pid');
            $view = array_column(self::$_all, 'view');
            array_multisort($pid,SORT_DESC,SORT_NUMERIC,$view,SORT_DESC,SORT_NUMERIC,self::$_all);
            return (array('state'=>true,'category'=>'post','all'=>self::$_all));
        }else{
            return (array('state'=>false));
        }
    }
    public static function getLocalPost($id,$interest,$country,$state,$municipal){
        if(!$id){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        }
        $table = array('user','posts','local_post','followers');
        $field = array('user.Id AS uid','user.Fullname','user.User_Name','user.Pix as uPix','posts.Id','posts.Tag','posts.Status','posts.Post',
            'posts.Pix','posts.Pix_height','posts.DateTime');
        $on = array('user.Id' => 'posts.UserId','posts.Id' => 'local_post.Post_id','followers.Followee'=>'user.Id');
        $where = array('posts.Interest' => $interest, 'local_post.Country' => $country, 'local_post.State' => $state, 
            'local_post.Municipal' => $municipal,'followers.Follower'=>$id);
        $start = 0;
        $lim = 10;
        $order=array('posts.DateTime'=>'DESC'); 
        $limit=array($start,$lim);   
        $sTable = array('user','followers','share_location_interest','local_post');
        $sField = array('user.Id AS uid','user.User_Name','user.Pix as uPix','share_location_interest.Post_Id',
            'share_location_interest.Comment','share_location_interest.Date_Time');
        $sOn = array('user.Id' => 'followers.Followee','share_location_interest.User_Id'=>'followers.Followee',
            'share_location_interest.Post_Id'=>'local_post.Post_id');
        $sWhere = array('followers.Follower'=>$id,'local_post.Country'=>$country,'local_post.State'=>$state,
            'local_post.Municipal'=>$municipal,'share_location_interest.Interest'=>$interest); 
        $ltab = array('user','followers','post_likes','posts','local_post');  
        $lfid = array('user.Id AS uid','user.User_Name','user.Pix as uPix','post_likes.Id as plid','post_likes.Post_Id','post_likes.DateTime');
        $lon = array('user.Id'=>'followers.Followee','post_likes.User_Id'=>'followers.Followee',
            'posts.Id'=>'post_likes.Post_Id','local_post.Post_id'=>'posts.Id');
        $lwhr = array('followers.Follower'=>$id,'posts.Interest'=>$interest,'local_post.Country'=>$country,'local_post.State'=>$state,'local_post.Municipal'=>$municipal);
        $ctab =  array('user','followers','post_comments','posts','local_post');
        $cfid = array('user.Id AS uid','user.User_Name','user.Pix as uPix','post_comments.Id as pcid','post_comments.Post_id',
            'post_comments.Comment','post_comments.DateTime');
        $con = array('user.Id'=>'followers.Followee','post_comments.User_id'=>'followers.Followee',
            'posts.Id'=>'post_comments.Post_id','local_post.Post_id'=>'posts.Id'); 
        $cwhr = array('followers.Follower'=>$id,'posts.Interest'=>$interest,'local_post.Country'=>$country,'local_post.State'=>
            $state,'local_post.Municipal'=>$municipal);
        self::getdetails($table, $field, $on, $where, $order, $limit, $id, $interest);
        self::getSharedetails($sTable, $sField, $sOn, $sWhere, 0, $limit, $id);
        self::getlikedetails($ltab,$lfid,$lon,$lwhr,0,$limit,$id,$interest);
        self::getCommentdetails($ctab,$cfid,$con,$cwhr,0,$limit,$id,$interest);
        if(count(self::$_all) > 0){
            $pid = array_column(self::$_all, 'date');
            $likes = array_column(self::$_all, 'pid');
            // echo ($pid);
            array_multisort($pid,SORT_DESC,SORT_REGULAR,$likes,SORT_DESC,SORT_NUMERIC,self::$_all);
            return (array('state'=>true,'category'=>'post','all'=>self::$_all));
        }else{
            return (array('state'=>false));
        }
    }
    public static function savePost($id,$pid){
        if(!$id || !$pid || !ctype_digit($pid)){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        } 
        self::check();
        if(self::$_con->select_where('saved_post',array('Id'),array('User_Id'=>$id,'Post_Id'=>$pid)) > 0){
            return (array('state' => true, 'res' => 'You already saved post'));
        }else{
            if(self::$_con->insert('saved_post',array('Post_Id','User_Id','Date_Time'),array($pid,$id,date('Y-m-d h:i:s')))){
                return (array('state' => true, 'res' => 'Saved post!'));
            }else{
                return (array('state' => false, 'error' => 'Server error'));
            }
        }
    }
    public static function viewedPost($id,$pid){
        if(!$id || !$pid || !ctype_digit($pid)){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        } 
        self::check();
        if(self::$_con->select_where('viewed_post',array('Id'),array('User_Id'=>$id,'Post_Id'=>$pid)) > 0){
            return (array('state' => true, 'res' => 'You already viewed post'));
        }else{
            if(self::$_con->insert('viewed_post',array('Post_Id','User_Id','Date_Time'),array($pid,$id,date('Y-m-d h:i:s')))){
                return (array('state' => true, 'res' => 'Viewed post!'));
            }else{
                return (array('state' => false, 'error' => 'Server error'));
            }
        }
    } 
    public static function reportedPost($id,$pid,$details,$suggestion){
        if(!$id || !$pid || !ctype_digit($pid) || !$details){
            return (array('state' => false, 'error' => 'Could not resolve request'));
        } 
        self::check();        
        if(self::$_con->select_where('reported_post',array('Id'),array('User_Id'=>$id,'Post_Id'=>$pid)) > 0){
            return (array('state' => true, 'res' => 'You already reported post'));
        }else{
            if($suggestion){
                $suggest = self::text($suggestion);
                if($suggest){
                    if(self::$_con->insert('reported_post',array('Post_Id','User_Id','Details','Suggestion','Date_Time'),array($pid,$id,$details,addslashes($suggest),date('Y-m-d h:i:s')))){
                        return (array('state' => true, 'res' => 'Post reported!'));
                    }else{
                        return (array('state' => false, 'error' => 'Server error'));
                    }
                }else{
                    return (array('state' => false, 'error' => 'Unwanted character'));
                }
            }else{
                if(self::$_con->insert('reported_post',array('Post_Id','User_Id','Details','Date_Time'),array($pid,$id,$details,date('Y-m-d h:i:s')))){
                        return (array('state' => true, 'res' => 'Post reported!'));
                    }else{
                        return (array('state' => false, 'error' => 'Server error'));
                    }
            }
            
        }
    } 

}
?>