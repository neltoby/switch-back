<?php
class Pos_Follow {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    public static $_pointer;
    protected static $_con;

    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }

    public static function followButton($uid, $id) 
    {
    	if(!self::$_con)
        {
            self::connect();
        }
		$field = array('*');
		$pointer = array('Follower' => $uid, 'Followee' => $id);
		if(self::$_con->select_where('follow',$field,$pointer) > 0)
		{
			// $value = array('follow' => 'yes');
			$value = 'yes';
		}else{
			// $value = array('follow' => 'no');
			$value = 'no';
		}
		// return json_encode($value);
		return $value;
    }

    public static function fellowDetails($id, $uid){
    	if(!self::$_con)
        {
            self::connect();
        }
        $field = array('*');
        $allpost = array('User_id' => $id);
        $pointer = array('Followee' => $id);
        $point = array('Follower' => $id);
        $table = array('follow','ff_reviews');
        $ff= array('ff_reviews.Content');
        $on = array('follow.Id' => 'ff_reviews.Follow_id');
        $where = array('follow.Follower' => $uid, 'follow.Followee' => $id);
        // $revpoint = array('Follower' => $uid, 'Followee' => $id);
        $review = self::$_con->join($table,$ff,$on,$where,0,0) > 0 ? 'yes' : 'no';
        $followers = self::$_con->select_where('follow',$field,$pointer); 
        $following = self::$_con->select_where('follow',$field,$point);
        $post = self::$_con->select_where('post',$field,$allpost);
        $values = array('followers' => $followers, 'following' => $following, 'post' => $post, 'review' => $review);
        return json_encode($values);
    }

    public static function submitReview($uid, $id, $text){
    	if(!self::$_con)
        {
            self::connect();
        }
        $fs = array('*');
        $pt = array('Follower' => $uid, 'Followee' => $id);
        if(self::$_con->select_where('follow',$fs,$pt) > 0){
        	$ff = self::$_con->fetch_select_where();
        	$text= htmlentities(trim($text), ENT_NOQUOTES);
	        $date = date('Y-m-d h:i:s');
	        $field = array('Follow_id','Content','Date_time');
	        $pointer = array($ff['Id'], $text, $date);
	        $insert = self::$_con->insert('ff_reviews',$field,$pointer);
	        	$ffs = array('User_Name','Pix');
	        	$pt = array('Id' => $uid);
	        	self::$_con->select_where('user',$ffs,$pt);
	        	$user = self::$_con->fetch_select_where();
				$review = array('cid' => $uid,'id' => $uid,'name' => $user['User_Name'],'pix' => $user['Pix'],'text' => $text,'date' => $date,'likes' => 0,'postId' => $insert,'like' => false);
				return json_encode($review);
        }        
    }

    public static function getReview($uid, $id){
    	if(!self::$_con)
        {
            self::connect();
        }
        $table =array('user','follow','ff_reviews');
        $field =array('user.User_Name','user.Id','user.Pix','ff_reviews.Content','ff_reviews.Date_time','ff_reviews.Id AS Rev_id');
        $on = array('user.Id' => 'follow.Follower', 'follow.Id' => 'ff_reviews.Follow_id');
        $where = array('follow.followee' => $id);
        $order = array('ff_reviews.Date_time' => 'DESC');
        $start = 0;
        $end = 10;
        $limit = array($start, $end);
        $result = array();
        if(self::$_con->join($table,$field,$on,$where,$order,$limit) > 0){
        	while($row = self::$_con->fetch_join()){
				$fd = array('Id');
				$pt = array('Review_id' => $row['Rev_id'],'Followee' => $uid);
				$like = self::$_con->select_where('review_support',$fd,$pt) > 0 ? true : false ;
				$pts = array('Review_id' => $row['Rev_id']);
				$likes = self::$_con->select_where('review_support',$fd,$pts);
				array_push($result, array('cid' => $uid,'id' => $row['Id'],'name' => $row['User_Name'],'pix' => $row['Pix'],'text' => $row['Content'],'date' => $row['Date_time'],'likes' => $likes,'postId' => $row['Rev_id'], 'like' => $like));
        	}
        	return json_encode($result);
        }
    }

    public static function setLike($uid, $status, $post){
    	if(!self::$_con)
        {
            self::connect();
        }
        $fid = array('Id');
        $point = array('Review_id' => $post, 'Followee' => $uid);
        if($status == 1){
            if(self::$_con->select_where('review_support', $fid, $point) < 1){
                $date = date('Y-m-d h:i:s');
                $field = array('Review_id', 'Followee','Date_time');
                $pointer = array($post, $uid, $date);
                self::$_con->insert('review_support',$field,$pointer);
            }
            // return 'liked';
        }else{
            self::$_con->delete('review_support',$point);
            // return 'unliked';
        }
    }

    public static function getPost($uid, $id){
        if(!self::$_con)
        {
            self::connect();
        }
        $statement = array();
        // $field = array('*');
        $pointer = array('User_id' => $id);
        if(self::$_con->select_all_where('post', $pointer, 0, 0) > 0){
            while($row = self::$_con->fetch_select_all_where()){
                $column = array('Like_id');
                $pointer = array('User_id' => $uid, 'Post_id' => $row['Id']);
                if(self::$_con->select_where('post_like', $column, $pointer) > 0)
                {
                    $like = true;
                }else{
                    $like = false;
                }
                $point = array('Post_id' => $row['Id']);
                $like_cnt = self::$_con->select_where('post_like', $column, $point);
                // $comment = array('Comment_id');
                // $comment_where = array('Post_id' => $row['Id']);
                // $comments = self::$_con->select_where('post_comments',$comment,$comment_where);
                $thread_id = array('Id'); 
                $thread_where = array('Threaded_id' => $row['Id']);
                $threads = self::$_con->select_where('threads',$thread_id,$thread_where);
                if(!empty($row['Post_pix']))
                {
                    $needle = '*?';
                    $all = array();
                    $all_pix = array();
                    $value=strpos($row['Post_pix'],$needle);
                    if($value)
                    {
                        $pix = explode('*?',$row['Post_pix']);
                    }else{
                        $pix=array($row['Post_pix']);
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
                            $m_com = self::$_con->select_where('multi_image_comment',$m_like,$m_like_where); 
                            $m_view = self::$_con->select_where('multi_image_view',$m_like,$m_like_where);
                            array_push($all_pix, array('pic' => $pix[$i],'picnt' => $m_likes,'imageLike' => $final,'mcom' => $m_com,'views'=>$m_view));
                        }
                    }else{
                        array_push($all_pix, array('pic' => $pix[0],'height' => $row['Pix_height']));
                    }

                    if(!empty($row['Post'])){
                        $result = array('category'=>$row['Category'],'location'=>$row['Location'],
                            'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'like'=>$like,'likes'=>$like_cnt,
                            'thread'=>$threads,'postId'=>$row['Id']);
                    }else{
                        $result = array('category'=>$row['Category'],'location'=>$row['Location'],
                            'title'=>$row['Title'],'date'=>$row['Date_time'],'like'=>$like,'likes'=>$like_cnt,
                            'thread'=>$threads,'postId'=>$row['Id']);
                    }
                    array_push($all, $result);
                    array_push($all, $all_pix);
                    array_push($statement, $all);
                }else{
                    // $post = preg_replace('/(^|\s)#(\w*[a-zA-Z])/', replacement, subject)
                    $result = array('category'=>$row['Category'],'location'=>$row['Location'],
                        'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'like'=>$like,
                        'likes'=>$like_cnt,'thread'=>$threads,'postId'=>$row['Id']);
                    array_push($statement, $result);
                }
            }
            return json_encode($statement);
        }
    }

    public static function clickFollow($uid, $id, $status){
        if(!self::$_con)
        {
            self::connect();
        }
        $pointer = array('Follower' => $uid, 'Followee' => $id);
        // $point = array()
        if($status == 1){
            $field = array('*');    
            if(self::$_con->select_where('follow',$field,$pointer) < 1){
                $field = array('Follower','Followee','Date_time');
                $values= array($uid, $id, date('Y-m-d h:i:s'));
                if($var = self::$_con->insert('follow',$field,$values))
                {
                    // self::getReview($uid, $id);
                    return 'added';
                }

            }
        }else{
            $field = array('Id'); 
            if(self::$_con->select_where('follow',$field,$pointer) > 0){
                $ff_id = self::$_con->fetch_select_where();
                if(self::$_con->delete('follow',$pointer)){
                    $point = array('Follow_id' => $ff_id['Id']);
                    if(self::$_con->select_where('ff_reviews',$field,$point) > 0){
                        if(self::$_con->delete('ff_reviews',$point)){
                            $tables = array('follow','ff_reviews');
                            $nfield = array('ff_reviews.Id AS Review');
                            $on = array('follow.Id' => 'ff_reviews.Follow_id');
                            $where = array('follow.Followee' => $id);
                            if(self::$_con->join($tables, $nfield, $on, $where, 0, 0) > 0){
                                while($row = self::$_con->fetch_join()){
                                    $npointer = array('Review_id' => $row['Review'], 'Followee' => $uid);
                                    if(self::$_con->delete('review_support',$npointer)){
                                       return 'removed'; 
                                    }
                                    
                                }
                            }
                            // return 'removed';
                        }
                    }
                }
            }
            // self::getReview($uid, $id);
            
        }
        // self::getReview($uid, $id);
        // echo $uid;
        
    }    

    public static function getProfile($uid, $id){
        if(!self::$_con)
        {
            self::connect();
        }

        $table = array('user','follow');
        $field = array('user.Id','user.User_Name','user.Pix');
        $on  = array('user.Id' => 'follow.Follower');
        $where = array('follow.Followee' => $id);
        $order = array('follow.Date_time' => 'DESC');
        $start = 0;
        $end = 10;
        $limit = array($start, $end);
        $all = array();
        if(self::$_con->join($table,$field,$on,$where,$order,$limit) > 0){
            while($row = self::$_con->fetch_join()){
                $ff = self::$_con->select_where('follow',array('*'),array('Followee' => $row['Id']));
                //checks if you are follow this user
                $checks = self::$_con->select_where('follow',array('*'),array('Followee' => $row['Id'], 'Follower' => $uid)) > 0 ?true : false ;
                array_push($all, array('cid'=>$uid, 'id'=>$row['Id'], 'followers'=>$ff, 'pix'=>$row['Pix'], 
                    'name'=>$row['User_Name'], 'status' => $checks));
            }
            return json_encode($all);
        }
    }

    public static function userFollower($uid){
        if(!self::$_con)
        {
            self::connect();
        }
        $following = self::$_con->select_where('follow', array('Id'), array('Follower' => $uid));
        $followers = self::$_con->select_where('follow', array('Id'), array('Followee' => $uid));
        $post = self::$_con->select_where('post', array('Id'), array('User_id' => $uid));
        if(is_numeric($following) && is_numeric($followers) && is_numeric($post)){
            return json_encode(array('follower' => $followers, 'following' => $following, 'post' => $post));
        }
    }

}