<?php
/**
 * Social Network connect modules
 * frsnconnect 0.16 by froZZen
 *
 * Register application: https://www.linkedin.com/secure/developer
 * 
 */

require_once 'frOAuth2Srv.php';


/**
 * LinkedIn provider class.
 */
class LDOAuthSrv extends frOAuth2Srv {	
	
    protected $client_id = '';
    protected $client_secret = '';
    protected $scope = 'r_basicprofile r_emailaddress r_contactinfo';
    
    protected $providerOptions = array(
        'authorize' => 'https://www.linkedin.com/uas/oauth2/authorization',
	'access_token' => 'https://www.linkedin.com/uas/oauth2/accessToken'
	);

        
    protected function fetchAttributes() {
            
        $url = 'https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,phone-numbers,main-address)';   
        if (!$this->getIsAuthenticated())
            $this->errors[] = 'Unable to complete the request because the user was not authenticated.';

        $options = array();
        $options['oauth2_access_token'] = $this->access_token;
        $options['format'] = 'json';
        
        $url .=  '?'.http_build_query($options);    

//        Logger::addLog($url, 1);
	$info = (object)$this->makeRequest($url);
        
	$this->attributes['id'] = $info->id;
	$this->attributes['name'] = $info->firstName.' '.$info->lastName;
                
 	$this->attributes['firstname'] = $info->firstName;
	$this->attributes['lastname'] = $info->lastName;
	
        $this->attributes['id_gender'] =  9;
	
        if(isset($info->mainAddress))
            $this->attributes['city'] =  $info->mainAddress;
        
        if(isset($info->emailAddress))
            $this->attributes['email'] =  $info->emailAddress;

        if(isset($info->phoneNumbers)) {
            $phones = (object)$info->phoneNumbers;
            foreach ($phones->values as $phone) {
                $ph = (object) $phone;
                if ($ph->phoneType == 'home')
                    $this->attributes['phone'] =  $ph->phoneNumber;
                elseif ($ph->phoneType == 'mobile') 
                    $this->attributes['phone_mobile'] =  $ph->phoneNumber;
           }
        }

        $this->attributes['birthday'] =  '';
//      $this->errors[] = print_r($info, true);          
                
    }
		
        
    protected function getCodeUrl($redirect_uri) {
        
        $redirect_state = '';   
        if ($this->hasState('redirect_params'))
            $redirect_state = $this->getState('redirect_params');
             
        if (strpos($redirect_uri, '?') !== false) {
            $url = explode('?', $redirect_uri);
            $url[1] = preg_replace('#[/]#', '%2F', $url[1]);
            $redirect_uri = $url[0];
            if (!strlen($redirect_state))
                $redirect_state .= 'state=snLogin_ld_id&'.$url[1];
	}

        $this->setState('redirect_uri', $redirect_uri);
        $this->setState('redirect_params', $redirect_state);
//        Logger::addLog('getCodeUrl $redirect_uri: '.$redirect_uri, 1);     
//        Logger::addLog('getCodeUrl $redirect_state: '.$redirect_state, 1);     
	$url = parent::getCodeUrl($redirect_uri);
        $url .= '&state='.strtr(base64_encode($redirect_state), '+/=', '-_,');
//        Logger::addLog('getCodeUrl: '.$url, 1);     
    
        return $url;
        
    }

    protected function getAccessToken($code) {
       
        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'code' => $code,
            'redirect_uri' => $this->getState('redirect_uri'),
            );
        
        $url = $this->getTokenUrl($code).'?'.http_build_query($params);
     
        // Tell streams to make a POST request
        $context = stream_context_create(
                    array('http' =>
                        array('method' => 'POST',
                        )
                    )
                );
 
        // Retrieve access token information
        $response = file_get_contents($url, false, $context);
 
        return $this->parseJson($response);
        
    }
    
    protected function getTokenUrl($code) {

        return $this->providerOptions['access_token'];

    }
	
		
    protected function saveAccessToken($token) {
        
        $this->setState('auth_token', $token->access_token);
//        $this->setState('expires', isset($token->expires_in) ? time() + (int)$token->expires_in - 60 : 0);
        $this->access_token = $token->access_token;
        
    }
	
    /**
     * Returns the error info from json.
     */
    protected function fetchJsonError($json) {
        if (isset($json->error)) {
            return array(
                'code' => $json->error->code,
		'message' => $json->error->message,
		);
	}
	else
            return null;
    }		
        
        
}