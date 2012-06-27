<?php
require_once 'facebook.php';


class CFacebook
{
	
	//private variables
	private $Config = "";
	private $FBObject = "";
	private $Code = "";
	//constructor
	public function CFacebook()
	{
		$this->FBObject = new Facebook();
		$this->FBObject->setApiSecret('caecd3e920056ad747bd08f3b8b099c3');
  		$this->FBObject->setAppId('391367060925444');
		
	}
		
	//Get profile information
	public function GetFBProfileInformation()
	{
		$FBProfile = "";
		$ID = $this->FBObject->getUser();
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
        $login_url = $this->FBObject->getLoginUrl(); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
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
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
		 
			}
	}
};

?>
