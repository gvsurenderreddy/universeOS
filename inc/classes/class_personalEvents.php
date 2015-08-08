<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Personalevents are the little popups you get (e.g. if someone sends a buddyrequest or comments one of your feed entries). For Events in the calendar, see 'class_events'.
 *
 * @author niczem
 */

class personalEvents{
    
                function get(){
                    $personalEventSql = mysql_query("SELECT * FROM personalEvents WHERE owner='".getUser()."' AND seen='0'");
                    while($personalEventData = mysql_fetch_array($personalEventSql)){
                        

                            //comments
                        if($personalEventData['event'] == "comment"){
                            if($personalEventData['info'] == "feed"){
                                $description = " has commented your post.";
                                $link = "reader.tabs.addTab(\'Comment\', \'\',gui.loadPage(\'modules/reader/showComment.php?type=$personalEventData[info]&itemid=$personalEventData[eventId]\'));";
                            }else if($personalEventData['info'] == "profile"){
                                $description = " has commented in your profile.";
                                $link = "showProfile(\'".getUser()."\');";
                            }
                        }
                            //events
                        if($personalEventData['event'] == "event"){
                            $events = new events($personalEventData['eventId']);
                            $eventData = $events->getData($personalEventData['eventId']);


                            $description = 'Invited you to the event "<a href="#" onclick="events.show(\\\''.$personalEventData['eventId'].'\\\');">'.$eventData['title'].'</a>"';

                            if(!empty($eventData['place'])){
                                    $description .= ' at '.$eventData['place'];
                            }

                            $link = "events.joinForm(\'".$personalEventData['eventId']."\');";

                        }
                        
                        $result[] = array(  
                                            'action'=>'notification', //for js reload action
                                            'subaction'=>'push', //for js reload action
                                            'data'=>array(
                                                'id'=>$personalEventData['id'],
                                                'type'=>$personalEventData['event'],
                                                'info'=>$personalEventData['info'],
                                                'eventId'=>$personalEventData['ecentId'],
                                                'user'=>$personalEventData['user'],
                                                'description'=>$description,
                                                'link'=>$link)
                                            );
                        
                    }
                    return $result;
                }
		
		function create($owner,$user,$event,$info,$eventId){
                    $values['owner'] = $owner;
                    $values['user'] = $user;
                    $values['event'] = $event;
                    $values['info'] = $info;
                    $values['eventId'] = $eventId;
                    $values['timestamp'] = time();
                    
                    $db = new db();
                    return $db->insert('personalEvents', $values);
	        
		}
                
                //cleans for user
                function clean($type, $info, $eventId){
                    $db = new db();
                    $db->delete('personalEvents', array('event', $type, '&&', 'info', $info, '&&', 'eventId', $eventId, '&&', 'owner', getUser()));
                }
}