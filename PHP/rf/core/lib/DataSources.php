<?php

class SQLiteDataSource extends DataSource{
	public function __construct($filePath)
	{
		$this->db = new PDO('sqlite:'.$filePath);
        $this->__setDataSourceIsReady ();
	}

	public function __getTimeFormatQuery($expr, $type) {
        $formatString = "";

        switch($type)
        {
            case 'year':
                $formatString = "%Y-01-01 00:00:00";
            break;
            case 'month':
                $formatString = "%Y-%m-01 00:00:00";
            break;
            case 'day':
                $formatString = "%Y-%m-%d 00:00:00";
            break;
            case 'hour':
                $formatString = "%Y-%m-%d %H:00:00";
            break;
            case 'minute':
                $formatString = "%Y-%m-%d %H:%M:00";
            break;
            case 'second':
                $formatString = "%Y-%m-%d %H:%M:%S";
            break;
        }

        // For sqlite, use the strftime function
        $expr = "strftime('$formatString', $expr)";

        return $expr;
	}
    public function __ifnullexp($expr, $default)
    {
        return "IFNULL($expr, $default)";
    }

    protected function getName () {
        return "SqliteDataSource";
    }
}

class MySQLDataSource extends DataSource {
	public function __construct($databaseName, $userName=NULL, $password=NULL, $serverHost = NULL, $serverPort = NULL, $timeout = NULL) {
		$this->db = new PDO("mysql:host=$serverHost;dbname=$databaseName", $userName, $password);
        $this->__setDataSourceIsReady ();
	}
	public function __getTimeFormatQuery($expr, $type) {
        $formatString = "";

        switch($type)
        {
            case 'year':
                $formatString = "%Y-01-01 00:00:00";
            break;
            case 'month':
                $formatString = "%Y-%m-01 00:00:00";
            break;
            case 'day':
                $formatString = "%Y-%m-%d 00:00:00";
            break;
            case 'hour':
                $formatString = "%Y-%m-%d %H:00:00";
            break;
            case 'minute':
                $formatString = "%Y-%m-%d %H:%i:00";
            break;
            case 'second':
                $formatString = "%Y-%m-%d %H:%i:%S";
            break;
        }

        // For sqlite, use the strftime function
        $expr = "DATE_FORMAT($expr, '$formatString')";

        return $expr;
	}
    public function __ifnullexp($expr, $default)
    {
        return "IFNULL($expr, $default)";
    }

    protected function getName () {
        return "MySqlDataSource";
    }
}

// Will be re-supported in a beta release
// class MSSQLDataSource extends DataSource {
// 	public function __construct($databaseName, $userName=NULL, $password=NULL, $serverAddress = NULL, $serverPort = NULL, $timeout = NULL) {
// 		$this->db = new fDatabase('mssql', $databaseName, $userName, $password, $serverAddress, $serverPort);
// 	}
// }

// class PostgreSQLDataSource extends DataSource {
// 	public function __construct($databaseName, $userName=NULL, $password=NULL, $serverAddress = NULL, $serverPort = NULL, $timeout = NULL) {
// 		$this->db = new fDatabase('postgresql', $databaseName, $userName, $password, $serverAddress, $serverPort);
// 	}
// }