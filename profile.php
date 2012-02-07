<?

require("inc/general.php");

$answer = array(
	"status"=> "ok",
	"errorMessage" => ""
);

if ($user = authorize_user($_GET["login"], $_GET["pass"])) {
	if (strlen($_GET["action"])) {
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
			    break;
			case "delPhoto":
			    break;
			case "":
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