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

//this class is used for user rights purposes
//e.g. if the realname or the buddylist of a user are public or not


class userPrivacy{
    public $userid;
    function __construct($userid){
        $this->userid = $userid;
    }
    function updateRights($values){
        $db = new db();
        $db->update('user_privacy_rights', $values, array('userid', $this->userid));
    }
    function getRight($rightName){
        $db = new db();
        $userrights = $db->select('user_privacy_rights', array('userid', $this->userid));
        if(is_array($userrights))
            return $userrights[$rightName];
        else{
            return 'p';
        }
    }
    function proofRight($rightName){
        $right = $this->getRight($rightName);
        if(empty($right))
            $right = 'p';
        
        switch($right){
            case 'p':
                //public
                return true;
                break;
            case 'f':
                //check if user is un buddylist of $_SESSION['userid']
                $buddylist = new buddylist();
                return $buddylist->buddy($this->userid);
                break;
            default:
                //check if $_SESSION['userid'] is in certain groups or on the budddylist
                //e.g. 1,2,3//f
                //would be groups 1,2 and 3 and
                //everyone on the buddylist
                
                break;
        }
        return false;
    }
    function getRights(){
        $db = new db();
        return $db->select('user_privacy_rights', array('userid', $this->userid));
    }
}