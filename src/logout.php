<?php

session_start();

$_SESSION['login'] = '';
$_SESSION['index_page'] = 0;
$_SESSION['index'] = 0;
$_SESSION['mode'] = 0;
$_SESSION['display'] = 0;

header("Location: index.php");
exit();

?>