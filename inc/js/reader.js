
//        This file is published by transparency - everywhere with the best deeds.
//        Check transparency - everywhere.com for further information.
//        Licensed under the CC License, Version 4.0 (the "License");
//        you may not use this file except in compliance with the License.
//        You
//        may obtain a copy of the License at
//        
//        https://creativecommons.org/licenses/by/4.0/legalcode
//        
//        Unless required by applicable law or agreed to in writing, software
//        distributed under the License is distributed on an "AS IS" BASIS,
//        WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//        See the License for the specific language governing permissions and
//        limitations under the License.
//        @author nicZem for Tranpanrency - everywhere.com
        

var reader = new function(){
    this.applicationVar;
    this.tabs;
    this.init = function(){
        this.applicationVar = new application('reader');
        this.applicationVar.create('Reader', 'url', 'modules/reader/index.php',{width: 5, height:  4, top: 0, left: 4, hidden: true});
        
        
	this.tabs = new tabs('#readerFrame');
        this.tabs.init();
	this.tabs.addTab('Home', '',gui.loadPage('modules/reader/fav.php'));
    };
    this.show = function(){
        reader.applicationVar.show();
    };
    this.openFile = function(file_id){
        var fileData = filesystem.getFileData(file_id);
        
        
        if(fileData['privacy']){
            var output = '';
            if(privacy.authorize(fileData['privacy'], "edit", fileData['owner'])){
                var readOnly = "false";
            }else{
                var readOnly = "true";
            }

            var title = fileData['title'];
            //this iframe is used to handle all the onload, onsubmit, onkeyup events, its necessary because of the fact that the dhtml-goddies tab script parses the damn js
            //dirty solution!!!
            output += "<iframe src=\"modules/reader/UFF/javascript.php?fileId=$fileId&readOnly=$readOnly\" style=\"display:none;\"></iframe>";
            output += "<div class=\"uffViewerNav\">";
                    output += "<div style=\"margin: 10px;\">";
                            output += "<ul>";
                        output += '<li style="font-size: 11pt; margin-bottom: 05px;"><i class="icon-user"></i>&nbsp;<strong>Active Users</strong></li>';
                        //show active users
    //	            $.each($activeUsers AS &$activeUser){
    //	                if(!empty($activeUser)){
    //	                output += "<li onclick=\"openProfile($activeUser);\" style=\"cursor: pointer;\">";
    //	                //$output .= showUserPicture($activeUser, "11");
    //	                output +=  "&nbsp;";
    //	                output +=  useridToUsername($activeUser);
    //	                output += "</li>";
    //	                }
    //	            }
                            output += "</ul>";
                    output += "</div>";
            output += "</div>";
            //document frame
            output += "<div class=\"uffViewerMain\">";
                    output += "<textarea class=\"uffViewer_$fileId WYSIWYGeditor\" id=\"editor1\">";
                    output += "</textarea>";
            output += "</div>";   
        }
        return output;
        
    };
    this.openLink = function(){
        
    };
    
};