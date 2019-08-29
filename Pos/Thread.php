<?php
class Pos_Thread {
	public static $_host;
    public static $_user;
    public static $_pass;
    public static $_db;
    public static $_pointer;
    protected static $_con;
    protected static $_file;

    private static function connect()
    {
        self::$_con = new Pos_Process(self::$_host,self::$_user,self::$_pass,self::$_db);
    }

    public static function image_comment($uid, $pid, $post, $image){
    	if(!self::$_con)
        {
            self::connect();
        }
        $date = date('Y-m-d h:i:s');
        $field = array('User_id','Post_id','Image','Comment','Date_time');
        $value = array($uid, $pid, $image, $post, $date);
        self::$_con->insert('multi_image_comment', $field, $value);
        $column = array('User_Name','Pix');
        $pointer = array('Id' => $uid);
        self::$_con->select_where('user', $column, $pointer);
		$row=self::$_con->fetch_select_where();
        $comment = array('uid'=>$uid, 'name'=>$row['User_Name'], 'pic'=>$row['Pix'], 'post'=>$post, 'date'=>$date);
        return json_encode($comment);

    }

    public static function get_image_comment($pid, $image){
    	if(!self::$_con)
        {
            self::connect();
        }

        $table = array('multi_image_comment','user');
        $field = array('multi_image_comment.User_id','multi_image_comment.Comment','multi_image_comment.Date_time','user.User_Name','user.Pix');
		$on = array('multi_image_comment.User_id' => 'user.Id');
		$where = array('multi_image_comment.Post_id' => $pid, 'multi_image_comment.Image' => $image);
		$start = 0;
        $lim = 8;
        $order=array('post.Date_time'=>'DESC');
        $limit=array($start,$lim);
        self::$_con->join($table, $field, $on ,$where, $order, $limit);
		$comments = array();
		while($row = self::$_con->fetch_join()){
			array_push($comments, array('uid'=>$row['User_id'], 'name'=>$row['User_Name'], 'pic'=>$row['Pix'], 'post'=>$row['Comment'],
				'date'=>$row['Date_time']));
		}
		return json_encode($comments);
	}

    public static function getThreads($uid, $pid, $category)
    {
        if(!self::$_con)
        {
            self::connect();
        }
        $all = array();
        $all_pix = array();
        $statement = array();
        $table = array('post','user');
        $field = array('user.User_Name','user.Pix','post.User_id','post.Title','post.Post','post.Post_pix','post.Pix_height','post.Date_time');
        $on = array('post.User_id' => 'user.Id');
        $where = array('post.Id' => $pid, 'post.Category' => $category);
        $start = 0;
        $lim = 50;
        $order=array('post.Date_time'=>'DESC');
        $limit=array($start,$lim);
        self::$_con->join($table, $field, $on ,$where, $order, $limit);
        $row = self::$_con->fetch_join(); 
        $column = array('Like_id');
        $pointer = array('User_id' => $uid, 'Post_id' => $pid);
        if(self::$_con->select_where('post_like', $column, $pointer) > 0)
        {
            $like = true;
        }else{
            $like = false;
        }
        $point = array('Post_id' => $pid);
        $like_cnt = self::$_con->select_where('post_like', $column, $point);
        $comment = array('Comment_id');
        $comment_where = array('Post_id' => $pid);
        $comments = self::$_con->select_where('post_comments',$comment,$comment_where);
        $thread_id = array('Id'); 
        $thread_where = array('Threaded_id' => $pid);
        $threads = self::$_con->select_where('threads',$thread_id,$thread_where);
        if(!empty($row['Post_pix']))
        {
            $needle = '*?';
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
                $result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],
                    'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$like,'comments'=>$comments,
                    'like'=>$like_cnt,'thread'=>$threads);
            }else{
                $result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],
                    'title'=>$row['Title'],'date'=>$row['Date_time'],'likes'=>$like,'comments'=>$comments,'like'=>$like_cnt,
                    'thread'=>$threads);
            }
            array_push($all, $result);
            array_push($all, $all_pix);
            array_push($statement, $all);
        }else{
            $result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],
                'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$like,'comments'=>$comments,
                'like'=>$like_cnt,'thread'=>$threads);
            array_push($statement, $result);
        }
        return json_encode($statement);

    }

    public static function upload($file=array(), $id)
    {
        self::$_file = $file;

        if(!isset(self::$_file))
        {
            throw new Exception("input type=file name='' Field name was not specified!
                                Constuctor expects atleast 2 parameters in the order (Field,Cookies_id).");
        }

        if(!is_array(self::$_file))
        {
            throw new Exception("Parameter 1 must be an array");
        }

        if(!self::$_con)
        {
            self::connect();
        }

        $folder=dirname('.',1).DIRECTORY_SEPARATOR.'postImages';
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
                        throw new Exception("Error code:".self::$_file["error"][$i]);
                    }else
                    {
                        list($width,$height,$type,$attr)=getimagesize(self::$_file["tmp_name"][$i]);
                        $pic=$id."_".date('d-m-Y-H-i-s').self::$_file["name"][$i];
                        array_push($pix, $pic);
                        array_push($pix_height, $height);       
                        $source = self::$_file["tmp_name"][$i];
                        $target = $folder. DIRECTORY_SEPARATOR.$pic;
                        if(!move_uploaded_file($source,$target)){
                            throw new Exception("File upload wasn't successful");
                        }
                    }
                }
            }
            
            $im_pix = implode('*?',$pix);
            $im_pix_height = implode('*?',$pix_height);
            $field = array('Post_pix' => $im_pix, 'Pix_height' => $im_pix_height);
            $pointer = array('Id' => $id);
            self::$_con->update_where('post', $field, $pointer);
            $final=array();
            for($i = 0; $i < count($pix); $i++)
            {
                array_push($final, array("pic" => $pix[$i],"height" => $pix_height[$i], "picnt" => 0, "imageLike" => false, "mcom" => 0, "views" => 0));
            }
                return $final;
        }
    }

    public static function addThreads($uid, $title, $post=false, $file=false, $category, $current, $country, $state=false, $local=false, 
        $pid)
    {
        if(!self::$_con)
        {
            self::connect();
        }

        if($post && $file)
            {
                
                $field=array('User_id','Title','Post','Category','Location','Date_time');
                $value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
                $id=self::$_con->insert('post', $field, $value);
                $images=self::upload($file, $id);
                if($current == 'country')
                {
                    $fild = array('Post_id','Country');
                    $values = array($id, $country);
                    self::$_con->insert('country_post', $fild, $values);
                }elseif($current == 'state'){
                    $fild = array('Post_id','Country','State');
                    $values = array($id, $country, $state);
                    self::$_con->insert('state_post', $fild, $values);
                }else{
                    $fild = array('Post_id','Country','State','Local');
                    $values = array($id, $country, $state, $local);
                    self::$_con->insert('local_post', $fild, $values);
                }  
                $fields = array('Threaded_id','Thread_id','Thread_userid');
                $vals = array($pid, $id, $uid);
                self::$_con->insert('threads', $fields, $vals);             
                $column = array('User_Name','Pix');
                $pointer = array('Id' => $uid);
                self::$_con->select_where('user', $column, $pointer);
                $row=self::$_con->fetch_select_where();
                $strings = array();
                $strings['cid'] = $uid;
                $strings['id'] = $uid;
                $strings['name'] = $row['User_Name'];
                $strings['profile'] = $row['Pix'];
                $strings['postId'] = $id;
                $strings['title'] = $title;
                $strings['post'] = $post;
                $strings['likes'] = false;
                $strings['like'] = 0;
                $strings['comments'] = 0;
                $strings['thread'] = 0;
                $strings['date'] = date('Y-m-d H:i:s');
                $info = array($strings,$images);
                return json_encode($info);
                
            }elseif($post && !$file)
            {
                $field=array('User_id','Title','Post','Category','Location','Date_time');
                $value=array($uid,$title,$post,$category,$current,date('Y-m-d H:i:s'));
                $id=self::$_con->insert('post', $field, $value);
                if($current == 'country')
                {
                    $fild = array('Post_id','Country');
                    $values = array($id, $country);
                    self::$_con->insert('country_post', $fild, $values);
                }elseif($current == 'state'){
                    $fild = array('Post_id','Country','State');
                    $values = array($id, $country, $state);
                    self::$_con->insert('state_post', $fild, $values);
                }else{
                    $fild = array('Post_id','Country','State','Local');
                    $values = array($id, $country, $state, $local);
                    self::$_con->insert('local_post', $fild, $values);
                }
                $fields = array('Threaded_id','Thread_id','Thread_userid');
                $vals = array($pid, $id, $uid);
                self::$_con->insert('threads', $fields, $vals);
                $column = array('User_Name','Pix');
                $pointer = array('Id' => $uid);
                self::$_con->select_where('user', $column, $pointer);
                $row=self::$_con->fetch_select_where();
                $strings = array();
                $strings['cid'] = $uid;
                $strings['id'] = $uid;
                $strings['name'] = $row['User_Name'];
                $strings['profile'] = $row['Pix'];
                $strings['postId'] = $id;
                $strings['title'] = $title;
                $strings['post'] = $post;
                $strings['likes'] = false;
                $strings['like'] = 0;
                $strings['comments'] = 0;
                $strings['thread'] = 0;
                $strings['date'] = date('Y-m-d H:i:s');
                $info = array($strings);
                return json_encode($info);

            }elseif(!$post && $file)
            {
                $field=array('User_id','Title','Category','Location','Date_time');
                $value=array($uid,$title,$category,$current,date('Y-m-d H:i:s'));
                $id=self::$_con->insert('post', $field, $value);
                $images=self::upload($file, $id);
                if($current == 'country')
                {
                    $fild = array('Post_id','Country');
                    $values = array($id, $country);
                    self::$_con->insert('country_post', $fild, $values);
                }elseif($current == 'state'){
                    $fild = array('Post_id','Country','State');
                    $values = array($id, $country, $state);
                    self::$_con->insert('state_post', $fild, $values);
                }else{
                    $fild = array('Post_id','Country','State','Local');
                    $values = array($id, $country, $state, $local);
                    self::$_con->insert('local_post', $fild, $values);
                }
                $$fields = array('Threaded_id','Thread_id','Thread_userid');
                $vals = array($pid, $id, $uid);
                self::$_con->insert('threads', $fields, $vals);
                $column = array('User_Name','Pix');
                $pointer = array('Id' => $uid);
                self::$_con->select_where('user', $column, $pointer);
                $row=self::$_con->fetch_select_where();
                $strings = array();
                $strings['cid'] = $uid;
                $strings['id'] = $uid;
                $strings['name'] = $row['User_Name'];
                $strings['profile'] = $row['Pix'];
                $strings['postId'] = $id;
                $strings['likes'] = false;
                $strings['like'] = 0;
                $strings['comments'] = 0;
                $strings['thread'] = 0;
                $strings['title'] = $title;
                $strings['date'] = date('Y-m-d H:i:s');
                $info = array($strings,$images);
                return json_encode($info);
            }
    }

    public static function getOtherThreads($uid, $pid){
        if(!self::$_con)
        {
            self::connect();
        }
        $statement = array();
        $table = array('post','user','threads');
        $field = array('user.User_Name','user.Pix','post.Id','post.User_id','post.Title','post.Post','post.Post_pix','post.Pix_height','post.Date_time');
        $on = array('post.User_id' => 'user.Id', 'post.Id' => 'Thread_id');
        $where = array('threads.Threaded_id' => $pid);
        $start = 0;
        $lim = 50;
        $order=array('post.Date_time'=>'DESC');
        $limit=array($start,$lim);
        if(self::$_con->join($table, $field, $on ,$where, $order, $limit) > 0){
            while($row = self::$_con->fetch_join()){ 
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
                $comment = array('Comment_id');
                $comment_where = array('Post_id' => $row['Id']);
                $comments = self::$_con->select_where('post_comments',$comment,$comment_where);
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
                        $result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],
                            'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$like,'comments'=>$comments,'like'=>$like_cnt,'thread'=>$threads,'postId'=>$row['Id']);
                    }else{
                        $result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],
                            'title'=>$row['Title'],'date'=>$row['Date_time'],'likes'=>$like,'comments'=>$comments,'like'=>$like_cnt,
                            'thread'=>$threads,'postId'=>$row['Id']);
                    }
                    array_push($all, $result);
                    array_push($all, $all_pix);
                    array_push($statement, $all);
                }else{
                    $result = array('cid'=>$uid,'id'=>$row['User_id'],'name'=>$row['User_Name'],'profile'=>$row['Pix'],
                        'title'=>$row['Title'],'post'=>$row['Post'],'date'=>$row['Date_time'],'likes'=>$like,'comments'=>$comments,
                        'like'=>$like_cnt,'thread'=>$threads,'postId'=>$row['Id']);
                    array_push($statement, $result);
                }
            }
            return json_encode($statement);
        }else{
            return '';
        }
    }

}
?>