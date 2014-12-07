<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author niczem
 */
class user {
    var $userid;
    function __construct($userid=NULL) {
        $this->userid = $userid;
    }
    public function login($username, $password){
  
        $username = mysql_real_escape_string($username);
        $db = new db();
        $data = $db->select('user', array('username', $username), array('userid', 'password', 'hash', 'cypher'));

        $userid = $data['userid'];

        $timestamp = time();
        $timestamp = ($timestamp / 2);
        $hash = md5($timestamp);
 
        //old version
        if($data['cypher'] == 'md5'){
            $password = md5($password);
        }
 
        if(!empty($userid) && $password == $data['password']){
            //set cookies
            $_SESSION['guest'] = false;
            $_SESSION['userid'] = $data['userid'];
            $_SESSION['userhash'] = $hash;

            //update db
            $values['hash'] = $hash;
            $db->update('user', $values, array('userid', $userid));

            $feedClass = new feed();
            $feedClass->create($_SESSION['userid'], "is logged in", "60", "feed", "p");
            
            $userClass = new user();
            $userClass->updateActivity($_SESSION['userid']);


            return 1;
        }else{
            return 0;
        }
        
    }
    public function usernameToUserid($username){
        $db = new db();
        $userData = $db->select('user', array('username', $username), array('userid', 'username'));
        return $userData['userid'];
    }
    public function getData($selector=NULL){
        if($selector == NULL){
            $userid = $this->userid;
        }else{
            if(is_numeric($selector))
                $userid = $selector;
            else
                $userid = $this->usernameToUserid($selector);
        }
        
        
  	if(empty($userid)){
  		$userid = getUser();
  	}
	$db = new db();
	$userData = $db->select('user', array('userid', $userid));
	return $userData;
        
    }
    public function updateActivity($userid){
          $time = time();
          $values['lastactivity'] = $time;
          $db = new db();
          $db->update('user', $values, array('userid', $userid));
    }
    
    public function getFav($userid=NULL){
        $return;
        if(empty($userid)){
                $userid=$this->userid;
        }
        $db = new db();
        $favs = $db->shiftResult($db->select('favs', array('user', $userid)),'user');
        foreach($favs AS $favData){
                $return[] = $favData;
        }

        return $return;
    }

    function getUserFavOutput($user){
		if(empty($user)){
			$user = $_SESSION['userid'];
		}
		
		
    }
    
    public function getUsername(){
        $userData = $this->getData();
        return $userData['username'];
    }
  
    public function create($username, $password, $authSalt, $keySalt, $privateKey, $publicKey){

        $username = save($_POST['username']);
        $db = new db();
        $data = $db->select('user', array('username', $username), array('username'));

        if(empty($data['username'])){
            $time = time();
            
            $values['password'] = $password;
            $values['cypher'] = 'sha512_2';
            $values['username'] = $username;
            $values['email'] = ''; //could be usefull for businesses
            $values['regdate'] = $time;
            $values['lastactivity'] = $time;
            
            $db = new db();
            $userid = $db->insert('user', $values);

                    //store salts
                    $saltClass = new salt();
                    $saltClass->create('auth', $userid, 'user', $userid, $authSalt);
                    $saltClass->create('privateKey', $userid, 'user', $userid, $keySalt);

                    //create signature
                    $sig = new signatures();
                    $sig->create('user', $userid, $privateKey, $publicKey);

            //create user folder(name=userid) in folder userFiles
            $folderClass = new folder();
            $userFolder = $folderClass->create("2", $userid, $userid, "h");

            //create folder for userpics in user folder
            $pictureFolder = $folderClass->create($userFolder, "userPictures", $userid, "h");

            //create thumb folders || NOT LISTED IN DB!
            $path3 = universeBasePath."//upload//userFiles//$userid//userPictures//thumb";
            $path4 = universeBasePath."//upload//userFiles//$userid//userPictures//thumb//25";
            $path5 = universeBasePath."//upload//userFiles//$userid//userPictures//thumb//40";
            $path6 = universeBasePath."//upload//userFiles//$userid//userPictures//thumb//300";
            mkdir($path3);  //Creates Thumbnail Folder
            mkdir($path4); //Creates Thumbnail Folder
            mkdir($path5); //Creates Thumbnail Folder
            mkdir($path6); //Creates Thumbnail Folder


            //create Element "myFiles" in userFolder
            $element = new element();
            $myFiles = $element->create($userFolder, "myFiles", "myFiles", $userid, "h");

            //create Element "user pictures" to collect profile pictures
            $pictureElement = $element->create($pictureFolder, "profile pictures", "image", $userid, "p");

            $updateValues['homefolder'] = $userFolder;
            $updateValues['myFiles'] = $myFiles;
            $updateValues['profilepictureelement'] = $pictureElement;
            
            $db->update('user', $updateValues, array('userid', $userid));
            
            return true;
        }
    }
    
    
    //not in use so far
    function delete($userid, $reason){
          $db = new db();
          $authorization = true;
          if($authorization){

              //delete all files
              $files = $db->select('files', array('owner', $userid), array('id'));
              foreach($files AS $fileData){
                  $fileClass = new file($fileData['id']);
                  $fileClass->deleteFile();
              }

              //delete all links
              $linkClass = new link();
              $links = $db->query('links', array('author', $userid), array('id'));
              foreach($links AS $linkData){
                $linkClass->deleteLink($linkData['id']);
              }


              //elements
              $elements = $db->select('elements', array('author', $userid), array('id'));
              foreach($elements AS $elementData){
                  $element = new element($elementData['id']);
                  $element->delete();
              }


              //folders
              $folders = $db->select('folders', array('creator', $userid), array('id'));
              foreach($folders AS $folderData){
                  $classFolder = new folder($folderData['id']);
                  $classFolder->delete();
              }

              //comments


              //buddy
              $db->delete('buddylist', array('buddy', $userid, 'OR', 'owner', $userid));

              //delete user
              $db->delete('user', array('userid', $userid));


              //log userid, username, reason

          }
    }

}



function updateUserPassword($oldPassword, $newPassword, $newAuthSalt=NULL, $newKeySalt=NULL, $privateKey=NULL, $userid=NULL){
  	if($userid == NULL){
  		$userid = getUser();
  	}
	
	$userClass = new user($userid);
	$userData = $userClass->getData($userid);
        
	if($userData['password'] == $oldPassword){
		
                $saltClass = new salt();
		if(!empty($newAuthSalt)){
			//store salt
			$saltClass->update('auth', $userid, $newAuthSalt);
		}
		if(!empty($newKeySalt)){
			//store salt
			$saltClass->update('privateKey', $userid, $newKeySalt);
		}
		  
		//create signature
		$sig = new signatures();
		$sig->updatePrivateKey('user', $userid, $privateKey); //store encrypted private key
		$db = new db();
                $values['password'] = $newPassword;
                $db->update('user', $values, array('user', $userid));
                
  		return $newPassword;
	}else{
		return 'Old Password was wrong';
	}
  }
  
function proofLogin(){
      if(isset($_SESSION['userid'])){
          return true;
      }else{
          return false;
      }
}

function proofLoginMobile($user, $hash){
  	
        $userClass = new user($user);
	$userData = $userClass->getData($user);
	
	if($userData['password'] == $hash){
		return true;
	}else{
		if(proofLogin()){
			return true;
		}else{
			return false;
		}
	}
  }

function getUser(){
  	
  	if(isset($_SESSION['userid'])){
  		return $_SESSION['userid'];
  	}else{
  		return false;
  	}
}

function hasRight($type){
	  //checks if user has right to ...
	  //whis is defined in config.php
	  $db = new db();
          
	  $userData = $db->select('user', array('userid', getUser()), array('usergroup'));
          $db->select('userGroups', array('id', $userData['usergroup']));
          
	  if($userGroupData["$type"] == "1"){
	  	return true;
	  }else{
	  	return false;
	  }
  	
  }

function checkMobileAuthentification($username, $hash){
      
        $username = $username;
        $hash = $hash;
        
        $db = new db();
        
        $loginData = $db->select('user', array('username', $username), array('userid', 'username', 'password'));
        $dbPassword = $loginData['password'];
        $dbPassword = hash('sha1', $dbPassword);
        if($hash == $dbPassword){
            return true;
        }
  }

function usernameToUserid($username){
        debug::write('use of function usernameToUserid');
        return user::usernameToUserid($username);
  }

function useridToUsername($userid){
        $db = new db();
        
        $loginData = $db->select('user', array('userid', $userid), array('username'));
        return $loginData['username'];
  }
function useridToRealname($userid){
        $db = new db();
        $loginData = $db->select('user', array('userid', $userid), array('realname'));
        if(empty($loginData['realname']))
            return 'no realname';
        
        return $loginData['realname'];
  }

function userSignature($userid, $timestamp, $subpath = NULL, $reverse=NULL){
    $db = new db();
    $feedUserData =  $db->select('user', array('userid', $userid), array('userid', 'username', 'userPicture'));
    if(isset($subpath)){
        $path = "./../.";
        $subPath = 1;
    }else{
        $subPath = NULL;
    }
      ?>
    <div class="signature" style="background: #EDEDED; border-bottom: 1px solid #c9c9c9;">
    <table width="100%">
        <tr width="100%">
            <?php
            if(empty($reverse)){ ?>
            <td style="width:50px; padding-right:10px;"><?=showUserPicture($feedUserData['userid'], "40", $subpath);?></td>
            <td>
                <table>
                    <tr>
                        <td style="font-size: 16px;line-height: 17px;" align="left"><a href="#" onclick="showProfile(<?=$feedUserData['userid'];?>);"><?=$feedUserData['username'];?></a></td>
                    </tr>             
                    <tr>
                        <td style="font-size: 12px;line-height: 23px;">
                            <i>
                            <?php
                            $guiClass = new gui();
                            echo $guiClass->universeTime($timestamp);?>
                            </i>
                        </td>
                    </tr>
                </table>
            </td>
            <?}else{?>
            <td>
                <table>
                    <tr>
                        <td style="font-size: 10pt;">&nbsp;<?=$feedUserData['username'];?></td>
                    </tr>             
                    <tr>
                        <td style="font-size: 08pt;">&nbsp;<i>
                        <?php
                        $guiClass = new gui();
                        $gui->universeTime($timestamp);?></i>
                        </td>
                    </tr>
                </table>
            </td>
            <td><span class="pictureInSignature"><?=showUserPicture($feedUserData['userid'], "40", $subpath);?></span></td>
            <?}?>
        </tr>
    </table>
    </div>
      <?php
  }
  

        
function showUserPicture($userid, $size, $subpath = NULL, $small = NULL /*defines if functions returns or echos and if script with bordercolor is loaded*/){
    
    $db = new db();
    $picData = $db->select('user', array('userid', $userid), array('userid', 'lastactivity', 'userPicture', 'priv_profilePicture'));
    $time = time();
    
    $difference = ($time - $picData['lastactivity']);
     if($difference < 90){
        $color = "#B1FFAD";
     }else{
        $color = "#FD5E48";
     }
     
    if(isset($subpath)) {
        if($subpath !== "total"){
        $path = "./../.";
        $subPath = 1;
        }
        
    }else{
        $subPath = NULL;
    }
      
        
        $style = '';
        //there are three different thumb sizes which are created when
        //the userpicture is uploaded, depending on the requested size
        //a different thumb needs to be choosen to minimize traffic
        if($size < "25"){
            $folderpath = "25";

        } else if($size < "40"){
            $folderpath = "40";

        } else if($size < "300"){
            $folderpath = "300";

        }
		$size.="px";
    
    if(empty($picData['userPicture'])){
        
        
    	$class = "standardUser";
    
    }else{
        
		$class = "";
		
        if($subpath !== "total"){
            
            $src = "$path./upload/userFiles/$userid/userPictures/thumb/$folderpath/".$picData['userPicture']."";
			if(empty($class)){
            	$style = "background-image: url('$src');";
			}
        }else{
            $src = "./upload/userFiles/$userid/userPictures/thumb/$folderpath/".$picData['userPicture']."";
			if(empty($class)){
            	$style = "background-image: url('$src');";
			}
        }
        
    }
       

        if($subpath !== "total"){
            
            $return="<div class=\"userPicture userPicture_$userid $class\" onload=\"updatePictureStatus('$userid', '$color');\" onclick=\"showProfile('$userid');\" style=\"width: $size; border-radius:".(int)($size/2)."px; height: $size; border-color: $color; $style\"></div>";
        }else{
            $return="<div class=\"userPicture userPicture_$userid $class\" onload=\"updatePictureStatus('$userid', '$color');\" onclick=\"showProfile('$userid;');\" style=\"width: $size; border-radius:".(int)($size/2)."px; height: $size; $style border-color: $color; $style\"></div>";
        }
      
      
      
      
        if($small){
        	if(!empty($picData['userPicture'])){
        		$style = " background-image: url(\\'$src\\');";
				if($small == 'unescaped'){
        			$style = " background-image: url(\\'$src\\');";
				}
			}
			
            $return="<div class=\"userPicture userPicture_$userid $class\" onload=\"updatePictureStatus(\'jjj$userid\', \\'$color\\');\" onclick=\"showProfile(\\'$userid\\');\" style=\"$style width: $size; height: $size; border-radius:".(int)($size/2)."px;border-color: $color;\"></div>";

            return $return;
        }else{
            echo $return;
        }
}