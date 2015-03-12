<?php

class SocialAuth {
	
	/**
	 * For internal use
	 * Set this to true only if the dashboard is using Social Auth
	 * @internal
	 * 
	 * @var bool
	 */
	protected static $usingAuth = false;
	
	/**
	 * For internal use
	 * Contains the provider configuration
	 * @internal
	 * 
	 * @var array
	 */
	protected static $providerConfig = array();
	
	/**
	 * For internal use
	 * @internal
	 * 
	 * @var string
	 */
	protected static $currentProvider = null;

	/**
	 * For internal use
	 * List of Allowed Google Accounts
	 * @internal
	 * 
	 * @var array
	 */
	protected static $allowedGoogleAccounts = array();

	/**
	 * For internal use
	 * List of Allowed Google Accounts
	 * @internal
	 * 
	 * @var array
	 */
	protected static $allowedGoogleDomains = array();

	/**
	 * For internal use
	 * List of Allowed Twitter Accounts
	 * @internal
	 * 
	 * @var array
	 */
	protected static $allowedTwitterAccounts = array();

	/**
	 * For internal use
	 * List of Allowed LinkedIn Accounts
	 * @internal
	 * 
	 * @var array
	 */
	protected static $allowedLinkedInAccounts = array();

	/**
	 * For internal use
	 * List of Allowed domains
	 * @internal
	 * 
	 * @var array
	 */
	protected static $allowedLinkedinDomains = array();

	/**
	 * For internal use
	 * @internal
	 * 
	 * @var string
	 */
	protected static $sessionKey = "rfAuthValid";


	/**
	 * For internal use
	 * custom template properties. 
	 * @internal
	 * 
	 * @var string
	 */
	protected static $template_path = "/core/scripts/renderauth.php";

	/**
	 * Messages for the login page
	 * @internal
	 *
	 * @var array
	 */
	public static $_messages;

	/**
	 * This is a internal method which gets a SHA1 of all the invalidators.
	 *
	 * @internal
	 * @return string
	 */
	protected static function _getHash () {
		return sha1(json_encode(self::$providerConfig).json_encode(self::$allowedGoogleAccounts).json_encode(self::$allowedTwitterAccounts).json_encode(self::$allowedLinkedInAccounts).json_encode(self::$allowedLinkedinDomains).json_encode(self::$allowedGoogleDomains).__FILE__);

	}

	/**
	 * This is a internal method which initializes the Social Auth.
	 *
	 * @internal
	 */
	public static function _OnDashboardInit() {
		self::$sessionKey = self::_getHash();
		
		if(RFRequest::getEndpoint() === "logout"){
			self::_logout();
		}

		if(isset($_SESSION[self::$sessionKey])){
			if($_SESSION[self::$sessionKey] === true) {
				Dashboard::__setOption('__loggedInAs', $_SESSION['__loggedInAs']);
				Dashboard::__setOption('__logoutUrl', RFRequest::getDashboardUrl(array('endpoint' => 'logout')));

				return;
			} 
			else {
				self::_logout();
			}
		} 

		if(RFRequest::getEndpoint() === "startauth") {
			$_SESSION['rfSocAuthSecret'] = "".rand(0, 5000);
			self::_startAuthorization();
		}
		else if(RFRequest::getEndpoint() === "success") {
			if(self::_isAuthorized()) {
				$_SESSION[self::$sessionKey] = true;
				$url = (RFRequest::getDashboardUrl(array(), array('endpoint')));
				header("Location: $url");
			}
			else {
				$hybridauthObj = new Hybrid_Auth(self::$providerConfig);	
				$adapter = $hybridauthObj->getAdapter(self::$currentProvider);
				$user_data = $adapter->getUserProfile();
				if(self::$currentProvider === 'Twitter')
					$user = $user_data->displayName;	
				else	
					$user = $user_data->email;

				$_SESSION['auth_flash_msg'] = "$user is not authorized to view this resource";
				self::_logout();
				$url = (RFRequest::getDashboardUrl(array(), array('endpoint')));
				header("Location: $url");
			}
		}

		else {
			
			self::_logout();				
			
			global $rfMessages;
			$rfMessages = self::$_messages->asArray();


			$loginurl = RFRequest::getDashboardUrl(array('endpoint' => 'startauth'));
			require RF_FOLDER_ROOT . self::$template_path;
			exit();
	 }		
		
}


	/**
	 * This is a internal method. Returns true if the dashboard is using SocialAuth
	 *
	 * @internal
	 * @return bool
	 */	
	public static function _usingSocialAuth() {
		if(self::$usingAuth)
			return true;
		else
			return false;
	}

	public static function _getSession(){
		return $_SESSION[self::$sessionKey];
	}
	/**
	 * This is a internal method
	 * If a user is authorized, redirect to dashboard and if not, redirect to login page.
	 *
	 * @internal
	 */
	public static function _startAuthorization() {
		$hybridauthObj = new Hybrid_Auth(self::$providerConfig);
		$redirecturl = RFRequest::getDashboardUrl(array('endpoint' => 'success', 'token' => $_SESSION['rfSocAuthSecret']));
		$adapter = $hybridauthObj->authenticate(self::$currentProvider, array('hauth_return_to' => $redirecturl));
		
	}


	/**
	 * This is a internal method. Check wether a user is authorized to access the dashboard.
	 *
	 * @internal
	 * @return bool
	 */
	public static function _isAuthorized() {
		$hybridauthObj = new Hybrid_Auth(self::$providerConfig);
		$adapter = $hybridauthObj->getAdapter(self::$currentProvider);
		$isAuth = false;

		if($hybridauthObj->isConnectedWith(self::$currentProvider)){
			$user_data = $adapter->getUserProfile();
		} else {
			return false;
		}

		if(!$_GET['token'] === $_SESSION['rfSocAuthSecret']){
			return false;
		} 
	
		if(self::$currentProvider === 'Google'){
			if(count(self::$allowedGoogleAccounts) > 0){
				// skyronic@gmail
				// 
				// skyronic@gmail.com, razorflow.com
				if(in_array($user_data->email, self::$allowedGoogleAccounts)){
					$isAuth = true;
				}
			} 

			if(count(self::$allowedGoogleDomains) > 0 && $isAuth === false)
			{
				$domain = substr(strrchr($user_data->email, "@"), 1);
				if(in_array($domain, self::$allowedGoogleDomains)){
					$isAuth = true;
				}
			} 

			if(!$isAuth) {
				return false;
			} else {
				$_SESSION['__loggedInAs'] = $user_data->email;
			}
		}

		if(self::$currentProvider === 'Twitter'){
			if(count(self::$allowedTwitterAccounts) > 0){
				if(in_array($user_data->displayName, self::$allowedTwitterAccounts)){
					$isAuth = true;
					$_SESSION['__loggedInAs'] = $user_data->displayName;
					return true;
				} else {
					return false;
				}
			}
			return false;
		}

		if(self::$currentProvider === 'LinkedIn'){
			if(count(self::$allowedLinkedInAccounts) > 0){
				if(in_array($user_data->email, self::$allowedLinkedInAccounts)){
					$isAuth = true;
				} else {
					return false;
				}
			}

			if(count(self::$allowedLinkedinDomains) > 0 && $isAuth === false){
				$domain = substr(strrchr($user_data->email, "@"), 1);
				if(in_array($domain, self::$allowedLinkedinDomains)){
					$isAuth = true;
				} else {
					return false;
				}
			}

			if($isAuth) {
				$_SESSION['__loggedInAs'] = $user_data->email;
				return true;
			}
			else {
				return false;
			}
		}

		return $isAuth;
		
	}


	/**
	 * This is a internal method which clears the user session and logsout the user.
	 *
	 * @internal
	 */
	public static function _logout() {
		if(isset($_SESSION['rfSocAuthSecret'])) {
			unset($_SESSION['rfSocAuthSecret']);
			unset($_SESSION[self::$sessionKey]);
		}
		$hybridauthObj = new Hybrid_Auth(self::$providerConfig);
		$adapter = $hybridauthObj->getAdapter(self::$currentProvider);
		$adapter->logout();
			
	}

	/**
	 * Setup Google as a service provider
	 *
	 * @param GoogleAuthOptions $opts The options to setup Google as a service provider
	 */
	public static function setupGoogle ($opts) {
		if(isset(self::$currentProvider)){
			RFAssert::Exception("Only one authentication provider is supported per dashboard. You are already using ". self::$currentProvider);
		}

		$googleOpts = new GoogleAuthOptions($opts);
		$options = get_object_vars($googleOpts);

		self::$usingAuth = true;
		self::$currentProvider = 'Google';
		self::$_messages->loginButtonText = "Login with Google";

		if(isset($options['template_path']))		
			self::$template_path = $options['template_path'];
		
		$baseurl = 'http://' . $_SERVER['HTTP_HOST'] . RFConfig::get('webroot') . "/core/auth/";
		
		self::$providerConfig['base_url'] = $baseurl;
		self::$providerConfig['providers'] = array(
																				"Google" => array (
																						"enabled" => true,
																						"keys"  => array ( "id" => $options['client_id'], "secret" => $options['client_secret'] )
																				)
																			 );
	}


	/**
	 * Create a list of Google accounts which have access to the dashboard
	 *
	 * @param array $acc List of Google accounts
	 */
	public static function allowGoogleAccounts ($acc) {
		foreach($acc as $email) {
			RFAssert::MatchRegexp("The email should be valid", $email, "^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$");
			self::$allowedGoogleAccounts []= $email;
		}
	}

	public static function allowGoogleDomains ($domainNames) {

		foreach($domainNames as $name)
		{
			self::$allowedGoogleDomains []= $name;
		}
	}


	/**
	 * Setup Twitter as a service provider
	 *
	 * @param TwitterAuthOptions $opts The options to setup Twitter as a service provider
	 */
	public static function setupTwitter ($opts) {

		if(isset(self::$currentProvider)){
			RFAssert::Exception("Only one authentication provider is supported per dashboard. You are already using ". self::$currentProvider);
		}

		$twitterOpts = new TwitterAuthOptions($opts);
		$options = get_object_vars($twitterOpts);

		self::$usingAuth = true;
		self::$currentProvider = 'Twitter';
		self::$_messages->loginButtonText = "Login with Twitter";

		if(isset($options['template_path']))		
			self::$template_path = $options['template_path'];
		
		$baseurl = 'http://' . $_SERVER['HTTP_HOST'] . RFConfig::get('webroot') . "/core/auth/";
		
		self::$providerConfig['base_url'] = $baseurl;
		self::$providerConfig['providers'] = array(
																				"Twitter" => array (
																						"enabled" => true,
																						"keys"  => array ( "key" => $options['consumer_key'], "secret" => $options['consumer_secret'] )
																				)
																			 );
	}


	/**
	 * Create a list of Twitter accounts which have access to the dashboard
	 *
	 * @param array $handles List of Twitter handles
	 */
	public static function allowTwitterAccounts ($handles) {
		foreach($handles as $handle) {
			RFAssert::MatchRegexp("Should be a valid Twitter handle", $handle, "^[A-Za-z0-9_]{1,15}$");
			self::$allowedTwitterAccounts []= $handle;
		}
	}


	/**
	 * Setup LinkedIn as a service provider
	 *
	 * @param LinkedInAuthOptions $opts The options to setup LinkedIn as a service provider
	 */  
	public static function setupLinkedIn ($opts) {
		if(isset(self::$currentProvider)){
			RFAssert::Exception("Only one authentication provider is supported per dashboard. You are already using ". self::$currentProvider);
		}

		$linkedinOpts = new LinkedInAuthOptions($opts);
		$options = $linkedinOpts->asArray();

		self::$usingAuth = true;
		self::$currentProvider = 'LinkedIn';
		self::$_messages->loginButtonText = "Login with LinkedIn";

		if(isset($options['template_path']))		
			self::$template_path = $options['template_path'];

		
		$baseurl = 'http://' . $_SERVER['HTTP_HOST'] . RFConfig::get('webroot') . "/core/auth/";
		
		self::$providerConfig['base_url'] = $baseurl;
		self::$providerConfig['providers'] = array(
			"LinkedIn" => array (
				"enabled" => true,
				"keys"  => array ( "key" => $options['api_key'], "secret" => $options['secret_key'] )
				)
			);
	}


	/**
	 * Create a list of LinkedIn users who have access to the dashboard
	 *
	 * @param array $emails List of LinkedIn accounts
	 */
	public static function allowLinkedInAccounts ($emails) {
		foreach($emails as $email) {
			RFAssert::MatchRegexp("Should be a valid LinkedIn account", $email, "^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$");
			self::$allowedLinkedInAccounts []= $email;
		}
	}


	/**
	 * Create a list of allowed domains
	 *
	 * @param array $domains List of domains
	 */
	public static function allowLinkedInDomains($domains){
		foreach($domains as $domain) {
			RFAssert::MatchRegexp("Should be a valid domain name", $domain, "^[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+");
			self::$allowedLinkedinDomains []= $domain;
		}
	}

  /**
   * Set custom text for different sections in the login page.
   *
   * @param LoginPageMessages $messages 
   */ 
	public static function setLoginPageMessages($messages){

		self::$_messages = new LoginPageMessages($messages);
	}

	/**
	 * returns the current provider
	 * @internal
	 */
	public static function getProvider(){
		return "dfg";
	}
}

/**
 * This is used to customize the Google Authentication for SocialAuth. To know how to get your client id and client secret
 * :ref:`click here <setup_google>`
 */
class GoogleAuthOptions extends RFOptions {
	/**
	 * The client_id for your Google application
	 *
	 * It will look like ``123456789012.apps.googleusercontent.com``
	 *
	 * @var string
	 */
	public $client_id;

	/**
	 * The client_secret for your Google application
	 *
	 * It will look like ``aBCdeFGh4JKlmN-2Pq3S4uvW``
	 *
	 * @var string
	 */
	public $client_secret;
	
	/**
	 * The path to the custom tempalate file.
	 *
	 * @var string
	 */
	public $template_path;

	public function __inheritedClassName () {
		return __CLASS__;
	}
}


/**
 * This is used to customize the Twitter Authentication for SocialAuth. To know how to get your consumer_key and consumer secret :ref:`click here <setup_twitter>`
 *
 * @var string
 */
class TwitterAuthOptions extends RFOptions {
	/**
	 * The consumer_key for your Twitter application
	 *
	 * It will look like ``mGtjw21nd9oISMOdd9aDs``
	 * 
	 * @var string
	 */
	public $consumer_key;

	/**
	 * The consumer_secret for you Twitter appliation
	 *
	 * It will look like ``JfJGDa992EUxonkiwNDA7X2aiyOXGfI8aLLxseRabaow``
	 * 
	 * @var string
	 */
	public $consumer_secret;

	/**
	 * The path to the custom template file.
	 *
	 * 
	 * @var string
	 */
	public $template_path;

	public function __inheritedClassName () {
		return __CLASS__;
	}
}


/**
 * This is used to customize the LinkedIn Authentication for SocialAuth. To know how to get your api key and secret key :ref:`click here <setup_linkedin>`
 *
 */
class LinkedInAuthOptions extends RFOptions {
	
	/**
	 * The api_key for your LinkedIn application
	 *
	 * It will look like ``syjqef9k3col``
	 * 
	 * @var string
	 */
  public $api_key;

	/**
	 * The secret_key for your LinkedIn application
	 *
	 * It will look like ``zBzsh8iXLxlBHiman``
	 * 
	 * @var string
	 */
	public $secret_key;

	/**
	 * Path to the custom template file.
	 *
	 * 
	 * @var string
	 */
	public $template_path;

	public function __inheritedClassName () {
		return __CLASS__;
	}
}

/**
 * This is used to customize the login page. 
 */
class LoginPageMessages extends RFOptions {
	/**
	 * To set the login page title and heading
	 * @var string
	 */
	public $loginTitle = "Login Required";

	/**
	 * To set the login page message
	 * @var string
	 */
	public $loginBody = "You need to login to view the dashboard ";

	/**
	 * To set the login button text
	 * @var string 
	 */
	public $loginButtonText = 'Login';
	// protected static $loginFooter;
	
	public function __inheritedClassName ()
    {
        return __CLASS__;
    }
}

SocialAuth::$_messages = new LoginPageMessages();
