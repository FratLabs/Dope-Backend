<?
require("config.local.php");
require("datahandler.php");


function authorize_user($login, $pass) {
	global $pdo, $table_prefix;
	$q = $pdo->prepare("SELECT `id`, `hash_id` FROM `{$table_prefix}users` WHERE `email`=? AND `password_md5`=? AND `verify_code`=\"\"");
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



/* Подключение к серверу MySQL */
//$sql = new mysqli($hostname, $username, $password, $database);
$pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);

?>