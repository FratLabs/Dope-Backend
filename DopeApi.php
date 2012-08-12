<<<<<<< HEAD
<?php //THIS IS NOT INCLUDED IN Pulse!  Only on FratLabs server!  Must FTP over after updates!
require_once 'FoursquareAPI.class.php';
require_once 'CFacebook.php';
?>
<?php
//OnLoad();
class funcs_code {
	// var $conn="";
	//   var $dba="dbdope";
	//   var $host="localhost";
	//   var $user="root";
	//   var $pass="";
	//
	var $conn = "";
	var $dba = "dope";
	var $host = "localhost";
	var $user = "root";
	var $pass = "McMHZhbC";


	
	public function connection() {
		/////	Connect to mysql server
		$this -> conn = mysql_connect($this -> host, $this -> user, $this -> pass) or die(mysql_error());
		/////	Sekect database
		$this -> dba = mysql_select_db($this -> dba, $this -> conn) or die(mysql_error());
	}

	public function query($sql_q) {
		/////	Put all data from $sql_q into $result
		$result = mysql_query($sql_q);
		
		/////	If nothing is copied over, throw connection error message
		if (!$result) {die(mysql_error());
		} else {
		/////	Otherwise, return	
			return $result;
		}
	}

};

function OnLoad() {
	/////	Load method value into appropriate register from user.
	$method = $_GET['method'];
	
	/////	Select appropriate function to run
	if ($method == 'SignIn') {
		SignIn();
	} else if ($method == 'SignUp') {
		SignUp();
	} else if ($method == 'ActivateUser') {
		ActivateUser();
	} else if ($method == 'Follows') {
		Follows();
	} else if ($method == 'GetFollowersCount') {
		GetFollowersCount();
	} else if ($method == 'SaveLocations') {
		SaveLocations();
	} else if ($method == 'GetLocations') {
		GetLocations();
	} else if ($method == 'GetFacebookUser') {
		GetFacebookUser();
	} else if ($method == 'UpdateUser') {
		UpdateUser();
	} else if ($method == 'GetUser') {
		GetUser();
	} else if ($method == 'AddFriend') {
		AddFriend();
	} else if ($method == 'RemoveFriend') {
		RemoveFriend();
	} else if ($method == 'FlagUser') {
		FlagUser();
	} else if ($method == 'AddClass') {
		AddClass();
	} else if ($method == 'RemoveClass'){
		RemoveClass();
	}
	
}

function SignIn() {
	//////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates an email/password entry field
	$email = $_POST['email'];
	$email = mysql_real_escape_string($email);
	$pwd = $_POST['pwd'];
	$pwd = md5(mysql_real_escape_string($pwd));
	/////	Resets output to blank, and searches for an exact email/pwd match.
	$output = '';
	$q = "SELECT * FROM chaysr_users WHERE Email='$email' AND Password='$pwd'";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	if (mysql_num_rows($res) > 0) {
		if ($row["5"] == "10") {
			$output = $row["0"];
		} else {
			$output = "-1";
		}
	} else {
		$output = "0";
	}
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function SignUp() {
	/////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a randomized activation code, sets the date, and takes in an email/pwd.
	$activationcode = mt_rand();
	$output = "";
	$date = date('Ymd');
	$email = $_POST["email"];
	$pwd = $_POST["pwd"];
	$email = mysql_escape_string($email);
	$pwd = mysql_escape_string(md5($pwd));
	/////	Searches for users with the same email
	$q = "SELECT * FROM chaysr_users WHERE Email = '$email'";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	/////	If there are no matches, follow the if statement below, otherwise return -1
	if (mysql_num_rows($res) == 0) {
	/////	Sets users values, and inserts them into the database. If successful, send email with activation code. Otherwise, return 0.	
		$q = "INSERT INTO users VALUES ('','$email','$pwd','$activationcode','$date',0)";
		if (mysql_query($q)) {
			$output = "1";
			$message = "Thank you for signing up for Chaysr. Please find your activation code below:" . "<br /><br />";
			$message = $message . $activationcode . "<br /><br />";
			$message = $message . "Please copy this code and insert it in the Chaysr mobile application to get started." . "<br /><br />";
			$message = $message . "If you have received this message in error and did not wish to sign up for Chaysr, please just ignore this message." . "<br /><br />";
			$message = $message . "<b>Why?</b>" . "<br />";
			$message = $message . "We do this to ensure our application stays exclusive to college students in a secure, trusted environment. By inputting this code, you are verifying you are the owner of a .edu email address - and therefore are part of a young, fun environment. This helps us keep unwanted people out to ensure the best and safest user experience possible." . "<br /><br />";
			$message = $message . "<b>What is Chaysr?</b>" . "<br />";
			$message = $message . "Chaysr is a mobile application for college students bringing them the most exclusive college discounts and event notifications businesses have to offer. Use Chaysr to follow your favorite companies and stay up to date on their latest specials and events. Companies can only send you a maximum of 5 notices per month so you'll be sure only to receive the information you want. You can unfollow businesses at anytime." . "<br /><br />";
			$message = $message . "To report abuse, or if you have any other questions, please contact us at mobile@FratLabs.com" . "<br /><br />";
			$message = $message . "Stay Classy," . "<br /><br />";
			$message = $message . "The Chaysr Team";
			$to = $email;
			$subject = "Your activation code";
			$from = "mobile@FratLabs.com";
			$headers = "From:" . $from;
			$headers .= "MIME-Version: Chaysr College App\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	/////	send email
			@mail($to, $subject, $message, $headers);
		} else {
			$output = "0";
		}
	} else {
		$output = "-1";
	}
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function ActivateUser() {
	/////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	/////	Sets up a text field for an activation code, and resets $output
	$obj = new funcs_code();
	$obj -> connection();
	$activationcode = $_POST["code"];
	$output = "";
	
	/////	Sends a query for users with a matching code, sets their status to activated.
	$q = "UPDATE chaysr_users SET Status = 10 WHERE ActivationCode = '$activationcode'";
	if (mysql_query($q)) {
	/////	If query is successful, copy result into $res, and fetch it's row # into $row
		$q = "SELECT UserID FROM chaysr_users WHERE ActivationCode = '$activationcode' AND Status = 10";
		$res = mysql_query($q);
		$row = mysql_fetch_row($res);
	/////	If there is one or more results, select the first result, otherwise, output 0.
		if (mysql_num_rows($res) > 0) {
			$output = $row["0"];
		} else {
			$output = "0";
		}
	} else {
		$output = "0";
	}
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

	/////	Used to follow a location
function Follows() {
	/////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Resets output, Sets date, and takes in User ID, Device ID, and a value for if user is currently following.
	$output = "";
	$date = date('Ymd');
	$recid = $_POST["recid"];
	$uid = $_POST["uid"];
	$tokenid = $_POST["deviceid"];
	$isfollow = $_POST["follow"];
	/////	If user is following...	
	if ($isfollow == "1") {
	/////	If query is successful, insert their information into the followers/push subscribers list
		$q = "INSERT INTO followers VALUES ('','$recid','$uid','$tokenid','$date')";
		if (mysql_query($q)) {
			define('URBAN_APP_MASTERKEY', '6Qa0Kp1jTASEa_reCJU83Q');
			define('URBAN_APIKEY', 'jwKh7JRjTZWQ5vZTnVqXfg');
			define('URBAN_APP_SECRETKEY', 'cbgSTg9JTaGEBbIbREHHkQ');
			define('PUSHURL', 'https://go.urbanairship.com/api/device_tokens/');

	/////	Initializes a contents array, and fills it with myalias
			$contents = array();
			$contents['alias'] = "myalias";
	/////	Signs user up for push updates.
			$push = array("aps" => $contents);
			$json = json_encode($push);
			$url = PUSHURL . $tokenid;
			//echo $json; 
			
	/////	Transfer and display the actual content through curl, output 1 if sucessful, else 0

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
		} else {
			$output = "0";
		}
	/////	Delete user from followers list, if sucessful, return 1, if not return 0
	} else {
		$q = "DELETE FROM followers where RecID = '$recid' AND UserID = $uid AND DeviceID = '$tokenid'";
		if (mysql_query($q)) {
			$output = "1";
		} else {
			$output = "0";
		}
	}
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function GetFollowersCount() {
	/////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	/////	Resets output, gets location's follow #
	$obj = new funcs_code();
	$obj -> connection();
	$output = "";
	$recid = $_GET["recid"];
	$q = "SELECT COUNT(*) FROM followers WHERE RecID = '$recid'";
	/////	Sends query to find followers.
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	/////	If followers count is above 0, set output to location's # of followers. If not, output 0.
	if (mysql_num_rows($res) > 0) {
		$output = $row["0"];
	} else {
		$output = "0";
	}
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function GetLocations() {
	/////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Resets output and query, and retrieves latitude and longitude.Creates a search field.
	$output = "";
	$query = "";
	$lat = $_GET["lat"];
	$lng = $_GET["lng"];
	$query = mysql_real_escape_string($_GET["search"]);
	/////	Initialize $lat and $lng holder variables
	$lat1 = 0;
	$lat2 = 0;
	$lng1 = 0;
	$lng2 = 0;
	$radius = 10000;
	$date = date('Ymd');
	$datedifference = 0;
	$milesPerDegree = 0.868976242 / 60.0 * 1.2;
	if ($query == "") {
		$milesDistance = 0.5;
	} else {
		$milesDistance = 7;
	}
	$degrees = $milesPerDegree * $milesDistance;

	$lng1 = $lng - $degrees;
	$lng2 = $lng + $degrees;
	$lat1 = $lat - $degrees;
	$lat2 = $lat + $degrees;

	$q = "SELECT DATEDIFF(CURDATE(),max(DateModified)) AS DataFetchedDate FROM locations WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2)";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	if (mysql_num_rows($res) > 0) {
		$datedifference = $row["0"];
	}

	if ($query == "") {
		$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2)";
	} else {
		$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2) AND (locations.Name LIKE '%$query%')";
	}

	$res = mysql_query($q);
	if (mysql_num_rows($res) > 0 && $datedifference < 30) {
		$locations = array();
		while ($location = mysql_fetch_assoc($res)) {
			$locations[] = array('location' => $location);
		}
		header('Content-type: application/json');
/////	returns output's contents in an JSON array 
		echo json_encode(array('location' => $locations));
	} else {
		$clientId = 'NKPDHOIAXZWAFOEYJFAC1A5WAXXCOEEWOTGYGLPJCEIKSWRS';
		$clientSecret = 'DZ01LVSPZ5ZMFD3AQNOGV4SGESMSAJVYBC5DZ4GZZ1COBV04';
		$params = array("ll" => $lat . "," . $lng, "v" => $date, "query" => $query, "radius" => $radius);
		$foursquare = new FoursquareAPI($clientId, $clientSecret);
		$data = $foursquare -> GetPublic('venues/search', $params);
		$results = json_encode($data);

		$locations = json_decode(trim(str_replace("\r\n", '\\n', $results)));
		$locations = json_decode($locations);
		foreach ($locations->response->venues as $result) {
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

			if (!empty($result -> id)) {
				$LocationID = $result -> id;
			}
			if (!empty($result -> name)) {
				$Name = $result -> name;
				$Name = mysql_real_escape_string($Name);
			}
			if (!empty($result -> contact -> phone)) {
				$Phone = $result -> contact -> phone;
			}
			if (!empty($result -> contact -> formattedPhone)) {
				$FormattedPhone = $result -> contact -> formattedPhone;
			}
			if (!empty($result -> location -> address)) {
				$Address = $result -> location -> address;
			}
			if (!empty($result -> location -> crossStreet)) {
				$CrossStreet = $result -> location -> crossStreet;
			}
			if (!empty($result -> location -> lat)) {
				$Lat = $result -> location -> lat;
			}
			if (!empty($result -> location -> lng)) {
				$Lng = $result -> location -> lng;
			}
			if (!empty($result -> location -> distance)) {
				$Distance = $result -> location -> distance;
			}
			if (!empty($result -> location -> postalCode)) {
				$PostalCode = $result -> location -> postalCode;
			}
			if (!empty($result -> location -> city)) {
				$City = $result -> location -> city;
			}
			if (!empty($result -> location -> state)) {
				$State = $result -> location -> state;
			}
			if (!empty($result -> location -> country)) {
				$Country = $result -> location -> country;
			}
			if (count($result -> categories) > 0) {
				$CategoryID = $result -> categories[0] -> id;
				$CategoryName = $result -> categories[0] -> name;
				$CategoryName = mysql_real_escape_string($CategoryName);
				$PluralName = $result -> categories[0] -> pluralName;
				$ShortName = $result -> categories[0] -> shortName;
				$ImageSrc = $result -> categories[0] -> icon -> prefix;
			}

			$q = "SELECT * FROM locations WHERE LocationID = '$LocationID'";
			$result = mysql_query($q);
			$rn = mysql_num_rows($result);
			if ($rn == "0") {
				$q = "INSERT INTO locations VALUES ('$LocationID','$Name','$Phone','$FormattedPhone','$Address','$CrossStreet',$Lat,$Lng,$Distance,'$PostalCode','$City','$State','$Country','$date')";
			} else {
				$q = "UPDATE locations SET Name='$Name',Phone='$Phone',FormattedPhone='$FormattedPhone',Address='$Address',CrossStreet='$CrossStreet',
						  Lat=$Lat,Lng=$Lng,Distance=$Distance,PostalCode='$PostalCode',City='$City',State='$State',Country='$Country', DateModified='$date'
						  WHERE LocationID = '$LocationID'";
			}

			if (mysql_query($q)) {
				if ($CategoryID != "") {
					$q = "SELECT * FROM categories WHERE LocationID = '$LocationID' AND CategoryID = '$CategoryID'";
					$result = mysql_query($q);
					$rn = mysql_num_rows($result);
					if ($rn == "0") {
						$q = "INSERT INTO categories VALUES('$CategoryID','$LocationID','$CategoryName','$PluralName','$ShortName','$ImageSrc')";
					} else {
						$q = "UPDATE categories SET Name='$CategoryName',PluralName='$PluralName',ShortName='$ShortName',ImageSrc='$ImageSrc' 
								  WHERE CategoryID = '$CategoryID' AND LocationID = '$LocationID'";
					}

					if (mysql_query($q)) {
						$output = "1";
					} else {
						$output = "0";
					}
				}
			} else {
				$output = "0";
			}

		}
		if ($query == "") {
			$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (lat > $lat1 AND lat < $lat2) AND (lng > $lng1 AND lng < $lng2)";
		} else {
			$q = "SELECT locations.*, COALESCE(categories.CategoryID,0) AS CategoryID,COALESCE(categories.Name,'') AS CategoryName, COALESCE(categories.ImageSrc,'') AS ImageSrc 
		FROM locations LEFT OUTER JOIN categories ON categories.LocationID = locations.LocationID WHERE (locations.Name LIKE '%$query%')";
		}

		$res = mysql_query($q);
		if (mysql_num_rows($res) > 0) {
			$locations = array();
			while ($location = mysql_fetch_assoc($res)) {
				$locations[] = array('location' => $location);
			}
			header('Content-type: application/json');
/////	returns output's contents in an JSON array 
			echo json_encode(array('location' => $locations));
		} else {
			$output = "0";
/////	returns output's contents in an JSON array 
			echo json_encode($output);
		}
	}
}

function AddFriend(){
	//////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a userID/friend entry field
	$userID = $_POST['userID'];
	$userID = mysql_real_escape_string($userID);
	$friend = $_POST['friend'];
	$friend = md5(mysql_real_escape_string($friend));
	/////	Resets output to blank, and searches for an exact userID match.
	$output = '';
	
	$q = "SELECT * FROM chaysr_users WHERE UserID='$userID'";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	if (mysql_num_rows($res) > 0) {
		if($row['12'] == 'NULL' or $row['12'] == ''){
			mysql_query("UPDATE chaysr_users SET friends=$friend WHERE UserID=$userID"); 
			$output = "Friend Add Successful" ;}
		else{
			$current =  explode( ',', $input1 );
			if(in_array($friend, $current)){
				$output= "Currently friends with user.";
			}else{$str=$row['12'] . '\,' . $friend;
			mysql_query("UPDATE chaysr_users SET friends=$friend WHERE UserID=$userID");
					$output = "Friend add successful!";}
			}
	}
	else{$output = '0';}
	 
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function RemoveFriend(){
	//////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a userID/friend entry field
	$userID = $_POST['userID'];
	$userID = mysql_real_escape_string($userID);
	$friend = $_POST['friend'];
	$friend = md5(mysql_real_escape_string($friend));
	/////	Resets output to blank, and searches for an exact userID match.
	$output = '';
	
	$q = "SELECT * FROM chaysr_users WHERE UserID='$userID'";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	if (mysql_num_rows($res) > 0) {
		if($row['12'] == 'NULL' or $row['12'] == '') {
			$output = "You have no friends";
		}
		else {
			$current =  explode( ',', $input1 );
			if(in_array($friend, $current)) {
				$str= array_diff($current,$friend);
				$str=implode("," , $str);
				mysql_query("UPDATE chaysr_users SET friends=$str WHERE UserID=$userID");
				$output= "Friend Removed.";
			} else{
				$output = "Friend Not Found!";
			}
		}
	}
	else{$output = '0';}
	 
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function AddClass(){
//////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a userID/friend entry field
	$userID = $_POST['userID'];
	$userID = mysql_real_escape_string($userID);
	$department = $_POST['department'];
	$department = md5(mysql_real_escape_string($department));
	$course = $_POST['course'];
	$course = md5(mysql_real_escape_string($course));
	$section = $_POST['section'];
	$section = md5(mysql_real_escape_string($section));
	$professor = $_POST['professor'];
	$professor = md5(mysql_real_escape_string($professor));
	$campus = $_POST['campus'];
	$campus = md5(mysql_real_escape_string($campus));
	$building = $_POST['building'];
	$building = md5(mysql_real_escape_string($building));
	$room = $_POST['room'];
	$room = md5(mysql_real_escape_string($room));
	$starttime = $_POST['starttime'];
	$starttime = md5(mysql_real_escape_string($starttime));
	$endtime = $_POST['endtime'];
	$endtime = md5(mysql_real_escape_string($endtime));
	$days = $_POST['days'];
	$days = md5(mysql_real_escape_string($days));

	
	/////	Resets output to blank, and searches for an exact userID match.
	$output = '';
	
	$q = "SELECT * FROM chaysr_classes WHERE Department=$department";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	///// If no matching class exists
	if (mysql_num_rows($res) == 0) {
		mysql_query("INSERT INTO chaysr_classes (department, course, section, professor, campus, building, room, starttime, endtime, days)
		($department, $course, $section, $professor, $campus, $building, $room, $starttime, $endtime, $days)");
		$res2 = mysql_query("SELECT * FROM chaysr_users WHERE UserID=$userID");
		$row2 = mysql_fetch_row($res2);
		/////  If User is found
		if (mysql_num_rows($res2) > 0) {
			$var = mysql_query("SELECT * FROM chaysr_classes WHERE department=$department");
			$r= mysql_fetch_row($var);
			/////  If Class is found
			if(mysql_num_rows($var) > 0){
				$msg = $r['0'];
			} else {
				$output = 'Error';
			}
			if($row['13'] == 'NULL' or $row['13'] == '') {
				mysql_query("Update chaysr_users SET classes=$msg WHERE UserID=$userID");
				$output = 'Class Added.';
			}
			else {
				$current =  explode( ',', $input1 );
				if(in_array($msg, $current)){
					$output= "Already In Class.";
				}
				else{
					$str=$row['13'] . '\,' . $msg;
					mysql_query("UPDATE chaysr_users SET classes=$msg WHERE UserID=$userID");
					$output = "Class add successful!";}
				}
			}
		else{
			$output = '0';
		}
	}
	else {
		$output = "-1";
	}
	 
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function MoveClass(){
	//////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a userID/friend entry field
	$userID = $_POST['userID'];
	$userID = mysql_real_escape_string($userID);
	$classID = $_POST['classID'];
	$classID = md5(mysql_real_escape_string($classID));
	/////	Resets output to blank, and searches for an exact userID match.
	$output = '';
	
	$q = "SELECT * FROM chaysr_users WHERE UserID='$userID'";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	if (mysql_num_rows($res) > 0) {
		if($row['13'] == 'NULL' or $row['13'] == '') {
			$output = "You have no classes, squid.";
		}
		else {
			$current =  explode( ',', $row['13'] );
			if(in_array($classID, $current)) {
				$str= array_diff($current,$classID);
				$str=implode("," , $str);
				mysql_query("UPDATE chaysr_users SET classes=$str WHERE UserID=$userID");
				$output= "Class Removed.";
			} else{
				$output = "Class Not Found!";
			}
		}
	}
	else{$output = '0';}
	 
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function FlagUser(){
	////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a randomized activation code, sets the date, and takes in an email/pwd.
	$output = "";
	$date = date('Ymd');
	$email = "ryan@fratlabs.com";
	$flagmessage = $_POST["message"];
	$userID = $_POST["userID"];
	$flagID = $_POST["flagID"];
	$flagmessage = mysql_escape_string($message);
	$userID = mysql_escape_string($userID);
	$flagID = mysql_escape_string($flagID);
			$output = "Thank you for your vigiliance. An email has been sent with information about this flag to a Chaysr admin.";
			$message = "This user: ". $flagID . " has been flagged for the following reason: <br /><br />";
			$message = $message . $flagmessage . "<br /><br />";
			$message = $message . "By user: " . $userID. "<br /><br />";
			$message = $message . "THYYYAAAANNNNKKKKKSSSS." . "<br /><br />";
			$to = $email;
			$subject = "FLAG ALERT";
			$from = "mobile@FratLabs.com";
			$headers = "From:" . $from;
			$headers .= "MIME-Version: Chaysr College App\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	/////	send email
			@mail($to, $subject, $message, $headers);
	/////	returns output's contents in an JSON array 
	echo json_encode($output);
}

function getUser(){
	//////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a userID/friend entry field
	$userID = $_POST['userID'];
	$userID = mysql_real_escape_string($userID);
	/////	Resets output to blank, and searches for an exact userID match.
	$output = '';
	
	$q = "SELECT * FROM chaysr_users WHERE UserID='$userID'";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	if (mysql_num_rows($res) > 0) {
		$output = $row;
		}
	else{$output = '0';}
	echo json_encode($output);
	}

function updateUser(){
	//////	Creates a new funcs_code(), $obj, as definied above, and connects it.
	$obj = new funcs_code();
	$obj -> connection();
	/////	Creates a userID/friend entry field
	$userID = $_POST['userID'];
	$userID = mysql_real_escape_string($userID);
	$email = $_POST['email'];
	$email = mysql_real_escape_string($email);
	$password = $_POST['Password'];
	$password = mysql_real_escape_string($password);
	$firstname = $_POST['firstname'];
	$firstname = mysql_real_escape_string($firstname);
	$lastname = $_POST['lastname'];
	$lastname = mysql_real_escape_string($lastname);
	$gender = $_POST['gender'];
	$gender = mysql_real_escape_string($gender);
	$school = $_POST['school'];
	$school = mysql_real_escape_string($school);
	$gradyear = $_POST['gradyear'];
	$gradyear = mysql_real_escape_string($gradyear);
	$major = $_POST['major'];
	$major = mysql_real_escape_string($major);
	/////	Resets output to blank, and searches for an exact userID match.
	$output = '';
	
	$q = "SELECT * FROM chaysr_users WHERE UserID='$userID'";
	$res = mysql_query($q);
	$row = mysql_fetch_row($res);
	if (mysql_num_rows($res) > 0) {
		mysql_query("UPDATE chaysr_users SET Email=$email Password=$password fisrtname=$firstname lastname=$lastname gender=$gender school=$school gradyear=$gradyear major=$major WHERE UserID=$userID");
		$output = 'User data updated.';              
	}
	else {
		$output = 'User not found.';
	}
	echo json_encode($output);
}