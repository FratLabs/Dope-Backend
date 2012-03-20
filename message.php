<?
require("inc/general.php");

$answer = array(
	"status"=> "ok",
	"errorMessage" => ""
);

if ($user = authorize_user($_GET["login"], $_GET["pass"])) {
	if (strlen($_GET["action"])) {
		$answer["hash_id"] = $user["hash_id"];
		switch($_GET["action"]) {
			case "save":
				saveProfile($_REQUEST["data"]);
			    break;
			case "get":
				$answer["profile"] = getProfile($user["hash_id"]);
			    break;
			case "getPhotos":
			    $answer["photos"] = getPhotos($user["hash_id"]);
			    break;
			case "savePhoto":
			    $answer["photos"] = savePhoto($_FILES["photo"]);
			    break;
			case "delPhoto":
			    $answer["photos"] = delPhoto($_REQUEST["index"]);
			    break;
			case "saveAvatar":
			    $answer["avatar"] = saveAvatar($_FILES["avatar"]);
			    break;
			case "updateCoords":
			    $answer["status"] = updateCoords($_REQUEST["lon"], $_REQUEST["lat"]);
			case "getFriends":
			    break;
			case "getEvents":
			    $answer["events"] = getEvents();
			case "sendMessage":

			    break;
			case "search":
			    $answer["people"] = searchPeople(trim($_REQUEST["q"]));
		}
	} else {
		$answer["errorMessage"] = "I'm not a teapot";
	}
} else {
	$answer["status"] = "error";
	$answer["errorMessage"] = "Invalid e-mail or password.\n";
}

//print("<pre>");
//print_r($answer);
//print("</pre>");

echo (json_encode($answer));


?>