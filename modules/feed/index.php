<?php
include_once("../../inc/config.php");
include(universeBasePath.'/inc/functions.php');
?>
        <div class="windowHeader" id="feedheader">
            <form id="feedInputForm" method="post" action=" doit.php?action=createFeed" target="submitter">
            <div style="margin: 15px;">
                        <textarea style="" id="feedInput" name="feedInput"></textarea>
                        <div style="display: none; width: 86%; margin-left: 7%; margin-bottom: 0px;" id="feedInputBar">
                                <div class="btn-toolbar" style="float: left;">
                                    <div class="btn-group">
                                        <a class="btn btn-mini" href="#" onclick="$('#feedInput').focus(); $('#addFeedFile').hide('slow'); $('#addFeedPrivacy').slideToggle(500);" title="privacy"> Privacy </a>
                                    </div>
                                </div>
                                <input type="submit" style="float:right; margin-top: 10px; margin-right:-13px;" value="submit" class="btn btn-success btn-mini">
                        </div>
                    </div>
                    <div id="addFeedPrivacy" class="coolGradient">
                        <?php
                        $privacyClass = new privacy("h//f");
                        $privacyClass->showPrivacySettings();
                        ?>
                    </div>
                    </form>
                    </div>

        <div id="feedFrame">
            <div class="addFeed">
            </div>
            <div class="feedMain">
                <?
                $classFeed = new feed();
                $classFeed->show("friends", getUser());
                echo "<div onclick=\"feedLoadMore('.feedMain' ,'friends', 'NULL', '1'); feedLoadMore('friends','1'); $(this).hide();\">...load more</div>";
                ?>
            </div>
            
        </div>
        <script>
            $('#feedInput').html(function(){
                 $(this).focus(function(){
                    $( "#feedheader" ).animate({ height: "100px" }, 500 );
                    $( "#feedFrame" ).animate({ top: "100px" }, 500 );
                    $('#feedInputBar').slideDown(500);
                    $( "#addFeedPrivacy" ).animate({ top: "100px" }, 500 );
                    initPrivacy();
                });
                    $(this).focusout(function(){
                        
//                        if($('#feedInput').val().length == 0){
//                            $( "#feedheader" ).animate({ height: "61px" }, 500 );
//                            $( "#feedFrame" ).animate({ top: "61px" }, 500 );
//                            $('#feedInputBar').slideUp(500);
//                            $( "#addFeedPrivacy" ).animate({ top: "61px" }, 500 );
//                            $('#addFeedPrivacy').slideUp(500);
//
//                        }
                    });
            });
            
            $('#feedInputForm').submit(function(){
                $(this).submit();
                $('#feedInput').val('');
            });
            
            
            //everything here could probaly be deleted
            $("#showGroups").click(function () {
                $("#selectGroups").toggle("slow");
            });
            $("#hideGroups").click(function () {
                $("#selectGroups").toggle("slow");
            });
            $("#selectGroups").click(function () {
                //maybe toggle
            });
            $("#enlargeSubmitArea").click(function () {
                    var cssOb = {
                        'display' : 'none'
                        }
                    $("#beforeToggle").css(cssOb);
                    var header = {
                        'height' : '120'
                        }
                    
                    var frame = {
                        'top' : '120'
                        }   
                    $("#feedheader").css(header);
                    $("#feedFrame").css(frame);
            $('#submitToggle').show();
            $('#submitText').show();
            });
            $("#minimizeSubmitArea").click(function () {
                    var cssOb = {
                        'display' : 'none'
                        }
                    var headera = {
                        'height' : '110px'
                        }
                    
                    var framea = {
                        'top' : '110px'
                        }   
                    $("#feedheader").css(headera);
                    $("#feedFrame").css(framea);    
                    $("#submitToggle").css(cssOb);
                    $(".submitArea").css(cssOb);
            $('#beforeToggle').show();
            });
            $("#feedAddText").click(function () {
                    var cssOb = {
                        'display' : 'none'
                        }
                    $(".submitArea").css(cssOb);
            $('#submitText').show();
            });
            $("#feedAddFile").click(function () {
                    var cssOb = {
                        'display' : 'none'
                        }
                    $(".submitArea").css(cssOb);
            $('#submitFile').show();
            });
            $("#feedAddVote").click(function () {
                    var cssOb = {
                        'display' : 'none'
                        }
                    $(".submitArea").css(cssOb);
            $('#submitVote').show();
            });
            </script>