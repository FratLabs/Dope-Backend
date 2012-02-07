<?

require("inc/general.php");

$answer = array(
	"status"=> "ok",
	"errorMessage" => "",
	"verifyNeeded" => true
);

preg_match("/[a-z0-9.-_]+@[a-z0-9.-_]+/", $_GET["login"], $matches);

if (! strlen($_GET["login"])) {
	$answer["status"] = "error";
	$answer["errorMessage"] .= "Email is empty.\n";
} else if (! count($matches)) {
	$answer["status"] = "error";
	$answer["errorMessage"] .= "Email address is not valid.\n";
}
if (! strlen($_GET["pass"]) || $_GET["pass"] == "d41d8cd98f00b204e9800998ecf8427e") {
	$answer["status"] = "error";
	$answer["errorMessage"] .= "Password is empty.\n";
}

if ($answer["status"] == "ok") {

	$verify_code = "";
	for ($i = 0; $i < 6; $i++) {
		$verify_code .= rand(0, 9);
	}

	$q = $pdo->prepare("SELECT * FROM `{$table_prefix}users` WHERE `email`=?");
	$q->execute(array(
		strtolower(trim($_GET["login"]))
	));
	$result = $q->fetchAll(PDO::FETCH_ASSOC);
	if (! count($result)) {

		$q = $pdo->prepare("INSERT INTO `{$table_prefix}users` SET `email`=?, `password_md5`=?, `verify_code`=?, `hash_id`=?");
		$q->execute(array(
			strtolower(trim($_GET["login"])),
			trim($_GET["pass"]),
			$verify_code,
			rand(100000000,
				 999999999)
		));
		$answer["verifyNeeded"] = true;
		
		
		$mail_headers =
			"From: site@jets-app.com\n" .
			"Reply-To: ". strtolower(trim($_GET["login"])) ."\n" .
			"X-Mailer: PHP/" . phpversion();

		mail(
			strtolower(trim($_GET["login"])),
			"Dope Activation Code",
			"EMAIL: " . strtolower(trim($_GET["login"])) ."\n".
			"CODE: ".$verify_code,
			$mail_headers
		);
		
	} else {
		$answer["status"] = "error";
		$answer["errorMessage"] .= "This email already registered.\n";
	}


}
echo (json_encode($answer));

?>