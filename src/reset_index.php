<?php

session_start();

if ($_SESSION['login'] == '')
{
	header("Location: index.php");
	exit();
}

$_SESSION['index'] = 0;
$_SESSION['index_page'] = 0;

header("Location: homepage.php");
exit();

?>