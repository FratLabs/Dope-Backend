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
		return $result[0]["profile_data"];
	} else {
		return false;
	}
}

function _checkProfileFields($data) {

}

function saveProfile($hash_id, $data) {
	global $pdo;
	global $table_prefix;
	
//	echo("data=".$data);
//	echo("hash_id=".$hash_id);

	$q = $pdo->prepare("UPDATE `{$table_prefix}users` SET `profile_data`=?  WHERE `hash_id`=?");
	$r = $q->execute(array(
	    $data,
	    $hash_id
	));
//	print_r($q->errorInfo());
	return true;
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
//			$ratio = $h / $w;
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

function savePhoto($hash_id, $file) {

	global $userdir;


	$uDir = $_SERVER["DOCUMENT_ROOT"]."/".$userdir.$hash_id;
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
//		return array("filename" => str_replace($_SERVER["DOCUMENT_ROOT"], "",$newName), "thumbname"=> str_replace($_SERVER["DOCUMENT_ROOT"], "",$thumbName));
	}
}

function saveAvatar($hash_id, $file) {

	global $userdir;
	$photodir = _getUserPhotoDir($hash_id);

	$uDir = $_SERVER["DOCUMENT_ROOT"]."/".$userdir.$hash_id;
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

	return $flag;
}

function getPhotos($hash_id) {
	global $userdir;
	$answer = array();
	$files = array();
//	phpinfo();
clearstatcache();
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

function delPhoto($hash_id, $index) {
	$photos = getPhotos($hash_id);
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