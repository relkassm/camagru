<?php

include_once('config/setup.php');

if (isset($_GET["key"]))
	$key = $_GET["key"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
	try 
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "UPDATE Camagru.User SET verified = 1
	    		WHERE vkey='$key';";
	    $conn->exec($sql);
	    header('Location: index.php');
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
	<title>Camagru - Verification</title>
	<link rel="stylesheet" type="text/css" href="css/profile.css">
	<link rel="icon" href="ressources/camagru-logo.png">

</head>
<body style="background-color: #FFFAFF; padding: 0; margin: 0; margin-top: 20vh;">
	<form method="post">
		<div id="inputs-container" style="text-align: center;">
				<input id="update-button" type="submit" name="sub" value="Verify Your Account" style="float: none;">
		</div>
	</form>
</body>
</html>

