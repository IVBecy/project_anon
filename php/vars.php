<?php
session_start();
$uname = $_SESSION["uname"];
#To be connected to the DB
include("./connect.php");
#cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
if ($uname){
  #Get all the data for the logged in user
  $query = "SELECT * FROM `users` WHERE `uname` = '$uname'";
  $data = mysqli_query($connection,$query);
  $data = mysqli_fetch_assoc($data);
  #Vars for the user
  $id = $data["id"];
  $logged_uname = $data["uname"];
  $logged_pass = $data["pass"];
  $logout_time = $data["logout-time"];
  $prof_img = $data["img"];
  $logged_email = $data["email"];
  $follows = $data["follows"];
  $follows = openssl_decrypt($follows,"AES-128-CBC",$id);
  $follows = json_decode($follows,true);
  $followers = $data["followers"];
  $followers  = openssl_decrypt($followers ,"AES-128-CBC",$id);
  $followers = json_decode($followers,true);
  #Posts
  $p_query = "SELECT `title`,`report` FROM `posts` WHERE `uname` = '$uname'";
  $projects = mysqli_query($connection,$p_query);
}
?>