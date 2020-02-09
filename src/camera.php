<html>
	<head>
		<title>Camagru - Camera</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/camera.css">
		<link rel="stylesheet" type="text/css" href="css/header.css">
		<link rel="stylesheet" type="text/css" href="css/footer.css">
		<link rel="icon" href="ressources/camagru-logo.png">
		<script src="jquery-3.4.1.js"></script>
	</head>

</html>
<?php 

session_start();

include_once('config/setup.php');
include('header.php');
include('footer.html'); 

if ($_SESSION['login'] == '')
{
	header("Location: index.php");
	exit();
}

$login = $_SESSION['login'];
$_SESSION['index_page'] = 0;

if (isset($_POST["url"]))
		$url = $_POST["url"];
if (isset($_POST["url2"]))
		$url2 = $_POST["url2"];
if (isset($_POST["url3"]))
		$url3 = $_POST["url3"];





//TAKE IMAGE

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit6']))
	{
		$data = $_POST['url'];
		$pokemon = $_POST['url2'];
		if ($pokemon)
		{
			list($type, $data) = explode(';', $data);
			list($base, $data) = explode(',', $data);
			$data = base64_decode($data);
			file_put_contents('pictures/temp.png', $data);

			$img = imagecreatefrompng('pictures/temp.png');
			$pok = imagecreatefrompng($pokemon);
			imagecopy($img, $pok, 400, 200, 0, 0, 400, 400);
			$img_name = "pictures/final.png";
			imagepng($img, $img_name);
			$_SESSION['display'] = 1;
		}
		else
			echo "<script>alert('Pick a Pokemon');</script>";
	}





//SAVE TAKEN IMAGE

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit1'])) 
	{
		try 
		{
		    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $sql = "INSERT INTO Camagru.Photo (url, id_user, creation_date)
		    		VALUES (:url , (SELECT id FROM User where login=:login), :xdate)";
		    $query = $conn->prepare($sql);
			$query->bindValue(':url', $url3);
			$query->bindValue(':login', $login);
			$query->bindValue(':xdate', date('Y-m-d H:i:s'));
		    $query->execute();
	    }
		catch(PDOException $e)
	    {
	   		sqlerror_mail($sql, $e);
	    }

		$conn = null;

		$_SESSION['index'] = 0;
	}





//SAVE UPLOADED IMAGE TO DATABASE

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit5'])) 
	{
			if ($url2)	
			{
				try 
				{
				    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
				    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				    $sql = "INSERT INTO Camagru.Photo (url, id_user, creation_date)
				    		VALUES (:url, (SELECT id FROM User where login=:login), :xdate);";
				    $query = $conn->prepare($sql);
				    $query->bindValue(':url', $url2);
					$query->bindValue(':login', $login);
					$query->bindValue(':xdate', date('Y-m-d H:i:s'));
				    $query->execute();
			    }
				catch(PDOException $e)
			    {
			   		sqlerror_mail($sql, $e);
			    }

				$conn = null;
			}
			else
				echo "<script>alert('Please Upload A Valid File');</script>";

		$_SESSION['index'] = 0;
	}



//DELETE CHECKED IMAGES

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit4']))
	{
		try
		{
		    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			for ($i=0; $i < 5; $i++)
			{
				if (isset($_POST["check$i"]))
				{
					$sql = "DELETE l.* FROM Camagru.Like l INNER JOIN Camagru.Photo p
							ON l.id_picture = p.id_picture
							WHERE l.id_picture = :check
							AND p.id_user = (SELECT id FROM User WHERE User.login = :login)";
					$query = $conn->prepare($sql);
					$query->bindValue(':check', $_POST["check$i"]);
					$query->bindValue(':login', $login);
					$query->execute();

					$sql = "DELETE c.* FROM Camagru.Comment c INNER JOIN Camagru.Photo p
							ON c.id_picture = p.id_picture
							WHERE c.id_picture = :check
							AND p.id_user = (SELECT id FROM User WHERE User.login = :login)";
					$query = $conn->prepare($sql);
					$query->bindValue(':check', $_POST["check$i"]);
					$query->bindValue(':login', $login);
					$query->execute();

					$sql = "DELETE FROM Camagru.Photo 
							WHERE id_picture = :check
							AND id_user = (SELECT id FROM User WHERE User.login = :login)";
					$query = $conn->prepare($sql);
					$query->bindValue(':check', $_POST["check$i"]);
					$query->bindValue(':login', $login);
					$query->execute();
				}
			}
		}
		catch(PDOException $e)
		{
			sqlerror_mail($sql, $e);
		}
		$_SESSION['index'] = 0;
		$conn = null;
	}






//DISPLAY USER'S IMAGES

	try
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "SELECT Photo.id_picture, Photo.url, User.login
	    		FROM Camagru.Photo
	    		INNER JOIN Camagru.User ON Photo.id_user=User.id
	    		WHERE User.login = :login
	    		ORDER BY creation_date DESC";
	    $query = $conn->prepare($sql);
		$query->bindValue(':login', $login);
		$query->execute();
		$result = $query->fetchAll();
	}
	catch(PDOException $e)
	{
		sqlerror_mail($sql, $e);
	}
	$conn = null;





//NAVIGATION

	if (isset($_POST['action']) && $_POST['action'] == "ft1" && $_SESSION['index'] > 0)
	{
		$_SESSION['index']--;
		echo "<script>alert('boooo');</script>";
		// if ($_SESSION['index'] > 0)
		// 	echo "<script>display('up');</script>";
		// if ($_SESSION['index'] < count($result)-5)
		// 	echo "<script>display('down');</script>";
	}

	if (isset($_POST['action']) && $_POST['action'] == "ft2" && $_SESSION['index'] < count($result)-5)
	{
		$_SESSION['index']++;
		echo "<script>alert('boooo');</script>";
		// if ($_SESSION['index'] > 0)
		// 	echo "<script>display('up');</script>";
		// if ($_SESSION['index'] < count($result)-5)
		// 	echo "<script>display('down');</script>";
	}
?>





	<body id="bodd">
		<div id="body-container">
			<div id="pokemonat">
				<a onclick="p(1)" class="link-pokemon"><img class="pokemon" src="ressources/frames/pok1.png" id="pok1"></a>
				<a onclick="p(2)" class="link-pokemon"><img class="pokemon" src="ressources/frames/pok2.png" id="pok2"></a>
				<a onclick="p(3)" class="link-pokemon"><img class="pokemon" src="ressources/frames/pok3.png" id="pok3"></a>
				<a onclick="p(4)" class="link-pokemon"><img class="pokemon" src="ressources/frames/pok4.png" id="pok4"></a>
				<a onclick="p(5)" class="link-pokemon"><img class="pokemon" src="ressources/frames/pok5.png" id="pok5"></a>
				<a onclick="p(6)" class="link-pokemon"><img class="pokemon" src="ressources/frames/pok6.png" id="pok6"></a>
				<a onclick="p(7)" class="link-pokemon"><img class="pokemon" src="ressources/frames/pok7.png" id="pok7"></a>
			</div>
			<div id="filters">
				<a onclick="ft1()" class="link-city"><img class="cities" src="ressources/cities/marrakech.png" style="filter: contrast(90%) brightness(120%) saturate(110%);"></a>
				<a onclick="ft2()" class="link-city"><img class="cities" src="ressources/cities/rabat.png" style="filter: contrast(150%) saturate(110%);"></a>
				<a onclick="ft3()" class="link-city"><img class="cities" src="ressources/cities/casablanca.png" style="filter: contrast(85%) brightness(110%) saturate(75%) sepia(22%);"></a>
				<a onclick="ft4()" class="link-city"><img class="cities" src="ressources/cities/tangier.png" style="filter: contrast(110%) brightness(110%) saturate(130%) invert(15%);"></a>
				<a onclick="ft5()" class="link-city"><img class="cities" src="ressources/cities/khouribga.png" style="filter: contrast(110%) brightness(110%) sepia(30%) grayscale(100%);"></a>
			</div>
			<form method="post" action="">
					<div id="cam-container">
						<video id="video"></video>
						<canvas id="pic-container" width="800px" height="600px" onclick="ft_cam()"></canvas>
						<canvas id="pic-container2" width="800px" height="600px"></canvas>
						<input type="hidden" id="hiddenURL" name="url">
						<input type="hidden" id="hiddenURL2" name="url2">
						<input type="hidden" id="hiddenURL3" name="url3">
						<input type="submit" class="pic-button" id="pic-button" value="Take Photo" name="submit6" disabled>
						<input type="submit" class="pic-button" id="save-button" value="Save Photo" name="submit1" style="display: none;" >
						<input type="submit" class="pic-button" id="upload-button" value="Upload Photo" name="submit5" disabled="true">
						<input type="file" id="inp">
					</div>
			</form>
			<div id="gal">				
				<form method="post" action="" id="nav">
						<div id="gallery">
							<input type="button" id="up" onclick="sub2()" class="nav" value="<<" name="submit2" style="display: block;">
							<?php
							$count = count($result);
							$index = $_SESSION['index'];
							if ($count > 0 && $count < 5)
							{
								for ($i = 0; $i < $count; $i++)
								{
									echo "<input class='check' type='checkbox' readonly='readonly' value=".$result[$i][0]." onclick='checks()' id='check".$i."' name='check".$i."'>";
									echo "<img class='pics' src=".$result[$i][1].">";
								}
							}
							else if ($count >= 5)
							{
								$j = 0;
								for ($i = $index; $i < 5+$index; $i++)
								{ 
									echo "<input class='check' type='checkbox' readonly='readonly' value=".$result[$i][0]." onclick='checks()' id='check".$j."' name='check".$j."'>";
									echo "<img class='pics' src=".$result[$i][1].">";
									$j++;
								}
							}
							?>
							<input type="button" id="down" onclick="sub3()" class="nav" value=">>" name="submit3" style="display: block;">
							<input type="submit" id="delete" class="nav" value="Delete" name="submit4" style="display: none;">
						</div>
				</form>
			</div>
		</div>
	</body>
<script type="text/javascript" src="scripts/camera-script.js"></script>



<?php

if ($_SESSION['display'] == 1)
{
	echo "
	<script type='text/javascript'>
		draw_png();
	</script>";
	$_SESSION['display'] = 0;
}
else
{
	echo "
	<script type='text/javascript'>
		ft_cam();
	</script>";
}


?>
