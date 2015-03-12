<?php

class GoogleAnalyticsHelper {
	public function __construct() {
    //session_start();
	}
 
  protected $access_token;
  protected $config;

  /**
   * Setup the configurtaion data for Hybrid Auth Google provider
   * 
   * @param  array  $options array
   */
  public function setupGoogleAnalytics($options) {

  	$baseurl = 'http://' . $_SERVER['HTTP_HOST'] . RFConfig::get('webroot') . "/core/auth/";

  	$this->config = array (
  		"base_url" => $baseurl,
  		"providers" => array(
  			"Google" => array (
  				"enabled" => true,
  				"keys" => array ( "id" => $options['client_id'], "secret" => $options['client_secret'] ),
  				"scope" => "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/analytics.readonly"
  			)
  		)
  	);
  }
  
  /**
   * If we have a refresh token, then exchange it with Google to get a new access_token
   * if not, ask the user the login as we have no other means to get the access_token
   * Refresh token is sent to us only the first time the user logins (ie after authorizing the app)
   * we need to put the refresh token in some persistent storage.
   * If we lose the refresh token, the user has to revoke the access to the app and authorize it again.
   * 
   * @param  
   */
  public function refreshToken($refresh_token) {
    $url = 'https://accounts.google.com/o/oauth2/token';
    $fields = array(
      'refresh_token' => $refresh_token,
      'client_id' => $this->config['providers']['Google']['keys']['id'],
      'client_secret' => $this->config['providers']['Google']['keys']['secret'],
      'grant_type' => 'refresh_token'
      );
    $fields_string = "" ;
    foreach($fields as $key=>$value) { 
      $fields_string .= $key .'='. $value .'&'; 
    }

    $fields_string = rtrim($fields_string, '&');

    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response);
    $_SESSION['access_token'] = $result->access_token;
    return $result->access_token;

  }

  /**
   * Setup the query to be made to the analytics API
   * First verify if the token is valid, if it is not, refresh the token
   * 
   * @param  array  $options array
   */
  public function setupQuery($options) {
    $profileID = $options['profile_id'];
  	$startDate = $options['startDate'];
  	$endDate = $options['endDate'];
    if(isset($options['dimensions']))
      $query = "&metrics=" . $options['metrics'] . "&dimensions=" . $options['dimensions'];
    else
      $query = "&metrics=" . $options['metrics'];  

    $access_token = "";

    $access_token_valid = true;

    if(isset($_SESSION['access_token']))
    {
      $access_token = $_SESSION['access_token'];
      $validationURL = "https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=$access_token";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $validationURL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($response);

      if(isset($result->error)){
        $access_token = $this->refreshToken($options['refresh_token']);
      }
    }
    else {
      $access_token = $this->refreshToken($options['refresh_token']);
    }


    $queryURL = "https://www.googleapis.com/analytics/v3/data/ga";
    $queryURL .= "?ids=ga:$profileID&start-date=$startDate&end-date=$endDate";
    $queryURL .= $query;
    $queryURL .= "&prettyPrint=true&access_token=$access_token";
      //var_dump($queryURL); die();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $queryURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    $queryResult = json_decode($result);

    if(isset($queryResult->error)){
      echo "Oopsie! there was an error!";
      echo "<br />Reason: " . $queryResult->error->errors[0]->reason;
      echo "<br />Message: ". $queryResult->error->errors[0]->message;
      die();
    }

    $metric = explode(",", $options['metrics']);
    $result = array();
    foreach ($metric as $item) {
      $result[$item] = $queryResult->totalsForAllResults->$item;
    }

    return $result;
  }


	/**
	 * Return the bounce rate for the last 30 days.
	 * 
	 * @param  array  $options array
	 */
	public function createVisitsDataSource($options = array()) {
		$dataSource = new RFArrayDataSource();

		$dataSource->setSchema (array(
			'startDate' => array(
				'type' => _RF_DTYPE_TEXT
			),
      'endDate' => array(
        'type' => _RF_DTYPE_TEXT
      ),
			'visitors' => array(
				'type' => _RF_DTYPE_NUM
			),
      'newVisits' => array(
        'type' => _RF_DTYPE_NUM
      ),
      'percentNewVisits' => array(
        'type' => _RF_DTYPE_NUM
      ),
      'visits' => array(
        'type' => _RF_DTYPE_NUM
      ),
      'bounces' => array(
        'type' => _RF_DTYPE_NUM
      ),
      'entranceBounceRate' => array(
        'type' => _RF_DTYPE_NUM
      ),
      'visitBounceRate' => array(
        'type' => _RF_DTYPE_NUM
      ),
      'timeOnSite' => array(
        'type' => _RF_DTYPE_NUM
      ),
      'avgTimeOnSite' => array(
        'type' => _RF_DTYPE_NUM
      )
		));

    $options['metrics'] = 'ga:visitors,ga:newVisits,ga:percentNewVisits,ga:visits,ga:bounces,ga:entranceBounceRate,ga:visitBounceRate,ga:timeOnSite,ga:avgTimeOnSite';
    $results = $this->setupQuery($options);	
    

		$dataSource->setData(array(
		 	array(
        'startDate' => $options['startDate'], 
        'endDate' => $options['endDate'],
        'visitors' => (int) $results['ga:visitors'],
        'newVisits' => (int) $results['ga:newVisits'],
        'percentNewVisits' => (float) $results['ga:percentNewVisits'],
        'visits' => (int) $results['ga:visits'],
        'bounces' => (int) $results['ga:bounces'],
        'entranceBounceRate' => (float) $results['ga:entranceBounceRate'],
        'visitBounceRate' => (float) $results['ga:visitBounceRate'],
        'timeOnSite' => (int) $results['ga:timeOnSite'],
        'avgTimeOnSite' => (float) $results['ga:avgTimeOnSite']

      )
    ));
	 return $dataSource;
	}

  //Now that we pass the profile ID during configuration, I guess we no longer require these methods.
  public function getAccount(){
    $accountsURL = "https://www.googleapis.com/analytics/v3/management/accounts?access_token=$access_token";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $accountsURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $accountsList = json_decode($result);

    var_dump($accountsList); die();

    $this->setToken($access_token['access_token']);
    $firstAccountID = $accounts_list->items[0]->id;
    $firstProfile = $this->getProfileForAccount($firstAccountID); //returns profile ID

    $this->profileID = $firstProfile;
  }

  public function getProfileForAccount($accountID){
    $webPropURL = "https://www.googleapis.com/analytics/v3/management/accounts/$accountID/webproperties?access_token=ya29.AHES6ZQyNGNOH20yaA8_ApJV_G1Ol7I9YJ4gzq5XWA2m7s9grVRslg";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webPropURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    $webPropertiesList = json_decode($result);
    $firstWebPropertyID = $webPropertiesList->items[0]->id; 
  
    $profileURL = "https://www.googleapis.com/analytics/v3/management/accounts/$accountID/webproperties/$firstWebPropertyID/profiles?access_token=ya29.AHES6ZQyNGNOH20yaA8_ApJV_G1Ol7I9YJ4gzq5XWA2m7s9grVRslg";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $profileURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    $profilesList = json_decode($result);
    $firstProfileID = $profilesList->items[0]->id;

    return $firstProfileID;

  } 

  public function setToken($tokenString) {
    $this->$tokenString = $tokenString;
  }

	
}