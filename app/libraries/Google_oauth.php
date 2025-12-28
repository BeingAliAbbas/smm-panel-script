<?php
require "Google/autoload.php";

class google_oauth{
    private $ClientID;
    private $ClientSecret;
    private $client;
    private $redirect_url;
    private $access_token;

<<<<<<< HEAD
    public function __construct($params = array()){
        // Extract parameters from the array (CodeIgniter passes params as an array)
        $client_id = isset($params['client_id']) ? $params['client_id'] : null;
        $client_secret = isset($params['client_secret']) ? $params['client_secret'] : null;
        $redirect_url = isset($params['redirect_url']) ? $params['redirect_url'] : "auth/google";
        
=======
    public function __construct($client_id = null, $client_secret = null, $redirect_url = "auth/google"){
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        $this->client = new Google_Client();
        $this->client->setAccessType("offline");
        $this->client->setApprovalPrompt("force");
        $this->client->setRedirectUri(cn($redirect_url));
        $this->client->setClientId($client_id);
        $this->client->setClientSecret($client_secret);
        $this->client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
        $this->redirect_url = $redirect_url;
    }

    function create_login_url(){
        return $this->client->createAuthUrl();
    }

    function get_access_token(){
        try {
            if(get("code")){
<<<<<<< HEAD
                // Authenticate with the authorization code
                $result = $this->client->authenticate(get("code"));
                
                // Get the access token
                $token = $this->client->getAccessToken();
                
                if (!$token) {
                    // Authentication failed - no token received
                    redirect(cn('auth/login'));
                    return false;
                }
                
                $this->access_token = $token;
                return $token;
            }else{
                // No authorization code provided
                redirect(cn('auth/login'));
                return false;
            }
            
        } catch (Exception $e) {
            // Log error for debugging (optional)
            // error_log('Google OAuth Error: ' . $e->getMessage());
            redirect(cn('auth/login'));
            return false;
=======
                $this->client->authenticate(get("code"));
                $oauth2 = new Google_Service_Oauth2($this->client);
                $token = $this->client->getAccessToken();
                $this->access_token = $token;
                return $token;
            }else{
                redirect(cn($this->redirect_url));
            }
            
        } catch (Exception $e) {
            redirect(cn($this->redirect_url));
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        }
    }

    function get_user_info(){
        try {
<<<<<<< HEAD
            if (!$this->access_token) {
                // No access token available
                return false;
            }
            
            // Set the access token before making API calls
            $this->client->setAccessToken($this->access_token);
            
            // Create OAuth2 service and get user info
            $oauth2 = new Google_Service_Oauth2($this->client);
            $userinfo = $oauth2->userinfo->get();
            
            return $userinfo;
        } catch (Exception $e) {
            // Log error for debugging (optional)
            // error_log('Google User Info Error: ' . $e->getMessage());
=======
            $oauth2 = new Google_Service_Oauth2($this->client);
            $this->client->setAccessToken($this->access_token);
            $userinfo = $oauth2->userinfo->get();
            return $userinfo;
        } catch (Exception $e) {
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            return false;
        }
    }
}