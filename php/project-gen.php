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
# Getting data from the form
$title =  mysqli_real_escape_string($connection, e($_POST['title']));
$desc =  mysqli_real_escape_string($connection, e($_POST['desc']));
#Appending data to the Database
$t = time();
$append_query = "INSERT INTO `posts` (uname,title,report,time) VALUES ('$uname','$title','$desc','$t')";
if ($connection->query($append_query) === TRUE) {
  $append = true;
  $message = "";
}
#If something goes wrong
else{
  $append = false;
} 
mysqli_close($connection);
header("Location: ./profile.php")
?>