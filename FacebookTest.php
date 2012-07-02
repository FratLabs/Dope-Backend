<?
/*
  // Remember to copy files from the SDK's src/ directory to a
  // directory in your application on the server, such as php-sdk/
  require_once('facebook.php');

  $facebook = new Facebook();
  $facebook->setApiSecret('caecd3e920056ad747bd08f3b8b099c3');
  $facebook->setAppId('391367060925444');
  $user_id = $facebook->getUser();
?>
<html>
  <head></head>
  <body>

  <?
    if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {

        $user_profile = $facebook->api('/me','GET');
        echo "Name: " . $user_profile['name'];

      } catch(FacebookApiException $e) {
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

      // No user, print a link for the user to login
      $login_url = $facebook->getLoginUrl();
	echo $facebook->getAccessToken()."<br />";
	echo $facebook->getAppId."<br />";
	echo $facebook->getApiSecret."<br />";
      echo 'Please <a href="' . $login_url . '">login.</a>';
    }
 * /*
 */

/*
require_once 'CFacebook.php';

$FB = new CFacebook();

$JSON = json_encode($FB->GetFBProfileInformation());
*/


require_once 'DopeApi.php';

$Answer = GetFacebookUser();
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



