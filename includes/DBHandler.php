<?php 
ini_set('display_errors',1);  
error_reporting(E_ALL);

$itemPP = 5;

class DBHandler {

    private $db;

    public function __construct() {
        require_once __dir__.'/DBConnection.php';
        session_start();

        $this->pdo = new DBConnection();
        $this->db = $this->pdo->connect();
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

        $result = $this->db->prepare("INSERT INTO user (username, encrypted_password, salt, email) VALUES (:username, :encrypted_password, :salt, :email)");
        $result->bindParam(":username", $username);
        $result->bindParam(":encrypted_password", $encrypted_password);
        $result->bindParam(":salt", $salt);
        $result->bindParam(":email", $email);
        
        $result = $result->execute();

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
        $count = $this->db->prepare('SELECT * FROM user WHERE username = :username');
        $count->bindParam(':username', $username);
        $count->execute();

        $query = $this->db->prepare('SELECT * FROM user WHERE username = :username');
        $query->bindParam(':username', $username);
        $query->execute();
        $count = $count->fetchColumn();
        if($count > 0) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            $salt = $user['salt'];
            $encrypted_password = $user['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);

            if ($encrypted_password == $hash) {
                if($this->checkIfHasProfile($user['userID'])) {
                    return "3";
                } else {
                    return "4";
                }
                
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
        $result = $this->db->prepare('SELECT COUNT(*) FROM `group` WHERE groupname = :groupname');
        $result->bindParam(":groupname", $groupname);
        $result->execute();
        $count = $result->fetchColumn();

        if($count > 0) {
            return "1";
        } else {
            $result = $this->db->prepare("INSERT INTO `group` (groupname, groupdescription, groupstatus) VALUES (:groupname, :groupdescription, :groupstatus)");
            $result->bindParam(":groupname", $groupname);
            $result->bindParam(":groupdescription", $groupdescription);
            $result->bindParam(":groupstatus", $groupstatus);
            $result = $result->execute();

            if($result) return true; else return "2";
        }
    }

    function getGroupID($groupname) {
        $result = $this->db->prepare("SELECT groupID FROM `group` WHERE groupname = :groupname");
        $result->bindParam(":groupname", $groupname);

        if($result->execute()) {
            $result = $result->fetch(PDO::FETCH_ASSOC);
            return $result['groupID'];
        }
    }

    function getAllGroups() {
        $result = $this->db->prepare("SELECT * FROM `group`");
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllGroupsCount() {
        $result = $this->db->prepare("SELECT COUNT(*) FROM `group`");
        $result->execute();
        return $result->fetchColumn();
    }

    function getGroupsByPage($start, $max) {
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        $result = $this->db->prepare("SELECT * FROM `group` ORDER BY groupID DESC LIMIT :start, :max");
        $result->bindValue(":start", $start, PDO::PARAM_INT);
        $result->bindValue(":max", $max, PDO::PARAM_INT);
        $check = $result->execute();
        if($check) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }

    function getGroupNameByID($groupID) {
        $result = $this->getGroupByID($groupID);
        return $result['groupname'];
    }

    function getGroupByID($groupID) {
        $result = $this->db->prepare("SELECT * FROM `group` WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    function deleteGroup($groupID) {
        $result = $this->db->prepare("DELETE FROM `group` WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        $result = $result->execute();
        if($result) return true; else return false;
    }

    function editGroup($groupID, $groupname, $groupdescription) {
        $result = $this->db->prepare("UPDATE `group` SET groupname = :groupname, groupdescription = :groupdescription WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":groupname", $groupname);
        $result->bindParam(":groupdescription", $groupdescription);
        $result = $result->execute();
        if($result) return true; else return false;
    }


    /***********************************
                    GROUP
    *************************************/
                    
    /***********************************
               USER & PROFILE
    *************************************/

    function getUserID($username) {
        $result = $this->db->prepare("SELECT userID FROM user WHERE username = :username");
        $result->bindParam(":username", $username);
        if($result->execute()) {
            $result = $result->fetch(PDO::FETCH_ASSOC);
            return $result['userID'];
        }
    }

    function getAllPeople() {
        $result = $this->db->prepare("SELECT * FROM user");
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllPeopleCount() {
        $result = $this->db->prepare("SELECT COUNT(*) FROM user");
        $result->execute();
        return $result->fetchColumn();
    }

    function getPeopleByPage($seed, $start, $max) {
        $result = $this->db->prepare("SELECT * FROM user a INNER JOIN userprofile b ON a.userID = b.userID ORDER BY RAND(:seed) LIMIT :start, :max");
        $result->bindParam(":seed", $seed);
        $result->bindParam(":start", $start, PDO::PARAM_INT);
        $result->bindParam(":max", $max, PDO::PARAM_INT);
        if($result->execute()) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } else return false;
    }

    /* PROFILE */
    function registerProfile($userID, $firstname, $lastname, $birthday, $gender, $hobbies, $skills, $about) {
        $result = $this->db->prepare("INSERT INTO userprofile (userID, firstname, lastname, birthday, gender, hobbies, skills, about) 
            VALUES (:userID, :firstname, :lastname, :birthday, :gender, :hobbies, :skills, :about)");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":firstname", $firstname);
        $result->bindParam(":lastname", $lastname);
        $result->bindParam(":birthday", $birthday);
        $result->bindParam(":gender", $gender);
        $result->bindParam(":hobbies", $hobbies);
        $result->bindParam(":skills", $skills);
        $result->bindParam(":about", $about);
        if($result->execute()) return true; else return false;
    }

    function editProfile($userID, $firstname, $lastname, $birthday, $gender, $hobbies, $skills, $about) {
        $result = $this->db->prepare("UPDATE userprofile 
            SET firstname = :firstname, lastname = :lastname, birthday = :birthday, gender = :gender, hobbies = :hobbies, skills = :skills, about = :about
            WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":firstname", $firstname);
        $result->bindParam(":lastname", $lastname);
        $result->bindParam(":birthday", $birthday);
        $result->bindParam(":gender", $gender);
        $result->bindParam(":hobbies", $hobbies);
        $result->bindParam(":skills", $skills);
        $result->bindParam(":about", $about);
        if($result->execute()) return true; else return false;
    }

    function checkIfHasProfile($userID) {
        $result = $this->db->query('SELECT COUNT(*) FROM userprofile WHERE userID = :userID');
        $result->bindParam(":userID", $userID);
        $result->execute();
        $count = $result->fetchColumn();

        if($count) return true; else return false;
    }

    function getLeaderName($userID) {
        $result = $this->db->prepare("SELECT * FROM userprofile WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        if($result->execute()) {
            $result = $result->fetch(PDO::FETCH_ASSOC);
            $leaderName = $result['firstname'] . " " . $result['lastname'];
            return $leaderName;
        }
        
    }

    function getUserByID($userID) {
        $result = $this->db->prepare("SELECT * FROM user a INNER JOIN userprofile b ON a.userID = b.userID WHERE a.userID = :userID");
        $result->bindParam(":userID", $userID);
        if($result->execute()) 
            return $result->fetch(PDO::FETCH_ASSOC); 
        else 
            return false;
    }

    /* User INNER JOIN for account settings  */

    /***********************************
               USER $ PROFILE
    *************************************/

    /***********************************
                MEMBER STATUS
    *************************************/

    /* CREATE / INSERT */
    function createLeaderGroup($userID, $groupID) {
        $statusID = 1;
        $result = $this->db->prepare("INSERT INTO memberstatus (userID, groupID, statusID) 
            VALUES (:userID, :groupID, :statusID)");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":statusID", $statusID, PDO::PARAM_INT);
        if($result->execute()) return true; else return false;
    }

    function createMember($userID, $groupID) {
        $statusID = 2;
        $result = $this->db->prepare("INSERT INTO memberstatus (userID, groupID, statusID) 
            VALUES (:userID, :groupID, :statusID)");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":statusID", $statusID, PDO::PARAM_INT);
        if($result->execute()) return true; else return false;
    }
    /* CREATE / INSERT */

    /* GET / RETRIEVE */
    function getLeaderID($groupID) {
        $statusID = 1;
        $result = $this->db->prepare("SELECT userID FROM memberstatus WHERE groupID = :groupID AND statusID = :statusID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":statusID", $statusID, PDO::PARAM_INT);
        $result->execute();
        $result = $result->fetch(PDO::FETCH_ASSOC);
        return $result['userID'];
    }
    function getAllYourGroup($userID) {
        $result = $this->db->prepare("SELECT * FROM memberstatus WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        if($result->execute())
            return $result->fetchAll(PDO::FETCH_ASSOC);
        else return false;
    }
    function getGroupsYouOwn($userID) {
        $statusID = 1;
        $result = $this->db->prepare("SELECT * FROM memberstatus WHERE userID = :userID AND statusID = :statusID");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":statusID", $statusID, PDO::PARAM_INT);
        if($result->execute()) 
            return $result->fetch(PDO::FETCH_ASSOC); 
        else return false;
    }
    function getGroupCount($groupID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM memberstatus WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        $result->execute();
        $count = $result->fetchColumn();
        return $count;
    }
    function getMembers($groupID) {
        $statusID = 2;
        $result = $this->db->prepare("SELECT * 
            FROM memberstatus 
            WHERE groupID = :groupID AND statusID = :statusID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":statusID", $statusID, PDO::PARAM_INT);
        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }
    function getYourGroupCount($userID) {
        $result = $this->db->prepare("SELECT COUNT(*) 
            FROM memberstatus 
            WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        $result->execute();
        $count = $result->fetchColumn();
        return $count;
    }
    /* GET / RETRIEVE */

    /* CHECK */
    function checkIfLeader($groupID, $userID) {
        $statusID = 1;
        $result = $this->db->prepare("SELECT COUNT(*) FROM memberstatus WHERE groupID = :groupID AND userID = :userID AND statusID = :statusID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":userID", $userID);
        $result->bindParam(":statusID", $statusID, PDO::PARAM_INT);
        $result->execute();
        $count = $result->fetchColumn();
        if($count > 0) return true; else return false;
    }
    function checkIfHasGroupANDLeader($userID) {
        $statusID = 1;
        $result = $this->db->prepare("SELECT COUNT(*) FROM memberstatus WHERE userID = :userID AND statusID = :statusID");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":statusID", $statusID, PDO::PARAM_INT);
        $result->execute();
        $count = $result->fetchColumn();
        if($count > 0) return true; else return false;
    }
    function checkIfInGroup($groupID, $userID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM memberstatus WHERE groupID = :groupID AND userID = :userID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":userID", $userID);
        $result->execute();
        $count = $result->fetchColumn();
        if($count > 0) return true; else return false;
    }
    /* CHECK */

    /* DELETE */
    function deleteMemberStatus($groupID) {
        $result = $this->db->prepare("DELETE FROM memberstatus WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        if($result->execute()) return true; else return false;
    }
    function kickMember($groupID, $userID) {
        $result = $this->db->prepare("DELETE FROM memberstatus WHERE groupID = :groupID AND userID = :userID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":userID", $userID);
        if($result->execute()) return true; else return false;
    }
    /* DELETE */

    /***********************************
                MEMBER STATUS
    *************************************/

    /***********************************
                  REQUESTS
    *************************************/

    function checkIfPending($userID, $groupID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM `requests` WHERE userID = :userID AND groupID = :groupID");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":groupID", $groupID);
        $result->execute();
        $count = $result->fetchColumn();
        if($count == 1) return true; else return false;
    }

    function joinGroup($userID, $groupID, $message) {
        $result = $this->db->prepare("INSERT INTO `requests` (userID, groupID, message) 
            VALUES (:userID, :groupID, :message)");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":message", $message);
        if($result->execute()) return true; else return false;
    }

    function cancelPending($userID, $groupID) {
        $result = $this->db->prepare("DELETE FROM `requests` WHERE userID = :userID AND groupID = :groupID");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":groupID", $groupID);
        if($result->execute()) return true; else return false;
    }

    function getPendings($groupID) {
        $result = $this->db->prepare("SELECT * FROM `requests` WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }

    function getReasonByUserID($userID, $groupID) {
        $count = $this->db->prepare("SELECT COUNT(*) FROM `requests` WHERE groupID = :groupID AND userID = :userID");
        $count->bindParam(":userID", $userID);
        $count->bindParam(":groupID", $groupID);
        $count->execute();

        $result = $this->db->prepare("SELECT * FROM `requests` WHERE groupID = :groupID AND userID = :userID");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":groupID", $groupID);
        if($result->execute() && $count->fetchColumn() == 1) {
            $result = $result->fetch(PDO::FETCH_ASSOC);
            $message = $result['message'];
            return $message;
        } else return $groupID;
    }

    function acceptMember($userID, $groupID) {
        $result = $this->cancelPending($userID, $groupID);
        if($result) {
            if($this->createMember($userID, $groupID)) return true; else return false;
        } else return false;
    }

    function getRequestCount($groupID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM `requests` WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        $result->execute();
        $count = $result->fetchColumn();
        return $count;
    }
    /***********************************
                  REQUESTS
    *************************************/

    /***********************************
                   INVITE
    *************************************/
    /* GET */ 
    function getAllYourInvites($userID) {
        $result = $this->db->prepare("SELECT * FROM invite WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }
    /* GET */ 
    /* CHECK */              
    function checkIfAlreadyInvited($groupID, $userID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM invite WHERE groupID = :groupID AND userID = :userID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":userID", $userID);
        $result->execute();
        $count = $result->fetchColumn();
        if($count == 1) return true; else return false;
    }
    function checkIfInviteLimit($groupID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM invite WHERE groupID = :groupID");
        $result->bindParam(":groupID", $groupID);
        $result->execute();
        $count = $result->fetchColumn();
        if($count == 5) return true; else if($count < 5 && $count >= 0) {
            return false;
        }
    }
    function checkIfHasInvites($userID) {
        $result = $this->db->prepare("SELECT * FROM invite WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        $result->execute();
        $count = $result->fetchColumn();
        if($count >= 1) return true; else return false;
    }
    /* CHECK */
    /* INSERT */
    function inviteToGroup($groupID, $userID) {
        $result = $this->db->prepare("INSERT INTO invite (groupID, userID) 
            VALUES (:groupID, :userID)");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":userID", $userID);
        if($result->execute()) return true; else return false;
    }
    /* INSERT */
    /* DELETE */
    function removeFromInvite($groupID, $userID) {
        $result = $this->db->prepare("DELETE FROM invite WHERE groupID = :groupID AND userID = :userID");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":userID", $userID);
        if($result->execute()) return true; else return false;
    }
    /* DELETE */
    /***********************************
                   INVITE
    *************************************/

    /***********************************
            USER PROFILE PICTURE
    *************************************/
    function uploadImage($userID, $imageName) {
        $isprofilepix = 0;
        $result = $this->db->prepare("INSERT INTO userprofilepicture (userID, imagename, isprofilepix) 
            VALUES (:userID, :imageName, :isprofilepix)");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":imageName", $imageName);
        $result->bindParam(":isprofilepix", $isprofilepix);
        if($result->execute()) return true; else return false;
    }
    function checkIfHasProfilePicture($userID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM userprofilepicture WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        $result->execute();
        $count = $result->fetchColumn();
        if($count == 1) return true; else return false;
    }
    function getImageName($userID) {
        $result = $this->db->prepare("SELECT * FROM userprofilepicture WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        
        if($result->execute()) {
            $result = $result->fetch(PDO::FETCH_ASSOC);
            return $result['imagename'];
        } else return false;
    }
    function deleteProfilePic($userID) {
        $result = $this->db->prepare("DELETE FROM userprofilepicture WHERE userID = :userID");
        $result->bindParam(":userID", $userID);
        if($result->execute()) return true; else return false;
    }
    /***********************************
            USER PROFILE PICTURE
    *************************************/

    /***********************************
              GROUP DISCUSSION
    *************************************/
    function getPostsByGroupID($groupID) {
        $result = $this->db->prepare("SELECT * FROM groupdiscussion 
            WHERE groupID = :groupID 
            ORDER BY groupdiscussionID DESC");
        $result->bindParam(":groupID", $groupID);

        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false; 
    }
    function postDiscussion($groupID, $userID, $message, $now) {
        $result = $this->db->prepare("INSERT INTO groupdiscussion (groupID, userID, message, now) 
            VALUES (:groupID, :userID, :message, :now)");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":userID", $userID);
        $result->bindParam(":message", $message);
        $result->bindParam(":now", $now);
        if($result->execute()) return true; else return false;  
    }

    function getPostsWithLimit($groupID, $position, $item_per_page) {
        $result = $this->db->prepare("SELECT * FROM groupdiscussion 
            WHERE groupID = :groupID ORDER BY groupdiscussionID 
            DESC LIMIT :position, :item_per_page");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":position", $position);
        $result->bindParam(":item_per_page", $item_per_page);

        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }

    function getPostsWithLimitCount($groupID, $position, $item_per_page) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM groupdiscussion 
            WHERE groupID = :groupID ORDER BY groupdiscussionID 
            DESC LIMIT :position, :item_per_page");
        $result->bindParam(":groupID", $groupID);
        $result->bindParam(":position", $position);
        $result->bindParam(":item_per_page", $item_per_page);

        if($result->execute()) return $result->fetchColumn(); else return false;
    }

    function getPostCountByGroupID($groupID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM groupdiscussion 
            WHERE groupID = :groupID 
            ORDER BY groupdiscussionID DESC");
        $result->bindParam(":groupID", $groupID);
        $result->execute();
        $count = $result->fetchColumn();
        return $count;
    }
    /***********************************
              GROUP DISCUSSION
    *************************************/

    /***********************************
                USER POST
    *************************************/
    function getPostsByUserID($userID) {
        $result = $this->db->prepare("SELECT * FROM userpost WHERE userID = :userID ORDER BY userpostID DESC");
        $result->bindParam(":userID", $userID);
        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false; 
    }
    function postStatus($userID, $message, $now) {
        $result = $this->db->prepare("INSERT INTO userpost (userID, message, now) 
            VALUES (:userID, :message, :now)");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":message", $message);
        $result->bindParam(":now", $now);
        if($result->execute()) return true; else return false; 
    }
    function getUserPostsWithLimit($userID, $position, $item_per_page) {
        $result = $this->db->prepare("SELECT * FROM userpost WHERE userID = :userID 
            ORDER BY userpostID DESC LIMIT :position, :item_per_page");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":position", $position);
        $result->bindParam(":item_per_page", $item_per_page);
        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }
    function getUserPostsWithLimitCount($userID, $position, $item_per_page) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM userpost WHERE userID = :userID 
            ORDER BY userpostID DESC LIMIT :position, :item_per_page");
        $result->bindParam(":userID", $userID);
        $result->bindParam(":position", $position);
        $result->bindParam(":item_per_page", $item_per_page);
        if($result->execute()) return $result->fetchColumn(); else return false;
    }
    function getUserPostCountByUserID($userID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM userpost WHERE userID = :userID ORDER BY userpostID DESC");
        $result->bindParam(":userID", $userID);
        $result->execute();
        $count = $result->fetchColumn();
        return $count;
    }
    /***********************************
                USER POST
    *************************************/

    /***********************************
            INBOX AND MESSAGES
    *************************************/
    /* INBOX */
    function sendNewMessage($yourUserID, $to, $message, $now) {
        $result = $this->db->prepare("INSERT INTO inbox (`from`, `to`) 
            VALUES (:yourUserID, :to)");
        $result->bindParam(":yourUserID", $yourUserID);
        $result->bindParam(":to", $to);
        $result = $result->execute();
        $inboxID = $this->db->lastInsertId('inboxID');
        if($result) {
            $result = $this->insertMessage($inboxID, $yourUserID, $message, $now);
            if($result) return true; else return false;
        } else return false;
    }

    function sendMessage($inboxID, $yourUserID, $message, $now) {
        $result = $this->insertMessage($inboxID, $yourUserID, $message, $now);
        if($result) return true; else return false;
    }

    function getMessageList($userID) {
        $userID = (int) $userID;
        $result = $this->db->prepare("SELECT i.inboxID, u.userID
            FROM user u, inbox i
            WHERE CASE WHEN i.`from` =  :userID1
            THEN i.`to` = u.userID
            WHEN i.`to` =  :userID2
            THEN i.`from` = u.userID
            END AND (
            i.`from` =  :userID3
            OR i.`to` =  :userID4)");
        $result->bindParam(":userID1", $userID);
        $result->bindParam(":userID2", $userID);
        $result->bindParam(":userID3", $userID);
        $result->bindParam(":userID4", $userID);
        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }
    function getMessageListCount($userID) {
        $userID = (int) $userID;
        $result = $this->db->prepare("SELECT COUNT(i.inboxID)
            FROM user u, inbox i
            WHERE CASE WHEN i.`from` =  :userID1
            THEN i.`to` = u.userID
            WHEN i.`to` =  :userID2
            THEN i.`from` = u.userID
            END AND (
            i.`from` =  :userID3
            OR i.`to` =  :userID4)");
        $result->bindParam(":userID1", $userID);
        $result->bindParam(":userID2", $userID);
        $result->bindParam(":userID3", $userID);
        $result->bindParam(":userID4", $userID);
        if($result->execute()) return $result->fetchColumn(); else return false;
    }
    function getInboxID($from, $to) {
        $result = $this->db->prepare("SELECT inboxID FROM `inbox` 
            WHERE (`from` = :fromID AND `to` = :to) OR (`from` = :to AND `to` = :fromID)");
        $result->bindParam(":fromID", $from);
        $result->bindParam(":to", $to);
        if($result->execute()) {
            $result = $result->fetch(PDO::FETCH_ASSOC);
            return $result['inboxID'];
        } else return false;
    }

    function checkIfHasInbox($from, $to) {
        $result = $this->db->prepare("SELECT COUNT(inboxID) FROM inbox 
            WHERE `from` = :fromID AND `to` = :to");
        $result->bindParam(":fromID", $from);
        $result->bindParam(":to", $to);
        $result->execute();
        $count = $result->fetchColumn();
        if($count > 0) return true; else return false;
    }
    /* INBOX */

    /* MESSAGES */
    function insertMessage($inboxID, $yourUserID, $message, $now) {
        $result = $this->db->prepare("INSERT INTO message (`inboxID`, `userID`, `message`, `now`) 
            VALUES (:inboxID, :yourUserID, :message, :now)");
        $result->bindParam(":inboxID", $inboxID);
        $result->bindParam(":yourUserID", $yourUserID);
        $result->bindParam(":message", $message);
        $result->bindParam(":now", $now);
        if($result->execute()) return true; else return false;
    }
    function sendReply($inboxID, $yourUserID, $message, $now) {
        $result = $this->db->prepare("INSERT INTO message (`inboxID`, `userID`, `message`, `now`) 
            VALUES (:inboxID, :yourUserID, :message, :now)");
        $result->bindParam(":inboxID", $inboxID);
        $result->bindParam(":yourUserID", $yourUserID);
        $result->bindParam(":message", $message);
        $result->bindParam(":now", $now);
        $result = $result->execute();
        $messageID = $this->db->lastInsertId('messageID');
        if($result) return $messageID; else return false;
    }
    function getLastMessage($inboxID) {
        $result = $this->db->prepare("SELECT * FROM message 
            WHERE inboxID = :inboxID ORDER BY messageID DESC LIMIT 1");
        $result->bindParam(":inboxID", $inboxID);
        if($result->execute()) return $result->fetch(PDO::FETCH_ASSOC); else return false;
    }
    function getLastMessageDate($inboxID) {
        $result = $this->db->prepare("SELECT `now` FROM message 
            WHERE inboxID = :inboxID ORDER BY messageID DESC LIMIT 1");
        $result->bindParam(":inboxID", $inboxID);
        if($result->execute()) return $result->fetch(PDO::FETCH_ASSOC); else return false;
    }
    function getChat($inboxID) {
        $result = $this->db->prepare("SELECT messageID, userID, message, `now` FROM message 
            WHERE  inboxID = :inboxID ORDER BY messageID DESC");
        $result->bindParam(":inboxID", $inboxID);
        if($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC); else return false;
    }
    function getMessage($messageID) {
        $result = $this->db->prepare("SELECT * FROM message WHERE  messageID = :messageID");
        $result->bindParam(":messageID", $messageID);
        if($result->execute()) return $result->fetch(PDO::FETCH_ASSOC); else return false;
    }
    function getChatCount($inboxID) {
        $result = $this->db->prepare("SELECT COUNT(*) FROM message WHERE  inboxID = :inboxID");
        $result->bindParam(":inboxID", $inboxID);
        if($result->execute()) return $result->fetchColumn(); else return false;
    }
    /* MESSAGES */
    /***********************************
            INBOX AND MESSAGES
    *************************************/
    function makeSeed() {
        $ip = $_SERVER['REMOTE_ADDR'];
        $hour = date("H");
        $day = date("j");
        $month = date("n");
        $ip = str_replace(".", "", $ip);
        $seed = ($ip + $hour + $day + $month);
        $_SESSION['seed'] = $seed;
    }


}

?>