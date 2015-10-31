<!DOCTYPE html>
<html lang="mul" dir="ltr">
	<head>
<?
include("../../../inc/config.php");
	$type = "Element";
	$itemId = $_GET['id'];
        
        $db = new db();
        $data = $db->query('elements', array('id', $itemId), array('folder','title'));
	$title = $data['title'];
	
	$metaDescriptionContent = "universeOS $type $title.";
	$metaDescriptionContent .= "Discover the social webOS. Connect with your friends, read your favourite book or RSS-Feed, watch your favourite movie, listen your favourite song and be creative...";
	echo"<meta name=\"description\" content='$metaDescriptionContent'>";


	//page title
	echo"<title>";
	echo"universeOS - $type $title";
	echo"</title>\n";
	
	
	
	//facebook open graph picture
	if(empty($picture)){
            echo"<meta property=\"og:image\" content=\"http://universeos.org/gfx/logo.png\"/>\n";
	}else{
            echo"<meta property=\"og:image\" content=\"http://universeos.org/$picture\"/>\n";
	}
	
	//meta information
	echo"<meta name=\"title\" content=\"universeOS - $type $title\">";
	echo"<meta name=\"keywords\" content=\"universeOS $type $title, $type, $title, $type $title\">";
	?>
	?>
		<script>
		window.location.href = "http://universeos.org/index.php?openFromLink=element&element=<?=$_GET['id'];?>";
		</script>
	</head>
</html>