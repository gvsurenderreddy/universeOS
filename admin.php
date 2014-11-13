<?php
session_start();
require_once("inc/config.php");
require_once("inc/functions.php");
$action = save($_GET['action']);
$subaction = save($_GET['subaction']);
//here should be a proof if the user is a admin

switch($action){
    default:
?>
        <div class="jqPopUp border-radius transparency" id="admin">
            <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
            <hgroup>
                <h2>Admin</h2>
            </hgroup>
            <div id="jqContent">
                <table width="100%">
                    <tr>
                        <td onclick="$('#loader').load('admin.php?action=users')">Users</td>
                        <td>Files</td>
                    </tr>
                    <tr>
                        <td onclick="$('#loader').load('admin.php?action=messages')">Messages</td>
                        
                        <td>FAQ´</td>
                    </tr>
                    <tr>
                        <td onclick="$('#loader').load('admin.php?action=contents')">Contents</td>
                    </tr>
                 </table>
            </div>
        </div>
<script>
    $("#closeAdmin").click(function () {
    $('#admin').slideUp();
    });
</script>
<?
break;
case 'users':
    if(empty($_GET[subaction])){?>
        <div class="jqPopUp border-radius transparency" id="admin">
            <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
            <hgroup>
                <h2>Admin - Users</h2>
                <span><a onclick="$('#loader').load('admin.php')">>> back</a></span>
            </hgroup>

            <div id="jqContent">
                <center>
                    <div style="height: 200px; overflow: auto">
                <table class="border-top-radius border-box" cellspacing="0" width="100%" style="border: 1px solid #c9c9c9; color: #131313; font-color: #131313;">
                          <tr class="grayBar" height="35" style="color: #131313!important; font-color: #131313!important;">
                              <td></td>
                              <td style="color: #131313!important; font-color: #131313!important;">&nbsp;Regdate</td>
                              <td style="color: #131313!important; font-color: #131313!important;">Last Activity</td>
                              <td style="color: #131313!important; font-color: #131313!important;">Username</td>
                              <td align="right">Actions</td>
                          </tr>
                            <?PHP
                            unset($i);
                    $userSql = mysql_query("SELECT * FROM  `user` ORDER BY lastactivity DESC");
                    while($userData = mysql_fetch_array($userSql)){
                            if($i%2 == 0){
                                $color="FFFFFF";
                            }else{
                                $color="e5f2ff";
                            }
                            $i++;
                            ?>
                            <tr border="0" bgcolor="#<?=$color;?>" width="100%" height="30">
                                <td width="27">&nbsp;<?=showUserPicture($userData['userid'], 20);?></td>
                                <td style="color: #131313!important; font-color: #131313!important;"><?=date("d.m.y", $userData['regdate']);?></td>
                                <td style="color: #131313!important; font-color: #131313!important;"><?=date("d.m.y", $userData['lastactivity']);?></td>
                                <td style="color: #131313!important; font-color: #131313!important;"><?=$userData['username'];?></td>
                                <td align="right">
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <a class="btn" href="#"><i class="icon-ok-circle"></i></a>
                                            <a class="btn" href="#" title="show info" onclick="$('#loader').load('admin.php?action=users&subaction=showInfo&user=<?=$userData['userid'];?>');"><i class="icon-user"></i></a>
                                            <a class="btn" href="#" title="delete" onclick="confirm('Are you sure to delete this user?');$('#loader').load('admin.php?action=users&userid=<?=$userData['userid'];?>')" title="mark as seen"><i class="icon-ban-circle"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?PHP
                            } 
                            if(empty($i)){
                                echo"no messages...";
                            }
                            ?>
                 </table>
                    </div>
                </center>
            </div>
        </div>
<script>
    $("#closeAdmin").click(function () {
    $('#admin').slideUp();
    });
</script>
<?
    }else if($_GET[subaction] == "showInfo"){
    	if($_POST[submit]){
                $db = new db();
                $values['usergroup'] = $_POST['usergroup'];
                
    		if($db->update('user', $values, array('userid',$_POST['user']))){
    			jsAlert("changes saved");
    		}
    	}
        $userClass = new user($_GET['user']);
    	$userData = $userClass->getData();
    	?>
        <div class="jqPopUp border-radius transparency" id="admin">
            <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
            <hgroup>
                <h2>Admin - Users</h2>
                <span><a onclick="$('#loader').load('admin.php')">>> back</a></span>
            </hgroup>

            <div id="jqContent">
                <center>
                    <div style="height: 200px; overflow: auto">
                    	<form action="admin.php?action=users&subaction=showInfo" target="submitter" method="post">
	                   <table class="border-top-radius border-box" cellspacing="0" width="100%" style="border: 1px solid #c9c9c9; color: #131313; font-color: #131313;">
	                     <tr>
	                     	<td>Username</td>
	                     	<td><?=$userData[username];?></td>

	                     	<td>Usergroup</td>
	                     	<td><input type="hidden" value="<?=$_GET[user];?>" name="user">
	                     		<select name="usergroup">
	                     			<?
	                     			$groupSQL = mysql_query("SELECT * FROM userGroups ORDER BY title ASC");
									while($groupData = mysql_fetch_array($groupSQL)){
										if($groupData[id] == $userData[usergroup]){
											$checked = 'selected="selected"';
										}else{
											unset($checked);
										}
										
										
										echo"<option value=\"$groupData[id]\" $checked>$groupData[title]</option>";
									}
									?>
	                     		</select>
	                     	</td>
	                     </tr>
	                     <tr>
	                     	<td>Regdate</td>
	                     	<td><?=date("d.m.y", $userData[regdate]);?></td>
	                     	
	                     	<td>Last Activity</td>
	                     	<td><?=date("d.m.y", $userData[lastactivity]);?></td>
	                     </tr>
	                     <tr>
	                     	<td><input type="submit" name="submit" value="save" class="btn"></td>
	                     </tr>
	                   </table>
	                   </form>
                    </div>
                </center>
            </div>
        </div>
<script>
    $("#closeAdmin").click(function () {
    $('#admin').slideUp();
    });
</script>   	
    <?}else if($_GET[subaction] == "delete"){
        
                    echo"
                    <script>
                    $('#loader').load('admin.php?action=messages');
                    </script>
                    ";
    }
break;
case 'files':?>
        <div class="jqPopUp border-radius transparency" id="admin">
            <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
            <hgroup>
                <h2>Admin</h2>
            </hgroup>
            <div id="jqContent">
                <table width="100%">
                    <tr>
                        <td>Users</td>
                        <td>Files</td>
                    </tr>
                    <tr>
                        <td>Bugs</td>
                        <td>FAQ´</td>
                    </tr>
                </table>
                
            </div>
        </div>
<script>
    $("#closeAdmin").click(function () {
    $('#admin').slideUp();
    });
</script>
<?
break;
case 'bugs':?>
        <div class="jqPopUp border-radius transparency" id="admin">
            <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
            <hgroup>
                <h2>Admin</h2>
            </hgroup>
            <div id="jqContent">
                <table width="100%">
                    <tr>
                        <td>Users</td>
                        <td>Files</td>
                    </tr>
                    <tr>
                        <td onclick="$('#admin').slideUp(); $('#loader').load('admin.php?action=messages')">Messages</td>
                        <td>FAQ´</td>
                    </tr>
                </table>
            </div>
        </div>
<script>
    $("#closeAdmin").click(function () {
    $('#admin').slideUp();
    });
</script>
<?
break;
case 'messages':
    if(empty($subaction)){
    ?>
        <div class="jqPopUp border-radius transparency" id="admin">
            <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
            <hgroup>
                <h2>Admin</h2>
                <span><a onclick="$('#loader').load('admin.php')">>> back</a></span>
            </hgroup>
            <div id="jqContent">
                <center>
                    <div style="height: 200px; overflow: auto">
                        <table class="border-top-radius border-box" cellspacing="0" width="100%" style="border: 1px solid #c9c9c9; color: #131313; font-color: #131313;">
                          <tr class="grayBar" height="35" style="color: #131313!important; font-color: #131313!important;">
                              <td width="27" style="color: #131313!important; font-color: #131313!important;">&nbsp;</td>
                              <td width="150" style="color: #131313!important; font-color: #131313!important;">Date</td>
                              <td style="color: #131313!important; font-color: #131313!important;">Message</td>
                              <td align="right" style="color: #131313!important; font-color: #131313!important;">Actions</td>
                          </tr>
                            <?PHP
                            unset($i);
                    $messageSql = mysql_query("SELECT * FROM  `adminMessages`");
                    while($messageData = mysql_fetch_array($messageSql)){
                            if($i%2 == 0){
                                $color="FFFFFF";
                            }else{
                                $color="e5f2ff";
                            }
                            $i++;
                            ?>
                            <tr border="0" bgcolor="#<?=$color;?>" width="100%" height="30">
                                <td width="27">&nbsp;<i class="icon-envelope"></i></td>
                                <td width="150" style="color: #131313!important; font-color: #131313!important;"><?=date("d.m.y - H.i", $messageData[timestamp]);?></td>
                                <td style="color: #131313!important; font-color: #131313!important;"><?=$messageData[message];?></td>
                                <td align="right">
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <a class="btn" href="#" title="mark as seen"><i class="icon-ok-circle"></i></a>
                                            <a class="btn" href="#" title="open"><i class="icon-hand-up"></i></a>
                                            <a class="btn" href="#" title="delete" onclick="$('#loader').load('admin.php?action=messages&subaction=delete&message=<?=$messageData[id];?>')"><i class="icon-ban-circle"></i></a>
                                        </div>
                                    </div>
                                    
                                </td>
                            </tr>
                            <?PHP
                            } 
                            if(empty($i)){
                                echo"no messages...";
                            }
                            ?>
                        </table>
                    </div>
                </center>
            </div>
        </div>
<script>
    $("#closeAdmin").click(function () {
    $('#admin').slideUp();
    });
</script>
<?
    }else if($subaction == "show"){
        
    }else if($subaction == "delete"){
                    $messageSql = mysql_query("DELETE FROM  `adminMessages` WHERE id='$_GET[message]'");
                    echo"
                    <script>
                    $('#loader').load('admin.php?action=messages');
                    </script>
                    ";
    }
break;
case 'contents':
       if(empty($subaction)){
    ?>
<script>
    function confirmContentDelete(id){
        
        var answer = confirm("Are you sure that you want to delete this content?");
        if (answer){
                $('#loader').load('admin.php?action=contents&subaction=delete&content='+id);
        }
    }
</script>
        <div class="jqPopUp border-radius transparency" id="admin">
            <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
            <hgroup>
                <h2>Admin</h2>
                <span><a onclick="$('#loader').load('admin.php')">>> back</a></span>
            </hgroup>
            <div id="jqContent">
                <center>
                    <span><a onclick="$('#loader').load('admin.php?action=contents&subaction=add')">New Content</a></span>
                    <div style="height: 200px; overflow: auto">
                        <table class="border-top-radius border-box" cellspacing="0" width="100%" style="border: 1px solid #c9c9c9; color: #131313; font-color: #131313;">
                          <tr class="grayBar" height="35" style="color: #131313!important; font-color: #131313!important;">
                              <td width="27" style="color: #131313!important; font-color: #131313!important;">&nbsp;</td>
                              <td width="150" style="color: #131313!important; font-color: #131313!important;">Title</td>
                              <td style="color: #131313!important; font-color: #131313!important;">Contents</td>
                              <td align="right" style="color: #131313!important; font-color: #131313!important;">Comments</td>
                              <td>Action</td>
                          </tr>
                            <?PHP
                            unset($i);
                    $contentSql = mysql_query("SELECT * FROM  `staticContents`");
                    while($contentData = mysql_fetch_array($contentSql)){
                            if($i%2 == 0){
                                $color="FFFFFF";
                            }else{
                                $color="e5f2ff";
                            }
                            $i++;
                            ?>
                            <tr border="0" bgcolor="#<?=$color;?>" width="100%" height="30">
                                <td width="27">&nbsp;<i class="icon-envelope"></i></td>
                                <td style="color: #131313!important; font-color: #131313!important;"><?=$contentData[title];?></td>
                                <td width="150" style="color: #131313!important; font-color: #131313!important;"><?=substr($contentData[content], 0,30);?></td>
                                <td style="color: #131313!important; font-color: #131313!important;"><?=substr($contentData[comment], 0,30);?></td>
                                <td align="right">
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <a class="btn" href="#" title="edit" onclick="$('#loader').load('admin.php?action=contents&subaction=edit&content=<?=$contentData[id];?>')"><i class="icon-edit"></i></a>
                                            <a class="btn" href="#" title="delete" onclick="confirmContentDelete('<?=$contentData[id];?>');"><i class="icon-ban-circle"></i></a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?PHP
                            } 
                            if(empty($i)){
                                echo"no messages...";
                            }
                            ?>
                        </table>
                    </div>
                </center>
            </div>
        </div>
        <script>
            $("#closeAdmin").click(function () {
            $('#admin').slideUp();
            });
        </script>
    <?
       }else if($_GET[subaction] == "add"){
           if(isset($_POST[submit])){
               if(!empty($_POST[title])){
                   mysql_query("INSERT INTO `staticContents` (`id`, `title`, `content`, `comment`) VALUES (NULL, '$_POST[title]', '$_POST[content]', '$_POST[comment]');");
                   jsAlert("The content has been added");
                   ?>
                   
                    <script>
                    parent.$('#loader').load('admin.php?action=contents'); 
                    </script>
                    <?
               }
           }
        ?>
        <form action="admin.php?action=contents&subaction=add" method="post" target="submitter">
            <div class="jqPopUp border-radius transparency" id="admin">
                <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
                <hgroup>
                    <h2>Admin</h2>
                    <div>
                        <span><a onclick="$('#loader').load('admin.php')">>> back</a></span>
                        <span style="padding-left: 100px;"><input type="submit" value="Save" class="btn"></span>
                    </div>
                </hgroup>
                <div id="jqContent">
                    <center>
                        <div style="height: 200px; overflow: auto">
                            <table class="border-top-radius border-box" cellspacing="0" width="100%" style="border: 1px solid #c9c9c9; color: #131313; font-color: #131313;">
                                <tr>
                                    <td>Title</td>
                                    <td><input type="text" name="title" placeholder="title"></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Content<br><textarea name="content" style="width: 100%; height: 170px"></textarea></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Comment<br><textarea name="comment" style="width: 100%; height: 120px"></textarea></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;<input type="hidden" name="submit" value="rofl"></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" value="Save" class="btn"></td>
                                </tr>
                            </table>
                        </div>
                    </center>
                </div>
            </div>
        </form>
        <script>
            $("#closeAdmin").click(function () {
            $('#admin').slideUp();
            });
        </script>
        <?
       }else if($_GET[subaction] == "edit"){
           if(isset($_POST[submit])){
               if(!empty($_POST[title])){
                   mysql_query("UPDATE `staticContents` SET `title` = '$_POST[title]', `content` = '$_POST[content]', `comment` = '$_POST[comment]' WHERE `id`='$_GET[content]';");
                   jsAlert("The content has been updated");
                   ?>
                   
                    <script>
                    parent.$('#loader').load('admin.php?action=contents'); 
                    </script>
                    <?
               }
           }
           $contentSQL = mysql_query("SELECT * FROM staticContents WHERE id='$_GET[content]'");
           $contentData = mysql_fetch_array($contentSQL);
        ?>
        <form action="admin.php?action=contents&subaction=edit&content=<?=$_GET[content];?>" method="post" target="submitter">
            <div class="jqPopUp border-radius transparency" id="admin">
                <a style="position: absolute; top: 10px; right: 10px; color: #FFF;" id="closeAdmin">X</a>
                <hgroup>
                    <h2>Admin</h2>
                    <div>
                        <span><a onclick="$('#loader').load('admin.php')">>> back</a></span>
                        <span style="padding-left: 100px;"><input type="submit" value="Save" class="btn"></span>
                    </div>
                </hgroup>
                <div id="jqContent">
                    <center>
                        <div style="height: 200px; overflow: auto">
                            <table class="border-top-radius border-box" cellspacing="0" width="100%" style="border: 1px solid #c9c9c9; color: #131313; font-color: #131313;">
                                <tr>
                                    <td>Title</td>
                                    <td><input type="text" name="title" placeholder="title" value="<?=$contentData[title];?>"></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Content<br><textarea name="content" style="width: 100%; height: 170px"><?=$contentData[content];?></textarea></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Comment<br><textarea name="comment" style="width: 100%; height: 120px"><?=$contentData[comment];?></textarea></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;<input type="hidden" name="submit" value="rofl"></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" value="Save" class="btn"></td>
                                </tr>
                            </table>
                        </div>
                    </center>
                </div>
            </div>
        </form>
        <script>
            $("#closeAdmin").click(function () {
            $('#admin').slideUp();
            });
        </script>
        <?
       }else if($_GET[subaction] == "delete"){
           mysql_query("DELETE FROM staticContents WHERE id='$_GET[content]'");
           jsAlert("The content has been deleted");
                   ?>
                   
                    <script>
                    parent.$('#loader').load('admin.php?action=contents'); 
                    </script>
                    <?
           
       }
    break;
}?>
