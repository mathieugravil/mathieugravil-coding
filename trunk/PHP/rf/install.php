<?php
$valid = array();
$valid['phpversion'] = false;
$valid['pdo'] = false;
$valid['sqlite'] = false;
$valid['confwritable'] = false;

$optional = array();
$optional['mysql'] = false;
$optional['noexistingconf'] = false;

$installable = true;

if(version_compare(phpversion(), '5.2.0', '>=')){
    $valid['phpversion'] = true;
}

if(class_exists('PDO'))
{
    $valid['pdo'] = true;

    if(in_array('sqlite', PDO::getAvailableDrivers()))
    {
        $valid['sqlite'] = true;
    }
    if(in_array('mysql', PDO::getAvailableDrivers()))
    {
        $optional['mysql'] = true;
    }
}

$configPath = dirname(__FILE__).DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR;

if(is_writeable('config'))
{
    $valid['confwritable'] = true;
}

if(!file_exists($configPath."config.php"))
{
    $optional['noexistingconf'] = true;
}

foreach($valid as $key=>$value) {
    if($value === false)
    {
        $installable = false;
    }
}

$installFlag = false;

if(isset($_GET['install'])) {
    if($_GET['install'] === 'true') {
        $installFlag = true && $installable;
    }
}

$installFinished = false;

if($installFlag) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $webRoot = substr($scriptName, 0, strlen($scriptName) - strlen('/install.php'));

    $configContents = file_get_contents($configPath."config.sample.php");
    $configContents = str_replace("{{webroot}}", $webRoot, $configContents);

    $handle = fopen($configPath."config.php", "w");
    fwrite($handle, $configContents);
    fclose($handle);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Install RazorFlow Dashboards for PHP</title>
    <link href="static/installer/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 0px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
    </style>
    <link href="static/installer/css/bootstrap-responsive.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>

<body>

<div class="container">
    <div class="row">
            <h1>RazorFlow PHP</h1>
    </div>
    <?php if($installFlag) : ?>
    <div class="row">
        <p>RazorFlow PHP was successfully installed.</p>
        <p><h3>Next Steps</h3></p>
        <ol>
            <li>For added security, please delete the "install.php" file</li>
            <li>Learn how to build your first dashboard with our Quick Start Guide</li>
            <li>Explore our pre-built demos</li>
        </ol>
    </div>
    <?php else: ?>
    <div class="row">
        <p>You're just a few minutes away from building interactive, mobile-friendly dashboards.</p>
        <h3>Checking requirements</h3>

        <?php if($valid['phpversion']):?>
        <div class="alert alert-success">
            <strong>Checking PHP Version:</strong> Your PHP version supports RazorFlow
        </div>
        <?php else: ?>
        <div class="alert alert-error">
            <strong>Checking PHP Version:</strong> Please upgrade your PHP version to 5.2 or above (5.3 is recommended) to use RazorFlow
        </div>
        <?php endif; ?>

        <?php if($valid['pdo']):?>
        <div class="alert alert-success">
            <strong>Checking PDO:</strong> PDO is installed and configured properly
        </div>
        <?php else: ?>
        <div class="alert alert-error">
            <strong>Checking PDO:</strong> PDO is not installed. Please see <a href="http://www.php.net/manual/en/book.pdo.php">PDO Install Guide</a> to install PDO.
        </div>
        <?php endif; ?>

        <?php if($valid['sqlite']):?>
        <div class="alert alert-success">
            <strong>Checking for SQLite Support:</strong> SQLite Support is installed and configured
        </div>
        <?php else: ?>
        <div class="alert alert-error">
            <strong>Checking for SQLite Support:</strong> SQLite Support hasn't been installed for PDO. Please see <a href="http://php.net/manual/en/pdo.installation.php">PDO Install Guide</a> for information on installing SQLite driver for PDO.
        </div>
        <?php endif; ?>

        <?php if($valid['sqlite']):?>
        <div class="alert alert-success">
            <strong>Checking for SQLite Support:</strong> SQLite Support is installed and configured
        </div>
        <?php else: ?>
        <div class="alert alert-error">
            <strong>Checking for SQLite Support:</strong> SQLite Support hasn't been installed for PDO. Please see <a href="http://php.net/manual/en/pdo.installation.php">PDO Install Guide</a> for information on installing SQLite driver for PDO.
        </div>
        <?php endif; ?>

        <?php if($optional['mysql']):?>
        <div class="alert alert-success">
            <strong>Checking for MySQL Support:</strong> MySQL Support is installed and configured
        </div>
        <?php else: ?>
        <div class="alert alert-warn">
            <strong>Checking for MySQL Support:</strong> MySQL Support hasn't been installed for PDO. Please see <a href="http://php.net/manual/en/pdo.installation.php">PDO Install Guide</a> for information on installing MySQL driver for PDO.

            <p>Note: If you don't want MySQL Support, you can ignore this warning.</p>
        </div>
        <?php endif; ?>

        <?php if ($valid['confwritable']): ?>
        <div class="alert alert-success">
            <strong>Checking if config writable:</strong> Your config folder is writable.
        </div>
        <?php else: ?>
        <div class="alert alert-error">
            <strong>Checking if config writable:</strong> <p>The configuration folder is not writable. RazorFlow needs to write configuration files to <?php echo $configPath;?></p>
            <p>Please make the folder wirtable. If you're using Linux, you can use this command to make it writable:</p>
            <pre>chmod -R 0777 <?php echo $configPath; ?></pre>
            </p>
        </div>
        <?php endif; ?>

        <?php if ($optional['noexistingconf']): ?>
        <?php else: ?>
        <div class="alert alert-warn">
            <strong>Checking for existing config file:</strong> <p>There's already an existing config file at the following path:
            <pre><?php echo $configPath."config.php"; ?></pre>
            If you proceed with the installation, this file will be overwritten.
        </p>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <?php if($installable) {?>
        <p>
            <a class="btn btn-large btn-primary" href="?install=true" >Install</a>
        </p>
        <?php } else { ?>
        <p>
            <button class="btn btn-large btn-danger" disabled=true>Cannot Install</button>
        </p>

        <?php } ?>

    </div>
<?php endif; ?>

</div> <!-- /container -->

<script src="static/installer/js/jquery.min.js"></script>
<script src="static/installer/js/bootstrap.min.js"></script>

</body>
</html>
