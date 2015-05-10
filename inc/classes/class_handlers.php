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

/**
 * Description of newPHPClass
 *
 * @author niczem
 */
class handler {
    public function getHandler($handler_title){
        
        $handlers[] = ['title'=>'youtube', 'class_name'=>'youtube_handler'];
        
        foreach($handlers AS $handler){
            if($handler_title == $handler['title'])
                return $handler;
        }
        
    }
    public function api($handler_title, $action, $parameters){
        $handler = $this->getHandler($handler_title);
        
        if(!$handler){
            return []; 
        }
        include('handlers/'.$handler['title'].'/class.php');
        $handler_class = new $handler['class_name']();
        switch($action){
            case 'query':
                echo json_encode($handler_class->query($parameters['query'], $parameters['offset'], $parameters['max_results']));
                break;
        }
    }
}
