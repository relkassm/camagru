<html>
<head>
	<title>Camagru - Settings</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/header.css">
	<link rel="stylesheet" type="text/css" href="css/footer.css">
	<link rel="stylesheet" type="text/css" href="css/settings.css">
	<link rel="icon" href="ressources/camagru-logo.png">
	<script src="jquery-3.4.1.js"></script>
</head>
</html>

<?php 

session_start();

include_once('config/setup.php');
include('footer.html');

if ($_SESSION['login'] == '')
{
	header("Location: index.php");
	exit();
}

$_SESSION['index'] = 0;
$_SESSION['index_page'] = 0;

$login = $_SESSION['login'];

try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT notification 
    		FROM Camagru.User
    		WHERE login = :login";
    $query = $conn->prepare($sql);
	$query->bindValue(':login', $login);
	$query->execute();
	$result = $query->fetchAll();
}
catch(PDOException $e)
{
	sqlerror_mail($sql, $e);
}

$conn = null;

if (isset($_POST['action']) && $_POST['action'] == "mode") 
{
	if ($_SESSION['mode'] == 0)
		$_SESSION['mode'] = 1;
	else
		$_SESSION['mode'] = 0;
}

if (isset($_POST['action']) && $_POST['action'] == "notif") 
{
	try 
	{
	    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $pass);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    if ($result[0][0] == '1')
	    {
	    	$sql = "UPDATE Camagru.User SET notification = '0' WHERE login = :login;";
	    	$result[0][0] = '0';
	    }
	    else
	    {
	    	$sql = "UPDATE Camagru.User SET notification = '1' WHERE login = :login;";
	    	$result[0][0] = '1';
	    }
	    $query = $conn->prepare($sql);
		$query->bindValue(':login', $login);
		$query->execute();
    }
    catch(PDOException $e)
	{
		sqlerror_mail($sql, $e);
	}

	$conn = null;
}



if ($result[0][0] == '1')
	$swt = "ressources/on.png";
else
	$swt = "ressources/off.png";

if ($_SESSION['mode'] == 1)
	$swt2 = "ressources/on.png";
else
	$swt2 = "ressources/off.png";
include('header.php');

?>

<body id="bodd">
	<div id="cont">
		<div class="container">
			<a onclick="on_off1()">
				Email Notifications
				<img id="switch1" src="<?php echo $swt; ?>">
			</a>
		</div>

		<div class="container">
			<a onclick="on_off2()">
				Dark Mode
				<img id="switch2" src="<?php echo $swt2; ?>">
			</a>
		</div>
	</div>
</body>

<script>
	function on_off1() 
	{
	    $.ajax({
	         type: "POST",
	         url: 'settings.php',
	         data:{action:'notif'},
	         success:function(result) 
	         {
          	  $("#cont").load(location.href + " #cont>*", "");
	         }
	    });
	}

	function on_off2()
	{
		$.ajax({
	         type: "POST",
	         url: 'settings.php',
	         data:{action:'mode'},
	         success:function(result)
	         {
          	  $("#cont").load(location.href + " #cont>*", "");
          	  if (document.body.style.backgroundColor == 'rgb(0, 0, 0)')
          	  	document.body.style.backgroundColor = '#F7F7F7';
          	  else
          	  	document.body.style.backgroundColor = '#000000';
	         }
	    });
	}
</script>



