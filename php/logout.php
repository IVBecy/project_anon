<?php
#Destroy the session, remove vars and redirect
session_start();
#connect to DB
include("./connect.php");
#Set logout time
$t = time();
$uname = $_SESSION["uname"];
$time_query = "UPDATE `users` SET `logout-time` = '$t' WHERE `uname` = '$uname'"; 
$connection->query($time_query);
session_destroy();
header('Location: ../root/index.html');
?>