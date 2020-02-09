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

$login = isset($_POST['login']) ? $_POST['login'] : '';
$passwd = isset($_POST['passwd']) ? hash('md5', $_POST["passwd"]) : '';

$error = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	try 
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "SELECT login, password, verified FROM Camagru.User WHERE login=:login AND password=:password";
	    $stmt = $conn->prepare($sql);
		$stmt->bindValue(':login', $login); 
		$stmt->bindValue(':password', $passwd);
		$stmt->execute();
    	$result = $stmt->fetchAll();
    	if (count($result) > 0)
    	{
    		if ($result[0][2] == '1')
    		{
    			$_SESSION['login'] = $login;
    			$_SESSION['index'] = 0;
    			$_SESSION['index_page'] = 0;
				$_SESSION['mode'] = 0;
				$_SESSION['display'] = 0;
				header("Location: homepage.php");
	    		exit();
    		}
    		else
    			$error = 2;
    	}
	    else
	    	$error = 1;
    }
	catch(PDOException $e)
    {
   		sqlerror_mail($sql, $e);
    }
	$conn = null;
}
?>

<html>
<head>
	<title>Camagru - Login Page</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<link rel="icon" href="ressources/camagru-logo.png">
	<script type="text/javascript" src="scripts/signin-script.js"></script>
</head>

<body>
	<div id="login-container">
		<div id="inputs-container">
			<img id="img-font" src="ressources/camagru-font.png">
			<form method="post">
				<input class="inputs" type="text" id="login" name="login" placeholder="Login" onchange="validate()" oninput="validate()">
				<input class="inputs" type="password" id="passwd" name="passwd" placeholder="Password" onchange="validate()" oninput="validate()">
				<input type="submit" id="submit-button" value="Log In">
			<div id="forgot-pass-div"><a id="forgot-pass" href="password-reset.php">Forgot Password?</a></div>
			</form>
		</div>
		<div class="signup-container" hidden="true" id="hidden-error"><a id="error-msg"></a></div>
		<div class="signup-container"><a class="signup1">New user? </a><a href="signup.php" class="signup2">Sign Up</a></div>
		<div class="signup-container"><a class="signup1">Continue as </a><a href="index.php" class="signup2">Guest</a></div>
	</div>
</body>
</html>

<?php
if ($error == 1) 
	echo "<script> logError(); </script>";
else if ($error == 2)
	echo "<script> logError2(); </script>";
?>







