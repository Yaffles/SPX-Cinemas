<?php
//namespace MySQL - useful when different database types ref: new MySQL\DBConnect
/**
 * DBConnect Class.
 * Used to create a persistent connection to the database
 */
//require(__DIR__.'/../utilities/cipher.php');

class Database {

    // Instance Variables

    public $dbUser;
    public $dbPassword;
    public $dbServer;
    public $dbName;
    public $conn;
    public $stmt;

    /**
     * Constructor.
     * Run when a connection instance is created
     *
     * @param  type $dbserver    .
     * @return type A         return value summary.
     */
    public function __construct($dbServer="localhost",$dbUser="12SEN", $dbPassword="12SEN",$dbName="12send8")
    {
        // echo("New Database<br/>");
        IF (!$this->conn) {
            $this->setDbServer($dbServer);
            $this->setDbUser($dbUser);
            $this->setDbPassword($dbPassword);
            $this->setDbName($dbName);
            $this->connect();
            //echo("Database: New connection<br/>");
        } ELSE {
            //echo("Database: Existing connection<br/>");

        }
    }

    public function setDbServer($dbServer='localhost') {
        IF ($dbServer) {
            $this->dbServer = $dbServer;
        // } ELSE {
        //     // $this->dbServer = $_SERVER['SERVER_NAME'];
        //     $this->dbServer = "localhost";
        }
    }
    public function setDbUser($dbUser=null) {
        IF ($dbUser) {
            $this->dbUser = $dbUser;
        }
    }
    public function setDbPassword($dbPassword=null) {
        IF ($dbPassword) {
            $this->dbPassword = $dbPassword;
        }
    }
    public function setDbName($dbName=null) {
        IF ($dbName) {
            $this->dbName = $dbName;
        }
    }
    public function setConn($conn) {
        if ($conn) {
            $this->conn = $conn;
        }
    }

    public function getDbServer() {
        return $this->dbServer;
    }
    public function getDbUser() {
        return $this->dbUser;
    }
    public function getDbPassword() {
        return $this->dbPassword;
    }
    public function getDbName() {
        return $this->dbName;
    }
    /**
     * getConn().
     * Gets a Connection
     * If it does no exist, the creates a database connection
     */
    public function getConn() {
        $this->connect();
        return $this->conn;
    }

    public function run($sql=null) {
        return ($this->getConn()->query($sql));
    }
    public function runMulti($sql=null) {
        return ($this->getConn()->multi_query($sql));
    }
    public function getError() {
        return $this->getConn()->error;
    }

    /**
     * connect().
     * Creates a database connection using MySQLi extension
     * PDO will work on 12 different database systems, whereas MySQLi will only work with MySQL databases.
     */
    public function connect() {

        $dbServer     = $this->getDbServer();
        $dbUser     = $this->getDbUser();
        $dbPassword = $this->getDbPassword();
        $dbName     = $this->getDbName();
        // echo("connect to: ".$dbServer.",".$dbUser.",".$dbPassword.",".$dbName."<br/>");
        $this->setConn(new mysqli($dbServer, $dbUser, $dbPassword, $dbName));

        // Check connection
        IF ($this->conn->connect_error) {
            die("Database: Failed to connect to MySQL: ".$this->conn->connect_error);
        }
    }

    public function commit() {
        $this->getConn()->commit();
    }

    public function close() {
        $this->getConn()->close();
        $this->setConn(null);
    }

}


?>