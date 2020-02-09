<?php

if ($_SESSION['mode'] == 1)
{
	echo "<script>document.body.style.backgroundColor = '#000000';</script>";
}
else
	echo "<script>document.body.style.backgroundColor = '#F7F7F7';</script>";

?>

<div id="top-bar">
	<a href="reset_index.php" id="home-link">
		<img id="img-home" src="ressources/camagru-home.png">
	</a><!--
	--><a href="logout.php" id="first-nav" class="nav-link">
		<img class="nav-ic" src="ressources/logout-nav.png">
	</a><!--
	--><a href="settings.php" class="nav-link">
		<img class="nav-ic" src="ressources/settings-nav.png">
	</a><!--
	--><a href="camera.php" class="nav-link">
		<img class="nav-ic" src="ressources/camera-nav.png">
	</a><!--
	--><a href="profile.php" class="nav-link">
		<img class="nav-ic" src="ressources/profile-nav.png">
	</a>
</div>