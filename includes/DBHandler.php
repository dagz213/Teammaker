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

    /***********************************
                    GROUP
    *************************************/

    function createGroup($groupname, $groupdescription, $groupstatus) {
        $result = mysql_query("SELECT * FROM `group` WHERE groupname='$groupname'");
        $no_of_rows = mysql_num_rows($result);
        if($no_of_rows > 0) {
            return "1";
        } else if(!$no_of_rows) {
            $insert = mysql_query("INSERT INTO `group` (groupname, groupdescription, groupstatus) VALUES ('$groupname', '$groupdescription', '$groupstatus')") or die(mysql_error());
            if($insert) return true; else return "2";
        }
    }

    function getGroupID($groupname) {
        $result = mysql_query("SELECT * FROM `group` WHERE groupname='$groupname'") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            return $result['groupID'];
        }
    }

    function getAllGroups() {
        $result = mysql_query("SELECT * FROM `group`") or die(mysql_error());
        return $result;
    }

    function getGroupsByPage($start, $max) {
        $result = mysql_query("SELECT * FROM `group` LIMIT $start, $max") or Die("End");
        return $result;
    }

    function getGroupNameByID($groupID) {
        $result = mysql_query("SELECT * FROM `group` WHERE groupID='$groupID'") or die(mysql_error());
        $result = mysql_fetch_array($result);
        $groupname =  $result['groupname'];
        return $groupname;
    }

    /***********************************
                    GROUP
    *************************************/
    /***********************************
               USER & PROFILE
    *************************************/

    function getUserID($username) {
        $result = mysql_query("SELECT * FROM user WHERE username='$username'") or die(mysql_error());
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            return $result['userID'];
        }
    }

    /* PROFILE */
    function getLeaderName($userID) {
        $result = mysql_query("SELECT * FROM userprofile WHERE userID = '$userID'") or die(mysql_error());
        $result = mysql_fetch_array($result);
        $leaderName = $result['firstname'] . " " . $result['lastname'];
        return $leaderName;
    }

    /* User INNER JOIN for account settings  */

    /***********************************
               USER $ PROFILE
    *************************************/

    /***********************************
                MEMBER STATUS
    *************************************/

    function createLeaderGroup($userID, $groupID) {
        $result = mysql_query("INSERT INTO memberstatus (userID, groupID, status) VALUES ($userID, $groupID, 'Leader')") or die(mysql_error());
        if($result) return true; else return false;
    }

    function getLeaderID($groupID) {
        $result = mysql_query("SELECT * FROM memberstatus WHERE groupID = '$groupID' AND status='Leader'") or die(mysql_error());
        $result = mysql_fetch_array($result);
        return $result['userID'];
    }

    function getAllYourGroup($userID) {
        $result = mysql_query("SELECT * FROM memberstatus WHERE userID = '$userID'") or die(mysql_error());
        return $result;
    }

    /***********************************
                MEMBER STATUS
    *************************************/
}

?>