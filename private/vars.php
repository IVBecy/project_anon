<?php
session_start();
if ($_SESSION["uname"]){
  $uname = $_SESSION["uname"];
  $logged_in = true;
  $html_id = "dropdown-img";
}else{  
  $uname = "<h5><a style='color:white' href='./index.html'>Login</a><h5>";
  $logged_in = false;
  $dir = $uname;
  $html_id = "login-link";
}
#To be connected to the DB
include("connect.php");
#cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
#creating csrf token
function createCSRF(){
  $_SESSION["csrf-token"] = bin2hex(random_bytes(64));
  setcookie("CSRF-Token", $_SESSION["csrf-token"], 2147483647, "/");
  return $_SESSION["csrf-token"];
}
#Get all the data for the logged in user
if ($uname && $logged_in === true){
  $query = "SELECT * FROM `users` WHERE `uname` = '$uname'";
  $data = mysqli_query($connection,$query);
  $data = mysqli_fetch_assoc($data);
  #Vars for the user
  $id = $data["id"];
  $logged_uname = $data["uname"];
  $logged_pass = $data["pass"];
  $logout_time = $data["logout-time"];
  $prof_img = $data["img"];
  if ($prof_img == ""){
    $prof_img_state = false;
    $dir = "<img src='../assets/imgs/profile-img.png'>";
  }
  else{
    $dir = '<img src="data:image/jpeg;base64,'.$prof_img.'"/>';
    $prof_img_state = true;
  }
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