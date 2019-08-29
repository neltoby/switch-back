<?php
class Pos_Login {
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
    private static function check(){
        if(!self::$_con)
        {
            self::connect();
        }
    }

    public static function createUser($required=array())
    {
	    if(!self::$_con)
        {
            self::connect();
        }
        
        $val = new Pos_Validator($required);
        $val->checkTextLength('pword', 5);
        $val->removeTags('fname');
        $val->removeTags('lname');
        $val->removeTags('tele');
        $val->removeTags('pword');
        $val->removeTags('prof');
        $val->isEmail('mail');
        $filtered = $val->validateInput();
        $missing = $val->getMissing();
        $errors = $val->getErrors();

        if(!$missing && !$errors)
        {
        	$field = array("FirstName","LastName","Email","Mobile","Password","Profession");
        	$value = array($filtered["fname"],$filtered["lname"],$filtered["mail"],$filtered["tele"],$filtered["pword"],$filtered["prof"]);
        	$session = self::$_con->insert("user",$field,$value);
        	return $session;
        }
    }

    public static function location($required=array())
    {
        if(!self::$_con)
        {
            self::connect();
        }

        $val = new Pos_Validator($required);
        $val->removeTags('_country');
        $val->removeTags('_states');
        $val->removeTags('_local');
        $val->removeTags('_question');
        $filtered = $val->validateInput();
        $missing = $val->getMissing();
        $errors = $val->getErrors();

        if(!$missing && !$errors)
        {
            $field = array("Country"=>$filtered["_country"],"State"=>$filtered["_states"],"Municipal"=>$filtered["_local"],
                "SecurityQ"=>$filtered["_question"]);
            $value = array("id"=> self::$_pointer);
            self::$_con->update_where("user",$field,$value);
        }
    }

    public static function signin($required=array())
    {
        if(!self::$_con)
        {
            self::connect();
        }

        $val = new Pos_Validator($required);
        $val->checkTextLength('pword', 5);
        $val->removeTags('pword');
        $val->isEmail('mail');
        $filtered = $val->validateInput();
        $missing = $val->getMissing();
        $errors = $val->getErrors();

        if(!$missing && !$errors)
        {
            $field=array('*');
            $pointer= array('Email'=>$filtered['mail'],'Password'=>$filtered['pword']);
            if(self::$_con->select_where("user",$field,$pointer) > 0){
                $value=self::$_con->fetch_select_where();
                if(empty($value['Country']) || empty($value['State']) || empty($value['Municipal'])){
                    $val = array('id' => $value[0],'status' => 'partial');
                    return $val;
                }else{
                    $val = array('id' => $value[0],'status' => 'full');
                    return $val;
                }
            }else{
                return array('id' => 'closed','status' => 'closed');
            }
        }
    }

    public static function verifyPassword($required=array())
    {
        if(!self::$_con)
        {
            self::connect();
        }

        $val = new Pos_Validator($required);
        $val->checkTextLength('tele', 8);
        $val->removeTags('tele');
        $val->isEmail('mail');
        $filtered = $val->validateInput();
        $missing = $val->getMissing();
        $errors = $val->getErrors();

        if(!$missing && !$errors)
        {
            $field=array('*');
            $pointer= array('Email'=>$filtered['mail'],'Mobile'=>$filtered['tele']);
            if(self::$_con->select_where("user",$field,$pointer) > 0)
            {
                // this code below is for retrieving the password from db
                $value=self::$_con->fetch_select_where();
                // code for sending email should go here

                return 'confirmed';
            }else
            {
                return 'not confirmed';
            }
        }
    }

    public static function getLocation($uid){
        if(!self::$_con)
        {
            self::connect();
        }

        $field=array('Country','State','Municipal','User_Name','Pix');
        $pointer= array('Id'=>$uid);
        if(self::$_con->select_where('user',$field,$pointer) > 0)
        {
            $value = self::$_con->fetch_select_where();
            $values = array('Country' => $value['Country'], 'State' => $value['State'], 'Local' => $value['Municipal'],
                'Uname' => $value['User_Name'], 'Pix' => $value['Pix']);
            return json_encode($values);
        }
    }

    public static function userProfile($uid, $id){
        if(!self::$_con)
        {
            self::connect();
        }

        $field = array('*');
        $pointer = array('Id' => $id );
        if(self::$_con->select_where('user',$field,$pointer) > 0)
        {
            $row = self::$_con->fetch_select_where();
            $values = array('cid'=>$uid,'id'=>$row['Id'],'fname'=>$row['FirstName'],'lname'=>$row['LastName'],
                'uname'=>$row['User_Name'],'email'=>$row['Email'],'mobile'=>$row['Mobile'],'pass'=>$row['Password'],
                'pix'=>$row['Pix'],'prof'=>$row['Profession'],'country'=>$row['Country'],'state'=>$row['State'],
                'local'=>$row['Municipal'],'about'=>$row['About'],'hobby'=>$row['Hobby']);
            return json_encode($values);
        }
    }

    public static function emailConfirmation($email){
        self::check();
        $field = array('Email');
        $pointer = array('Email' => $email );
        if(self::$_con->select_where('user',$field,$pointer) > 0){
            $status = true;
        }else{
            $status = false;
        }
        return json_encode(array('status' => $status));
    }

}
?>