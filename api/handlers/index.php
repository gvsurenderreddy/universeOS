<?
include("../../inc/config.php");
include("../../inc/functions.php");
$handler = new handler();
$handler->api($_POST['handler_title'], $_POST['action'], $_POST['parameters']);