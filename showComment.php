<?php
session_start();
include("inc/config.php");
include("inc/functions.php");
if(proofLogin()){
if(isset($_POST['comment'])) {
    echo'<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>';
    $commentClass = new comments();
    $commentClass->addComment($_POST['type'], $_POST['itemid'], $_POST['user'], $_POST['comment']);
    
    
    ?>
    <script>
    $('#<?=$_POST[type];?>Comment_<?=$_POST[itemid];?>', parent.document).html( function(){
        $(this).html('');
        $(this).load('doit.php?action=showSingleComment&type=<?=$_POST[type];?>&itemid=<?=$_POST[itemid];?>');
    });
    </script>
    <?php
    
    
}
}else{
    jsAlert("Please login or sign up to write a comment.");
}
$commentid = $_GET['id'];
$type = $_GET['type'];
$classComments = new comments();
$classComments->showComments($type, $commentid);
?>