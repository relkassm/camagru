	<html id="page">
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
	include('header.php');
	include('footer.html');

	if ($_SESSION['login'] == '')
	{
		header("Location: index.php");
		exit();
	}

	$login = $_SESSION['login'];

//Pagination
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['previous']) && $_SESSION['index_page'] != 0)
		$_SESSION['index_page'] -= 5;
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['next']))
		$_SESSION['index_page'] += 5;

	$index_page = $_SESSION['index_page'];

//STORE PHOTOS ON ARRAY result[][]
	
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

//ADD ? REMOVE LIKE ON PHOTO
	
	for ($i = $min; $i <= $max; $i++)
	{
		if (isset($_POST['action']) && $_POST['action'] == "fLike".$i) 
		{
			try 
			{
			    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
			    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			    $sql = "INSERT INTO Camagru.Like (id_picture, id_user)
			    		VALUES (:idpic, (SELECT id FROM Camagru.User WHERE User.login = :login));";
				$query = $conn->prepare($sql);
	    		$query->bindValue(':idpic', $i);
				$query->bindValue(':login', $login);
				$query->execute();
		    }
			catch(PDOException $e)
		    {
		   		try 
				{
				    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
				    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				    $sql = "DELETE FROM Camagru.Like 
				    		WHERE id_picture = :idpic
				    		AND id_user = (SELECT id 
				    		FROM Camagru.User WHERE User.login = :login);";
				    $query = $conn->prepare($sql);
	    			$query->bindValue(':idpic', $i);
					$query->bindValue(':login', $login);
					$query->execute();
			    }
			    catch(PDOException $e)
		    	{
					sqlerror_mail($sql, $e);
		    	}
		    }
			$conn = null;
		}
	}

//ADDING COMMENT

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idpic']))
	{
		try 
		{
		    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO Camagru.Comment (id_picture, id_user, comment_text, comment_date)
					VALUES (:idpic, (SELECT id FROM Camagru.User WHERE User.login = :login), :comment, :xdate);";
			$query = $conn->prepare($sql);
	    	$query->bindValue(':idpic', $_POST['idpic']);
			$query->bindValue(':login', $login);
			$query->bindValue(':comment', $_POST['comment']);
			$query->bindValue(':xdate', date('Y-m-d H:i:s'));
			$query->execute();

			$sql = "SELECT User.email, User.notification
		    		FROM Camagru.User INNER JOIN Camagru.Photo
		    		ON User.id = Photo.id_user
		    		WHERE Photo.id_picture = ".$_POST['idpic'].";";
		    $query = $conn->prepare($sql);
			$query->execute();
			$result3 = $query->fetchAll();
			
			if ($result3[0][1] == '1')
			{
				$to = $result3[0][0];
				$subject = "You received a new comment";

				$message = "
				<html>
				<head>
				<title>You received a new comment</title>
				</head>
				<body>
				<h3>".$login." commented on your picture :</h3>
				<h4>".$_POST['comment']."</h4>
				</body>
				</html>";

				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: <camagru@1337.com>' . "\r\n";
				$headers .= 'Reply-To: <camagru@1337.com>' . "\r\n";

				mail($to,$subject,$message,$headers);	
			}

		}
		catch(PDOException $e)
		{
			sqlerror_mail($sql, $e);
		}
		$conn = null;
	}


//SPLIT TIME INTO Days->Hours->Minutes->Second

	$end = $count;
	if ($end == 6)
		$end--;
	$now = New DateTime();
	for ($i = 0; $i < $end ; $i++) 
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

//CHECKS IF PHOTO IS LIKED BY USER

		    $sql = "SELECT * 
		    		FROM Camagru.Like 
		    		WHERE Like.id_picture=".$result[$i][0]." 
		    		AND Like.id_user=(SELECT id FROM Camagru.User 
		    		WHERE User.login= :login);";
		    $query = $conn->prepare($sql);
	    	$query->bindValue(':login', $login);
			$query->execute();
		    $res_liked = $query->fetchAll();
			if (count($res_liked))
				$heart = "on";
		}
		catch(PDOException $e)
		{
			sqlerror_mail($sql, $e);
		}

		$conn = null;

// DISPLAY RESULT ON HTML FOREACH PHOTO

		echo "
		<html>
			<body id='bod'>
				<div class='container-feed' id='xd".$result[$i][0]."'>
					<h2>".ucfirst(htmlspecialchars($result[$i][2]))." ".strtoupper(htmlspecialchars($result[$i][3]))."</h2>
					<img class='img-feed' src=".$result[$i][1].">
					<div class='r-b'>
						<div><a class='right-bot'>".$date_res."</a></div>
						<div><a class='right-bot' src='' onclick='dspComments(".$result[$i][0].")'>".$count_likes." ♥ ".$count_comm." ▤</a></div>
					</div>
					<div class='lik-con'>
							<a src='' onclick='myAjax(".$result[$i][0].")' class='gg' id='".$result[$i][0]."'>
								<img src='ressources/heart-".$heart.".png' class='heart-off'>
							</a>
							<a src='' onclick='display(".$result[$i][0].")' class='gg' id='".$result[$i][0]."'>
								<img src='ressources/comment-off.png' class='comment-off' id='id".$result[$i][0]."'>
							</a>
					</div>
					<div class='com-con' id='com".$result[$i][0]."'>
						<form class='form-comment' action='' method='post' id='".$result[$i][0]."'>
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










