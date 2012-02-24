get photostream<br>
<form action="profile.php?action=getPhotos&login=123@123&pass=123" method="post" target="res_frame">
<input type="submit" value="Submit">
</form>

<br><br>

photostream upload form<br>
<form action="profile.php?action=savePhoto&login=123@123&pass=123" method="post" target="res_frame" enctype="multipart/form-data">
	<input type="hidden" name="action" value="savePhoto">
	<input type="file" name="photo">
	<input type="submit" value="Submit">
</form>

<br><br>

photostream delete index form<br>
<form action="profile.php?action=delPhoto&login=123@123&pass=123" method="post" target="res_frame">
	<input type="text" name="index">
	<input type="submit" value="Submit">
</form>

<br><br>

avatar upload form<br>
<form action="profile.php?action=saveAvatar&login=123@123&pass=123" method="post" target="res_frame" enctype="multipart/form-data">
	<input type="hidden" name="action" value="saveAvatar">
	<input type="file" name="avatar">
	<input type="submit" value="Submit">
</form>


<iframe name="res_frame" width="1000" height="300"></iframe>