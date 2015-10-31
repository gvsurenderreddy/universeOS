<?php
/*
This file is published by transparency-everywhere with the best deeds.
Check transparency-everywhere.com for further information.
Licensed under the CC License, Version 4.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

https://creativecommons.org/licenses/by/4.0/legalcode

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

@author nicZem for Tranpanrency-everywhere.com
 */


class sessionHashes{
	private $validity; //time in seconds untill hash expires
	
	
	function create($userid){
		
		//create unique identifyer and hash it with rand salt (the user agent needs to be encrypted!)
		$uniqueSystemIdentifyer = hash('sha512', $_SERVER['HTTP_USER_AGENT']);
		//add salt
		$randomSalt = hash('sha512', $uniqueSystemIdentifyer+substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 1) . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10));
		$db = new db();
		$db->query("DELETE FROM `sessionHashes` WHERE timestamp>'".(time()-$this->validity)."' AND userid='".save($userid)."' AND uniqueSystemIdentifyer='".save($uniqueSystemIdentifyer)."'");
		//save id, identifier and salt
                $values[''] = $type; //???
                $values['itemId'] = $itemId;
                $values['privateKey'] = $privateKey;
                $values['publicKey'] = $publicKey;
                $values['timestamp'] = time();
                
                //mysql class is comment because first row of values array or the first field of query below is ``and the value is $type that should be checked
                //return $db->insert('signatures', $values);
                return $db->insert('signatures', $values);
	}
	
	function get($id){
                $db = new db();
                return $db->select('signatur',  array('type', $type, '&&', 'itemId', $itemId));;
	}
	function delete($type, $itemId){
                $db = new db();
                $db->delete('signatures', array('type', $type, '&&', 'itemId', $itemId));
	}
}
