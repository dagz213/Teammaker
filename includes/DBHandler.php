<?php 
ini_set('display_errors',1);  
error_reporting(E_ALL);

class DBHandler {
	public function __construct() {
		require_once __dir__.'/DBConnection.php';
		session_start();

		$this->db = new DBConnection();
		$this->db->connect();
	}

	public function __destruct() {
        // $this->close();
    }

	public function isLoggedIn() {
        if(isset($_SESSION['username']) && !empty($_SESSION['username'])) return true; else return false;
    }

    /***********************************
		         REGISTRATION
    *************************************/
	function register($username, $password, $email) {
    	$hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt

        $result = mysql_query("INSERT INTO user (username, encrypted_password, salt, email) VALUES ('$username', '$encrypted_password', '$salt', '$email')");

        if($result) return true; else return false;
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
        return $hash;
    }
    /***********************************
		         REGISTRATION
    *************************************/

 	/***********************************
		            LOGIN
    *************************************/

    function login($username, $password) {
    	$result = mysql_query("SELECT * FROM user WHERE username='$username'") or die(mysql_error());
    	$no_of_rows = mysql_num_rows($result);
    	if ($no_of_rows > 0) {
    		$result = mysql_fetch_array($result);
    		$salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);

            if ($encrypted_password == $hash) {
                return true;
            } else {
                return "2";
            }

    	} else return "1";
    }
    /***********************************
		            LOGIN
    *************************************/
}

?>