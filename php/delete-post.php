<?php 
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
# cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
# Turn off all notices
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
#Deletion cookie
$delete_item = e($_COOKIE["ToBeDeleted"]);
#Delete the post from the DB
foreach($collection as $k) {
  if ($k["title"] == $delete_item){
    $delete_query = "DELETE FROM `posts` WHERE `uname` = '$uname' AND `title` = '$delete_item'";
    $connection->query($delete_query);
  }
}
#Delete cookie 
setcookie("ToBeDeleted", NULL, 0);
mysqli_close($connection);
header("Location: ./profile.php")
?>