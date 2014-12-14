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

class groups{
	public function get($userid=NULL){
            $groups;
            if(empty($userid))
                    $userid = getUser();
            $db = new db();
            $userGroups = $db->select('groupAttachments', array('item', 'user', '&&', 'validated','1','&&','itemId',$userid), array('group'));
            foreach($userGroups AS $data){
                    $groups[] = $data['group'];
            }
            return $groups;
	}
	public function getTitle($groupId){
            $db = new db();
            $data = $db->select('groups', array('id', $groupId), array('title'));
            return $data['title'];
	}
        public function getMembers($groupId){
            $db = new db();
            return $db->shiftResult($db->select('group_attachments', array('group', $groupId)), 'group');
        }
        function userJoinGroup($group, $user=NULL){

                        $userid = getUser();

                        if(empty($user)){
                                $user = $userid;
                        }

                    $db = new db();
                    $values['group'] = $group;
                    $values['item'] = 'user';
                    $values['itemId'] = $user;
                    $values['timestamp'] = $time;
                    $values['author'] = $userid;
                    $db->insert('groupAttachments', $values);
                }

        function userLeaveGroup($group, $user=NULL){
            
                    $db = new db();
                   
                        if( $db->delete('groupAttachments', array('group', $group, '&&', 'item', $user))){
                                return true;
                        }
                }

        function getGroups($userid=NULL){
                        //moved to class groups->get();
                        $groups = new Groups();
                        return $groups->get($userid);

                }
                
        function getGroupData($groupId){
                    $db = new db();
                    return $db->select('groups', array('id', $groupId));;
                }

        function getGroupName($groupId){
                    $db = new db();
                    $data = $db->select('groups', array('id', $groupId), array('title'));
                    return $data['title'];
                }

        function countGroupMembers($groupId){
                $total = mysql_query("SELECT COUNT(*) FROM `groupAttachments` WHERE `group`='$groupId' AND `item`='user' AND `validated`='1' "); 
                $total = mysql_fetch_array($total); 
                return $total[0];
            }

        function createGroup($title, $privacy, $description, $users){


                    $userid = getUser();

                    //check if nessecary informations are given
                    if((isset($description)) && (isset($title)) && (isset($privacy))){
                        $db = new db();
                        $values['title'] = $title;
                        $values['description'] = $description;
                        $values['public'] = $privacy;
                        $values['admin'] = $userid;
                    //insert group into db    
                    $groupId = $db->insert('groups', $values);

                        //add users to group
                        if(isset($users)){
                        foreach ($users as &$user) {
                            unset($values);
                            $values['group'] = $groupId;
                            $values['item'] = 'user';
                            $values['itemId'] = $user;
                            $values['timestamp']= time();
                            $values['author'] = $userid;
                            $db->insert('groupAttachments', $values);
                        }}
                        $folderCLass = new folder();
                        $groupFolder = $folderCLass->create("3", $groupId, $userid, "$groupId//$groupId");
                        $element = new element();
                        $groupElement = $element->create($groupFolder, $title, "other", $userid,  "$groupId//$groupId");
                        
                        unset($values);
                        $values['homeFolder'] = $groupFolder;
                        $values['homeElement'] = $groupElement;
                        $db->update('groups', $values);
                        
                                //add user which added group to group and validate
                                unset($values);
                                $values['group'] = $groupId;
                                $values['item'] = 'user';
                                $values['itemId'] = $userid;
                                $values['timestamp'] = $time;
                                $values['author'] = $userid;
                                $values['validated'] = '1';
                                $db->insert('groupAttachments', $values);
                                return true;

                        }else{
                        jsAlert("please fill out everything");
                    }

          }

        function deleteUserFromGroup($userid, $groupid){
            $db = new db();
            if($db->delete('groupAttachments', array('group', $groupid, 'AND', 'item', 'user', 'AND', 'itemId', $userid))){
                    return true;
            }
          }

        function update($groupId, $privacy, $description, $membersInvite){
                    $db = new db();
                    $values['public'] = $privacy;
                    $values['description'] = $description;
                    $values['membersInvite'] = $membersInvite;
                    
                    if($db->update('groups', $values, array('id', $groupId))){
                            return true;
                    }

          }

        function makeUserAdmin($groupId, $userId){
                    $db = new db();
                        $groupData = $this->getGroupData($groupId);

                        $adminString = $groupData['admin'];

                        //proof if user is allready admin
                        $admins = explode($adminString, ";");
                        if(!in_array("$userId", $admins)){
                                $adminString = "$adminString;$userId";
                                $values['admin'] = $adminString;
                                
                                if($db->update('groups', $values, array('id', $groupId))){
                                        return true;
                                }

                        }

          }
        function groupRemoveAdmin($groupId, $userId){

                        $groupData = $this->getGroupData($groupId);

                        $adminString = $groupData['admin'];

                        //proof if user is allready admin
                        $admins = explode($adminString, ";");
          }
}


