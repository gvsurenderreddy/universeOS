//        This file is published by transparency - everywhere with the best deeds.
//        Check transparency - everywhere.com for further information.
//        Licensed under the CC License, Version 4.0 (the "License");
//        you may not use this file except in compliance with the License.
//        You may obtain a copy of the License at
//        
//        https://creativecommons.org/licenses/by/4.0/legalcode
//        
//        Unless required by applicable law or agreed to in writing, software
//        distributed under the License is distributed on an "AS IS" BASIS,
//        WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//        See the License for the specific language governing permissions and
//        limitations under the License.
//        @author nicZem for Tranpanrency - everywhere.com
        

var buddylist = new function(){
    this.getBuddies = function(){
                                   var res;
			  		$.ajax({
				      url:"api.php?action=getBuddylist",
				      async: false,  
					  type: "POST",
				      success:function(data) {
				         res = $.parseJSON(data); 
				      }
				   });
				   return res;
			  	};
    this.addBuddy = function(){
                                
              	$("#loader").load("addbuddy.php?user=" + userId +"");
                            };
    this.init = function(){
	
        this.applicationVar = new application('buddylist');
	this.applicationVar.create('Buddylist', 'url', 'buddylist.php',{width: 2, height:  5, top: 0, left: 9});
	
    };
};