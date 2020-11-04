<?php 
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
# cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#Getting the projects from the database
$collection = [];
$p_query = "SELECT `title`,`report` FROM `posts` WHERE `uname` = '$uname'";
$projects = mysqli_query($connection,$p_query);
#Appending all the projects to one array
while ($row = mysqli_fetch_assoc($projects)) {
    array_push($collection,$row);
};
#Edited Post and reassign vars
$newArray = json_decode($_COOKIE["editedPost"],true);
$newTitle = mysqli_real_escape_string($connection,e($newArray["newTitle"]));
$newDesc = mysqli_real_escape_string($connection,e($newArray["newDesc"]));
$oldArray = json_decode($_COOKIE["oldPost"],true);
$oldTitle = mysqli_real_escape_string($connection,e($oldArray["oldTitle"]));
$oldDesc = mysqli_real_escape_string($connection,e($oldArray["oldDesc"]));
#Adding new title and description
foreach($collection as $k) {
  if ($k["title"] == $oldTitle){
    $k["title"] = $newTitle;
    $k["report"] = $newDesc;
  }
}
#Delete cookies
setcookie("editedPost", NULL, 0);
setcookie("oldPost", NULL, 0); 
#Making the changes in the Database
$append_query = "UPDATE `posts` SET `title` = '$newTitle', `report` = '$newDesc' WHERE `uname` = '$uname' AND `title` = '$oldTitle'";
$connection->query($append_query);
mysqli_close($connection);
header("Location: ./profile.php")
?>