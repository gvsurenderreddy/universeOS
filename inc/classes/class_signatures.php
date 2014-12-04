<?php
/**
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

class signatures{
	
	function create($type, $itemId, $privateKey, $publicKey){
                $db = new db();
                
                $db->delete('signatures', array('type', $type, '&&', 'itemId', $itemId));
                
                $values['type'] = $type;
                $values['itemId'] = $itemId;
                $values['privateKey'] = $privateKey;
                $values['publicKey'] = $publicKey;
                $values['timestamp'] = time();
                $db->insert('signatures', $values);}
	
	function get($type, $itemId){
                $db = new db();
		$data = $db->select('signatures', array('type', $type, '&&', 'itemId', $itemId));
		return $data;
	}
	
	function updatePrivateKey($type, $itemId, $privateKey){
		if(!empty($privateKey)){
                        $db = new db();
                        $values['priavateKey'] = $privateKey;
                        $db->update('signatures', $values, array('type', $type, '&&', 'itemId', $itemId));
		}
	}
	
	function delete($type, $itemId){
            $db = new db();
            $db->delete('signature', array('type', $type, '&&', 'itemId', $itemId));
	}
}
