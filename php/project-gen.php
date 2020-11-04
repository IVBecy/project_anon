<?php
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
#cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#Getting the projects from the database
$p_query = "SELECT `projects` FROM `users` WHERE `uname` = '$uname'";
$projects = mysqli_query($connection,$p_query);
$projects = mysqli_fetch_row($projects);
$projects = $projects[0];
$projects = json_decode($projects,true);
# Getting data from the form
$title =  mysqli_real_escape_string($connection, e($_POST['title']));
$desc =  mysqli_real_escape_string($connection, e($_POST['desc']));
# Append new project to the dict
$projects[$title] = $desc;
$projects = json_encode($projects);
#Appending data to the Database
$append_query = "UPDATE `users` SET projects = '$projects' WHERE `uname` = '$uname'";
if ($connection->query($append_query) === TRUE) {
  $append = true;
  $message = "";
}
#If something goes wrong
else{
  $append = false;
  $message = "Something went wrong please try again.";
} 
mysqli_close($connection);
header("Location: ./profile.php")
?>