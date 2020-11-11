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
# Getting data from the form
$title =  mysqli_real_escape_string($connection, e($_POST['title']));
$desc =  mysqli_real_escape_string($connection, e($_POST['desc']));
if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
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
  #Create new csrf token
  createCSRF();
  mysqli_close($connection);
  header("Location: ../public/profile.php");
}
?>