get profile data<br>
<form action="profile.php?action=get&login=111@111&pass=96e79218965eb72c92a549dd5a330112" method="post" target="res_frame">
	<input type="submit" value="Submit">
</form>
save profile data<br>
<form action="profile.php?action=save&login=111@111&pass=96e79218965eb72c92a549dd5a330112" method="post" target="res_frame">
	<input type="text" name="data" size="150" value='{"Name":"John Doe","Gender":0,"Graduation Year":"2012","Major":"","Greek":"","Relationship":2,"classes":[{"classSection":0,"class":"123","classSubject":"123"}]}'>
	<br/>
	<input type="submit" value="Submit">
</form>
<hr>
<br>

OUTPUT:<br>

<iframe name="res_frame" width="1000" height="300"></iframe>