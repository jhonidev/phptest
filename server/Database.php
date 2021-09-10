<?php

class mySQL {

	public $username;
    public $password;
    public $database;
    public $host;
    public $conn;

    function __construct() {
    	$this->username = "root";
    	$this->password = "";
    	$this->database = "test";
    	$this->host = "localhost";
    	$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database . ";charset=utf8", $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    function getDb() {
        return $this->conn;
    }

}

?>