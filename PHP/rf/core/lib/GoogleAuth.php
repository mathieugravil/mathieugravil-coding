<?php

// class GoogleAuth {
// 	// public methods
	

// 	/**
// 	 * Restrict the dashboard to a particular list of users
// 	 * 
// 	 * @param array $userList an array of email addresses
// 	 */
// 	public static function AllowUsers($userList) {
// 		foreach($userList as $email) {
// 			// TODO: Check that the email is in correct format.
// 			RFAssert::MatchRegexp("The email should be valid", $email, "email_regex");
// 			self::$allowedUsers []= $email;
// 		}

// 		self::BindEvents();
// 	}

// 	/**
// 	 * Restrict to users only who have a Google Apps account with this
// 	 * domain name:
// 	 *
// 	 * Ex. "funflowers.com" allows "joe@funflowers.com" and "jane@funflowers.com"
// 	 * @param string $domainName The text after "@" in the email
// 	 */
// 	public static function AllowUsersFromGoogleAppsDomain ($domainName) {
// 		self::$allowedDomains []= $domainName;
// 		self::BindEvents();
// 	}

// 	public static function GetLoggedInUserEmail() {
// 		return FALSE;
// 	}

// 	public static function setClientID($appClientID) {
// 		self::$clientID = $appClientID;
// 	}

// 	public static function setConfiguration($options){
// 		foreach ($options as $key => $value) {
// 			switch ($key) {
// 				case 'provider':
// 					self::$provider = $value;
// 					break;
// 				case 'client_id':
// 					self::$clientID = $value;
// 				case 'client_secret':
// 					self::$clientSecret = $value;
// 					break;
// 				default:
// 					//RFUtil::Exception("Invalid Options!");
// 					break;
// 			}//switch
// 		}//foreach
// 	}

// 	protected static $clientID = null;

// 	protected static $clientSecret = null;

// 	protected static $provider = null;

// 	protected static $config = null;

// 	protected static $allowedUsers = array();

// 	protected static $allowedDomains = array();

// 	protected static $bindEventFinished = false;

// 	protected static $bindObj = null;


// 	protected static function BindEvents () {
// 		if(self::$bindEventFinished) {
// 			return;
// 		}
// 		self::$bindEventFinished = true;

// 		self::$bindObj = new GoogleAuth();
// 		RFMessageBroker::bind('dashboardBeforeProcess', self::$bindObj, '__checkAuth');

// 		RFMessageBroker::bindUnsafe('onGoogleAuthUserSignIn', self::$bindObj, '__onSignIn');
// 	}
// 	public function __onSignIn($params) {
// 		RFUtil::emitJSON(array(
// 			'result' => 'echoooo',
// 			'params' => $params
// 		));
// 	}

// 	public function __checkAuth () {
// 		$authSuccess = false;

// 		if(isset($_GET['auth'])){
// 			$baseurl = 'http://' . $_SERVER['HTTP_HOST'] . RFConfig::get('webroot') . "/core/vendor/Classes/hybridauth/";
// 			if(self::$provider == 'Google') {
// 				$config = array(
// 					"base_url" => $baseurl,
// 					"providers" => array(
// 						"Google" => array (
// 							"enabled" => true,
// 							"keys"    => array ( "id" => self::$clientID, "secret" => self::$clientSecret )
// 						)
// 					),
// 					"debug_mode" => true,
// 					"debug_file" => "/tmp/auth.txt"
// 			  );	

// 				$hybridauth = new Hybrid_Auth( $config );
// 				$redirecturl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?success=true";
// 				$adapter = $hybridauth->authenticate(self::$provider, array('hauth_return_to' => $redirecturl));
// 				//$user_data = $adapter->getUserProfile();
// 				//$hybridauth->redirect($redirecturl);
// 				//RFUtil::RequestRedirect($redirecturl);
// 			}	
// 		}


// 		if(isset($_GET['success'])){
// 			$authSuccess = true;
// 		}

// 		if(isset($_GET['logout'])){

// 			$baseurl = 'http://' . $_SERVER['HTTP_HOST'] . RFConfig::get('webroot') . "/core/vendor/Classes/hybridauth/";
// 			if(self::$provider == 'Google') {
// 				$config = array(
// 					"base_url" => $baseurl,
// 					"providers" => array(
// 						"Google" => array (
// 							"enabled" => true,
// 							"keys"    => array ( "id" => self::$clientID, "secret" => self::$clientSecret )
// 						)
// 					),
// 					"debug_mode" => true,
// 					"debug_file" => "/tmp/auth.txt"
// 			  );	

// 				$hybridauth = new Hybrid_Auth( $config );
// 				$adapter = $hybridauth->getAdapter( self::$provider );
// 				$authSuccess = false;
// 				$adapter->logout();
				
// 				//$loginurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
// 				//RFUtil::RequestRedirect($loginurl);
				
// 		}
// 	}	
// 		if($authSuccess) {
// 			return;
// 		}


// 		$response = array(
// 			'result' => 'needsAuth',
// 			'params' => array(
// 				'authType' => 'google',
// 				'clientID' => self::$clientID
// 				)
// 			);

// 				// This stops execution.
// 		RFUtil::emitJSON($response);
// 	}
// }

		// TODO: Check the authentication.
		//Our Implementation of Google OAuth2
/*		
		session_start();
		if(!isset($_SESSION['access_token'])){
			if(isset($_GET['code']))	{

				$url = 'https://accounts.google.com/o/oauth2/token';
				$redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
				$fields = array(
					  'code' => $_GET['code'],
					  'client_id' => self::$clientID,
					  'client_secret' => "jYUHvUJbQKZMuM-8He451JI6",
					  'redirect_uri' => $redirect_url,
					  'grant_type' => "authorization_code"
				);

				$fields_string = null;
				foreach($fields as $key=>$value) { 
					$fields_string .= $key.'='.$value.'&'; 
				}

				$fields_string = rtrim($fields_string,'&');

				$ch = curl_init();

				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch,CURLOPT_POST,count($fields));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

				$result = curl_exec($ch);
				$res = json_decode($result, true);
				curl_close($ch);

				if(isset($res['access_token']))
					$_SESSION['access_token'] = $res['access_token'];
				else if(isset($res['error']))
					$_SESSION['isAuthorized'] = false;
		  }
		else
		  $_SESSION['isAuthorized'] = false;
	}

	if(isset($_SESSION['access_token'])){
		//Validate the Token

		$req = curl_init();
    $url = "https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=" . $_SESSION['access_token'];

    curl_setopt($req, CURLOPT_URL,$url );
    curl_setopt($req, CURLOPT_RETURNTRANSFER, 1 );
    $tokenValidator = curl_exec($req);
    curl_close($req);

    $tokenValidator = json_decode($tokenValidator, true);

    if(isset($tokenValidator['expires_in']) && $tokenValidator['expires_in'] > 1) {
    	$_SESSION['isAuthorized'] = true;
    }
    else {
    	unset($_SESSION['access_token']);
    	$_SESSION['isAuthorized'] = false;
    }
  }*/

		/*//For Google Plus
		if(isset($_COOKIE['isAuthorized']) && $_COOKIE['isAuthorized'] === 'true') {
			$authSuccess = true;
			$_SESSION['isAuthorized'] = true;
		}
		else {
			$_SESSION['isAuthorized'] = false;
			$authSuccess = false;
		}*/

		

