<?php
session_start();
include_once("inc/config.php");
include_once("inc/functions.php");
header('Access-Control-Allow-Origin: *');

$action = $_GET['action'];



switch($action){
	case 'authentificate':
		echo userLogin($_POST['username'], $_POST['password']);
		break;
	case 'getUserCypher':
			if(empty($_POST['userid'])){
				$userid = usernameToUserid($_POST['username']);
			}else{
				$userid = $_POST['userid'];
			}
				
				$userData = getUserData($userid);
			
				echo $userData['cypher'];
			
		break;
	case 'getUserSalt':
			if(empty($_POST['userid'])){
				$userid = usernameToUserid($_POST['username']);
			}else{
				$userid = $_POST['userid'];
			}
				
				$userData = getUserData($userid);
			
				echo $userData['salt'];
			
		break;
	case 'updatePasswordAndCreateSignatures':
		
		$userid = save($_POST['userid']);
		$userData = getUserData($userid);
		$password = save($_POST['password']);
		$oldPassword = save($_POST['oldPassword']);
		$salt = save($_POST['salt']);
		
		$privateKey = save($_POST['privateKey']);
		$publicKey = save($_POST['publicKey']);
		
		//check if current cypher is md5 and if password is correct
		if($userData['cypher'] == 'md5' && $oldPassword == $userData['password']){
			
			//store salt
			createSalt('auth', $userid, 'user', $userid, $salt);
	        
			//create signature
			$sig = new signatures();
			$sig->create('user', $userid, $privateKey, $publicKey);
			mysql_query("UPDATE user SET password='$password', cypher='sha512' WHERE userid='$userid'");
			echo "1";
		}
	break;
		
		
		
	case 'updatePassword':
		
		echo updateUserPassword($_POST['oldPassword'], $_POST['newPassword'], $_POST['newSalt'], $_POST['newPrivateKey']);
		
	break;
		
		
		
   	case 'login':
		//old version
		if(empty($_POST['username']))
			$username = save($_GET['username']);
		else 
			$username = save($_POST['username']);
		
        $loginSQL = mysql_query("SELECT username, password FROM user WHERE username='$username'");
        $loginData = mysql_fetch_array($loginSQL);
        
        
        $dbPassword = $loginData['password'];
        $dbPassword = hash('sha1', $dbPassword);
		
		if(empty($_POST['signature']))
			$password = $_GET['signature'];
		else 
			$password = $_POST['signature'];
		
        if($password == $dbPassword){
        echo"true";
        }else{
            echo"false";
        }
    break;
    case 'messengerLogin':
		if(empty($_POST['username']))
			$username = save($_GET['username']);
		else 
			$username = save($_POST['username']);
		
        $loginSQL = mysql_query("SELECT username, password FROM user WHERE username='$username'");
        $loginData = mysql_fetch_array($loginSQL);
        
        
        $dbPassword = $loginData['password'];
        $dbPassword = hash('sha1', $dbPassword);
		
		if(empty($_POST['password']))
			$password = $_GET['password'];
		else 
			$password = $_POST['password'];
		
        if($password == $dbPassword){
        echo"true";
        }else{
            echo"false";
        }
    break;
	case 'getBuddylist':
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			echo json_encode(buddyListArray($_POST['userid']));
		}
		break;
	case 'getOpenRequests':
		$user = $_POST['userid'];
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			echo json_encode(getOpenRequests($user));
		}
		break;
	case 'replyFriendRequest':
		
			$user = $_POST['userid'];
			$buddy = $_POST['buddy'];
			if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
				replyRequest($buddy, $user);
			}
			
		break;
	case 'denyFriendRequest':
	
			
			$user = $_POST['userid'];
			$buddy = $_POST['buddy'];
			if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
				denyRequest($buddy, $user);
			}
				
	
		break;
	case 'addBuddy':
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			
			if(addBuddy($_POST['buddy'], $_POST['userid'])){
				echo true;
			}else{
				echo false;
			}
		}
		break;
	case 'chatGetMessages':
		
		$receiver = $_POST['receiver'];
		$userid = $_POST['userid'];
		$limit = $_POST['limit'];
		
		
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			echo json_encode(getMessages($userid, $receiver, $limit));
		}
		break;
	case 'markMessageAsRead':
		
		
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			
			echo $_POST['buddy'].$_POST['userid'];
			markMessageAsRead($_POST['buddy'], $_POST['userid']);
			
		}
		
		break;
	case 'getLastMessage':
		// This action is used to synchronize the chat which uses a locally saved variable
		// to store the last message which is received. If this var is not equal to the
		// value which is echoed by this action the chat can reload.
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			echo getLastMessage($_POST['userid']);
		}
		
		break;
	case 'getUnseenMessageAuthors':
		// To load dialoges with unseen messages the authorid's of those messages are needed
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			echo json_encode(getUnseenMessageAuthors($_POST['userid']));
		}
		
		break;
	case 'chatSendMessage':
		$receiver = $_POST['receiver'];
		$sender = $_POST['userid'];
		$message = $_POST['message'];
		
		
		if(proofLoginMobile($_POST['userid'], $_POST['hash'])){
			$message = sendMessage($receiver, $message, '0', $sender);
			if($message)
				echo $message;
		}
		
		break;
    case 'getBuddyFromMessageId':
        //get request data
        $user = save($_POST['username']); 
        $messageId = save($_POST['message']);
        $hash = save($_POST['password']);
        //get the login data
        $loginSQL = mysql_query("SELECT userid, username, password FROM user WHERE username='$user'");
        $loginData = mysql_fetch_array($loginSQL);
        $userid = $loginData['userid'];
        
        //get messageData
        $messageSQL = mysql_query("SELECT sender, receiver FROM messages WHERE id='$messageId'");
        $messageData = mysql_fetch_array($messageSQL);
        //check if buddy is sender or receiver
        if($messageData['sender'] == $userid){
            $buddyId = $messageData['receiver'];
        }else if($messageData['receiver'] == $userid){
            $buddyId = $messageData['sender'];
        }
        
        //getting the userdata of the buddy
        $userSQL = mysql_query("SELECT username FROM user WHERE userid='$buddyId'");
        $userData = mysql_fetch_array($userSQL);
        echo $userData['username'];
    break;
    
    
    //returns the javascript functions to load/reload the dialoges on client
    case 'loadNewChatDialoges':
           //get request data
           $user = save($_POST[username]);
           $hash = save($_POST[password]);
           //get the login data
           $loginSQL = mysql_query("SELECT userid, username, password FROM user WHERE username='$user'");
           $loginData = mysql_fetch_array($loginSQL);
           $userid = $loginData[userid];
        
            //secret password stuff
            $dbPassword = $loginData[password];
            $dbPassword = hash('sha1', $dbPassword);
           
           
            if($hash == $dbPassword){
            //check for new messages
            $newMessagesSql = mysql_query("SELECT * FROM  `messages` WHERE  receiver='$userid' AND  `read`='0'  ORDER BY timestamp DESC LIMIT 0, 3");
            $newMessagesData = mysql_fetch_array($newMessagesSql);
                //reverse
                if(empty($newMessagesData[id])){
                    $newMessagesSql = mysql_query("SELECT * FROM  `messages` WHERE  sender='$userid' AND  `seen`='0' ORDER BY timestamp DESC LIMIT 0, 3");
                    $newMessagesData = mysql_fetch_array($newMessagesSql);
                }
           if($newMessagesData[sender] == $userid){
                $buddyId = $newMessagesData[receiver];
           }else if($newMessagesData[receiver] == $userid){
                $buddyId = $newMessagesData[sender];
           }
           $buddySQL = mysql_query("SELECT username FROM user WHERE userid='$buddyId'");
           $buddyData = mysql_fetch_array($buddySQL);
           $buddyName = $buddyData[username];
           echo"$buddyName";
            }
    break;
	
	
	case 'getUserPicture':
		
		$userid = save($_POST['userid']);
		
		$userData = getUserData($userid);
		
		//check if user is standard user
		if(empty($userData['userPicture'])){
			$src = 'gfx/standardusersm.png';
		}else{
			$src = 'upload/userFiles/'.$userid.'/userPictures/thumb/40/'.$userData['userPicture'];
		}
		$mime = mime_content_type($src);
		
	    $file = fopen($src, 'r');
	    $output = base64_encode(fread($file, filesize($src)));
					
		echo 'data:'.$mime.';base64,'.$output;
		
		break;
		
		
	case 'useridToUsername':
		echo useridToUsername($_POST['userid']);
		break;
		
		
	case 'searchUserByString':
		echo json_encode(searchUserByString($_POST['string'], $_POST['limit']));
		break;
		
		
	case 'useridToRealname':
		echo useridToRealname($_POST['userid']);
		break;
		
		
	case 'usernameToUserid':
		echo usernameToUserid($_POST['username']);
		break;
		
		
	case 'getLastActivity':
	
	
        $userid = save($_POST['userid']);
		
        $data = mysql_fetch_array(mysql_query("SELECT lastactivity FROM user WHERE userid='$userid'"));
		
		$diff = time() - $data['lastactivity'];
		if($diff < 90){
			echo 1;
		}else{
			echo 0;
		}
		
		break;
		
		
	//checks if a username is taken
    case 'checkUsername':
        
    $user = save($_POST[username]);
    $sql = mysql_query("SELECT username FROM user WHERE username='$user'");
    $data = mysql_fetch_array($sql);
    
        if(empty($data[username])){
            echo"1";
        }else{
            echo"0";
        }
        
        
    break;
	
    //is used for universeOS registration form
    case 'processSiteRegistration':
    
	    if(validateCapatcha($_POST['captcha'])){
	        createUser($_POST['username'], $_POST['password'], $_POST['salt'], $_POST['privateKey'], $_POST['publicKey']);
			echo "1";
	   	}else{
	    	echo "The Captcha was wrong";
	   	}
        
    break;
	
    //is used for universeIM registration form
    case 'processSiteRegistrationMobile':
    
	        createUser($_POST['username'], $_POST['password'], $_POST['salt'], $_POST['privateKey'], $_POST['publicKey']);
			echo "1";
			
    break;
	
    case 'checkForFeeds':
        
        $user = mysql_real_escape_string($_GET[user]);
        if(empty($user)){
            $user = $_SESSION[userid];
        }
        
        $type = mysql_real_escape_string($_GET[type]);
        
        
        //the limit is set to this value until it causes probs
        $limit = "0,30";
        
            switch($type){
                //shows every entry in the system
                case "public":

                    $where = "ORDER BY timestamp DESC LIMIT $limit"; //defines Query


                break;

                //shows just entries of buddies
                case "friends":


                    //get all users which are in the buddylist
                    $buddies = buddyListArray();
                    $buddies[] = $_SESSION['userid'];
                    $buddies = join(',',$buddies);  
                    //push array with the user, which is logged in

                    $where = "WHERE author IN ($buddies) ORDER BY timestamp DESC LIMIT  $limit";

                break;

                //only shows entries of one user
                case "singleUser":

                    $where = "WHERE author='$user' ORDER BY timestamp DESC LIMIT  $limit";
                break;

                //only shows entries which are attached to a grouo $user => $groupId
                case "group":


                    $group = $user; //$user is used in this cased to pass the groupId
                    $where = "WHERE INSTR(`privacy`, '{$group}') > 0 ORDER BY timestamp DESC limit $limit";

                break;

                //only shows a single feed entry
                case "singleEntry":
                    $where = "WHERE id='$feedId'";
                    break;
            }
        
            //get specific feedsession in which the userids are saved
            
            $token = explode(';', $_SESSION["feedsession_$type"]);

            //proof if feedid is in list of allready loaded feeds
            $feedSql = mysql_query("SELECT id FROM feed $where");
            while($feedData = mysql_fetch_array($feedSql)) {
                //if new id occurs
                if(!in_array($feedData['id'], $token)){
                    if(empty($return)){
                    $return = true;
                    }

                }
            }
        if(!$return){
            echo"1";
        }
        
        break;
		
    case 'showFeed':
        $username = save($_POST['username']);
        $hash = $_POST[hash];
        if(checkMobileAuthentification($username, $hash)){
        showFeed('','','1');
        }
    break;
	
    case 'submitFeedEntry':
        $username = save($_POST[user]); 
        $message = save($_POST[msg]);
        $hash = save($_POST[pwd]);
        if(checkMobileAuthentification($username, $hash)){
            $userid = usernameToUserid($username);
            addFeed($userid, $message, feed);
            return true;
        }
        
    break;
	
    case 'showFeedComments':
        $username = save($_POST['username']);
        $hash = $_POST[hash];
        if(checkMobileAuthentification($username, $hash)){
            
            $commentid = $_GET['feedid'];
            showFeedComments($commentid);
            
        }
        
    break;


//salts and signatures
	case 'createSalt':
		$type = $_POST['type'];
		$itemId = $_POST['itemId'];
		$receiverType = $_POST['type'];
		$receiverId = $_POST['receiverId'];
		$salt = $_POST['salt'];
		
		//store salt
		echo createSalt($type, $itemId, $receiverType, $receiverId, $salt);
		
		break;
		
		
	case 'getSalt':
		
		echo getSalt($_POST['type'], $_POST['itemId']);
		
		break;
		
		
	case 'getPublicKey':
		$type = $_POST['type'];
		$itemId = $_POST['itemId'];
		
		$signature = new signatures();
		$data = $signature->get($type, $itemId);
		echo $data['publicKey'];
		break;
		
		
	case 'getPrivateKey':
		$type = $_POST['type'];
		$itemId = $_POST['itemId'];
		
		$signature = new signatures();
		$data = $signature->get($type, $itemId);
		echo $data['privateKey'];
		break;
		
		
	case 'getPublicKey':
		$type = $_POST['type'];
		$itemId = $_POST['itemId'];
		
		$signature = new signatures();
		$data = $signature->get($type, $itemId);
		echo $data['privateKey'];
		break;
		
		
//filesystem
	case 'fileIdToFileTitle':
		echo fileIdToFileTitle($_POST['fileId']);
		break;
}
