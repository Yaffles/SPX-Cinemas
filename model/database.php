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
    public function __construct($dbServer="spx-webtest-s01",$dbUser='FURI01', $dbPassword='FURI01',$dbName='furi01db')
    {
        // echo("New Database<br/>");
        IF (!$this->conn) {
            $dbServer = "localhost";
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

    /**
     * Run SQL query and return Result
     */
    protected function run($sql=null) {
        return ($this->getConn()->query($sql));
    }

    /** ***************************************************************************
     * Executes a parameterized MySQLi query.
     *
     * @param string $sql The SQL query string with placeholders (?).
     * @param array $params An array of parameters to bind to the query.
     * Parameters should be in the correct order corresponding to the placeholders.
     * The function attempts to determine types ('i', 'd', 's', 'b') automatically.
     * @return array|int|false Returns an array of associative arrays for SELECT queries on success,
     * the number of affected rows for INSERT/UPDATE/DELETE on success,
     * or false on failure (logs error).
     */
    protected function query($sql, $params = []) {

    // 1. Prepare the statement
        // echo("QUERY FUNCTION: ".$sql.", ".$params[0]."<br/>");

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            echo("Error preparing statement: " . $conn->error);
            return false; // Indicate failure
        }
    // 2. Bind parameters (if any)
        if (!empty($params)) {
            $types = "";
            $bindArgs = [$types]; // The first element will hold the type string

            foreach ($params as &$param) {
                // Attempt to determine parameter type
                if (is_int($param)) {
                    $types .= 'i'; // integer
                } elseif (is_float($param)) {
                    $types .= 'd'; // double
                } elseif (is_string($param)) {
                    $types .= 's'; // string
                } else {
                    // Default to blob for other types (e.g., boolean, null) or handle specifically
                    $types .= 'b'; // blob
                }
                $bindArgs[] = &$param; // Add parameter by reference
            }

            // Update the type string in the bindArgs array
            $bindArgs[0] = $types;
            // echo("BindArgs: ".$bindArgs[0]." ".$bindArgs[1]."<br/>");
            // Bind parameters using the spread operator (...)
            // The spread operator unpacks the $bindArgs array into separate arguments for bind_param
            // This is equivalent to calling $stmt->bind_param($types, $params[0], $params[1], ...);
            if (!($stmt->bind_param(...$bindArgs))) {
                echo("Error binding parameters: " . $stmt->error);
                $stmt->close();
                unset($param); // Clean up the last reference from the loop
                return false;
            }
            unset($param); // Clean up the last reference from the loop
        }

    // 3. Execute the statement
    if (!$stmt->execute()) {
        echo("Error executing statement: " . $stmt->error);
        $stmt->close();
        return false; // Indicate failure
    }

    // 4. Process results based on query type
    // A simple check for SELECT. More robust methods might check the result metadata.
    $isSelect = stripos(trim($sql), 'SELECT') === 0;

    if ($isSelect) {
        // For SELECT queries, get the result set and fetch all rows
        $result = $stmt->get_result();

        if ($result === false) {
             echo("Error getting result set: " . $stmt->error);
             $stmt->close();
             return false;
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $result->free(); // Free result memory
        $stmt->close(); // Close the statement

        return $data; // Return the fetched data as an array of associative arrays
    } 
    // for INSERT
    elseif (stripos(trim($sql), 'INSERT') === 0) {
        // For INSERT, get the last inserted ID
        $lastId = $stmt->insert_id;
        $stmt->close(); // Close the statement

        return $lastId; // Return the last inserted ID
    }
    else {
        // For INSERT, UPDATE, DELETE, etc., return the number of affected rows
        $affectedRows = $stmt->affected_rows;
        $stmt->close(); // Close the statement

        return $affectedRows; // Return the number of affected rows
    }
    }


    /** ********************************************************************************
     * Run Multiple SQL query and return Result
     *
     */
    public function runMulti($sql=null) {
        return ($this->getConn()->multi_query($sql));
    }
    public function getError() {
        return $this->getConn()->error;
    }

    /** ********************************************************************************
     * connect().
     * Creates a database connection using MySQLi extension
     * PDO will work on 12 different database systems, whereas MySQLi will only work with MySQL databases.
     */
    public function connect() {

        $dbServer     = $this->getDbServer();
        $dbUser     = $this->getDbUser();
        $dbPassword = $this->getDbPassword();
        $dbName     = $this->getDbName();

        TRY {
            // echo("connect to: ".$dbServer.",".$dbUser.",".$dbPassword.",".$dbName."<br/>");
            $this->setConn(@new mysqli($dbServer, $dbUser, $dbPassword, $dbName));
            // Check connection to dbserver is not working
        }
        CATCH(Exception $e) {
            $this->setConn(@new mysqli("localhost", $dbUser, $dbPassword, $dbName));
        }
        IF ($this->conn->connect_error) {
            die("Database: Failed to connect to MySQL: ".$this->conn->connect_error);
        }
    }

    /**
     * connect2().
     * Creates a database connection using PHP Data Objects (PDO) extension
     * PDO will work on 12 different database systems, whereas MySQLi will only work with MySQL databases.
     */
    // public function connect2() {

    //     try {
    //         $this->conn = new PDO("mysql:host=$this->dbServer;dbname=$this->dbName", $this->dbUser, $this->dbPassword);
    //         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //     } catch(PDOException $e) {
    //         die("Failed to connect to MySQL: ".$e->getMessage());
    //     }
    // }
    public function commit() {
        $this->conn->commit();
    }

    public function close() {
        $this->conn->close();
        $this->setConn(null);
    }

}


?>