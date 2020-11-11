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
#Getting the projects from the database
$collection = [];
$p_query = "SELECT `title`,`report` FROM `posts` WHERE `uname` = '$uname'";
$projects = mysqli_query($connection,$p_query);
if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
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
  #Create new csrf token
  createCSRF();
  #Delete cookies
  setcookie("editedPost", NULL, 0);
  setcookie("oldPost", NULL, 0); 
  #Making the changes in the Database
  $append_query = "UPDATE `posts` SET `title` = '$newTitle', `report` = '$newDesc' WHERE `uname` = '$uname' AND `title` = '$oldTitle'";
  if ($connection->query($append_query) === true){
    echo "SUCCESS";
  }
  else{
    echo $connection->error;
  }
  mysqli_close($connection); 
}
header("Location: ../public/profile.php")
?>