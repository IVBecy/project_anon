<?php
session_start();
$uname = $_SESSION["uname"];
# cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
# Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
# All the database data we need to connect
$DB_SERVER = "127.0.0.1:3307";
$DB_USERNAME = "root";
$DB_PASSWORD = ""; 
$DB_NAME = "anon";
# Connecting to the server
$connection = mysqli_connect($DB_SERVER,$DB_USERNAME,$DB_PASSWORD,$DB_NAME);
# on connection error
if($connection == false){
  echo "<h2>Something went wrong</h2>";
}
# Getting the projects from the database
$p_query = "SELECT `projects` FROM `users` WHERE `uname` = '$uname'";
$projects = mysqli_query($connection,$p_query);
$projects = mysqli_fetch_row($projects);
$projects = $projects[0];
$projects = unserialize($projects);
# Getting data from the form
$title =  mysqli_real_escape_string($connection, e($_POST['title']));
$desc =  mysqli_real_escape_string($connection, e($_POST['desc']));
# Append new project to the dict
$projects[$title] = $desc;
$projects = serialize($projects);
#Appending data to the Database
$append_query = "UPDATE `users` SET projects = '$projects' WHERE `uname` = '$uname'";
if ($connection->query($append_query) === TRUE) {
  $append = true;
  $message = "";
}
## Check for the same uname
else{
  $append = false;
  $message = "Something went wrong please try again.";
} 
mysqli_close($connection);
header("Location: ./profile.php")
?>