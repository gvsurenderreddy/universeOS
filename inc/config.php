<?php

//disable error reporting
error_reporting(0);
//@ini_set('display_errors', 0);


//include dbConfig
include("config/dbConfig.php");

//serverstuf
$universeURL = 'http://localhost/universe'; //url of current installation


$timestamp = time();


//start session
if(!isset($_SESSION)){ 
    session_start(); 
}

//mysql connect	or die
mysql_connect("$server","$user","$password");
mysql_select_db("$db");

if(!mysql_connect("$server","$user","$password") OR !mysql_select_db("$db")) {
    die("Something went wrong with the Database... WTF?!");
}

//$mysqli = new mysqli("$server", "$user", "$password", "$db");
//if ($mysqli->connect_errno) {
//    echo "Something went wrong with the Database... WTF?! - Error Notification: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; //NOTIFICATION SHOULD BE REMOVED
//}


define('analytic_script',  "<!-- Piwik --> <script type=\"text/javascript\"> var _paq = _paq || []; _paq.push(['trackPageView']); _paq.push(['enableLinkTracking']); (function() { var u=\"//analytics.transparency-everywhere.com/piwik/\"; _paq.push(['setTrackerUrl', u+'piwik.php']); _paq.push(['setSiteId', 2]); var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s); })(); </script> <noscript><p><img src=\"//analytics.transparency-everywhere.com/piwik/piwik.php?idsite=2\" style=\"border:0;\" alt=\"\" /></p></noscript> <!-- End Piwik Code -->");
?>