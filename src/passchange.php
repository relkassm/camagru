<?php

session_start();

include_once('config/setup.php');

$passwd = isset($_POST['passwd']) ? hash('md5', $_POST["passwd"]) : '';
$passwd2 = isset($_POST['passwd2']) ? hash('md5', $_POST["passwd"]) : '';

$encrypted_email = isset($_GET['key']) ? $_GET['key'] : '';

$encrypted_email = str_replace(' ', '+', $encrypted_email);

$decrypted_email = openssl_decrypt($encrypted_email, "AES-128-ECB", "salt");

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
	if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[\S]{8,30}$/", $_POST["passwd"]))
	  echo "<script>alert('Invalid Password');</script>";
	else if ($_POST["passwd"] != $_POST["passwd2"])
	  echo "<script>alert('Passwords Do Not Match');</script>";
	else
	{
		try 
		{
		    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $sql = "UPDATE Camagru.User SET password = :passwd WHERE email = :email";
		    $query = $conn->prepare($sql);
			$query->bindValue(':passwd', $passwd);
			$query->bindValue(':email', $decrypted_email);
			$query->execute();
			header('Location: profile.php');
			exit();
	    }
		catch(PDOException $e)
	    {
	   		sqlerror_mail($sql, $e);
	    }
		$conn = null;
	}
}

?>

<html>
<head>
	<title>Camagru - Password Change</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<link rel="icon" href="ressources/camagru-logo.png">
	<script>
		function validatePassword(password) 
		{
		    var re = /^(?=.*[A-Za-z])(?=.*\d)[\S]{8,30}$/;
		    return re.test(password);
		}

		function validate()
		{
			var passwd = document.getElementById('passwd').value;
			var passwd2 = document.getElementById('passwd2').value;

			if (validatePassword(passwd) && passwd === passwd2)
			{
				document.getElementById('submit-button').style.backgroundColor = "#000000";
				document.getElementById('submit-button').disabled = false;
			}
			else
			{
				document.getElementById('submit-button').style.backgroundColor = "#555555";
				document.getElementById('submit-button').disabled = true;
			}
		}

	</script>
</head>

<body>
	<div id="login-container">
		<div id="inputs-container">
			<img id="img-font" src="ressources/camagru-font.png">
			<form method="post">
				<input class="inputs" type="password" id="passwd" name="passwd" placeholder="Password" onchange="validate()" oninput="validate()">
				<input class="inputs" type="password" id="passwd2" name="passwd2" placeholder="Confirm Password" onchange="validate()" oninput="validate()">
				<input type="submit" id="submit-button" value="Change Password">
			</form>
		</div>
		<div class="signup-container" hidden="true" id="hidden-error"><a id="error-msg"></a></div>
	</div>
</body>
</html>