<?php

class Github {

    public $LOGIN_URL;
    public $CLIENT_ID = 'h34345j345l345'; 
    public $CLIENT_SECRET = 'h34345j345l345h34345j345l345h34345j345l345h34345j345l345'; 
    public $CLIENT_REDIRECT_URL = 'https://domain.com/account/github-redirect';
    public $authorizeURL = "https://github.com/login/oauth/authorize"; 
    public $TOKEN_URL = "https://github.com/login/oauth/access_token"; 
    public $API_URL_BASE = "https://api.github.com"; 

    function __construct()
    {
        $this->LOGIN_URL = $this->authorizeURL.'?client_id='. $this->CLIENT_ID. "&redirect_url=".$this->CLIENT_REDIRECT_URL."&scope=user";
    }

    public function getAuthorizeURL($state){ 
        return $this->authorizeURL . '?' . http_build_query([ 
            'client_id' => $this->CLIENT_ID, 
            'redirect_uri' => $this->CLIENT_REDIRECT_URL, 
            'state' => $state, 
            'scope' => 'user:email' 
        ]); 
    } 
     
    /** 
     * Exchange token and code for an access token 
     */ 
    public function getAccessToken($state, $oauth_code){ 
        $token = self::apiRequest($this->TOKEN_URL . '?' . http_build_query([ 
            'client_id' => $this->CLIENT_ID, 
            'client_secret' => $this->CLIENT_SECRET, 
            'state' => $state, 
            'code' => $oauth_code 
        ])); 

        if(isset($token->access_token)){
            return $token->access_token; 
        } else {
            return $token;
        }
    } 
     
    /** 
     * Make an API request 
     * 
     * @return API results 
     */ 
    public function apiRequest($access_token_url){ 
        $apiURL = filter_var($access_token_url, FILTER_VALIDATE_URL)?$access_token_url:$this->API_URL_BASE.'user?access_token='.$access_token_url; 
        $context  = stream_context_create([ 
          'http' => [ 
            'user_agent' => 'CodexWorld GitHub OAuth Login', 
            'header' => 'Accept: application/json' 
          ] 
        ]); 
        $response = file_get_contents($apiURL, false, $context); 
         
        return $response ? json_decode($response) : $response; 
    } 
 
    /** 
     * Get the authenticated user 
     * 
     * @returns object 
     */ 
    public function getAuthenticatedUser($access_token) { 
        $apiURL = $this->API_URL_BASE . '/user'; 
         
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $apiURL); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: token '. $access_token)); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CodexWorld GitHub OAuth Login'); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); 
        $api_response = curl_exec($ch); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);          
          
        if($http_code != 200){ 
            if (curl_errno($ch)) {  
                $error_msg = curl_error($ch);  
            }else{ 
                $error_msg = $api_response; 
            } 
            throw new Exception('Error '.$http_code.': '.$error_msg); 
        }else{ 
            return json_decode($api_response); 
        } 
    } 
}


?>