<?php
      session_start();
  include_once("../../inc/config.php");
  include_once("../../inc/functions.php");
    if(isset($_POST[submit])){
        $newAlias = htmlspecialchars($_POST[alias]);
        mysql_query("UPDATE buddylist SET alias='$newAlias' WHERE owner='$_SESSION[userid]' && buddy='$_POST[buddy]'");
        jsAlert("worked:)");
    }
?>
     <div class="frameRight" id="settingsFrame">
         <div id="content">
             <ul>
                 <li>
                     <span><?=showUserPicture($buddyEditData[buddy], 30);?></span>
                     <span>
                         <form action="modules/settings/index.php?action=friends&reload=1" target="submitter" method="post"><?=$userpicture;?>&nbsp;
                            <input type="hidden" name="buddy" value="<?=$buddyEditData[buddy];?>">
                            <input type="text" name="alias" value="<?=$alias;?>">&nbsp;
                            <input type="submit" name="submit" value="save">
                        </form>
                     </span>
                 </li>
             </ul>
             
             
         <center>Your Buddies:
         <table>
             <tr>
                 <td>&nbsp;</td>
             </tr>
         <?
         
            if(isset($_GET[delete])){
                mysql_query("DELETE FROM buddylist WHERE owner='$_SESSION[userid]' && buddy='".mysql_real_escape_string($_GET[buddy])."' LIMIT 1");
                mysql_query("DELETE FROM buddylist WHERE owner='".mysql_real_escape_string($_GET[buddy])."' && buddy='$_SESSION[userid]' LIMIT 1");
                jsAlert("worked :(");
            }
         $buddyEditSql = mysql_query("SELECT * FROM buddylist WHERE owner='$_SESSION[userid]'");
         while($buddyEditData = mysql_fetch_array($buddyEditSql)){
            $path = "../../";
             if(empty($buddyEditData[alias])){  
            $blUserSql = mysql_query("SELECT userid, username FROM user WHERE userid='$buddyEditData[buddy]'");
            $blUserData = mysql_fetch_array($blUserSql);
            $alias = "$blUserData[username]"; 
            } else{
            $alias = "$buddyEditData[alias]";
            }
         ?>
             <tr>
                 <td>&nbsp;</td>
                <td><a href="#" onclick="confirmation(<?=$buddyEditData[buddy];?>);"><img src="./gfx/delete_2.png" width="16"></a></td>
                <td><?=showUserPicture($buddyEditData[buddy], 30);?></td>
                <td><form action="modules/settings/index.php?action=friends&reload=1" target="submitter" method="post"><?=$userpicture;?>&nbsp;
                    <input type="hidden" name="buddy" value="<?=$buddyEditData[buddy];?>">
                    <input type="text" name="alias" value="<?=$alias;?>">&nbsp;
                    <input type="submit" name="submit" value="save">
                    </form></td>
             </tr>
         <? } ?>  
         </table>
         </center>
         </ul>
         </div>
     </div><script type="text/javascript">
function confirmation(id) {
	var answer = confirm("Are you sure to delete this buddy?")
	if (answer){
            
		$("#submitter").load("modules/settings/index.php?action=friends&reload=1&delete=1&buddy=" + id +"");
	}
	else{
            return false;
	}
}
</script>
<div>