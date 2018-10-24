<?php
/**
 * Social Network connect modules
 * frsnconnect 0.16 by froZZen
 *
 * Register application: https://dev.twitter.com/apps/new
 * 
 */

require_once 'frOAuthSrv.php';

/**
 * Twitter provider class.
 */
class TwOAuthSrv extends frOAuthSrv {	
	
			
    protected $providerOptions = array(
        'request' => 'https://api.twitter.com/oauth/request_token',
	'authorize' => 'https://api.twitter.com/oauth/authenticate', 
//		'authorize' => 'https://api.twitter.com/oauth/authorize',
	'access' => 'https://api.twitter.com/oauth/access_token',
	);
        
    public function init($options = array()) {
        
        $this->type = 'tw_id';
        parent::init($options);

    }	
    
    protected function fetchAttributes() {

/*        
Invalid response http code: 400. 
 * URL: https://api.twitter.com/1.1/account/verify_credentials.json?
 * oauth_consumer_key=&
 * oauth_nonce=e7ea40e735bd5e4ab624c6c3f8a19198&
 * oauth_signature=zpI8YDE7dUYXC1L8Yrs82A0oSvg%3D&
 * oauth_signature_method=HMAC-SHA1&
 * oauth_timestamp=1389082887&
 * oauth_token=386522313-VMPT3lkA6XsF8eKIvBZG07fSAAiGR7jvqtc3U6KN&
 * oauth_version=1.0
 *  Options: array ( ) Result: {"errors":[{"message":"Bad Authentication data","code":215}]}        
 */
        
	$info = $this->makeSignedRequest('https://api.twitter.com/1.1/account/verify_credentials.json');
                
	$this->attributes['id'] = $info->id;
	$this->attributes['name'] = (!isset($info->name))? 'Twitter User' : $info->name;

	$this->attributes['url'] = 'http://twitter.com/account/redirect_by_id?id='.$info->id_str;

    $this->attributes['city'] = ($info->location?$info->location:'City name');
	$this->attributes['id_gender'] =  9;
	$this->attributes['email'] =  $info->email;
	$nn = explode(' ', trim($info->name));
	$this->attributes['firstname'] = $nn[0];
	$this->attributes['lastname'] = $nn[count($nn)-1];
//	$this->attributes['birthday'] =  (!isset($info->birthday))? '' : $this->GetBDate($info->birthday);
//           $this->attributes['id_country'] = $id_country;
                
//        $this->errors[] = print_r($info, true);          

    }
	
    /**
     * Authenticate the user.
     */
    public function authenticate() {

	if (isset($_GET['denied']))
		$this->cancel();	
	return parent::authenticate();
	
    }
    
}