<?
require("config.local.php");
require("datahandler.php");


$_DEFAULT_FRIEND_REQUEST_TEXT = "Please, allow me to add you to my friend's list";

function authorize_user($login, $pass) {
	global $pdo, $table_prefix;
	$q = $pdo->prepare("SELECT * FROM `{$table_prefix}users` WHERE `email`=? AND `password_md5`=? AND `verify_code`=\"\"");
	$q->execute(array(
		strtolower(trim($login)),
		trim($pass)
	));
	$result = $q->fetchAll(PDO::FETCH_ASSOC);
	if (! count($result)) {
		return false;
	} else {
	    return $result[0];
	}
}


$pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);

?>