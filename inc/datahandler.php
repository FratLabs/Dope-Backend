<?

function getProfile($hash_id) {
	global $answer;
	global $pdo;
	global $table_prefix;
	
	$q = $pdo->prepare("SELECT `profile_data` FROM `{$table_prefix}users` WHERE `hash_id`=?");
	$q->execute(array(
		$hash_id
	));
	$result = $q->fetchAll(PDO::FETCH_ASSOC);
	if (count($result)) {
	    _checkProfileFields($result[0]["profile_data"]);
		return (array)json_decode($result[0]["profile_data"]);
	} else {
		return false;
	}
}

function _checkProfileFields($data) {

}

function saveProfile($data) {
	global $pdo;
	global $table_prefix;
	global $user;

	$q = $pdo->prepare("UPDATE `{$table_prefix}users` SET `profile_data`=?  WHERE `hash_id`=?");
	$r = $q->execute(array(
	    $data,
	    $user["hash_id"]
	));
	_parseProfileData($user["hash_id"], $data);
	return true;
}

function _parseProfileData($hash_id, $data) {
	global $pdo;
	global $table_prefix;

	$obj = json_decode($data);
	$arr = (array) $obj;
//	print_r($arr);
	$q = $pdo->prepare("UPDATE `{$table_prefix}users` SET `name`=?, `major`=?, `gender`=?, `greek`=?, `grad_year`=? WHERE `hash_id`=?");
	$r = $q->execute(array(
		$arr["Name"],
		$arr["Major"],
		$arr["Gender"],
		$arr["Greek"],
		$arr["Graduation Year"],
		$hash_id
	));
}


function updateCoords($lon, $lat) {
	global $pdo;
	global $table_prefix;
	global $user;

	$q = $pdo->prepare("UPDATE `{$table_prefix}users` SET `lon`=?, `lat`=? WHERE `hash_id`=?");
	$r = $q->execute(array(
		$lon,
		$lat,
		$user["hash_id"]
	));
	return true;
}

function getEvents($prev_stamp = 0) {
	global $user;
	global $pdo;
	global $table_prefix;

	$events = array();
	$events = array_merge($events, _getMessages($prev_stamp));
	$events = array_merge($events, _getGossips($prev_stamp));
	
	for ($i = 0; $i < count($events); $i++) {
		for ($j = 0; $j < count($events); $j++) {
			if ($events[$i]["timestamp"] > $events[$j]["timestamp"]) {
				$tmp = $events[$i];
				$events[$i] = $events[$j];
				$events[$j] = $tmp;
			}
		}
	}
	return $events;
}

function _getMessages($prev_stamp = 0) {
	global $user;
	global $pdo;
	global $table_prefix;

	$q = $pdo->prepare("SELECT * FROM `{$table_prefix}chat` WHERE `timestamp`>? AND `receiver_id`=? ORDER BY `timestmp` ASC, `photo_id` ASC");
	$q->execute(array(
		$prev_stamp,
		$user["hash_id"]
	));
	$r = $q->fetchAll(PDO::FETCH_ASSOC);
	for ($i = 0; $i < count($r); $i++) {
		$r[$i]["timestamp"] = strtotime($r[$i]["timestamp"]);
	}
	return $r;
}

function registerNewPlace() {

}

function _getGossips($prev_stamp = 0) {
	global $user;
	global $pdo;
	global $table_prefix;

	return array();
}

function _getPhotoGalleryMessages($prev_stamp = 0) {
	global $user;
	global $pdo;
	global $table_prefix;
	
	$q = $pdo->prepare("SELECT * FROM `{$table_prefix}chat` WHERE `timestamp`>? AND `receiver_id`=?");
	$q->execute(array(
		$prev_stamp,
		$user["hash_id"]
	));
	$r = $q->fetchAll(PDO::FETCH_ASSOC);
	for ($i = 0; $i < count($r); $i++) {
		$r[$i]["timestamp"] = strtotime($r[$i]["timestamp"]);
	}
	return $r;

}


function _calculateDistance($point1, $point2) {
	$lon1 = deg2rad($point1["lon"]);
	$lat1 = deg2rad($point1["lat"]);

	$lon2 = deg2rad($point2["lon"]);
	$lat2 = deg2rad($point2["lat"]);
	
	$R = 6371;
	$dist = arccos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lon1 - $lon2)) * $R;

	return $dist;
}

function _calculateRectangle($centerPoint, $dist) {
	$R = 6371;
	
    $r = $dist/$R;

	$lat = deg2rad($centerPoint["lat"]);
	$lon = deg2rad($centerPoint["lon"]);
	
	$latT = arcsin(sin($lat)/cos($r));

	$lonDelta = arccos((cos($r) - sin($latT) * sin($lat)) / (cos($latT) * cos($lat)));
	$lonDelta2 = arcsin($r)/cos($lat);
	

    $latMin = $lat - $r;
    $latMax = $lat + $r;
    
    $lonMin = $lon - $lonDelta;
    $lonMax = $lon + $lonDelta;
    
    return array(
		"latMin" => $latMin,
		"lonMin" => $lonMin,
		"latMax" => $latMax,
		"lonMax" => $lonMax
	);
    
}

function _sortDistance($lon, $lat, $arr) {
	for ($i = 0; $i < count($arr); $i++) {
		$arr[$i]["distance"] = _calculateDistance(array("lon"=>$lon, "lat"=>$lat), array("lon"=>$arr[$i]["lon"],"lat"=>$arr[$i]["lat"]) );
	}
	for ($i = 0; $i < count($arr); $i++) {
		for ($j = 0; $j < count($arr); $j++) {
			if ($arr[$i]["distance"] > $arr[$j]["distance"]) {
				$tmp = $arr[$i];
				$arr[$i] = $arr[$j];
				$arr[$j] = $tmp;
			}
		}
	}
	return $arr;
}

function searchPeople($q) {
	global $pdo;
	global $table_prefix;
	global $user;

	$str = "SELECT * FROM `{$table_prefix}users` WHERE `name` LIKE \"%$q%\" AND `hash_id`!=?";
	$q = $pdo->prepare($str);
	$q->execute(array($user["hash_id"]));

	$r = $q->fetchAll(PDO::FETCH_ASSOC);
	$r = _sortDistance($user["lon"], $user["lat"], $r);
	return $r;
}

function peopleAroundMe($distance = 1, $lon = 0, $lat = 0) {
	global $pdo;
	global $table_prefix;
	global $user;

	if ($lon == 0 && $lat == 0) {
		$lon = $user["lon"];
		$lat = $user["lat"];
	}
	
	$r = _calculateRectangle(array("lon"=>$lon,"lat"=>$lat), $distance);
	$str = "SELECT * FROM `{$table_prefix}users` WHERE `lon`>? AND `lon`<? AND `lat`>? AND `lat`<?";
	
	$q = $pdo->prepare($str);
	$q->execute(array(
	    $r['lonMin'],
	    $r['lonMax'],
	    $r['latMin'],
	    $r['latMax']
	));
	$r = $q->fetchAll(PDO::FETCH_ASSOC);
	$r = _sortDistance($lon, $lat, $r);
	return $r;
}


function sendFriendRequest($recepient_hash_id, $message) {
	global $_DEFAULT_FRIEND_REQUEST_TEXT;
//	global $user;

	if (! isset($message["message"])) {
		$message = $_DEFAULT_FRIEND_REQUEST_TEXT;
	}
	$message["friendRequest"] = true;
	return sendPrivateMessage($recepient_hash_id, $message);
}

function sendPrivateMessage($recepient_hash_id, $message) {
	global $user;
	global $pdo;
	global $table_prefix;
	
	if(isset($message["message"])) {

/*
		$str = "SELECT * FROM `{$table_prefix}users` WHERE `hash_id`=?";
		$q = $pdo->prepare($str);
		$q->execute(array($recepient_hash_id));
		$recipient = $q->fetch(PDO::FETCH_ASSOC);
*/

		$q = $pdo->prepare("INSERT INTO `{$table_prefix}chat` SET `sender_id`=?, `receiver_id`=?, `lon`=?, `lat`=?, `message`=?, `photo_attach_id`=?, `friend_request`=?");
		$q->execute(array(
			$user["hash_id"],
			$recepient_hash_id,
			0,
			0,
			$message["message"],
			(integer)$message["photoAttachID"],
			(integer)$message["friendRequest"]
		));
		$message_id = $pdo->lastInsertId();
		$q = $pdo->prepare("SELECT * FROM `{$table_prefix}chat` WHERE `id`=".$message_id);
		$q->execute();
		$r = $q->fetch(PDO::FETCH_ASSOC);
		return strtotime($r["timestamp"]);
	}

	return 0;
}

function rescalePhoto($photo, $fname) {
	$maxSize = 1280;
	$quality = 80;

    $img = imagecreatefromstring($photo);
	$w = imagesx($img);
	$h = imagesy($img);
	if ($w > $maxSize || $h > $maxSize) {
	    $ratio = $w / $h;
	    if ($ratio > 1) {
			$w2 = $maxSize;
			$h2 = intval($maxSize / $ratio);
		} else {
			$w2 = intval($maxSize * $ratio);
			$h2 = $maxSize;
		}
		$newImg = imagecreatetruecolor($w2, $h2);
		imagecopyresampled($newImg, $img, 0,0,0,0, $w2, $h2, $w, $h);
		imagejpeg($newImg, $fname, $quality);
		return;
	}
	
	imagejpeg($img, $fname, $quality);
	return;
}

function thumbPhoto($photo, $fname) {
	$thumbSize = 200;
	$quality = 60;

    $img = imagecreatefromstring($photo);
	$w = imagesx($img);
	$h = imagesy($img);
	$delta = intval(($w - $h) / 2);

	$newImg = imagecreatetruecolor($thumbSize, $thumbSize);
	
	if ($delta > 0) {
	    $side = $h;
		$x = $delta;
		imagecopyresampled($newImg, $img,0,0,$x,0,$thumbSize, $thumbSize, $h, $h);
	} else {
		$side = $w;
		$y = intval(($h - $w)/2);
		imagecopyresampled($newImg, $img,0,0,0,$y,$thumbSize, $thumbSize, $w, $w);
	}
	imagejpeg($newImg, $fname, $quality);
	return;
}

function savePhoto($file) {
	global $userdir;
	global $user;


	$uDir = $_SERVER["DOCUMENT_ROOT"]."/".$userdir.$user["hash_id"];
    $pDir = $uDir."/photos";
	$photoname = rand(100000000, 999999999);

	$newName = $pDir."/".$photoname.".jpg";
	$thumbName = $pDir."/thumbs/".$photoname."-thumb.jpg";
	


	if ($file["error"] > 0) {
		return array("error"=> "Error uploading photo");
	} else {
	    $photodir = _getUserPhotoDir($hash_id);

		$data = file_get_contents($file["tmp_name"]);
	    rescalePhoto($data, $newName);
		thumbPhoto($data, $thumbName);
		return getPhotos($hash_id);
	}
}

function saveAvatar($file) {
	global $userdir;
	global $user;

	$photodir = _getUserPhotoDir($hash_id);

	$uDir = $_SERVER["DOCUMENT_ROOT"]."/".$userdir.$user["hash_id"];
	$avatarName = $uDir."/avatar.jpg";

	$data = file_get_contents($file["tmp_name"]);
	thumbPhoto($data, $avatarName);
	return array("filename" => str_replace($_SERVER["DOCUMENT_ROOT"], "", $avatarName));
}

function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
}
 
 

function _getUserPhotoDir($hash_id) {
	global $userdir;


	$uDir = $_SERVER["DOCUMENT_ROOT"]."/".$userdir.$hash_id;
    $pDir = $uDir."/photos";
    $pThumbDir = $uDir."/photos/thumbs";

	_checkUserDir($hash_id);
	
    return opendir($pDir);
}

function _checkUserDir($hash_id) {
	global $userdir;

	$uDir = $_SERVER["DOCUMENT_ROOT"]."/".$userdir.$hash_id;
    $pDir = $uDir."/photos";
    $cDir = $uDir."/chats";
    $cThumbDir = $uDir."/chats/thumbs";
    $pThumbDir = $uDir."/photos/thumbs";
    
	$flag = true;
    if (! is_dir($uDir)) {
    	mkdir($uDir, 0777);
		chmod($uDir, 0777);
		$flag = false;
	}
    if (! is_dir($pDir)) {
	    mkdir($pDir, 0777);
    	chmod($pDir, 0777);
		$flag = false;
	}
    if (! is_dir($cDir)) {
	    mkdir($cDir, 0777);
    	chmod($cDir, 0777);
		$flag = false;
	}
	if (! is_dir($pThumbDir)) {
    	mkdir($pThumbDir, 0777);
    	chmod($pThumbDir, 0777);
		$flag = false;
	}
	if (! is_dir($cThumbDir)) {
    	mkdir($cThumbDir, 0777);
    	chmod($cThumbDir, 0777);
		$flag = false;
	}

	return $flag;
}

function getPhotos($hash_id) {
	global $userdir;
	$answer = array();
	$files = array();
//	phpinfo();
//	clearstatcache();

	if ($dh = _getUserPhotoDir($hash_id)) {
		while (($file = readdir($dh)) !== false) {
		    if ($file != "." && $file != ".." && $file != "" && $file != "thumbs") {
//            	echo "filename: $file : filetype: " . filetype($dir . $file) . "<br>\n";
@				$files[] = array("name"=>$file, "thumbName"=>str_replace(".", "-thumb.", $file), "mTime"=> filemtime($_SERVER["DOCUMENT_ROOT"]."/".$userdir.$hash_id."/photos/".$file));;
			}
		}
        closedir($dh);
		for ($i = 0; $i < count($files); $i++) {
			for ($j = 0; $j < count($files); $j++) {
				if ($files[$i]["mTime"] < $files[$j]["mTime"]) {
					$tmp = $files[$i];
					$files[$i] = $files[$j];
					$files[$j] = $tmp;
				}
				
			}
		}
	} else {
	}
	$answer["folder"] = $userdir.$hash_id."/photos";
	$answer["thumbFolder"] = $userdir.$hash_id."/photos/thumbs";
	$answer["list"] = $files;
	
	return $answer;
}

function delPhoto($index) {
	global $user;
	
	$photos = getPhotos($user["hash_id"]);
	if ($index < count($photos["list"])) {
		$thumb2Delete =
			$_SERVER["DOCUMENT_ROOT"]."/".
			$photos["thumbFolder"]."/".
			$photos["list"][$index]["thumbName"];
			
		$photo2Delete =
			$_SERVER["DOCUMENT_ROOT"]."/".
			$photos["folder"]."/".
			$photos["list"][$index]["name"];
			
		//print_r($thumb2Delete."<br>");
		//print_r($photo2Delete."<br>");
		
		array_splice($photos["list"], $index, 1);

		unlink($thumb2Delete);
		unlink($photo2Delete);
	}
	return $photos;
}


?>