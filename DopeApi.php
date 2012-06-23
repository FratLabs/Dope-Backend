<?php
//THIS IS NOT INCLUDED IN Pulse!  Only on FratLabs server!  Must FTP over after updates! 
require_once 'FoursquareAPI.class.php';
require_once 'CFacebook.php';
?>
<?php
OnLoad();
class funcs_code 
{   
  // var $conn="";
//   var $dba="dbdope"; 
//   var $host="localhost";
//   var $user="root";
//   var $pass="";   
//   
   var $conn="";
   var $dba="dope"; 
   var $host="localhost";
   var $user="root";
   var $pass="McMHZhbC";
   
   public function connection()
	{
	 	$this->conn=mysql_connect($this->host,$this->user,$this->pass) or die(mysql_error());	
 	 	$this->dba=mysql_select_db($this->dba,$this->conn) or die(mysql_error());	
	}
	
   public function query($sql_q)
   {
     $result=mysql_query($sql_q);
	 if(!$result){die(mysql_error());}else{return $result;}
   }  
}
   
 function OnLoad()
   {
	   $method = $_GET['method'];
	   if($method == 'SignIn')
	   {
		   SignIn();
	   }
       else if($method == 'SignUp')
	   {
		   SignUp();
	   }
	   else if ($method == 'ActivateUser')
	   {
	   	   ActivateUser();
	   }
	   else if($method == 'Follows')
	   {
		   Follows();
	   }
	   else if($method == 'GetFollowersCount')
	   {
		   GetFollowersCount();
	   }
 	   else if($method == 'SaveLocations')
	   {
		   SaveLocations();
	   }
	   else if($method == 'GetLocations')
	   {
		   GetLocations();
	   }	       
   }   
	
	function SignIn()
	{
		$obj=new funcs_code();
   		$obj->connection();	
		$email = $_POST['email'];		
		$email = mysql_real_escape_string($email);
		$pwd  = $_POST['pwd'];	
		$pwd = md5(mysql_real_escape_string($pwd));
		$output = '';
		$q = "SELECT * FROM users WHERE Email='$email' AND Password='$pwd'";
		$res=mysql_query($q);
        $row=mysql_fetch_row($res);
		if(mysql_num_rows($res)>0)
		{
			if($row["5"] == "10")
			{
			   $output = $row["0"];
			}
			else
			{
			   $output = "-1";
			}
		}		
		else
		{
			$output = "0";
		}				
		echo json_encode($output); 
	}
	
	function SignUp()
	{
		$obj=new funcs_code();
   		$obj->connection();		  
		$activationcode = mt_rand();
		$output = "";	
		$date = date('Ymd');
		$email = $_POST["email"];
		$pwd = $_POST["pwd"];
		$email = mysql_escape_string($email);
		$pwd = mysql_escape_string(md5($pwd));
$q = "SELECT * FROM users WHERE Email = '$email'";
		$res=mysql_query($q);	
		$row=mysql_fetch_row($res);	
		if(mysql_num_rows($res)==0)
		{	
		$q = "INSERT INTO users VALUES ('','$email','$pwd','$activationcode','$date',0)";
		if(mysql_query($q))
		{
			$output = "1";
			$message = "Thank you for signing up for Dope. Please find your activation code below:"."<br /><br />";
		$message = $message.$activationcode."<br /><br />";
		$message = $message."Please copy this code and insert it in the Dope mobile application to get started."."<br /><br />";
		$message = $message."If you have received this message in error and did not wish to sign up for Dope, please just ignore this message."."<br /><br />";
		$message = $message."<b>Why?</b>"."<br />";
		$message = $message."We do this to ensure our application stays exclusive to college students in a secure, trusted environment. By inputting this code, you are verifying you are the owner of a .edu email address - and therefore are part of a young, fun environment. This helps us keep unwanted people out to ensure the best and safest user experience possible."."<br /><br />";
$message = $message."<b>What is Dope?</b>"."<br />";
$message = $message."Dope is a mobile application for college students bringing them the most exclusive college discounts and event notifications businesses have to offer. Use Dope to follow your favorite companies and stay up to date on their latest specials and events. Companies can only send you a maximum of 5 notices per month so you'll be sure only to receive the information you want. You can unfollow businesses at anytime."."<br /><br />"; 
$message = $message."To report abuse, or if you have any other questions, please contact us at Dope@FratLabs.com"."<br /><br />"; 
$message = $message."Stay Classy,"."<br /><br />";
$message = $message."The Dope Team";
			$to = $email;
			$subject = "Your activation code";
			$from ="Dope@FratLabs.com";
			$headers = "From:" . $from;
$headers .= "MIME-Version: Dope College App\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 
			@mail($to,$subject,$message,$headers);
		}
		else
		{
		   $output = "0";		
		}
}
else
		{
		   $output = "-1";		
		}
		echo json_encode($output); 
	}
	
	function ActivateUser()
	{
		$obj=new funcs_code();
   		$obj->connection();		  
		$activationcode = $_POST["code"];
		$output = "";
		$q = "UPDATE users SET Status = 10 WHERE ActivationCode = '$activationcode'";
		if(mysql_query($q))
		{
			$q = "SELECT UserID FROM users WHERE ActivationCode = '$activationcode' AND Status = 10";
			$res=mysql_query($q);
			$row=mysql_fetch_row($res);
			if(mysql_num_rows($res)>0)
			{
				$output = $row["0"];
			}
			else
			{
			   $output = "0";		
			}	
		}
		else
		{
		   $output = "0";		
		}
		echo json_encode($output); 
	}
	
	function Follows()
	{
		$obj=new funcs_code();
   		$obj->connection();
		$output = "";	
		$date = date('Ymd');
		$recid = $_POST["recid"];
		$uid = $_POST["uid"];
		$tokenid = $_POST["deviceid"];	
		$isfollow = $_POST["follow"];
		if($isfollow == "1")
		{		
			$q = "INSERT INTO followers VALUES ('','$recid','$uid','$tokenid','$date')";
			if(mysql_query($q))
			{			
				define ('URBAN_APP_MASTERKEY', '6Qa0Kp1jTASEa_reCJU83Q'); 
				define ('URBAN_APIKEY','jwKh7JRjTZWQ5vZTnVqXfg'); 
				define ('URBAN_APP_SECRETKEY','cbgSTg9JTaGEBbIbREHHkQ'); 
				define('PUSHURL', 'https://go.urbanairship.com/api/device_tokens/'); 
				 
				$contents = array(); 
				$contents['alias'] = "myalias";		
				 
				$push = array("aps" => $contents);			 
				$json = json_encode($push); 
				$url = PUSHURL.$tokenid; 
				//echo $json; //display the actual content 
				 
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $url); 
				curl_setopt($ch, CURLOPT_USERPWD, URBAN_APIKEY . ':' . URBAN_APP_MASTERKEY); 
				curl_setopt($ch, CURLOPT_POST, 1); 
				curl_setopt($ch, CURLOPT_POSTFIELDS, '$json'); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, True); 
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
				 
				curl_exec($ch);
				curl_close($ch);
				$output = "1";
			}
			else
			{
			   $output = "0";		
			}
		}
		else
		{
			$q = "DELETE FROM followers where RecID = '$recid' AND UserID = $uid AND DeviceID = '$tokenid'";
			if(mysql_query($q))
			{
				$output = "1";
			}
			else
			{
			   $output = "0";		
			}
		}
		echo json_encode($output); 
	}
	
	function GetFollowersCount()
	{
		$obj=new funcs_code();
   		$obj->connection();
		$output = "";		
		$recid = $_GET["recid"];			
		$q = "SELECT COUNT(*) FROM followers WHERE RecID = '$recid'";
		$res=mysql_query($q);
		$row=mysql_fetch_row($res);
		if(mysql_num_rows($res)>0)
		{
			$output = $row["0"];
		}
		else
		{
		   $output = "0";		
		}
		echo json_encode($output); 
	}

function GetLocations()
{
		$obj=new funcs_code();
   		$obj->connection();
		$output = "";
        $query = "";
		$lat = $_GET["lat"];
		$lng = $_GET["lng"];
        $query = mysql_real_escape_string($_GET["search"]);
		$lat1 = 0;
		$lat2 = 0;
		$lng1 = 0;
		$lng2 = 0;
		$radius = 10000;
		$date = date('Ymd');
		$datedifference = 0;
		$milesPerDegree = 0.868976242 / 60.0 * 1.2;
		if($query == "")
		{
        	$milesDistance = 0.5;
		}
		else
		{
			$milesDistance = 7;
		}
        $degrees = $milesPerDegree * $milesDistance;

        $lng1 = $lng - $degrees;
        $lng2 = $lng + $degrees;
        $lat1 = $lat - $degrees;
        $lat2 = $lat + $degrees;
		
		$q = "SELECT DATEDIFF(CURDATE(),max(DateModified)) AS DataFetchedDate FROM locations WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2)";
		$res=mysql_query($q);	
		$row=mysql_fetch_row($res);	
		if(mysql_num_rows($res)>0)
		{
		   $datedifference = $row["0"];
		}
		
		if($query == "")
		{
		$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2)";
		}
		else
		{
		$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2) AND (locations.Name LIKE '%$query%')";
		}
	
		$res=mysql_query($q);		
		if(mysql_num_rows($res) > 0 && $datedifference < 30)
		{		
		 	$locations = array();
			while($location = mysql_fetch_assoc($res)) {
      		$locations[] = array('location'=>$location);
    		}
			header('Content-type: application/json');
			echo json_encode(array('location'=>$locations));
		}
		else
		{		
		    $clientId = 'NKPDHOIAXZWAFOEYJFAC1A5WAXXCOEEWOTGYGLPJCEIKSWRS';
			$clientSecret = 'DZ01LVSPZ5ZMFD3AQNOGV4SGESMSAJVYBC5DZ4GZZ1COBV04';			
			$params = array("ll"=>$lat.",".$lng , "v"=> $date , "query"=> $query, "radius"=> $radius);			
			$foursquare= new FoursquareAPI($clientId, $clientSecret);
			$data = $foursquare->GetPublic('venues/search',$params);
        	$results = json_encode($data);	
			
        	$locations = json_decode(trim(str_replace("\r\n", '\\n', $results)));
			$locations = json_decode($locations);			
			foreach ($locations->response->venues as $result)
        	{
				$LocationID = "";
				$Name = "";
				$Phone = "";
				$FormattedPhone = "";
				$Address = "";
				$CrossStreet = "";
				$Lat = "";
				$Lng = "";
				$Distance = "";
				$PostalCode = "";
				$City = "";
				$State = "";
				$Country = "";
				
				$CategoryID = "";
				$CategoryName = "";
				$PluralName = "";
				$ShortName = "";
				$ImageSrc = "";
				
			
				if(!empty($result->id))
				{
					$LocationID = $result->id;
				}
				if(!empty($result->name))
				{
					$Name = $result->name;
					$Name = mysql_real_escape_string($Name);
				}			
				if(!empty($result->contact->phone))
				{
					$Phone = $result->contact->phone;
				}
				if(!empty($result->contact->formattedPhone))
				{
					$FormattedPhone = $result->contact->formattedPhone;
				}			
				if(!empty($result->location->address))
				{
					$Address = $result->location->address;
				}
				if(!empty($result->location->crossStreet))
				{
					$CrossStreet = $result->location->crossStreet;
				}
				if(!empty($result->location->lat))
				{
					$Lat = $result->location->lat;
				}
				if(!empty($result->location->lng))
				{
					$Lng = $result->location->lng;
				}
				if(!empty($result->location->distance))
				{
					$Distance = $result->location->distance;
				}
				if(!empty($result->location->postalCode))
				{
					$PostalCode = $result->location->postalCode;
				}
				if(!empty($result->location->city))
				{
					$City = $result->location->city;
				}
				if(!empty($result->location->state))
				{
					$State = $result->location->state;
				}
				if(!empty($result->location->country))
				{
					$Country = $result->location->country;
				}
				if(count($result->categories) > 0)
				{
					$CategoryID = $result->categories[0]->id;
					$CategoryName = $result->categories[0]->name;
					$CategoryName = mysql_real_escape_string($CategoryName);
					$PluralName = $result->categories[0]->pluralName;
					$ShortName = $result->categories[0]->shortName;
					$ImageSrc = $result->categories[0]->icon->prefix;
				}
				
				$q="SELECT * FROM locations WHERE LocationID = '$LocationID'";
				$result=mysql_query($q);
				$rn =mysql_num_rows($result);					
				if($rn=="0")
				{
$q = "INSERT INTO locations VALUES ('$LocationID','$Name','$Phone','$FormattedPhone','$Address','$CrossStreet',$Lat,$Lng,$Distance,'$PostalCode','$City','$State','$Country','$date')";
				}
				else
				{
					$q = "UPDATE locations SET Name='$Name',Phone='$Phone',FormattedPhone='$FormattedPhone',Address='$Address',CrossStreet='$CrossStreet',
						  Lat=$Lat,Lng=$Lng,Distance=$Distance,PostalCode='$PostalCode',City='$City',State='$State',Country='$Country', DateModified='$date'
						  WHERE LocationID = '$LocationID'";
				}
		
				if(mysql_query($q))
				{
					if($CategoryID != "")
					{
						$q="SELECT * FROM categories WHERE LocationID = '$LocationID' AND CategoryID = '$CategoryID'";
						$result=mysql_query($q);
						$rn =mysql_num_rows($result);						
						if($rn=="0")
						{
							$q = "INSERT INTO categories VALUES('$CategoryID','$LocationID','$CategoryName','$PluralName','$ShortName','$ImageSrc')";
						}
						else
						{
							$q = "UPDATE categories SET Name='$CategoryName',PluralName='$PluralName',ShortName='$ShortName',ImageSrc='$ImageSrc' 
								  WHERE CategoryID = '$CategoryID' AND LocationID = '$LocationID'";
						}
				
						if(mysql_query($q))
						{
							$output = "1";
						}
						else
						{
						   $output = "0";		
						}	
					}		
				}
				else
				{
				   $output = "0";		
				}
						
		}
		if($query == "")
		{
		$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2)";
		}
		else
		{
		$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (locations.Name LIKE '%$query%')";
		}
		 
		$res=mysql_query($q);		
		if(mysql_num_rows($res)>0)
		{
		 	$locations = array();
			while($location = mysql_fetch_assoc($res)) {
      		$locations[] = array('location'=>$location);
    		}
			header('Content-type: application/json');
			echo json_encode(array('location'=>$locations));
		}
		else
		{
		   $output = "0";
		   echo json_encode($output);		
		}
	}		
}	
function GetFacebookUser()
{
	$Facebook = new CFacebook();
	$User = $Facebook->GetFBProfileInformation();
	
	
}	