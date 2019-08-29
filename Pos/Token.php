<?php
class Pos_Token extends Pos_Process{
	private $_algo;
    private $_key;
    private $_iss;
    private $_auto;
    private $_con;
    // public $_host;
    // public $_user;
    // public $_pass;
    // public $_db;

    private function __constructor($host,$user,$pass,$db)
    {
    	 parent::__construct($host,$user,$pass,$db);
        // $this->$_con = new Pos_Process($this->$_host,$this->$_user,$this->$_pass,$this->$_db);
    }
    // private function check(){
    //     if(!$this->$_con)
    //     {
    //         $this->connect();
    //     }
    // }
    private function encode($data){
    	if($data){
    		try{
				$urlSafeData = strtr(base64_encode($data), '+/', '-_'); 
			    return rtrim($urlSafeData, '=');
    		}catch(Exception $e){
    			return $e->getMessage();
    		}
    	}else{
    		throw new Exception('Invalid request');
    	}   	
    }
    private function decode($data){
    	if($data){
    		try{
    			$urlUnsafeData = strtr($data, '-_', '+/'); 
			    $paddedData = str_pad($urlUnsafeData, strlen($data) % 4, '=', STR_PAD_RIGHT);
			    return base64_decode($paddedData);
    		}catch(Exception $e){
    			return $e->getMessage();
    		}
    	}
    	
    }
    public function JWT_token(string $algo, array $header, array $payload, string $secret){
    	if(!$algo){
    		throw new Exception('signation key was missing and operation failed');
    	}
    	if(!$header){
    		throw new Exception('Token could not be initialized because header was missing');
    	}
    	if(!$payload){
    		throw new Exception('Token could not be initialized because payload was missing');
    	}
    	if(!$secret){
    		throw new Exception('Token could not be initialized because secret key was missing');
    	}
    	if($header && !is_array($header)){
    		throw new Exception('Invalid header format. Header should be an array');
    	}
    	if($payload && !is_array($payload)){
    		throw new Exception('Invalid payload format. Payload should be an array');
    	}
    	try{
	    	$headerEncoded = $this->encode(json_encode($header));
		    $payloadEncoded = $this->encode(json_encode($payload));	 
		    // Delimit with period (.)
		    $dataEncoded = "$headerEncoded.$payloadEncoded";	 
		    $rawSignature = hash_hmac($algo, $dataEncoded, $secret, true);	 
		    $signatureEncoded = $this->encode($rawSignature);	 
		    // Delimit with second period (.)
		    $jwt = "$dataEncoded.$signatureEncoded";	 
		    return $jwt;
		}catch(Exception $e){
			return $e->getMessage();
		}
    }
    public function verifyJWT(string $algo, string $jwt, string $secret){
    	if(!$algo){
    		throw new Exception('signation key was missing so verification failed');
    	}
    	if(!$secret){
    		throw new Exception('Token could not be verified because secret key was missing');
    	}
    	if(!$jwt){
    		throw new Exception('Operation could not continue because token was missing');
    	}
    	list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);
	    $dataEncoded = "$headerEncoded.$payloadEncoded";	 
	    $signature = $this->decode($signatureEncoded);	 
	    $rawSignature = hash_hmac($algo, $dataEncoded, $secret, true);	 
	    return hash_equals($rawSignature, $signature);
    }
    public function checkJWt(string $algo, string $jwt, string $secret){
    	// $verified = $this->verifyJWT($algo, $jwt, $secret);
    	// require_once '..'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'domain.php';
    	if($this->verifyJWT($algo, $jwt, $secret)){
    		list($headerEncoded, $payloadEncoded, $signatureEncoded) = explode('.', $jwt);   		
    		$decodedPayload = json_decode($this->decode($payloadEncoded), true);    		
    		// if($decodedPayload['iss'] == 'http://localhost:8080/newSwitch/Domain/domain.php'){
    		// $this->check();
    		$this->select_where('user',array('Token'),array('Id'=>$decodedPayload['ref']));
    		$row = $this->fetch_select_where();
    		if($row['Token'] == $jwt){
    			if($decodedPayload['exp'] >= time()){
					return array('state'=>true,'token'=>$jwt,'ref'=>$decodedPayload['ref'],
						'username'=>$decodedPayload['username']);
    			}else{
    				$current = time();
                    $nbf = $current + 5;
                    $exp = $current + 3605;
                    $header = ["alg"=>"HS256","typ"=>"JWT"];
                    $payload = ["iat"=>$current,"jti"=>$decodedPayload['jti'],"iss"=>$decodedPayload['iss'],"nbf"=>$nbf,"exp"=>$exp,"ref"=>$decodedPayload['ref'],"username"=>$decodedPayload['username']];
                    $token = $this->JWT_token('sha256',$header,$payload,$secret);                    
                    if($this->update_where('user',array('Token'=>$token),array('Id'=>$decodedPayload['ref']))){
                        return array('state' => true,'token' => $token,'ref'=>$decodedPayload['ref'],
                        	'username'=>$decodedPayload['username']);
                    }
    			}
    		}else{
    			return array('state' => false);
    		}
    		// }else{
    		// 	return array('state'=>false, 'error'=>'Invalid Domain name');
    		// }
    	}
    }
}