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
class uff {
    //put your code here
    public $fileId;
    
    function __construct($fileId=NULL){
        if($fileId != NULL){
            $this->fileId = $fileId;
        }
            
    }
    
    function create($element, $title, $filename, $privacy){
        $user = getUser();


        //upload file
        $elementSQL = mysql_query("SELECT folder, title FROM elements WHERE id='$element'");
        $elementData = mysql_fetch_array($elementSQL);
        
        $folderClass = new folder($elementData['folder']);
        $path = universeBasePath.'/'.$folderClass->getPath();
        
        $filename = "$filename.UFF";
        $folder = $element;
        $timestamp = time();
        
        
        $ourFileName = "$path$filename";
        
        $ourFileHandle = fopen($ourFileName, 'w') or jsAlert("can\'t open file");
        fclose($ourFileHandle);
        
        if(mysql_query("INSERT INTO `files` (`id`, `folder`, `title`, `size`, `timestamp`, `filename`, `language`, `type`, `owner`, `votes`, `score`, `privacy`) VALUES (NULL, '$folder', '$title', '', '$timestamp', '$filename', '', 'UFF', '$user', '0', '0', '$privacy');")){
          
            //add feed
            $fileId = mysql_insert_id();
            $feed = "has created a new UFF-file";
            
            $feedClass = new feed();
            $feedClass->create($user, $feed, "", "showThumb", $privacy, "file", $fileId);
            
            return $fileId;
        }else{
            return false;
        }
                        
    }

    function addChecksumToUffCookie($checksum){
        $fileId = $this->fileId;
                    //for each opened UFF the file id is added to the $_SESSION[openUffs]
                    //and a $_SESSION with the checksum will be created, which shows the 
                    //reload.php if a reload of the document is nessacary

                    $userid = getUser();

                    if(!empty($_SESSION['openUffs'])){

                        //parse SESSION
                        $sessionArray = explode(";", $_SESSION['openUffs']);

                        //check if there is a cookie set for the fileId
                        if (!in_array("$fileId", $sessionArray)) {
                            //set cookie
                            $_SESSION['openUffs'] = "$fileId;".$_SESSION['openUffs'];
                        }

                        //check if checksum needs to be updated
                        if($_SESSION["UFFsum_$fileId"] != $checksum){
                            //update checksum for fileId
                            $_SESSION["UFFsum_$fileId"] = $checksum;
                        }
                    }else{
                        $_SESSION['openUffs'] = "$fileId";
                        $_SESSION["UFFsum_$fileId"] = $checksum;
                    }
                    //add user to active users list


                    $fileData = mysql_query("SELECT var1 FROM files WHERE id='$fileId'");
                    $fileData = mysql_fetch_array($fileData);

                    //var1 with UFFs is used to 
                    $activeUserArray = explode(";", $fileData['var1']);
                    //check if user is allready in list
                    if (!in_array("$userid", $activeUserArray)) {
                        //add user to array
                        $activeUserArray[] = $userid;

                        //parse array
                        $activeUserArray = implode(";", $activeUserArray);

                        //update db
                        mysql_query("UPDATE files SET var1='$activeUserArray' WHERE id='$fileId'");
                    }
        }
        
    function removeUFFcookie(){
            $fileId = $this->fileId;
                //removes checksum and caller from $_SESSION so that the 
                //reload.php dont handels and empty request
                
                	$userid = getUser();
                    
                    if(empty($fileId)){
                        unset($_SESSION['openUffs']);
                    }
                    //parse SESSION
                    $sessionArray = explode(";", $_SESSION['openUffs']);
                    
                    //check if there is a cookie set for the fileId
                    if (in_array("$fileId", $sessionArray)) {
                        //delete cookie
                        foreach (array_keys($sessionArray, $fileId) as $key) {
                            unset($sessionArray[$key]);
                        }
                        $_SESSION['openUffs'] = implode(";", $sessionArray);
                        
                        
                        
                        //add user to active users list


                        $dbClass = new db();
                        $fileData = $dbClass->select('files', array('id', $fileId));

                        //var1 with UFFs is used to 
                        $activeUserArray = explode(";", $fileData['var1']);
                        //get user out of array
                        foreach($activeUserArray AS &$user){
                            if($user != $userid){
                                $newArray[] = $user;
                            }
                        }
                        //parse array
                        $newArray = implode(";", $newArray);
                        //update db
                        mysql_query("UPDATE files SET var1='$newArray' WHERE id='$fileId'");
                        
                    }
    }
    
    //writes Data into an UFF
    function write($input){
            $fileId = $this->fileId;
            $fileClass = new file($fileId);
               
                $filePath = universeBasePath.'/'.$fileClass->getFullFilePath();
                $file = fopen($filePath, 'w');

                fwrite($file, $input);
                fclose($file);

                $checksum = md5_file($filePath);
                $this->addChecksumToUffCookie($checksum);
                return true;
    }
    
    function show(){
                
                $fileId = $this->fileId;
                $fileClass = new file($fileId);
               
                $filePath = universeBasePath.'/'.$fileClass->getFullFilePath();

                $file = fopen($filePath, 'r');
                $return = fread($file, filesize($filePath));
                fclose($file);
                $checksum = md5_file($filePath);
                $uff = new uff($fileId);
                $uff->addChecksumToUffCookie($checksum);
                return $return;
    }

    

}


