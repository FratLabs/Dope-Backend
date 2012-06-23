<?php
require_once 'facebook.php';


class CFacebook
{
	
	//private variables
	private $Config = "";
	private $FBObject = "";
	//constructor
	public function CFacebook()
	{
		$this->Config = array('appID' => '391367060925444','secret'=>'caecd3e920056ad747bd08f3b8b099c3');
		$this->FBObject = new Facebook($Config);
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
				/ If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl(); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
			}
		}else
			{
				/ If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl(); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
			}
	}
};

?>
