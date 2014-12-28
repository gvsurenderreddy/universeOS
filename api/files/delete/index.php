<?php

//This file is published by transparency-everywhere with the best deeds.
//Check transparency-everywhere.com for further information.
//Licensed under the CC License, Version 4.0 (the "License");
//you may not use this file except in compliance with the License.
//You may obtain a copy of the License at
//
//https://creativecommons.org/licenses/by/4.0/legalcode
//
//Unless required by applicable law or agreed to in writing, software
//distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//See the License for the specific language governing permissions and
//limitations under the License.
//
//@author nicZem for Tranpanrency-everywhere.com

include('../../../inc/config.php');
include('../../../inc/functions.php');
                    
                    
error_reporting(E_ALL);
                $fileId = $_POST['file_id'];
                $dbClass = new db();
                $fileData = $dbClass->select('files', array('id', $fileId));
                if(authorize($fileData['privacy'], "edit", $fileData['owner'])){
                    $fileClass = new file($fileId);
                    if($fileClass->delete()){
                        echo 1;
                    }else{
                        echo 0;
                    }
		}
?>