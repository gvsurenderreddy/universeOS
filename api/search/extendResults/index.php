<?
include("../../../inc/config.php");
include("../../../inc/functions.php");

include("../../../inc/classes/class_search.php");


    $search = new search($_POST['search']);
    
    echo $search->parseSearchResults();
    
echo '<script>';
    echo"search.initResultHandlers('".htmlentities($_POST['search'])."');";
echo '</script>';