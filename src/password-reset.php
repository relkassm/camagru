<?php

session_start();

include_once('config/setup.php');

$email = isset($_POST['email']) ? $_POST['email'] : '';
$error = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
	try 
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "SELECT * FROM Camagru.User WHERE email=:email;";
	    $query = $conn->prepare($sql);
	    $query->bindValue(':email', $email);
    	$query->execute();
    	$result = $query->fetchAll();
    	if (count($result) > 0)
    	{
    		$to = $email;
			$encrypted_email=openssl_encrypt($email,"AES-128-ECB", "salt");
			$subject = "Email Reintialisation";
			$message = "
			<html>
			<head>
			<title>Email Reintialisation</title>
			</head>
			<body>
			<h3>Good to see you</h3>
			<a href='localhost/passchange.php?key=".$encrypted_email."'>Change Your Password</a>
			</body>
			</html>";

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <camagru.1337@gmail.com>' . "\r\n";
			$headers .= 'Reply-To: <camagru.1337@gmail.com>' . "\r\n";

			mail($to,$subject,$message,$headers);

			$error = 2;
	    }
	    else
	    {
	    	$error = 1;
	    }
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
	<title>Camagru - Password Reintialisation</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/login.css">
	<link rel="icon" href="ressources/camagru-logo.png">
	<script type="text/javascript" src="scripts/signin-script.js"></script>
</head>

<body>
	<div id="login-container">
		<div id="inputs-container">
			<a href="."><img id="img-font" src="ressources/camagru-font.png"></a>
			<form method="post">
				<input class="inputs" type="text" id="email" name="email" placeholder="Email">
				<input type="submit" id="submit-button" value="Send Email" style="background: #000000;">
			</form>
		</div>
		<div class="signup-container" hidden="true" id="hidden-error"><a id="error-msg"></a></div>
	</div>
</body>
</html>

<?php

if ($error == 1) 
	echo "<script> logError3(); </script>";
else if ($error == 2)
	echo "<script> logNoError2('".$email."'); </script>";

?>