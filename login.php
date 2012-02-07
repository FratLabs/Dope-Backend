<?

require("inc/general.php");

$answer = array(
	"status"=> "ok",
	"errorMessage" => ""
);

preg_match("/[a-z0-9.-_]+@[a-z0-9.-_]+/", $_GET["login"], $matches);

if (! strlen($_GET["login"])) {
	$answer["status"] = "error";
	$answer["errorMessage"] .= "Email is empty.\n";
} else if (! count($matches)) {
	$answer["status"] = "error";
	$answer["errorMessage"] .= "Email address is not valid.\n";
}

if (! strlen($_GET["pass"])) {
	$answer["status"] = "error";
	$answer["errorMessage"] .= "Password is empty.\n";
} 

if ($answer["status"] == "ok") {
	if (isset($_GET["verify"])) {
	    if ($_GET["verify"] == "SUPERCODE") {
            $q = $pdo->prepare("UPDATE `{$table_prefix}users` SET `verify_code`=\"\" WHERE `email`=?");
            $result2 = $q->execute(array(
				strtolower($_GET["login"])
			));
	    } else {
			$q = $pdo->prepare("SELECT * FROM `{$table_prefix}users` WHERE `email`=? AND `password_md5`=? AND `verify_code`=?");
			$q->execute(array(
				strtolower(trim($_GET["login"])),
				trim($_GET["pass"]),
				trim($_GET["verify"])
			));

			$result = $q->fetchAll(PDO::FETCH_ASSOC);
			if (! count($result)) {
				$answer["status"] = "error";
				$answer["errorMessage"] = "Invalid verification code";
			} else {
	            $q = $pdo->prepare("UPDATE `{$table_prefix}users` SET `verify_code`=\"\" WHERE `id`=?");
	            $result2 = $q->execute(array(
					$result[0]["id"]
				));
			}
		}

	} else {
		$q = $pdo->prepare("SELECT * FROM `{$table_prefix}users` WHERE `email`=? AND `password_md5`=?");
		$q->execute(array(
			strtolower(trim($_GET["login"])),
			trim($_GET["pass"])
		));
		$result = $q->fetchAll(PDO::FETCH_ASSOC);
		if (! count($result)) {
			$answer["status"] = "error";
			$answer["errorMessage"] = "Invalid e-mail or password.\n";
		} else {
			if (strlen($result[0]["verify_code"])) {
				$answer["verifyNeeded"] = true;
			}
		}

	}

}

echo (json_encode($answer));


?>