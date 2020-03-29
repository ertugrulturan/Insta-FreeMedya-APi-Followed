<?php

Class FreeMedya
{
public $username;
public $password;
private $guid;
private $my_uid;
private $userAgent = 'Instagram 6.21.2 Android (19/4.4.2; 480dpi; 1152x1920; Meizu; MX4; mx4; mt6595; en_US)';
private $instaSignature ='673581b0ddb792bf47da5f9ca816b613d7996f342723aa06993a3f0552311c7d';
private $instagramUrl = 'https://i.instagram.com/api/v1/';

function __construct()	{	
    if (!extension_loaded('curl')) trigger_error('php_curl extension is not loaded', E_USER_ERROR);	
}

function __destruct()	{

}	
	
private function Request($url, $post, $post_data, $cookies) {	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->instagramUrl . $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    if($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
		if ((version_compare(PHP_VERSION, '5.5') >= 0)) {
		} 		
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
	
    if($cookies) {
        curl_setopt($ch, CURLOPT_COOKIEFILE,   dirname(__FILE__). '/cookies.txt');            
    } else {
        curl_setopt($ch, CURLOPT_COOKIEJAR,  dirname(__FILE__). '/cookies.txt');
    }
    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);    
    curl_close($ch);    
    return array($http, $response);
}

private function GenerateGuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
            mt_rand(0, 65535), 
            mt_rand(0, 65535), 
            mt_rand(0, 65535), 
            mt_rand(16384, 20479), 
            mt_rand(32768, 49151), 
            mt_rand(0, 65535), 
            mt_rand(0, 65535), 
            mt_rand(0, 65535));
}

private function GenerateSignature($data) {
    return hash_hmac('sha256', $data, $this->instaSignature); 
}

public function Login($username, $password) {
    $this->username = $username;
    $this->password = $password;	
	$this->guid = $this->GenerateGuid();
	$device_id = "android-" . $this->guid;	
	$data = '{"device_id":"'.$device_id.'","guid":"'.$this->guid.'","username":"'. $this->username.'","password":"'.$this->password.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
	$sig = $this->GenerateSignature($data);
	$data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=6';	
	$myid = $this->Request('accounts/login/', true, $data, false);	
	$decode = json_decode($myid[1], true); 
    $this->my_uid = $decode['logged_in_user']['username'];
	print_r($this->my_uid);	
	return $myid;
}



public function TakipEt($user_id) {
    $device_id = "android-".$this->guid;
    $data = '{"device_id":"'.$device_id.'","guid":"'. $this->guid .'","uid":"'.$this->my_uid.'","module_name":"feed_timeline","user_id":"'.$user_id.'","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';   
    $sig = $this->GenerateSignature($data);
    $new_data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=6';
	return $this->Request('friendships/create/'.$user_id.'/', true, $new_data, true);	
}




}
?>
