<?php 

include('config/setup.php');

function adrmLike($n)
{
	include('config/database.php');

	$login = $_SESSION['login'];

	echo "<script>alert(boo);</script>";

	try 
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "INSERT INTO Camagru.Like (id_picture, id_user)
	    		VALUES (:idpic, (SELECT id FROM Camagru.User WHERE User.login = :login));";
		$query = $conn->prepare($sql);
		$query->bindValue(':idpic', $n);
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
			$query->bindValue(':idpic', $n);
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

?>