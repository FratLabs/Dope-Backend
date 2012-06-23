<?php
  // Remember to copy files from the SDK's src/ directory to a
  // directory in your application on the server, such as php-sdk/
  require_once('facebook.php');

  $config = array(
    'appId' => '391367060925444',
    'secret' => 'caecd3e920056ad747bd08f3b8b099c3',
  );

  $facebook = new Facebook($config);
  $user_id = $facebook->getUser();
?>
<html>
  <head></head>
  <body>
  <?php
    if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {

        $user_profile = $facebook->api('/me','GET');
		$friends = $facebook->api('/me/friends','GET');
		echo "ID: " . $user_profile['id']."<br />";
        echo "Name: " . $user_profile['name']."<br />";
		echo "Access token: ".$facebook->getAccessToken()."<br />";
		echo "updated time: ". $user_profile['updated_time']."<br />";
		$work = $user_profile['work'];
		echo "My Work List: <br />";
		foreach($work as $job)
		{
			echo "Employer: ".$job['employer']['name']."<br />";
			echo "Position: ".$job['position']['name']."<br />";
		}
		
		echo "<br /><br />";
		echo "My Friends List: <br />";
		foreach($friends['data'] as $friend)
		{
			echo "ID: ".$friend['id']."<br />";
			echo "Name: ".$friend['name']."<br />";
			echo "<hr><br />";
		}

      } catch(FacebookApiException $e) {
      	echo "in exception";
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl(); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
      }   
    } else {
	echo "no user";
      // No user, print a link for the user to login
      $login_url = $facebook->getLoginUrl();
      echo 'Please <a href="' . $login_url . '">login.</a>';

    }

  ?>

  </body>
</html>