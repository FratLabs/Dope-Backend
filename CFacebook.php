<?php
require_once 'facebook.php';


class CFacebook
{
	
	//private variables
	private $Config = "";
	private $FBObject = "";
	private $Code = "";
	private $Session = "";
	private $AppId = '391367060925444';
	private $ApiSecret = 'caecd3e920056ad747bd08f3b8b099c3';
	//constructor
	public function CFacebook()
	{
		$this->FBObject = new Facebook();
		$this->FBObject->setApiSecret($this->ApiSecret);
  		$this->FBObject->setAppId($this->AppId);
	}
		
	//Get profile information
	public function GetFBProfileInformation()
	{
		echo $_GET['signed_request'];
		echo $this->FBObject->getSignedRequest();
		$FBProfile = "";
		$ID = $this->FBObject->getUser();
		echo "<br />";
		if($ID)
		{
			try {
				$FBProfile = $this->FBObject->api('/me','GET');
				return $FBProfile;
			}catch (FacebookApiException $e)
			{
				//send back to UI to have user sign in
				
				// If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        echo "has ID <br />";
        $login_url = $this->FBObject->getLoginUrl(); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
		echo $this->FBObject->getAccessToken()."<br />";
		echo $this->FBObject->getApiSecret()."<br />";
        error_log($e->getType());
        error_log($e->getMessage());
			}
		}else
			{
				//return to UI taht user isn't logged in and have user re-sign in
								
				//If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $Page = "http://fratlabs.com/FacebookTest.php";
        $login_url = $this->FBObject->getLoginUrl(array('scope'=>'email,publish_stream,user_likes,user_hometown','redirect_uri'=>$Page));
		//$logout_url = $this->FBObject->getLogoutUrl(array('next'=>'http://fratlabs.com/FacebookTest.php')); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
		//echo 'Logout Url: <a href="'. $logout_url.'">Logout</a>';
        error_log($e->getType());
        error_log($e->getMessage());
		 
			}
			
		//echo $this->FBObject->getLogoutUrl(array('next'=>'http://fratlabs.com/FacebookTest.php'));
	}



};

?>
