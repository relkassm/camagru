<html>
	<head>
	<title>Camagru - Profile</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/header.css">
	<link rel="stylesheet" type="text/css" href="css/footer.css">
	<link rel="stylesheet" type="text/css" href="css/profile.css">
	<link rel="icon" href="ressources/camagru-logo.png">
	<script type="text/javascript" src="scripts/update-script.js"></script>
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

$_SESSION['index'] = 0;
$_SESSION['index_page'] = 0;
$login = $_SESSION['login'];
$error = 0;
try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT email, firstname, lastname, login, password FROM Camagru.User WHERE login = :login";
    $query = $conn->prepare($sql);
	$query->bindValue(':login', $login);
	$query->execute();
	$result = $query->fetchAll();
	$xemail = $result[0][0];
	$xfirstname = $result[0][1];
	$xlastname = $result[0][2];
	$xlogin = $result[0][3];
	$xpasswd = $result[0][4];
}
catch(PDOException $e)
{
	sqlerror_mail($sql, $e);
}

$conn = null;


$encrypted_email=openssl_encrypt($xemail,"AES-128-ECB", "salt");
$href = "location.href='passchange.php?key=".$encrypted_email."'";

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
	$email = isset($_POST["email"]) ? $_POST["email"] : "";
	$firstname = isset($_POST["firstname"]) ? strtolower($_POST["firstname"]) : "";
	$lastname = isset($_POST["lastname"]) ? strtolower($_POST["lastname"]) : "";
	$nlogin = isset($_POST["login"]) ? $_POST["login"] : "";
	$passwd = isset($_POST["passwd"]) ? hash('md5', $_POST["passwd"]) : "";

	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
	  echo "<script>alert('Invalid Email');</script>";
	else if (!preg_match("/^[a-zA-Z ]{2,15}$/", $firstname))
	  echo "<script>alert('Invalid First Name');</script>";
	else if (!preg_match("/^[a-zA-Z ]{2,15}$/", $lastname))
	  echo "<script>alert('Invalid Last Name');</script>";
	else if (!preg_match("/^[a-zA-Z0-9]{2,10}$/", $nlogin))
	  echo "<script>alert('Invalid Login');</script>";
	else
	{
		try 
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "UPDATE Camagru.User set email=:email, firstname=:firstname, lastname=:lastname, login=:login WHERE login = :xlogin AND password=:xpasswd";
	    $query = $conn->prepare($sql);
		$query->bindValue(':email', $email);
		$query->bindValue(':firstname', $firstname);
		$query->bindValue(':lastname', $lastname);
		$query->bindValue(':login', $nlogin);
		$query->bindValue(':xlogin', $xlogin);
		$query->bindValue(':xpasswd', $passwd);
		$query->execute();
		if ($passwd == $xpasswd)
		{
		    $_SESSION['login'] = $nlogin;
			header("Refresh:0");
		}
		else
	  		echo "<script>alert('Incorrect Password');</script>";
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

<body>
	<div>
		<h1><?php echo ucfirst(htmlspecialchars($_SESSION['login']))."'s Profile"; ?></h1>
	</div>
	<div>
		<form method="post" action="" id="inputs-container">
			<div class="con-input"><a>Email :</a><input class="inputs" type="text" id="email" name="email" value="<?php echo $xemail; ?>" disabled="true" onchange="validate()" oninput="validate()"></div>
			<div class="con-input"><a>First Name :</a><input class="inputs" type="text" id="firstname" name="firstname" value="<?php echo $xfirstname; ?>" disabled="true" onchange="validate()" oninput="validate()"></div>
			<div class="con-input"><a>Last Name :</a><input class="inputs" type="text" id="lastname" name="lastname" value="<?php echo $xlastname; ?>" disabled="true" onchange="validate()" oninput="validate()"></div>
			<div class="con-input"><a>Login :</a><input class="inputs" type="text" id="login" name="login" value="<?php echo $xlogin; ?>" disabled="true" onchange="validate()" oninput="validate()"></div>
			<div class="con-input"><a>Password :</a><input class="inputs" type="password" id="passwd" name="passwd" value="***************" disabled="true" onchange="validate()" oninput="validate()"></div>
			<div class="con-input" id="update1"><input type="button" id="update-button" value="Update Info" onclick="proceed()"></div>
			<div class="con-input" id="update2" style="display: none;"><input type="submit" id="submit-button" value="Update"></div>
			<div class="con-input" id="update3"><input type="button" id="password-button" value="Update Password" onclick="<?php echo $href; ?>"></div>
		</form>
	</div>
</body>

<?php

if ($error == 1)
{
	echo "<script>alert('Email Already Used');</script>";
	header("Refresh:0");
}
if ($error == 2)
{
	echo "<script>alert('Login Already Taken');</script>";
	header("Refresh:0");
}

?>

