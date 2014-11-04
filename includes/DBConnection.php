<?php    

class DBConnection {
    
    // constructor
    function __construct() {
         
    }
 
    // destructor
    function __destruct() {
        // $this->close();
    }
 
    // Connecting to database
    public function connect() {
        require_once __dir__ . '/config.php';
        $dbhost = DB_HOST;
        $dbname = DB_DATABASE;
        $dbuser = DB_USER;
        $dbpass = DB_PASSWORD;
        
        // connecting to mysql
        $con = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, 
             array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        // return database handler
        return $con;
    }
 
    // Closing database connection
    public function close() {
        mysql_close();
    }
}

?>