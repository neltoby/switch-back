<?php
class  Pos_RemoteConnector{
    protected $_url;
    protected $_remoteFile;
    protected $_error;
    protected $_urlParts;
    protected $_header;
    protected $_status;
    
    public function __construct($url){
        $this->_url = $url;
        $this->_header =array();
        $this->checkURL();
        if(ini_get('allow_url_open')){
            $this->accessDirect();
        }elseif(function_exists('cur_init')){
            $this->useCurl();
        }else{
            $this->useSocket();
        }
    }
    
    protected function checkURL(){
        $flags = FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED;
        $urlOK = filter_var($this->_url, FILTER_VALIDATE_URL, $flags);
        $this->_urlParts = parse_url($this->_url);
        $domainOK = preg_match('/^[^.]+?\.\w{2}/', $this->_urlParts['host']);
        if(!$urlOK || $this->_urlParts['scheme'] != 'http' || !$domainOK){
            throw new Exception($this->_url . ' is not a valid URL');
        }
    }
    
    protected function accessDirect(){
        $this->_remoteFile = @file_get_contents($this->_url);
        $this->_header = @get_headers($this->_url, 1);
        if($this->_header){
            preg_match('/\d{3}/', $this->_header[0], $m);
            $this->_status = $m[0];
        }        

    }
    
    protected function useCurl(){
        if($session = curl_init($this->_url)){
            // Suppress the HTTP headers
            curl_setopt($session, CURLOPT_HEADER, false);
            // Return the remote file as a string,
            // rather than output it directly
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            // Get the remote file and store it in the $remoteFile property
             $this->_remoteFile = curl_exec($session);
             // Get the HTTP status
             $this->_status = curl_getinfo($session, CURLINFO_HTTP_CODE);
             echo $this->_status;
             // Close the cURL session
             curl_close($session);
        }else{
            $this->_error = 'Cannot establish cURL session';
        }
    }
    
    protected function useSocket(){
        //checks if port number is set in the url parts
        $port = isset($this->_urlParts['port']) ? $this->_urlParts['port'] : 80;
        //creates a resource handle / open a socket connection
        $remote = @fsockopen($this->_urlParts['host'],$port,$errno,$errstr,30);
        //set remoteFile to false if connection fails
        if(!$remote){
            $this->_remoteFile = false;
            $this->_error = "Couldn't create a socket connection: ";
            if($errstr){
                $this->_error.=$errstr;
            }else{
                $this->_error.='check the domain name or IP address.';
            }
        }else{
        // Add the query string to the path, if it exists
        if(isset($this->_urlParts['query'])){
            $path = $this->_urlParts['path'].'?'.$this->_urlParts['query'];
        }else{
            $path = $this->_urlParts['path'];
        }
        // Create the request headers
        $out = "GET $path HTTP/1.1\r\n";
        $out.="Host: {$this->_urlParts['host']}\r\n";
        $out.="Connection: Close\r\n\r\n";
        // Send the headers
        fwrite($remote,$out);
        // Capture the response
        $this->_remoteFile = stream_get_contents($remote);
        fclose($remote);
        if($this->_remoteFile){
            $this->removeHeaders();
        }
        }
    }
    
    protected function removeHeaders(){
        $parts=preg_split('#\r\n\r\n|\n\n#',$this->_remoteFile);
        if(is_array($parts)){
            $header=array_shift($parts);
            $file=implode("\n\n",$parts);
        if(preg_match('#HTTP/1\.\d\s+(\d{3})#',$header,$m)){
            $this->_status = $m[1];
        }
        if(preg_match('#Content-Type:([^\r\n]+)#i',$header,$m)){
            if(stripos($m[1],'xml') !== false || stripos($m[1],'html') !== false){
                if(preg_match('/<.+/s',$file,$m)){
                    $this->_remoteFile =$m[0];
                }else{
                    $this->_remoteFile = trim($file);
                }
            }else{
                $this->_remoteFile = trim($file);
            }
        }
        }

    }
    
    public function __toString(){
        if(!$this->_remoteFile){
            $this->_remoteFile = '';
        }
        
        return $this->_remoteFile;
    
    }
    
    public function getErrorMessage(){
        if(is_null($this->_error)){
            $this->setErrorMessage();
        }
        return $this->_error;
    }
    
    protected function setErrorMessage(){
        if($this->_status == 200 && $this->_remoteFile){
            $this->_error = '';
        }else{
            switch ($this->_status) {
case 200:
case 204:
$this->_error = 'Connection OK, but file is empty.';
break;
case 301:
case 302:
case 303:
case 307:
case 410:
$this->_error = 'File has been moved or does not exist.';
break;
case 305:
$this->_error = 'File must be accessed through a proxy.';
break;
case 400:
$this->_error = 'Malformed request.';
break;
case 401:
case 403:
$this->_error = 'You are not authorized to access this page.';
break;
case 404:
$this->_error = 'File not found.';
break;
case 407:
$this->_error = 'Proxy requires authentication.';
break;
case 408:
$this->_error = 'Request timed out.';
break;
case 500:
$this->_error = 'The remote server encountered an internal error.';
break;
case 503:
$this->_error = 'The server cannot handle the request at the moment.';
break;
default:
$this->_error = 'Undefined error. Check URL and domain name.';
break;
}
        }
    }
    
}
?>