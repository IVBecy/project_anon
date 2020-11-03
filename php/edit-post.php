<?php 
session_start();
$uname = $_SESSION["uname"];
# cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
# Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
include("./connect.php");
# Getting the projects from the database
$p_query = "SELECT `projects` FROM `users` WHERE `uname` = '$uname'";
$projects = mysqli_query($connection,$p_query);
$projects = mysqli_fetch_row($projects);
$projects = $projects[0];
$projects = json_decode($projects,true);
#Edited Post and reassign vars
$newArray = json_decode($_COOKIE["editedPost"],true);
$newTitle = $newArray["newTitle"];
$newDesc = $newArray["newDesc"];
$oldArray = json_decode($_COOKIE["oldPost"],true);
$oldTitle = $oldArray["oldTitle"];
$oldDesc = $oldArray["oldDesc"];
# Adding new title and description
unset($projects[$oldTitle]);
$projects[$newTitle] = $newDesc;
$projects = json_encode($projects);
setcookie("editedPost", NULL, 0);
setcookie("oldPost", NULL, 0);
$append_query = "UPDATE `users` SET projects = '$projects' WHERE `uname` = '$uname'";
if ($connection->query($append_query) === TRUE) {
  $append = true;
  $message = "";
}
# If something goes wrong
else{
  $append = false;
  $message = "Something went wrong please try again.";
} 
mysqli_close($connection);
header("Location: ./profile.php")
?>