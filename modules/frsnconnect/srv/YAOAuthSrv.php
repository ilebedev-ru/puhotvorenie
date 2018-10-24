<?php
/**
  * Social Network connect modules
  * frsnconnect 0.16 by froZZen
  *
 * Register application: http://vkontakte.ru/editapp?act=create&site=1
 * 
  */

require_once 'frOAuth2Srv.php';

/**
 * Yandex provider class.
 * 
  Яндекс.Логин

    Адрес электронной почты
    Дата рождения
    Имя пользователя, ФИО, пол


 * 
 */
class YAOAuthSrv extends frOAuth2Srv {	

    protected $client_id = '';
    protected $client_secret = '';
//    protected $scope = 'notify';
    protected $providerOptions = array(
        'authorize' => 'https://oauth.yandex.ru/authorize',
	'access_token' => 'https://oauth.yandex.ru/token'
	);

//    protected $uid = null;
        
    private function GetSex($sex) {
        switch ($sex){
            case 'male':
                return 1;
                break;
            case 'female':
                return 2;
                break;
            default:
                return 9;
        }  
    } 
/*
    private function GetBDate($bdate) {
        // bdate format: "23.11.1981" or "21.9" (if year hide)
        $bday = explode('.', $bdate);
        $result = (!isset($bday[2]) ? '' : (int)($bday[2]).'-'.(int)($bday[1]).'-'.(int)($bday[0]));
        return $result; 
   }
*/


    protected function fetchAttributes() {
        

        $info = $this->makeRequest(
            'https://login.yandex.ru/info', 
            array(
                'query' => array(
                    'oauth_token' => $this->access_token,
                ),
            )
        );        
        

//        $this->errors[] = print_r($info, true);

	$this->attributes['id'] = $info->id;
	if (isset($info->default_email))
            $this->attributes['email'] =  $info->default_email;
	$this->attributes['birthday'] =  (isset($info->birthday)) ? $info->birthday : '';
	$this->attributes['id_gender'] =  (!isset($info->sex)) ? 9 : $this->GetSex($info->sex);
	$this->attributes['name'] = (isset($info->display_name)) ? $info->display_name : 'Yandex_User';
        if (isset($info->real_name)) {
            $realname = explode(' ', $info->real_name);
            
            $this->attributes['firstname'] = $realname[0];
            if (isset($realname[1]))
                $this->attributes['lastname'] = $realname[1];
        }    
        
/*        
	$this->attributes['name'] = (!isset($info->nickname))? 'VK User' : $info->nickname;
	$this->attributes['url'] = 'http://vk.com/id'.$info->uid;
//		$this->attributes['email'] =  '';
        $id_country = $this->GetCountryName($info->country);
        if ($id_country > 0) 
            $this->attributes['id_country'] = $id_country;
	if (isset($info->city)) 
           	$cityname = $this->GetCityName($info->city);
        if (strlen($cityname) > 0) 
            $this->attributes['city'] = $cityname;
        
//        if (isset($info->contacts)) {
//            foreach ($info->contacts as $key=>$value) {
//                if ($key == 'mobile_phone')
//                    $this->attributes['phone_mobile'] = $value;
//                if ($key == 'home_phone')
//                    $this->attributes['phone'] = $value;
//            }
//        }
 * 
 */
    }
	
    /**
     * Returns the url to request to get OAuth2 code.
     */
    protected function getCodeUrl($redirect_uri) {

        $redirect_state = '';   
        if ($this->hasState('redirect_params'))
            $redirect_state = $this->getState('redirect_params');
             
        if (strpos($redirect_uri, '?') !== false) {
            $url = explode('?', $redirect_uri);
            $url[1] = preg_replace('#[/]#', '%2F', $url[1]);
            $redirect_uri = $url[0];
            if (!strlen($redirect_state))
                $redirect_state .= 'state=snLogin_ya_id&'.$url[1];
	}

        $this->setState('redirect_uri', $redirect_uri);
        $this->setState('redirect_params', $redirect_state);
//	$url = parent::getCodeUrl($redirect_uri);
        $url = $this->providerOptions['authorize'].'?client_id='.$this->client_id.'&response_type=code';
        $url .= '&state='.strtr(base64_encode($redirect_state), '+/=', '-_,');

        return $url;
        
        
        
/*        
        if (strpos($redirect_uri, '?') !== false) {
            $url = explode('?', $redirect_uri);
            $url[1] = preg_replace('#[/]#', '%2F', $url[1]);
            $redirect_uri = implode('?', $url);
	}
		
	$this->setState('redirect_uri', $redirect_uri);
        
//	$url = parent::getCodeUrl($redirect_uri).'&state=snLogin_ya_id';
        
        $url = $this->providerOptions['authorize'].'?client_id='.$this->client_id.'&response_type=code&state=snLogin_ya_id';
        
//	if (isset($_GET['js']))
//            $url .= '&js=true';
		
	return $url;
*/
    }


    protected function getAccessToken($code) {

//        $url = $this->getTokenUrl($code);

        $url = $this->providerOptions['access_token'];
        
        
                
//                .'?client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&code='.$code;
        $post_data = 'grant_type=authorization_code&code='.$code.'&client_id='.$this->client_id.'&client_secret='.$this->client_secret;
        
        return $this->makeRequest($url, array('data' => $post_data));
        
    }
    
/*    
    protected function getTokenUrl($code) {
        
        return parent::getTokenUrl($code).'&redirect_uri='.urlencode($this->getState('redirect_uri'));
        
    }
*/	
    /**
     * Save access token to the session.
     */
    protected function saveAccessToken($token) {

        $this->setState('auth_token', $token->access_token);
//	$this->setState('uid', $token->user_id);
        if (isset($token->expires_in))
            $this->setState('expires', time() + $token->expires_in - 60);
        else
            $this->setState('expires', time() + 60*24*365);
        
//	$this->uid = $token->user_id;
	$this->access_token = $token->access_token;
        
    }
	
    /**
     * Restore access token from the session.
     * @return boolean whether the access token was successfuly restored.
     */
/*    
    protected function restoreAccessToken() {
        
        if ($this->hasState('uid') && parent::restoreAccessToken()) {
            $this->uid = $this->getState('uid');
        
            return true;
	}
	else {
            $this->uid = null;
            return false;
	}
        
    }
*/
    /**
     * Returns the error info from json.
     */
    protected function fetchJsonError($json) {

        if (isset($json->error)) {
            return array(
                'code' => $json->error->error_code,
		'message' => $json->error->error_msg,
		);
	}
        else
            return null;
    }
    
}
