get photostream<br>
<form action="profile.php?action=getPhotos&login=111@111&pass=96e79218965eb72c92a549dd5a330112" method="post" target="res_frame">
<input type="submit" value="Submit">
</form>


<hr>
photostream upload form<br>
<form action="profile.php?action=savePhoto&login=111@111&pass=96e79218965eb72c92a549dd5a330112" method="post" target="res_frame" enctype="multipart/form-data">
	<input type="hidden" name="action" value="savePhoto">
	<input type="file" name="photo">
	<input type="submit" value="Submit">
</form>

<hr>

photostream delete index form<br>
<form action="profile.php?action=delPhoto&login=111@111&pass=96e79218965eb72c92a549dd5a330112" method="post" target="res_frame">
	<input type="text" name="index">
	<input type="submit" value="Submit">
</form>

<hr>

avatar upload form<br>
<form action="profile.php?action=saveAvatar&login=111@111&pass=96e79218965eb72c92a549dd5a330112" method="post" target="res_frame" enctype="multipart/form-data">
	<input type="hidden" name="action" value="saveAvatar">
	<input type="file" name="avatar">
	<input type="submit" value="Submit">
</form>
<hr>
<br>

OUTPUT:<br>

<iframe name="res_frame" width="1000" height="300"></iframe>