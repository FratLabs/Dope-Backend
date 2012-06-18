<?php

require_once('facebook.php');

$config = array('appid' => '335313573148955','secret'=>'9d240057462bab9fbd5d05cba6806638');

$facebook = new Facebook($config);

$user_id = $facebook->getUser();

?>

<html>
	<head></head>
	<body>

<?php

if($user_id)
{
	try {
		$user_profile = $facebook->api('/me','GET');
		echo "Name: ".$user_profile['name'];
	} catch(FacebookApiException $e){
		$login_url = $facebook->getLoginUrl();
		echo 'Please <a href="'.$login_url.'">login.</a>';
		error_log($e->getType());
		error_log($e->getMessage());
	}
}else{
	$login_url = $facebook->getLoginUrl();
	echo 'Please <a href="'.$login_url.'">login.</a>';
}
?>