<?php

include('database.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS Camagru.User
    	(id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    	email varchar(50) NOT NULL UNIQUE,
    	firstname varchar(30) NOT NULL,
    	lastname varchar(30) NOT NULL,
    	login varchar(30) NOT NULL UNIQUE,
    	password varchar(255) NOT NULL,
    	vkey varchar(255) NOT NULL,
    	verified varchar(1) NOT NULL,
        notification varchar(1));";
    $conn->exec($sql);
}
catch(PDOException $e)
{
		echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS Camagru.Photo
    	(id_picture INT(6) UNSIGNED AUTO_INCREMENT,
    	url LONGTEXT NOT NULL,
    	id_user INT(6) UNSIGNED,
    	creation_date DATETIME NOT NULL,
    	PRIMARY KEY (id_picture),
    	FOREIGN KEY (id_user) REFERENCES User(id));";
    $conn->exec($sql);
}
catch(PDOException $e)
{
		echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS Camagru.Like
        (id_picture INT(6) UNSIGNED,
        id_user INT(6) UNSIGNED,
        PRIMARY KEY (id_picture, id_user),
        FOREIGN KEY (id_picture) REFERENCES Photo(id_picture),
        FOREIGN KEY (id_user) REFERENCES User(id));";
    $conn->exec($sql);
}
catch(PDOException $e)
{
        echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS Camagru.Comment
        (id_comment INT(6) UNSIGNED AUTO_INCREMENT,
        id_picture INT(6) UNSIGNED,
        id_user INT(6) UNSIGNED,
        comment_text TEXT NOT NULL,
        comment_date DATETIME NOT NULL,
        PRIMARY KEY (id_comment),
        FOREIGN KEY (id_picture) REFERENCES Photo(id_picture),
        FOREIGN KEY (id_user) REFERENCES User(id));";
    $conn->exec($sql);
}
catch(PDOException $e)
{
        echo $sql . "<br>" . $e->getMessage();
}

$conn = null;

function sqlerror_mail($sql, $e)
{
    echo "<script>alert('Unexpected error occured, try again later');</script>";

    $to = "reda.imcreation@gmail.com";
    $subject = "Camagru SQL Error";

    $message = "
    <html>
    <head>
    <title>Confirmation Email</title>
    </head>
    <body>
    <h3>".$sql."<br>".$e->getMessage()."</h3>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <camagru.1337@gmail.com>' . "\r\n";
    $headers .= 'Reply-To: <camagru.1337@gmail.com>' . "\r\n";

    mail($to,$subject,$message,$headers);
}

?>