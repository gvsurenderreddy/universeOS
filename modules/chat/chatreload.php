<?
session_start();
require_once("../../inc/config.php");
require_once("../../inc/functions.php");
$buddy = $_GET[buddy];
if(proofLogin()){
	
	
	
	
$userid = getUser();
$buddyName = str_replace("_"," ",$buddy); //get username of receiver
$buddyData = getUserData(usernameToUserid($buddyName));

//get userdata of 
$userData = getUserData($userid);
$buddy = $buddyData['userid'];
$buddyName = str_replace(" ","_",$buddyData['username']);
$intWindows = "$buddy.key";
if(isset($_SESSION[$intWindows])){
    $lockIcon = "locked.png";
    $cryptText = "<a href=\"doit.php?action=chatUnsetCrypt&buddy=$buddy&buddyname=$buddyName\" target=\"submitter\">&nbsp;deactivate key</a>";
}else{
    $lockIcon = "lock.png";
}

markMessageAsRead($buddy, $userid);
if(empty($_GET['initter'])){
 ?>
      <div class="chatMainFrame">
          <header class="grayBar">
          	  <!-- toggle description key box -->
              <span><a href="javascript: toggleKey('<?=$buddyName;?>');" id="toggleKey_<?=$buddyName;?>"><i class="lockIcon"></i></a></span>
              
              <!-- buddydata -->
              <span><?=showUserPicture($buddyData[userid], 20);?></span>
              <span><a href="#" onclick="showProfile(<?=$buddyData[userid];?>); return false;"><?=$buddyData[username];?></a></span>
          </header>
          <!-- box for caht encription key -->
          <div id="chatKeySettings_<?=$buddyName;?>" class="chatKeySettings">
          </div>
          <div id="test_<?=$buddyName;?>" class="dialog">
<? } ?>          
		  <script>
		   	chatEncrypt('<?=$buddyName;?>');
		  </script>
          <div class="chatMainFrame_<?=$buddyName;?>">
			  <?
			  showMessages($userid, $buddy, "0,10");
		      unset($intWindows);?>
              <div onclick="chatLoadMore('<?=$buddyName;?>', '1');">...load more</div>
          </div>
          <?
          if(empty($_GET[initter])){ ?>
              
          </div>
      </div>
      <div class="chatAdditionalSettings" onclick="$(this).hide(); return true;">
          <ul>
              <li><a class="smiley smiley1" onclick="addStrToChatInput('<?=$buddy;?>', ':\'(');"></a><a class="smiley smiley2" onclick="addStrToChatInput('<?=$buddy;?>', ':|');"></a><a class="smiley smiley3" onclick="addStrToChatInput('<?=$buddy;?>', ';)');"></a><a class="smiley smiley4" onclick="addStrToChatInput('<?=$buddy;?>', ':P');"></a></li>
              <li><a class="smiley smiley5" onclick="addStrToChatInput('<?=$buddy;?>', ':D');"></a><a class="smiley smiley6" onclick="addStrToChatInput('<?=$buddy;?>', ':)');"></a><a class="smiley smiley7" onclick="addStrToChatInput('<?=$buddy;?>', ':(');"></a><a class="smiley smiley8" onclick="addStrToChatInput('<?=$buddy;?>', ':-*');"></a></li>
              <li><a href="#" onclick="popper('doit.php?action=chatSendItem&buddy=<?=$buddyData[userid];?>');">Send File</a></li>
          </ul>
      </div>
      <footer class="blackGradient">
          <center style="margin-top: 6px;">
              <form action="doit.php?action=chatSendMessage&buddy=<?=$buddy;?>&buddyname=<?=$buddyName;?>" method="post" target="submitter"  autocomplete="off" onsubmit="chatMessageSubmit('<?=$buddyName;?>', '<?=$buddy;?>');">
                  <a class="btn" onclick="$('.chatAdditionalSettings').toggle();">
                      <i class="icon-plus"></i>
                  </a>
                  <input type="text" placeholder="type a message..." name="message" class="input border-radius chatInput" id="chatInput_<?=$buddy;?>" style="">
				  <input type="hidden" name="cryption" value="false" id="chatCryptionMarker_<?=$buddyName;?>">
                  <input type="submit" value="Send" class="btn">
              </form>
          </center>
      </footer>
<script>
    $("#toggleKey<?=$buddy;?>").click(function () {
    $("#toggleValue<?=$buddy;?>").show("slow");
    });
</script>
<?}}?>