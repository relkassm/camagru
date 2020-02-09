	<html>
	<head>
		<title>Camagru</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/home.css">
		<link rel="icon" href="ressources/camagru-logo.png">
		<link rel="stylesheet" type="text/css" href="css/header.css">
		<link rel="stylesheet" type="text/css" href="css/footer.css">
		<script src="jquery-3.4.1.js"></script>
		<script type="text/javascript" src="scripts/homepage-script.js"></script>
	</head>
	</html>
	
	<?php 

	session_start();

	include_once('config/setup.php');
	include_once('header2.html');

	if (isset($_SESSION['login']) && $_SESSION['login'] != '')
	{
		header("Location: login.php");
		exit();
	}

//Pagination
	if (!isset($_SESSION['index_page']))
	{
		$_SESSION['index_page'] = 0;
	}
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['previous']) && $_SESSION['index_page'] != 0)
		$_SESSION['index_page'] -= 5;
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['next']))
		$_SESSION['index_page'] += 5;

	$index_page = $_SESSION['index_page'];

//STORE PHOTOS ON ARRAY result[][];

	try 
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "SELECT Photo.id_picture, Photo.url, User.firstname, User.lastname, Photo.creation_date 
	    		FROM Camagru.Photo 
	    		INNER JOIN Camagru.User ON Photo.id_user=User.id
	    		ORDER BY creation_date DESC
	    		LIMIT 6 OFFSET :index";
	    $query = $conn->prepare($sql);
	    $query->bindValue(':index', $index_page, PDO::PARAM_INT);
		$query->execute();
		$result = $query->fetchAll();
		$count = count($result);
		
		$sql = "SELECT MAX(id_picture) as max, MIN(id_picture) as min FROM Camagru.Photo";
		$query = $conn->prepare($sql);
		$query->execute();
		$result2 = $query->fetchAll();
		$max = $result2[0][0];
		$min = $result2[0][1];
	}
	catch(PDOException $e)
	{
		sqlerror_mail($sql, $e);
	}

	$conn = null;

//SPLIT TIME INTO Days->Hours->Minutes->Seconds
	$end = $count;
	if ($end == 6)
		$end--;
	$now = New DateTime();
	for ($i = 0; $i < $count ; $i++) 
	{
		$date_sql = $result[$i][4];
		$date = New DateTime($date_sql);
		$date_days = $date->diff($now)->format("%d");
		$date_hours = $date->diff($now)->format("%h");
		$date_minutes = $date->diff($now)->format("%i");
		$date_seconds = $date->diff($now)->format("%s");
		if ($date_days != "0")
		{
			if ($date_days == "1")
				$date_res = "1 day ago";
			else
				$date_res = $date_days." days ago";
		}
		else if ($date_hours != "0")
		{		
			if ($date_hours == "1")
				$date_res = "1 hour ago";
			else	
				$date_res = $date_hours." hours ago";
		}
		else if ($date_minutes != "0")
		{
			if ($date_minutes == "1")
				$date_res = "1 minute ago";
			else	
				$date_res = $date_minutes." minutes ago";
		}
		else
		{
			if ($date_seconds == "1")
				$date_res = "1 second ago";
			else
				$date_res = $date_seconds." seconds ago";
		}

		$heart = "off";
		$comment = "off";

		try 
		{

//DISPLAY NUMBER OF LIKES

		    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $sql = "SELECT * 
		    		FROM Camagru.Like 
		    		WHERE Like.id_picture = :id_pic;";
		    $query = $conn->prepare($sql);
	    	$query->bindValue(':id_pic', $result[$i][0]);
			$query->execute();
			$res_like = $query->fetchAll();
			$count_likes = count($res_like);

//DISPLAY NUMBER OF COMMENTS

			$sql = "SELECT Comment.comment_text, User.login 
					FROM Camagru.Comment INNER JOIN Camagru.User
					ON User.id = Comment.id_user
					WHERE Comment.id_picture = :id_pic
					ORDER BY comment_date DESC";
		    $query = $conn->prepare($sql);
	    	$query->bindValue(':id_pic', $result[$i][0]);
			$query->execute();
			$res_comm = $query->fetchAll();
			$count_comm = count($res_comm);
		}
		catch(PDOException $e)
		{
			sqlerror_mail($sql, $e);
		}

		$conn = null;

// DISPLAY RESULT ON HTML FOREACH PHOTO

		echo "
		<html>
			<body id='bod' style='padding-top: 15vw;'>
				<div class='container-feed'>
					<h2>".ucfirst(htmlspecialchars($result[$i][2]))." ".strtoupper(htmlspecialchars($result[$i][3]))."</h2>
					<img class='img-feed' src=".$result[$i][1].">
					<div class='r-b'>
						<div><a class='right-bot'>".$date_res."</a></div>
						<div><a class='right-bot' src='' onclick='dspComments(".$result[$i][0].")'>".$count_likes." ♥ ".$count_comm." ▤</a></div>
					</div>
					<div class='lik-con'>
							<a src='' onclick='myAjax(".$result[$i][0].")' class='gg' id='".$result[$i][0]."'>
							</a>
							<a src='' onclick='display(".$result[$i][0].")' class='gg' id='".$result[$i][0]."'>
							</a>
					</div>
					<div class='com-con' id='com".$result[$i][0]."'>
						<form class='form-comment' action='' method='post'>
							<input type='text' class='comment' name='comment'>
							<input type='hidden' name='idpic' value='".$result[$i][0]."'>
							<input type='submit' class='submit-comment' value='Comment'>
						</form>
					</div>
					<div class='comment-display' id='cmt".$result[$i][0]."'>";
					for ($j=0; $j < $count_comm; $j++)
					{
						echo"
						<div class='single-comment'>
							<a><strong>".ucfirst(htmlspecialchars($res_comm[$j][1]))."</strong> ".htmlspecialchars($res_comm[$j][0])."</a>
						</div>";
					}
		echo "
					</div>
				</div>
			</body>
		</html>";
	}

	?>

<?php include_once('footer.html'); ?>

<form method="post">
	<div id="navigation">
		<input type="submit" name="previous" id="prev" class="input-page" value="<" style="display: none;">
		<input type="submit" name="next" id="next" class="input-page" value=">" style="display: none;">
	</div>
</form>


<?php

if ($index_page > 0)
	echo "<script>display_nav('prev');</script>";
if ($count == 6)
	echo "<script>display_nav('next');</script>";

?>












