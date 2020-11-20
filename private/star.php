<?php 
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
#Username to like post for
$src_uname = $_SESSION["src_uname"];
#Post title
$like_title = $_POST["title"];
$src_id = $_SESSION["src_id"];
#Getting likes from the posts table
$src_query = "SELECT `likes` FROM `posts` WHERE `uname` = '$src_uname' AND `title` = '$like_title'";
$likes = mysqli_query($connection,$src_query);
$likes = mysqli_fetch_row($likes);
$likes = $likes[0];
$likes = openssl_decrypt($likes,"AES-128-CBC",$src_id);
$likes = json_decode($likes,true);
if (in_array($uname,$likes)){
  # Remove the user's username from the like list
  unset($likes[array_search($uname,$likes)]);
  $likes = array_values($likes);
}else{
  # Add the user's username to the like list
  array_push($likes,$uname);
}
$likes = json_encode($likes);
$likes = openssl_encrypt($likes,"AES-128-CBC",$src_id);
$append_q = "UPDATE `posts` SET `likes` = '$likes'  WHERE `uname` = '$src_uname' AND `title` = '$like_title'";
$connection->query($append_q);
//header("Location: ../public/$src_uname");
?>