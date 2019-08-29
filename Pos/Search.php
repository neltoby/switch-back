<?php
class Pos_Search {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    protected static $_con;
    private static $_all;
    private static $_num;

    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
        self::$_all = [];
    }
    private static function check(){
        if(!self::$_con)
        {
            self::connect();
        }
    }
    private static function search_join($table,$field,$match,$search,$on,$where,$order,$limit,$id){
        if(self::$_con->join_search($table,$field,$match,$search,$on,$where,$order,$limit) > 0){
            while($row = self::$_con->fetch_join_search()){
                $like_f= array('Id');
                $like_p = array('Post_id' => $row['Id']);
                $likes = self::$_con->select_where('post_likes', $like_f, $like_p);
                $com_f= array('Id');
                $com_p = array('Post_id' => $row['Id']);
                $coms = self::$_con->select_where('post_comments', $com_f, $com_p);
                $thread = self::$_con->select_where('thread',array('Id'),array('Thread' => $row['Id']));
                if(self::$_con->select_where('post_likes',$like_f,array('Post_id'=>$row['Id'],'User_id'=>$id)) > 0){
                    $like = true;
                }else{
                    $like = false;
                }
                $view = self::$_con->select_where('viewed_post',array('Id'),array('Post_Id' => $row['Id']));
                $saved = self::$_con->select_where('saved_post',array('Id'),array('Post_Id'=>$row['Id'],'User_Id'=>$id)) > 0 ? true : false ;
                $followable = $id == $row['UserId'] ? false : true ;
                if($followable){
                    $follow = self::$_con->select_where('followers',array('Id'),
                        array('Followee'=>$row['UserId'],'Follower'=>$id)) > 0 ? true : false ;
                }else{
                    $follow = false;
                }
                if(!empty($row['Post_Pix'])){
                    $all_pix = array();
                    $needle = '*?';
                    $value=strpos($row['Post_Pix'],$needle);
                    if($value)
                    {
                        $pix = explode('*?',$row['Post_Pix']);
                    }else{
                        $pix=array($row['Post_Pix']);
                    }
                    if(count($pix) > 1)
                    {
                        for ($i = 0; $i < count($pix) ; $i++) {
                            $m_like = array('Id');
                            $m_like_where = array('Image' => $pix[$i]);
                            $m_likes_where = array('Image' => $pix[$i],'User_id' => $id);
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
                    $res = array('uid'=>$row['UserId'],'uname'=>$row['User_Name'],'upix'=>$row['Pix'],'pid'=>$row['Id'],'tag'=>    
                        $row['Tag'],'post'=>$row['Post'],'postpix'=>$all_pix,'comment'=>$coms,
                        'likes'=>$likes,'like' =>$like,'followable'=>$followable,'follow'=>$follow,'thread'=>$thread,
                        'saved'=>$saved,'date'=>$row['DateTime'],'view'=>$view);
                     array_push(self::$_all, $res); 
                }
            }
            // return array('state'=>true,'all'=>$all);
        }
    }
    private static function search_hashtag($table,$field,$on,$where,$order,$limit,$id){
        if(self::$_con->join($table,$field,$on,$where,$order,$limit) > 0){
            while($row = self::$_con->fetch_join()){
                $like_f= array('Id');
                $like_p = array('Post_id' => $row['Id']);
                $likes = self::$_con->select_where('post_likes', $like_f, $like_p);
                $com_f= array('Id');
                $com_p = array('Post_id' => $row['Id']);
                $coms = self::$_con->select_where('post_comments', $com_f, $com_p);
                $thread = self::$_con->select_where('thread',array('Id'),array('Thread' => $row['Id']));
                if(self::$_con->select_where('post_likes',$like_f,array('Post_id'=>$row['Id'],'User_id'=>$id)) > 0){
                    $like = true;
                }else{
                    $like = false;
                }
                $saved = self::$_con->select_where('saved_post',array('Id'),array('Post_Id'=>$row['Id'],'User_Id'=>$id)) > 0 ? true : false ;
                $followable = $id == $row['UserId'] ? false : true ;
                if($followable){
                    $follow = self::$_con->select_where('followers',array('Id'),
                        array('Followee'=>$row['UserId'],'Follower'=>$id)) > 0 ? true : false ;
                }else{
                    $follow = false;
                }
                if(!empty($row['Post_Pix'])){
                    $all_pix = array();
                    $needle = '*?';
                    $value=strpos($row['Post_Pix'],$needle);
                    if($value)
                    {
                        $pix = explode('*?',$row['Post_Pix']);
                    }else{
                        $pix=array($row['Post_Pix']);
                    }
                    if(count($pix) > 1)
                    {
                        for ($i = 0; $i < count($pix) ; $i++) {
                            $m_like = array('Id');
                            $m_like_where = array('Image' => $pix[$i]);
                            $m_likes_where = array('Image' => $pix[$i],'User_id' => $id);
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
                    $res = array('uid'=>$row['UserId'],'uname'=>$row['User_Name'],'upix'=>$row['Pix'],'pid'=>$row['Id'],'tag'=>    
                        $row['Tag'],'post'=>$row['Post'],'postpix'=>$all_pix,'comment'=>$coms,
                        'likes'=>$likes,'like' =>$like,'followable'=>$followable,'follow'=>$follow,'thread'=>$thread,
                        'saved'=>$saved,'date'=>$row['DateTime']) ;
                     array_push(self::$_all, $res); 
                }
            }
        }
    }
    private static function search_pple($table,$field,$match,$search,$on,$where,$order,$limit,$id,$country,$state=false,$local=false,$interest){
        if(self::$_con->join_search($table,$field,$match,$search,$on,$where,$order,$limit,$id) > 0){
            while ($row = self::$_con->fetch_join_search()) {
                if($local){
                    $tab = array('posts','local_post');
                    $fid = array('posts.Id');
                    $onb = array('posts.Id'=>'local_post.Post_id');
                    $whr = array('posts.Interest'=>$interest,'posts.UserId'=>$row['Id'],'local_post.Country'=>$country,'local_post.State'=>
                        $state,'local_post.Municipal'=>$local);
                    $post = self::$_con->join($tab,$fid,$onb,$whr,0,0);
                    $current = 'local';
                    self::$_num = $post;
                }elseif($state && !$local){
                    $tab = array('posts','state_post');
                    $fid = array('posts.Id');
                    $onb = array('posts.Id'=>'state_post.Post_id');
                    $whr = array('posts.Interest'=>$interest,'posts.UserId'=>$row['Id'],'state_post.Country'=>$country,'state_post.State'=>
                        $state);
                    $post_i = self::$_con->join($tab,$fid,$onb,$whr,0,0);
                    $tab = array('posts','local_post');
                    $fid = array('posts.Id');
                    $onb = array('posts.Id'=>'local_post.Post_id');
                    $whr = array('posts.Interest'=>$interest,'posts.UserId'=>$row['Id'],'local_post.Country'=>$country,'local_post.State'=>$state);
                    $post = self::$_con->join($tab,$fid,$onb,$whr,0,0);
                    $current = 'state';
                    self::$_num = $post_i + $post;
                }elseif($country && !$state && !$local){
                    $tab = array('posts','country_post');
                    $fid = array('posts.Id');
                    $onb = array('posts.Id'=>'country_post.Post_id');
                    $whr = array('posts.Interest'=>$interest,'posts.UserId'=>$row['Id'],'country_post.Country'=>$country);
                    $post = self::$_con->join($tab,$fid,$onb,$whr,0,0);
                    $tab = array('posts','state_post');
                    $fid = array('posts.Id');
                    $onb = array('posts.Id'=>'state_post.Post_id');
                    $whr = array('posts.Interest'=>$interest,'posts.UserId'=>$row['Id'],'state_post.Country'=>$country);
                    $post_i = self::$_con->join($tab,$fid,$onb,$whr,0,0);
                    $tab = array('posts','local_post');
                    $fid = array('posts.Id');
                    $onb = array('posts.Id'=>'local_post.Post_id');
                    $whr = array('posts.Interest'=>$interest,'posts.UserId'=>$row['Id'],'local_post.Country'=>$country);
                    $post_ii = self::$_con->join($tab,$fid,$onb,$whr,0,0);
                    self::$_num = $post + $post_i + $post_ii ;
                    $current = 'country';
                }
                $p_userint_array = array();
                $userint_array = array();
                if($p_userInterest = self::$_con->select_where('interest',array('Interest'),array('User_id'=>$row['Id'])) > 0){
                    while($int = self::$_con->fetch_select_where()){
                        // $value = array_values($int['Interest']); 
                        array_push($p_userint_array,$int['Interest']);            
                    }
                }
                if($userInterest = self::$_con->select_where('interest',array('Interest'),array('User_id'=>$id)) > 0){
                    while($int = self::$_con->fetch_select_where()){
                        // $value = array_values($int['Interest']); 
                        array_push($userint_array,$int['Interest']);                                              
                    }
                }
                $similar = array_values(array_intersect($p_userint_array,$userint_array));
                // $shared = array_values($similar);
                $followers = self::$_con->select_where('followers',array('Id'),array('Followee'=>$row['Id']));
                $following = self::$_con->select_where('followers',array('Id'),array('Followee'=>$row['Id'],'Follower'=>$id)) ? true : false ;
                $all_post = self::$_con->select_where('posts',array('Id'),array('UserId'=>$row['Id']));
                $res = array('uid'=>$row['Id'],'fullname'=>$row['Fullname'],'username'=>$row['User_Name'],'pix'=>$row['Pix'],
                    'profession'=>$row['Profession'],'post'=>self::$_num,'current'=>$current,'followers'=>$followers,
                    'following'=>$following,'shared'=>$similar,'allPost'=>$all_post);
                // $post = self::$_con->join($tab,$fid,$onb,$whr,0,0)
                array_push(self::$_all, $res);
            }
            return array('state'=>true,'all'=>self::$_all);
        }
    }
    public static function getPeopleDetails($table,$field,$match,$search,$on,$where,$order,$limit,$id,$country,$state,$local,
        $interest){
        self::check();
        return self::search_pple($table,$field,$match,$search,$on,$where,$order,$limit,$id,$country,$state,$local,$interest);
    }
    public static function getDetails($table,$field,$match,$search,$on,$where,$order,$limit,$id){
        self::check();
        self::search_join($table,$field,$match,$search,$on,$where,$order,$limit,$id);
        return array('state'=>true,'all'=>self::$_all);

    }
    public static function getDetailsState($table_s,$table_l,$field_s,$field_l,$match,$search,$on_s,$on_l,$where_s,$where_l,$order,$limit,$id){
        self::check();
        self::search_join($table_s,$field_s,$match,$search,$on_s,$where_s,$order,$limit,$id);
        self::search_join($table_l,$field_l,$match,$search,$on_l,$where_l,$order,$limit,$id);
        return array('state'=>true,'all'=>self::$_all);

    }
    public static function getDetailsCountry($table_c,$table_s,$table_l,$field_c,$field_s,$field_l,$match,$search,$on_c,$on_s,$on_l,$where_c,$where_s,$where_l,$order,$limit,$id){
        self::check();
        self::search_join($table_s,$field_s,$match,$search,$on_s,$where_s,$order,$limit,$id);
        self::search_join($table_c,$field_c,$match,$search,$on_c,$where_c,$order,$limit,$id);
        self::search_join($table_l,$field_l,$match,$search,$on_l,$where_l,$order,$limit,$id);
        $pid = array_column(self::$_all, 'pid');
        $view = array_column(self::$_all, 'view');
        array_multisort($pid,SORT_DESC,SORT_NUMERIC,$view,SORT_DESC,SORT_NUMERIC,self::$_all);
        return array('state'=>true,'all'=>self::$_all);
    }
    public static function gethashtags($table,$field,$on,$where,$order,$limit,$id){
        self::check();
        self::search_hashtag($table,$field,$on,$where,$order,$limit,$id);
        return array('state'=>true,'all'=>self::$_all);
    }
    public static function gethashtagsState($table_s,$table_l,$field_s,$field_l,$on_s,$on_l,$where_s,$where_l,$order,$limit,$id){
        self::check();
        self::search_hashtag($table_s,$field_s,$on_s,$where_s,$order,$limit,$id);
        self::search_hashtag($table_l,$field_l,$on_l,$where_l,$order,$limit,$id);
        return array('state'=>true,'all'=>self::$_all);
    }
    public static function gethashtagsCountry($table_c,$table_s,$table_l,$field_c,$field_s,$field_l,$on_c,$on_s,$on_l,$where_c,
        $where_s,$where_l,$order,$limit,$id){
        self::check();
        self::search_hashtag($table_s,$field_s,$on_s,$where_s,$order,$limit,$id);
        self::search_hashtag($table_c,$field_c,$on_c,$where_c,$order,$limit,$id);
        self::search_hashtag($table_l,$field_l,$on_l,$where_l,$order,$limit,$id);
        return array('state'=>true,'all'=>self::$_all);
    }
    public static function getLocalPost($id,$interest,$country,$state,$local,$search){
        $needle = '#';
        $values=substr_count($search,$needle);
        $key =str_replace('#', '', $search);
        if($values < 1){
            $table = array('user','posts','local_post');
            $field = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime');
            $match = array('posts.Tag','posts.Post');
            $on = array('user.Id' => 'posts.UserId', 'posts.Id' => 'local_post.Post_id');
            $where = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','local_post.Country'=>$country,'local_post.State'=>$state,'local_post.Municipal'=>$local);
            $start = 0;
            $lim = 50;
            $order=array('posts.DateTime'=>'DESC'); 
            $limit=array($start,$lim);
            return json_encode(self::getDetails($table,$field,$match,$search,$on,$where,$order,$limit,$id));
        }else{
            $table = array('user','posts','local_post','hashtags');
            $field = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime');
            $on = array('user.Id'=>'posts.UserId', 'posts.Id'=>'local_post.Post_id','local_post.Post_id'=>'hashtags.Post_id');
            $where = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','local_post.Country'=>$country,'local_post.State'=>$state,'local_post.Municipal'=>$local,'hashtags.Hashtags'=>$key);
            $start = 0;
            $lim = 50;
            $order=array('posts.DateTime'=>'DESC'); 
            $limit=array($start,$lim);
            return json_encode(self::gethashtags($table,$field,$on,$where,$order,$limit,$id));
        }
    }
    public static function getStatePost($id,$interest,$country,$state,$search){
        $needle = '#';
        $values=substr_count($search,$needle);
        $key =str_replace('#', '', $search);
        if($values < 1){
            $table_s = array('user','posts','state_post');
            $table_l = array('user','posts','local_post');
            $field_s = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
            'posts.DateTime');
            $field_l = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
            'posts.DateTime','local_post.Municipal');
            $match = array('posts.Tag','posts.Post');
            $on_s = array('user.Id' => 'posts.UserId', 'posts.Id' => 'state_post.Post_id');
            $on_l = array('user.Id' => 'posts.UserId', 'posts.Id' => 'local_post.Post_id');
            $where_s = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','state_post.Country'=>$country,'state_post.State'=>$state);
            $where_l = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','local_post.Country'=>$country,'local_post.State'=>$state);
            $start = 0;
            $lim = 50;
            $order=array('posts.DateTime'=>'DESC'); 
            $limit=array($start,$lim);
            return json_encode(self::getDetailsState($table_s,$table_l,$field_s,$field_l,$match,$search,$on_s,$on_l,$where_s,$where_l,$order,
                $limit,$id));
        }else{
            $table_s = array('user','posts','state_post','hashtags');
            $table_l = array('user','posts','local_post','hashtags');
            $field_s = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime');
            $field_l = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime','local_post.Municipal');
            $on_s = array('user.Id'=>'posts.UserId', 'posts.Id'=>'state_post.Post_id','state_post.Post_id'=>'hashtags.Post_id');
            $on_l = array('user.Id'=>'posts.UserId', 'posts.Id'=>'local_post.Post_id','local_post.Post_id'=>'hashtags.Post_id');
            $where_s = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','state_post.Country'=>$country,'state_post.State'=>$state,'hashtags.Hashtags'=>$key);
            $where_l = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','local_post.Country'=>$country,'local_post.State'=>$state,'hashtags.Hashtags'=>$key);
            $start = 0;
            $lim = 50;
            $order=array('posts.DateTime'=>'DESC'); 
            $limit=array($start,$lim);
            return json_encode(self::gethashtagsState($table_s,$table_l,$field_s,$field_l,$on_s,$on_l,$where_s,$where_l,$order,$limit,$id));
        }
    }
    public static function getCountryPost($id,$interest,$country,$search){
        $needle = '#';
        $values=substr_count($search,$needle);
        $key =str_replace('#', '', $search);
        if($values < 1){
            $table_c = array('user','posts','country_post');
            $table_s = array('user','posts','state_post');
            $table_l = array('user','posts','local_post');
            $field_c = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime');
            $field_s = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime','state_post.State');
            $field_l = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime','local_post.State','local_post.Municipal');
            $match = array('posts.Tag','posts.Post');
            $on_c = array('user.Id' => 'posts.UserId', 'posts.Id' => 'country_post.Post_id');
            $on_s = array('user.Id' => 'posts.UserId', 'posts.Id' => 'state_post.Post_id');
            $on_l = array('user.Id' => 'posts.UserId', 'posts.Id' => 'local_post.Post_id');
            $where_c = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified',
                'country_post.Country'=>$country);
            $where_s = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified',
                'state_post.Country'=>$country);
            $where_l = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified',
                'local_post.Country'=>$country);
            $start = 0;
            $lim = 50;
            $order=array('posts.DateTime'=>'DESC'); 
            $limit=array($start,$lim);
            return json_encode(self::getDetailsCountry($table_c,$table_s,$table_l,$field_c,$field_s,$field_l,$match,$search,$on_c,$on_s,$on_l,$where_c,$where_s,$where_l,$order,$limit,$id));
        }else{
            $table_c = array('user','posts','country_post','hashtags');
            $table_s = array('user','posts','state_post','hashtags');
            $table_l = array('user','posts','local_post','hashtags');
            $field_c = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime');
            $field_s = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime','state_post.State');
            $field_l = array('posts.Id','posts.UserId','posts.Tag','posts.Post','posts.Pix AS Post_Pix','user.User_Name','user.Pix',
                'posts.DateTime','local_post.State','local_post.Municipal');
            $on_c = array('user.Id'=>'posts.UserId', 'posts.Id'=>'country_post.Post_id','country_post.Post_id'=>'hashtags.Post_id');
            $on_s = array('user.Id'=>'posts.UserId', 'posts.Id'=>'state_post.Post_id','state_post.Post_id'=>'hashtags.Post_id');
            $on_l = array('user.Id'=>'posts.UserId', 'posts.Id'=>'local_post.Post_id','local_post.Post_id'=>'hashtags.Post_id');
            $where_c = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','country_post.Country'=>$country,'hashtags.Hashtags'=>$key);
            $where_s = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','state_post.Country'=>$country,'hashtags.Hashtags'=>$key);
            $where_l = array('posts.Interest'=>$interest,'posts.Status'=>'Public','posts.Report_Status'=>'Unverified','local_post.Country'=>$country,'hashtags.Hashtags'=>$key);
            $start = 0;
            $lim = 50;
            $order=array('posts.DateTime'=>'DESC'); 
            $limit=array($start,$lim);
            return json_encode(self::gethashtagsCountry($table_c,$table_s,$table_l,$field_c,$field_s,$field_l,$on_c,$on_s,$on_l,$where_c,
                $where_s,$where_l,$order,$limit,$id));
        }
    }
    public static function getLocalPeople($id,$interest,$country,$state,$local,$search){
        $table = array('user','interest');
        $field = array('user.Id','user.Fullname','user.User_Name','user.Pix','user.Profession');
        $match = array('user.Fullname','user.User_Name');
        $on = array('user.Id' => 'interest.User_id');
        $where = array('interest.Interest'=>$interest,'!user.Id'=>$id,'user.Country'=>$country,'user.State'=>$state,'user.Municipal'=>$local);
        $start = 0;
        $lim = 50;
        $order=0; 
        $limit=array($start,$lim);
        return json_encode(self::getPeopleDetails($table,$field,$match,$search,$on,$where,$order,$limit,$id,$country,$state,$local,$interest));
    }
    public static function getStatePeople($id,$interest,$country,$state,$search){
        $table = array('user','interest');
        $field = array('user.Id','user.Fullname','user.User_Name','user.Pix','user.Profession');
        $match = array('user.Fullname','user.User_Name');
        $on = array('user.Id' => 'interest.User_id');
        $where = array('interest.Interest'=>$interest,'!user.Id'=>$id,'user.Country'=>$country,'user.State'=>$state);
        $start = 0;
        $lim = 50;
        $order=0; 
        $limit=array($start,$lim);
        return json_encode(self::getPeopleDetails($table,$field,$match,$search,$on,$where,$order,$limit,$id,$country,$state,0,
            $interest));
    }
    public static function getCountryPeople($id,$interest,$country,$search){
        $table = array('user','interest');
        $field = array('user.Id','user.Fullname','user.User_Name','user.Pix','user.Profession');
        $match = array('user.Fullname','user.User_Name');
        $on = array('user.Id' => 'interest.User_id');
        $where = array('interest.Interest'=>$interest,'!user.Id'=>$id,'user.Country'=>$country);
        $start = 0;
        $lim = 50;
        $order=0; 
        $limit=array($start,$lim);
        return json_encode(self::getPeopleDetails($table,$field,$match,$search,$on,$where,$order,$limit,$id,$country,0,0,
            $interest));
    }
}