<?php

class PSMS
{
    const GATE = 'gate.prostor-sms.ru';
    
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /*
        * функция передачи сообщения
    */
    public function send($phone, $text, $sender = false, $wapurl = false )
    {
        $fp = fsockopen('gate.prostor-sms.ru', 80, $errno, $errstr);
        if (!$fp) {
            return "errno: $errno \nerrstr: $errstr\n";
        }

        fwrite($fp, "GET /send/" . "?phone=" . rawurlencode($phone) . "&text=" . rawurlencode($text) . ($sender ? "&sender=" . rawurlencode($sender) : "") . ($wapurl ? "&wapurl=" . rawurlencode($wapurl) : "") . "  HTTP/1.0\n");

        fwrite($fp, "Host: " . 'gate.prostor-sms.ru' . "\r\n");
        if ($this->login != "") {
            fwrite($fp, "Authorization: Basic " . base64_encode($this->login. ":" . $this->password) . "\n");
        }

        fwrite($fp, "\n");
        $response = "";
        while(!feof($fp)) {
            $response .= fread($fp, 1);
        }

        fclose($fp);
        list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
        return $responseBody;
    }
 
    /*
        * функция проверки состояния отправленного сообщения
    */
    public function status($sms_id)
    {
        $fp = fsockopen('gate.prostor-sms.ru', 80, $errno, $errstr);
        if (!$fp) {
            return "errno: $errno \nerrstr: $errstr\n";
        }

        fwrite($fp, "GET /status/" . "?id=" . $sms_id . "  HTTP/1.0\n");
        fwrite($fp, "Host: " . 'gate.prostor-sms.ru' . "\r\n");
        if ($this->login != "") {
            fwrite($fp, "Authorization: Basic " . base64_encode($this->login. ":" . $this->password) . "\n");
        }

        fwrite($fp, "\n");
        $response = "";
        while(!feof($fp)) {
            $response .= fread($fp, 1);
        }

        fclose($fp);
        list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
        return $responseBody;
    }
    
    public function sendSms($phone, $text, $sender = false, $wapurl = false)
    {
        $method = '/send/';
        $params = array(
            'phone' => rawurldecode($phone),
            'text' => rawurlencode($text),
        );
        if ($sender) {
            $params['sender'] = rawurlencode($sender);
        }
        if ($wapurl) {
            $params['wapurl'] = rawurlencode($wapurl);
        }
        return $this->getResponse($method, $params);
    }
    
    public function getStatus($sms_id)
    {
        return $this->getResponse('/status/', array('id' => $sms_id));
    }
    
    public function getAvailableSenders()
    {
        return $this->getResponse('/messages/v2/senders/');
    }
    
    public function getBalance()
    {
        return $this->getResponse('/messages/v2/balance/');
    }
    
    protected function getResponse($method, $params = array())
    {
        if (!empty($params)) {
            $query = urldecode(http_build_query($params));
        }
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL . 'Authorization: Basic ' . base64_encode($this->login.":".$this->password). PHP_EOL,
                'content' => $query,
            ),
        ));
        $url .= 'http://'.self::GATE.$method;

        return Tools::file_get_contents($url, false, $context);
    }
}