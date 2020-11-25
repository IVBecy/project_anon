<?php 
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#getting some vars
include("./vars.php");
#Edited Post and reassign vars
$newArray = json_decode($_COOKIE["editedPost"],true);
$newTitle = mysqli_real_escape_string($connection,e($newArray["newTitle"]));
$newDesc = mysqli_real_escape_string($connection,e($newArray["newDesc"]));
$oldArray = json_decode($_COOKIE["oldPost"],true);
$oldTitle = mysqli_real_escape_string($connection,e($oldArray["oldTitle"]));
$oldDesc = mysqli_real_escape_string($connection,e($oldArray["oldDesc"]));
#Getting the projects from the database
$p_query = "SELECT `title`,`report` FROM `posts` WHERE `name_id` = '$id' AND `title` = '$oldTitle'";
$project = mysqli_query($connection,$p_query);
$project = mysqli_fetch_assoc($project);
if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
  #Adding new title and description
  $project["title"] = $newTitle;
  $project["report"] = $newDesc;
  #Create new csrf token
  createCSRF();
  #Delete cookies
  setcookie("editedPost", NULL, 0);
  setcookie("oldPost", NULL, 0); 
  #Making the changes in the Database
  $append_query = "UPDATE `posts` SET `title` = '$newTitle', `report` = '$newDesc' WHERE `name_id` = '$id' AND `title` = '$oldTitle'";
  $connection->query($append_query);
  mysqli_close($connection); 
}
header("Location: ../public/profile.php")
?>