<?php 
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
# Turn off all notices
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
  #Deletion cookie
  $delete_item = e($_COOKIE["ToBeDeleted"]);
  #Delete the post from the DB
  foreach($collection as $k) {
  if ($k["title"] == $delete_item){
    $delete_query = "DELETE FROM `posts` WHERE `uname` = '$uname' AND `title` = '$delete_item'";
    $connection->query($delete_query);
    }
  }
  #Create new csrf token
  createCSRF();
  #Delete cookie 
  setcookie("ToBeDeleted", NULL, 0);
  mysqli_close($connection);
  header("Location: ../public/profile");
};
?>