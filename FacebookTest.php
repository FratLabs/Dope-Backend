<?php

require_once 'CFacebook.php';


$MyFacebook = new CFacebook();

$Answer = $MyFacebook->GetFBProfileInformation();
echo "<b>JSON Encoded Data:</b><br /> ";
var_dump($Answer);
$Data = json_decode($Answer,true);
echo "<br />";
echo "<br />";
echo "<b>JSON Decoded Data:</b><br />";
var_dump($Data);
echo "<br />";
echo "<br />";
echo "<b>Profile Info:</b> <br />";
echo "<b>Picture:</b> <br />";
echo "<img src=\"https://graph.facebook.com/".$Data['UserName']."/picture\" />";
echo "<br />";
echo "Name: ".$Data["Name"]."<br />";
echo "Hometown ID: ".$Data["HometownID"]."<br />";
echo "Hometown Name: ".$Data["HometownName"]."<br /><br />";
echo "<b>Favorite Teams:</b><br />";
foreach($Data["FavoriteTeams"] as $Team)
{
	echo "Team Name: ".$Team["name"]."<br />";
}

?>