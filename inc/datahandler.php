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
		return $result[0]["profile_data"];
	} else {
		return false;
	}
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

//    print($uDir."<br>");
//    print($pDir."<br>");

	if (is_dir($pDir)) {
		return opendir($pDir);
	} else {
	    if (! is_dir($uDir)) {
	    	mkdir($uDir, 0777);
	    }
	    mkdir($pDir, 0777);

		chmod($uDir, 0777);
    	chmod($pDir, 0777);

	    return opendir($pDir);
	}
}

function getPhotos($hash_id) {
	global $userdir;
	$answer = array();
	$files = array();
//	phpinfo();
clearstatcache();
	if ($dh = _getUserPhotoDir($hash_id)) {
		while (($file = readdir($dh)) !== false) {
		    if ($file != "." && $file != ".." && $file != "") {
//            	echo "filename: $file : filetype: " . filetype($dir . $file) . "<br>\n";
@				$files[] = array("name"=>$file, "mTime"=> filemtime($_SERVER["DOCUMENT_ROOT"]."/".$userdir.$hash_id."/photos/".$file));;
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
	$answer["list"] = $files;
	return $answer;
}

?>