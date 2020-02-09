<?php

session_start();

include_once('config/setup.php');

if (isset($_SESSION['login']))
	$ss_login = $_SESSION['login'];
else
	$ss_login = '';

if ($ss_login != '')
{
	header("Location: homepage.php");
	exit();
}

$email = isset($_POST["email"]) ? $_POST["email"] : "";
$firstname = isset($_POST["firstname"]) ? strtolower($_POST["firstname"]) : "";
$lastname = isset($_POST["lastname"]) ? strtolower($_POST["lastname"]) : "";
$login = isset($_POST["login"]) ? $_POST["login"] : "";
$passwd = isset($_POST["passwd"]) ? hash('md5', $_POST["passwd"]) : "";
$passwd2 = isset($_POST["passwd2"]) ? hash('md5', $_POST["passwd2"]) : "";
$key = hash('md5', time().$login);


$error = 0;
$go = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	  echo "<script>alert('Invalid Email');</script>";
	else if (!preg_match("/^[a-zA-Z ]{2,15}$/", $firstname))
	  echo "<script>alert('Invalid First Name');</script>";
	else if (!preg_match("/^[a-zA-Z ]{2,15}$/", $lastname))
	  echo "<script>alert('Invalid Last Name');</script>";
	else if (!preg_match("/^[a-zA-Z0-9]{2,10}$/", $login))
	  echo "<script>alert('Invalid Login');</script>";
	else if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[\S]{8,30}$/", $_POST["passwd"]))
	  echo "<script>alert('Invalid Password');</script>";
	else if ($_POST["passwd"] != $_POST["passwd2"])
	  echo "<script>alert('Passwords Do Not Match');</script>";
	else
	{
		try 
		{
		    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $sql = "INSERT INTO Camagru.User (email, firstname, lastname, login, password, vkey, verified, notification)
		    		VALUES (:email, :firstname, :lastname, :login, :passwd, :key, '0', '1');";
		    $stmt = $conn->prepare($sql);
			$stmt->bindValue(':email', $email);
			$stmt->bindValue(':firstname', $firstname);
			$stmt->bindValue(':lastname', $lastname);
			$stmt->bindValue(':login', $login);
			$stmt->bindValue(':passwd', $passwd);
			$stmt->bindValue(':key', $key);
			$stmt->execute();

		    $to = $email;
			$subject = "Confirmation Email";

			$message = "
			<html>
			<head>
			<title>Confirmation Email</title>
			</head>
			<body>
			<h3>Good to see you ".$firstname." ".$lastname." </h3>
			<a href='localhost/verify.php?key=".$key."' target='_blank'>Verify Your Account</a>
			</body>
			</html>";

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <camagru.1337@gmail.com>' . "\r\n";
			$headers .= 'Reply-To: <camagru.1337@gmail.com>' . "\r\n";

			mail($to,$subject,$message,$headers);

			$go = 1;
		    
	    }
		catch(PDOException $e)
	    {
	   		$e->getMessage();
	   		if (strpos($e, "key 'email'") !== false) 
				$error = 1;
			else if (strpos($e, "key 'login'") !== false) 
				$error = 2;
			else
				sqlerror_mail($sql, $e);
	    }

		$conn = null;
	}
}

?>

<html>
<head>
	<title>Camagru - Sign Up</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/signup.css">
	<script type="text/javascript" src="scripts/signup-script.js"></script>
	<link rel="icon" href="ressources/camagru-logo.png">
</head>

<body>
	<div id="signup-container">
		<div id="inputs-container">
			<img id="img-font" src="ressources/camagru-font.png">
			<form method="post" action="">
				<input class="inputs" type="text" id="email" name="email" placeholder="Email" title="Must be a valid email adress" onchange="validate()" oninput="validate()">
				<input class="inputs" type="text" id="firstname" name="firstname" placeholder="First Name" title="2 to 15 letters" onchange="validate()" oninput="validate()">
				<input class="inputs" type="text" id="lastname" name="lastname" placeholder="Last Name" title="2 to 15 letters" onchange="validate()" oninput="validate()">
				<input class="inputs" type="text" id="login" name="login" placeholder="Login" title="2 to 10 letters/ numbers" onchange="validate()" oninput="validate()">
				<input class="inputs" type="password" id="passwd" name="passwd" placeholder="Password" title="8 to 30 characters, at lesast one letter and number" onchange="validate()" oninput="validate()">
				<input class="inputs" type="password" id="passwd2" name="passwd2" placeholder="Verify Password" title="Must match the first password" onchange="validate()" oninput="validate()">
				<input type="submit" id="submit-button" value="Sign Up" disabled="true">
			</form>
		</div>
		<div class="login-container" hidden="true" id="hidden-error"><a id="error-msg"></a></div>
		<div class="login-container"><a id="login1">Already a User? </a><a href="login.php" id="login2">Sign In</a></div>
	</div>
</body>
</html>

<?php

if ($error == 1) 
	echo "<script> error1(); </script>";
else if ($error == 2)
	echo "<script> error2(); </script>";

if ($go == 1)
{
	header('Location: login.php');
	exit();
}

?>




