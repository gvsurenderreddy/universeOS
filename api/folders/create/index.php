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

$db = new db();
$checkData = $db->select('folders', array('id', $_POST['folder']));
if(authorize($checkData['privacy'], "edit", $checkData['creator'])){
    //set privacy
    $customShow = $_POST['privacyCustomSee'];
    $customEdit = $_POST['privacyCustomEdit'];


    $privacy = exploitPrivacy("".$_POST['privacyPublic']."", "".$_POST['privacyHidden']."", $customEdit, $customShow);
    $user = getUser();
    if(!empty($_POST['folder']) AND !empty($_POST['name'])){
        $folder = new folder();
        $answer = $folder->create($_POST['folder'], $_POST['name'], $user, $privacy);

    }
}
?>