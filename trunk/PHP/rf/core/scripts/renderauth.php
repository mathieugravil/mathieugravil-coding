<?php global $rfMessages;?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title><?php echo $rfMessages['loginTitle'] ?></title>

    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <link rel="stylesheet" type="text/css" href="../static/installer/css/bootstrap.min.css" />
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="span12">
				<h1><?php echo $rfMessages['loginTitle']?></h1> <br />

				<?php if(isset($_SESSION['auth_flash_msg'])) {
					echo "<p class='alert alert-error'>" . $_SESSION['auth_flash_msg'] . "</p>";
					unset($_SESSION['auth_flash_msg']);
				} ?>

				<p><?php echo $rfMessages['loginBody']?></p> 
				<a class="btn btn-primary" href="<?php echo $loginurl;?>"><i class="icon-user icon-white"></i><?php echo " " . $rfMessages['loginButtonText'] ?></a>
			
			</div>
		</div>
	</div>
	<script type="text/javascript" src="../static/installer/js/jquery.min.js"></script>
	<script type="text/javascript" src="../static/installer/js/bootstrap.min.js"></script>
</body>
</html>
