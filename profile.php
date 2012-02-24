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
				saveProfile($user["hash_id"], $_REQUEST["data"]);
			    break;
			case "get":
				$answer["profile"] = getProfile($user["hash_id"]);
			    break;
			case "getPhotos":
			    $answer["photos"] = getPhotos($user["hash_id"]);
			    break;
			case "savePhoto":
			    $answer["photos"] = savePhoto($user["hash_id"], $_FILES["photo"]);
			    break;
			case "delPhoto":
			    $answer["photos"] = delPhoto($user["hash_id"], $_REQUEST["index"]);
			    break;
			case "saveAvatar":
			    $answer["avatar"] = saveAvatar($user["hash_id"], $_FILES["avatar"]);
			    break;
			case "getFriends":
			    break;
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