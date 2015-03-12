<?php
session_start();

if(isset($_GET['login'])){
		
		require_once('../auth/Hybrid/Auth.php');	

		
		$scriptName = $_SERVER['SCRIPT_NAME'];
		$webRoot = substr($scriptName, 0, strlen($scriptName) - strlen('/core/scripts/renderanalytics.php'));


		$baseurl = 'http://' . $_SERVER['HTTP_HOST'] . $webRoot .  "/core/auth/";

		if(!isset($_SESSION['client_id']))
		{
			$_SESSION['client_id'] = $_POST['client_id'];
			$_SESSION['client_secret'] = $_POST['client_secret'];
		}

		$config = array (
			"base_url" => $baseurl,
			"providers" => array(
				"Google" => array (
					"enabled" => true,
					"keys" => array ( "id" => $_SESSION['client_id'], "secret" => $_SESSION['client_secret']),
					"scope" => "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/analytics.readonly"
				)
			)
		);

		$hybridauth = new Hybrid_Auth( $config );

		$adapter = $hybridauth->authenticate("Google"); 
		$accounts_list = $adapter->api()->api( "https://www.googleapis.com/analytics/v3/management/accounts" ); 

		if(isset($accounts_list->error)){
			$adapter->logout();
			die("Logged out due to access_token expiry");
		}
		$access_token = $adapter->getAccessToken();
		//var_dump($access_token); die();
		$_SESSION['access_token'] = $access_token['access_token'];
		$_SESSION['refresh_token'] = $access_token['refresh_token'];
		setcookie("access_token", $access_token['access_token']);
		//var_dump($accounts_list->items); die();
}
?><!doctype html>
<html>
	<head>
		<title> Google Analytics Helper </title>
		<link rel="stylesheet" type="text/css" href="../../static/installer/css/bootstrap.min.css" />
		<?php if(isset($_SESSION['access_token']))
		{?>
		<script>
			window.rfAccessToken = '<?php echo $_SESSION['access_token']; ?>';
		</script>
		<?php } ?>
	</head>

	<body>
	 <div class="container">	
		<h3> Google Analytics Token Helper</h3>
			<?php if(isset($_SESSION['access_token']) and isset($_GET['login'])) { ?>
				<div class="row">
					<div class="span4">
						<form class="form-horizontal">
							<div class="control-group">
								<label>Account Name</label>
								<select id='accounts'>
									<option>Select an account</option>
										<?php	foreach ($accounts_list->items as $account) { 
									  		echo "<option value=$account->id>$account->name</option>"; 
										} ?>
								</select>
							</div>

							<div class="control-group">
								<label>Web Property</label>
								<select id="webProperties"></select>
							</div>

							<div class="control-group">
								<label>Profile</label>
								<select id="profiles"></select>
							</div>
						</form>
					</div>
					<div class="span4">
						<br />
						<table id="info" class="table table-striped">
							<tbody>
								<tr><td>Refresh Token</td><td><?php echo $_SESSION['refresh_token']; ?></td></tr>
								<tr><td>Profile Id</td><td><span class='rfProfileIdText'>Please select a profile</span></td></tr>
							</tbody>
						</table>
					</div>
				</div>
			<?php } else { ?>
		  <p> Please log in with your Google account here to fetch your analytics details.</p>
		  <form class="form-horizontal" method="POST" action="?login">
		  	<div class="control-group">
		  		<label class="control-label" for="client_id">Client ID</label>
		  		<div class="controls">
		  			<input type="text" id="client_id" placeholder="" name='client_id' value="471636057128.apps.googleusercontent.com">
		  		</div>
		  	</div>
		  	<div class="control-group">
		  		<label class="control-label" for="client_secret">Client Secret</label>
		  		<div class="controls">
		  			<input type="text" id="client_secret" name='client_secret' placeholder="" value="jYUHvUJbQKZMuM-8He451JI6">
		  		</div>
		  	</div>
		  	<div class="control-group">
		  		<div class="controls">
		  			<button type="submit" class="btn">Start</button>
		  		</div>
		  	</div>
		  </form>
		  <?php } ?>
		</div>
		<script type="text/javascript" src="../../static/installer/js/jquery.min.js"></script>
		<script type="text/javascript" src="../../static/installer/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="../../static/installer/js/analytics.js"></script>
	</body>
</html>
