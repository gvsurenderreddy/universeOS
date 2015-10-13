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
 * @author transpevstefan
 */
class fav {
    function select($user){
        $dbClass = new db();
        $favs = $dbClass->shiftResult($dbClass->select('fav', array('user', $user)), 'type');
        $result = array();
        foreach($favs AS $favData) {
            //get favs from table favs and select them from the appropriate table. 
            if($favData['type'] == "folder"){
                $favFolders = $dbClass->shiftResult($dbClass->select('folders', array('id', $favData['item'])), 'id');
                foreach ($favFolders as $folderData) {
                    $folderData['iconsrc'] = "img/folder_dark.png";
                    $folderData['favId'] = $favData['id'];
                    if(authorize($folderData['privacy'], "show", $folderData['creator']))
                        $result[] = array('type' => 'folder', 'data' => $folderData);
                }
            }else if($favData['type'] == "element"){
                $favElements = $dbClass->shiftResult($dbClass->select('elements', array('id', $favData['item'])), 'id');
                foreach ($favElements as $elementData) {
                    $elementData['iconsrc'] = "gfx/icons/filesystem/element.png";
                    $elementData['favId'] = $favData['id'];
                    if(authorize($elementData['privacy'], "show", $elementData['creator']))
                        $result[] = array('type' => 'element', 'data' => $elementData);
                }
            }else if($favData['type'] == "file"){
                $favFiles = $dbClass->shiftResult($dbClass->select('files', array('id', $favData['item'])), 'id');
                foreach ($favFiles as $fileData) {
                    $fileClass = new file($favData['item']);
                    $fileType = $fileClass->getFileType();
                    $fileClass2 = new files();
                    $fileData['iconsrc'] = "fileIcons/".$fileClass2->getFileIcon($fileType);
                    $fileData['favId'] = $favData['id'];
                    if(authorize($fileData['privacy'], "show", $fileData['owner']))
                        $result[] = array('type' => 'file', 'data' => $fileData);
                }
            }else if($favData['type'] == "link"){
                $favLinks = $dbClass->shiftResult($dbClass->select('links', array('id', $favData['item'])), 'id');
                foreach ($favLinks as $linkData) {
                    $classLinks = new link();
                    $fileType = $classLinks->getType($favData['item']);
                    $filesClass = new files();
                    $linkData['iconsrc'] = "gfx/icons/filesystem/element.png";
                    $linkData['favId'] = $favData['id'];
                    if(authorize($linkData['privacy'], "show", $linkData['creator']))
                        $result[] = array('type' => 'link', 'data' => $linkData);
                }
            }
        }
        return $result;
    }
    function show($user=NULL){
        if($user == NULL){
                $user = getUser();
        }
        $userClass = new user($user);
        $userFavs = $this->select($user);
        
        $i = 0;
        $output = '';
        foreach($userFavs AS $filefdata){
            $item = $filefdata['data']['id'];
            $type = $filefdata['type'];

            //derive the table and the image from fav-type
            if($type == "folder"){
                $typeTable = "folders";
                $img = "folder";
                $link = "openFolder($item); return false;";
            }else if($type == "element"){
                $typeTable = "elements";
                $img = "archive";
                $link = "openElement($item); return false;";
            }else if($type == "file"){
                $typeTable = "files";
                $fileClass = new file($item);
                $fileType = $fileClass->getFileType();
                $filesClass = new files();
                //$img = "fileIcons/".$filesClass->getFileIcon($fileType);
                $img = 'file';
            }else if($type == "link"){
                $typeTable = "links";
                $classLinks = new link();
                $fileType = $classLinks->getType($item);
                $filesClass = new files();
                //$img = "fileIcons/".$filesClass->getFileIcon($fileType);
                $img = 'link';
            }
            $dbClass = new db();
            $favFolderData = $dbClass->select($typeTable, array('id', $item));
            
            
                if(isset($favFolderData['name'])){
                    $favFolderData['title'] = $favFolderData['name'];
                }else{
                    $favFolderData['name'] = ''; //fix so the notice 'undefined index' won't be shown anymore
                }

            if($i%2 == 0){
                $color="FFFFFF";
            }else {
                $color="e5f2ff";
            }
            $i++;

                                        $output .= "<tr class=\"strippedRow\" onmouseup=\"showMenu('folder".$filefdata['id']."')\">";
                                                $output .= "<td onmouseup=\"showMenu(".$favFolderData['id'].")\" width=\"35\">&nbsp;<i class=\"icon-$img\"></i></td>";
                                                $output .= "<td onmouseup=\"showMenu(".$favFolderData['id'].")\"><a href=\"#\" onclick=\"$link\">".$favFolderData['name'].""."".$favFolderData['title']."/</a></td>";
                    if($user == getUser()){

                    $output .= "<td align=\"right\"><a class=\"btn btn-mini\" onclick=\"fav.remove('$type', '$item')\"><i class=\"icon icon-minus\"></i></a></td>";

                    }

                $output .= "</tr>";
        }
        if($i == 0){
            $output .="<tr style=\"display:table-row; background: none; padding-top: 0px;\">";
                $output .="<td colspan=\"2\" style=\"padding: 5px; padding-top: 0px;\">";
                $output .="You don't have any favourites so far. Add folders, elements, files, playlists or other items to your favourites and they will appear here.";
                $output .="</td>";
            $output .="</tr>";
        }
        return $output;
    }
    function showUL($user=NULL){
        if($user == NULL){
                $user = getUser();
        }
        $userClass = new user($user);
        $userFavs = $this->select($user);
        
        $i = 0;
        $output = '';
        foreach($userFavs AS $filefdata){
            $item = $filefdata['data']['id'];
            $type = $filefdata['type'];

            //derive the table and the image from fav-type
            if($type == "folder"){
                $typeTable = "folders";
                $img = "folder";
                $link = "openFolder($item); return false;";
            }else if($type == "element"){
                $typeTable = "elements";
                $img = "archive";
                $link = "openElement($item); return false;";
            }else if($type == "file"){
                $typeTable = "files";
                $fileClass = new file($item);
                $fileType = $fileClass->getFileType();
                $filesClass = new files();
                //$img = "fileIcons/".$filesClass->getFileIcon($fileType);
                $img = 'file';
            }else if($type == "link"){
                $typeTable = "links";
                $classLinks = new link();
                $fileType = $classLinks->getType($item);
                $filesClass = new files();
                //$img = "fileIcons/".$filesClass->getFileIcon($fileType);
                $img = 'link';
            }
            $dbClass = new db();
            $favFolderData = $dbClass->select($typeTable, array('id', $item));
            
            
                if(isset($favFolderData['name'])){
                    $favFolderData['title'] = $favFolderData['name'];
                }else{
                    $favFolderData['name'] = ''; //fix so the notice 'undefined index' won't be shown anymore
                }

            if($i%2 == 0){
                $color="FFFFFF";
            }else {
                $color="e5f2ff";
            }
            $i++;

                $output .= "<li onmouseup=\"showMenu('folder".$filefdata['id']."')\">";
                        $output .= "<a class=\"pull-left\" href=\"#\" onclick=\"$link\"><i class=\"icon white-$img\" style=\"height:22px; width:22px; margin-bottom:-6px;\"></i>".substr($favFolderData['title'], 0,6)."/</a>";
                if($user == getUser()){

                        $output .= "<a class=\"btn btn-mini pull-right\" onclick=\"fav.remove('$type', '$item')\"><i class=\"icon white-minus\" style=\"height:22px; width:22px;\"></i></a>";

                }

                $output .= "</li>";
        }
        if($i == 0){
            $output .='<li class="overlength">';
                $output .="You don't have any favourites so far.";
            $output .="</li>";
        }
        return $output;
    }
    function favTable($type){
       if($type == "folder"){
           $typeTable = "folders";
       }else if($type == "element"){
           $typeTable = "elements";
       }else if($type == "file"){
           $typeTable = "files";
       }
       
       echo $typeTable;
    }

    function addFav($type, $typeid, $userid=NULL){
        
        if(empty($userid))
            $userid = getUser();
        
        
        $db = new db();
        $checkData = $db->select('fav', array('type', $type, '&&', 'item', $typeid));
        if(is_array($checkData)){
            echo "allready your favourite";
            return false;
        } else {
            $db = new db();
            
            $values['type'] = $type;
            $values['item'] = $typeid;
            $values['user'] = $userid;
            $values['timestamp'] = time();
            
            $db->insert('fav', $values);
            return true;
        }
    }

    function remove($type, $item){
        $db = new db();
        if($db->delete('fav', array('type', $type, '&&', 'item', $item))){
            return true;
        }
    }
}

    
