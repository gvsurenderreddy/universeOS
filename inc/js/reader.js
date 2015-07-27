
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
//        @author nicZem for transparency-everywhere.com
//        @author pabst for transparency-everywhere.com
        

var reader = new function(){
    this.applicationVar;
    this.tabs;
    
    this.uffChecksums = []; //var to store checksums for reload
    
    this.init = function(){
        var grid = {width: 5, height:  4, top: 6, left: 3, hidden: true};
        if(proofLogin())
            grid = {width: 8, height:  8, top: 0, left: 2, hidden: true};
        this.applicationVar = new application('reader');
        this.applicationVar.create('Display', 'url', 'modules/reader/index.php', grid);
        
        
	this.tabs = new tabs('#readerFrame');
        this.tabs.init();
	this.tabs.addTab('Home', '', reader.generateStartpage());
    };
    this.show = function(){
        reader.applicationVar.show();
    };
    this.generateStartpage = function(){
        console.log('generateStartpage');
        var html = '';
        if(proofLogin()){
            html += '<div>USERPICTURE Hidiho, '+ useridToUsername(User.userid) +', good to see you back!</div>';
            html += '<div class="startpageNavi">';
                html += '<ul>';
                    html += '<li>' + filesystem.generateIcon('home', 'blue') + '</li>';
                    html += '<li>Groups</li>';
                    html += '<li>Favorites</li>';
                    html += '<li>Playlist</li>';
                    html += '<li>My Files</li>';
                html += '</ul>';
            html += '</div>';
            //alles ab hier wird dynamisch durch reader.initTabs() generiert
            html += reader.initTabs();

//            html += '<div>';
//                html += '<div>';
//                    html += '<span>' + filesystem.generateIcon('clock', 'blue') + ' Display History</span>';
//                html += '</div>';
//                html += '<div>';
//                    html += '<ul>';
//                        html += '<li>File1</li><li>File2</li><li>File3</li><li>File4</li><li>File5</li>';
//                    html += '</ul>';
//                html += '</div>';
//                html += '<div>';
//                    html += '<span>SHOW MORE FUNCTION</span>';
//                html += '</div>';
//            html += '</div>';
//            html += '<div>';
//                html += '<div>';
//                    html += '<span>' + filesystem.generateIcon('suggestion', 'blue') + ' Popular in the universe</span>';
//                html += '</div>';
//                html += '<div>';
//                    html += '<ul>';
//                        html += '<li>Featured1</li><li>Featured2</li><li>Featured3</li>';
//                    html += '</ul>';
//                html += '</div>';
//                html += '<div>';
//                    html += '<ul>';
//                        html += '<li>File1</li><li>File2</li><li>File3</li><li>File4</li><li>File5</li>';
//                    html += '</ul>';
//                html += '</div>';
//                html += '<div>';
//                    html += '<span>SHOW MORE FUNCTION</span>';
//                html += '</div>';
//            html += '</div>';
        } else {
            //hier die "not logged in startpage" zusammenfriemeln
            html = '';
        }
        
        
        return html;
            
    }
    this.initTabs = function(){
        console.log('initTabs');
        
        var html = '';
            
            
        var history_items = User.getHistory(); //get array with 5 dummy entries (functions.js)
        
        var popular_items = filesystem.getPopularItemsArray(); //get popular public items
        
        var feature = true; //wtf feature?!
        
        
        var group_items = groups.getGroupArray(User.userid); //get groups of the user
        
        var popular_groups; //popular groups > public and with the highest membercount
        
        
        var fav_history = User.getFavHistory(); //get latest 5 favorites of the user
        
        var fav_items = fav.getFavArray(User.userid); //get favorites of the user   
        
        
        var playlist_items; //get playlists of the user !!! .getUserPlaylists wirft net so den scheiß raus, wie geht das?!
        
        var popular_playlists; //get popular public playlists !!!
        
        
        var myFiles_items; //get files of the user - looks like filesystem - machen wie in alter startseite (abfragen zusammenfassen) und dann via api als myFilesArray durchschleusen


        html += '<div class="startpageTabs">';
        
            //generate home view
            html += this.buildTab('home', 'clock', 'Display History', history_items, 'suggestion', 'Popular in the universeOS', popular_items, feature);

            //generate groups view
            html += this.buildTab('groups', 'groups', 'My groups', group_items, 'suggestion', 'Popular groups', popular_groups);

            //generate favorites view
            html += this.buildTab('favorites', 'clock', 'My latest favorites', fav_history, 'fav', 'All my favorites', fav_items);

            //generate playlist view
            html += this.buildTab('playlists', 'playlist', 'Your playlists', playlist_items, 'suggestion', 'Popular playlists', popular_playlists);

            //generate my files view
            html += this.buildTab('myFiles', 'file', 'My files', myFiles_items);

        html += '</div>';
        return html;
    }
    this.buildTab = function(tab, iconA, titleA, itemsA, iconB, titleB, itemsB, feature){
        console.log('buildTab ' + tab);
        //generate view
        var html = '';
        html += '<div class="' + tab + ' readerStartTab">';
            html += '<div class="' + tab + ' sectionA">';
                html += '<div class="' + tab + ' iconA">';
                    html += filesystem.generateIcon(iconA, 'blue');
                html += '</div>';
                html += '<div class="' + tab + ' titleA">';
                    html += titleA;
                html += '</div>';
                html += '<div class="' + tab + ' itemsA">';
                    html += '<ul>';
                    if(typeof itemsA !== 'undefined' || itemsA !== undefined){
                        var onclick = "";
                        $.each(itemsA, function(key, value){
                            if( value['type'] === 'element'){
                                onclick = "onclick=\"elements.open('" + value['itemId'] + "'); return false;\"";
                            } else if( value['type'] === 'file'){
                                onclick = "onclick=\"reader.openFile('" + value['itemId'] + "'); return false;\"";
                            } else if( value['type'] === 'link'){
                                onclick = "onclick=\"reader.openLink('" + value['itemId'] + "'); return false;\"";
                            }
                            html += '<li ' + onclick + '>';
                                html += '<div class="' + tab + ' itemsA icon">' + filesystem.generateIcon(value['type']) + '</div>';
                                html += '<div class="' + tab + ' itemsA title">' + value['title'] + '</div>';
                            html += '</li>';
                        });
                    };
                    html += '</ul>';
                html += '</div>';
            html += '</div>';
            html += '<div class="' + tab + ' sectionB">';
                html += '<div class="' + tab + ' iconB">';
                    html += filesystem.generateIcon(iconB, 'blue');
                html += '</div>';
                html += '<div class="' + tab + ' titleB">';
                    html += titleB;
                html += '</div>';
                if(feature){
                    html += '<div class="' + tab + ' feature">';
                        html += '<ul>';
                            html += '<li>Featured1</li><li>Featured2</li><li>Featured3</li>';
                        html += '</ul>';
                    html += '</div>';
                }
                html += '<div class="' + tab + ' itemsB">';
                    html += '<ul>';
                    if(typeof itemsB !== 'undefined' || itemsB !== undefined){
                        var onclick = "";
                        $.each(itemsB, function(key, value){
                            if( value['type'] === 'element'){
                                onclick = "onclick=\"elements.open('" + value['itemId'] + "'); return false;\"";
                            } else if( value['type'] === 'file'){
                                onclick = "onclick=\"reader.openFile('" + value['itemId'] + "'); return false;\"";
                            } else if( value['type'] === 'link'){
                                onclick = "onclick=\"reader.openLink('" + value['itemId'] + "'); return false;\"";
                            }
                            html += '<li ' + onclick + '>';
                                html += '<div class="' + tab + ' itemsB icon">' + filesystem.generateIcon(value['type']) + '</div>';
                                html += '<div class="' + tab + ' itemsB title">' + value['title'] + '</div>';
                            html += '</li>';
                        });
                    };
                    html += '</ul>';
                html += '</div>';
            html += '</div>';
        html += '</div>';
        return html;
    }
    this.openFile = function(file_id){
        var fileData = filesystem.getFileData(file_id);
        var zoomInString = 'zoomIn(' + fileData['folder'] + ')';
        var zoomOutString = 'zoomOut(' + fileData['folder'] + ')';
        
        var header = "<header class=\"white-gradient\">";
        header += filesystem.generateIcon(fileData['type'], 'grey');
        header += "<span class=\"title\">" + fileData['title'] + "</span>";
        header += '<div class="whiteGradientScoreButton">' + item.showScoreButton('file', file_id) + '</div>';
        header += '<a href="./out/download/?fileId=' + file_id + '" target="submitter" class="btn btn-mini" title="download file">' + filesystem.generateIcon('download', 'grey') + '</a>';
        header += item.showItemSettings('file', file_id);
        if(fileData['type'] === 'image' || fileData['type'] === 'image/jpeg' || fileData['type'] === 'image/png' || fileData['type'] === 'image/tiff' || fileData['type'] === 'image/gif') {
            header += '<div id="zoom_in">' + filesystem.generateIcon('plus', 'grey', '', zoomInString) + '</div>';
            header += '<div id="zoom_out">' + filesystem.generateIcon('minus', 'grey', '', zoomOutString) + '</div>';
        }
        header += "</header>";
        
        var output = '';
        var path = "./upload/" + folders.getPath(elements.getData(fileData['folder'])['folder']) + fileData['filename'];
        console.log(fileData['type']);
        
        switch(fileData['type']){
            //cases: text, uff, image, pdf?, audio?, video?
            case 'image':
            case 'image/jpeg':
            case 'image/png':
            case 'image/tiff':
            case 'image/gif':
                var title = fileData['title'];
                output += '<div class="openFile">';
                    output += header;
                    output += "<div class=\"fileWindow\" id=\"fileWindowId\">";
                        output += "<div class=\"imageReader\">";
                            output += "<div class=\"mainImage\">";
                                output += "<img src=\"" + path + "\" / id=\"viewedPicture_" + fileData['folder'] + "\">";
                            output += "</div>";
                            output += "<div class=\"previewImages\">";
                                $.each(elements.getFileList(fileData['folder']), function(key, value){
                                    var thumbPath = "./upload/" + folders.getPath(elements.getData(fileData['folder'])['folder']) + "thumbs/" + value['data']['filename'];
                                    if(value['data']['type'] === "image" || value['data']['type'] === "image/jpeg" || value['data']['type'] === "image/png" || value['data']['type'] === "image/tiff" || value['data']['type'] === "image/gif"){
                                        output += "<img src=\"" + thumbPath + "\" onclick=\"reader.openFile('" + value['data']['id'] + "'); return false\"/>";
                                    }
                                });
                            output += "</div>";
                        output += "</div>";
                    output += '</div>';
                output += '</div>';
            break;
            
            
            case 'text/plain':
            case 'text/x-c++':
                var title = fileData['title'];
                output += '<div class="openFile">';
                    output += header;
                    output += "<div class=\"fileWindow\" id=\"fileWindowId\">";
                        output += "<div class=\"textReader\">";
                            output += nl2br(htmlentities(filesystem.readFile(fileData['id'])));
                        output += "</div>";
                    output += '</div>';
                output += '</div>';
            break;
            
            
            case 'audio':
            case 'audio/wav':
            case 'audio/mpeg':
                var title = fileData['title'];
                output += '<div class="openFile">';
                    output += header;
                    output += "<div class=\"fileWindow\" id=\"fileWindowId\">";
                        output += "<div class=\"audioReader\">";
                            output += "<audio controls>";
                                if(fileData['type'] === "audio/wav"){
                                    output += "<source src=" + path + " type=\"audio/wav\">";
                                }
                                else if(fileData['type'] === "audio/mpeg"){
                                    output += "<source src=" + path + " type=\"audio/mpeg\">";
                                }
                                else if(fileData['type'] === "audio"){
                                    output += "This audio file isn't compatible with HTML5. Please convert it to Ogg, Wav or MP3 files.";
                                }
                                else {
                                    output += "Your browser does not support the audio element.";
                                }
                            output += "</audio>";
                        output += "</div>";
                    output += '</div>';
                output += '</div>';
            break;
            
            
            case 'video':
            case 'video/mp4':
            case 'video/ogg':
            case 'video/webm':
                var title = fileData['title'];
                output += '<div class="openFile">';
                    output += header;
                    output += "<div class=\"fileWindow\" id=\"fileWindowId\">";
                        output += "<div class=\"videoReader\">";
                            output += "<video controls>";
                                if(fileData['type'] === "video/mp4"){
                                    output += "<source src=" + path + " type=\"video/mp4\">";
                                }
                                else if(fileData['type'] === "video/ogg"){
                                    output += "<source src=" + path + " type=\"video/ogg\">";
                                }
                                else if(fileData['type'] === "video/webm"){
                                    output += "<source src=" + path + " type=\"video/webm\">";
                                }
                                else if(fileData['type'] === "video"){
                                    output += "This video isn't compatible with HTML5. Please convert it to Ogg, WebM or MP4 files.";
                                }
                                else {
                                    output += "Your browser does not support HTML5 video.";
                                }
                            output += "</video>";
                        output += "</div>";
                    output += '</div>';
                output += '</div>';
            break;
            
            
            case 'application/pdf':
                //@sec including pdf could be a privacy problem because you can get informations by include analytic images etc
                var title = fileData['title'];
                output += '<div class="openFile">';
                    output += header;
                    output += "<div class=\"fileWindow\" id=\"fileWindowId\">";
                        output += "<div class=\"pdfReader\">";
                            output += "<div class=\"iframeFrame\">";
                                output += "<iframe src=\"./" + path + "\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\"></iframe>";
                            output += "</div>";
                        output += "</div>";
                    output += '</div>';
                output += '</div>';
            break;
            
            case 'UFF':
                //by nicZem
                if(privacy.authorize(fileData['privacy'], "edit", fileData['owner'])){
                    var readOnly = "false";
                }else{
                    var readOnly = "true";
                }
                var title = fileData['title'];
                output += '<div class="openFile">';
                    output += header;
                    output += "<div class=\"fileWindow\" id=\"fileWindowId\">";
                        //this iframe is used to handle all the onload, onsubmit, onkeyup events, its necessary because of the fact that the dhtml-goddies tab script parses the damn js
                        //dirty solution!!!
                        output += "<div class=\"uffViewerNav\">";
                                output += "<div style=\"margin: 10px;\">";
                                        output += "<ul>";
                                            output += '<li style="font-size: 11pt; margin-bottom: 05px;"><i class="icon icon-user"></i>&nbsp;<strong>Active Users</strong></li>';
                                        output += "</ul>";
                                output += "</div>";
                        output += "</div>";
                        //document frame
                        output += "<div class=\"uffViewerMain\">";
                                output += "<textarea class=\"uffViewer_"+file_id+" WYSIWYGeditor\" id=\"editor1\">";
                                output += "</textarea>";
                        output += "</div>";
                    output += '</div>';
                output += '</div>';
            break;
            
            default:
                var title = fileData['title'];
                output += '<div class="openFile">';
                    output += header;
                    output += "<div class=\"fileWindow\" id=\"fileWindowId\">";
                        output += "<div class=\"swwReader\">";
                            output += "<span>Sorry, but this file isn't compatible with the universeOS Reader. :( You just can download it by clicking on the download button above.</span>";
                        output += "</div>";
                    output += '</div>';
                output += '</div>';
            break;
        }
        
        reader.tabs.addTab(title, 'html', output, function(){
            //onclose
            delete reader.uffChecksums[file_id];
        });
        switch(fileData['type']){
            case'UFF':
                
                
                
                api.query('api/files/read/', { file_id : file_id}, function(uffContent){
                    
                    initUffReader(file_id, uffContent, "false");
                    
                    
                    //store hash
                    reader.uffChecksums[file_id] = hash.MD5(uffContent);
                });
                
                break;
        }
        reader.applicationVar.show();
        return output;
        
    };
    this.openLink = function(type, link, title){
        //just youtube and wikipedia links
        switch(type){
            case 'youtube':
                
                player.openItem(type, link);
                
                break;
        }
    };
};