<?php 
#Session (start and vars)
session_start();
#Username from URL query
$src_uname = $_SESSION["src_uname"];
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#Getting some vars
include("./vars.php");
# If not logged in, redirect to login page
if ($logged_in === false){
  http_response_code(404);
  header("Location: ../public/index.html");
  die();
}
#ID
$id_src_name = "SELECT `id` FROM `users` WHERE `uname` = '$src_uname'";
$followers_id = mysqli_query($connection,$id_src_name);
$followers_id = mysqli_fetch_row($followers_id);
$followers_id = $followers_id[0];
$id_name = "SELECT `id` FROM `users` WHERE `uname` = '$uname'";
$follows_id = mysqli_query($connection,$id_name);
$follows_id = mysqli_fetch_row($follows_id);
$follows_id = $follows_id[0];
#Following system
$followers_query = "SELECT `followers` FROM `users` WHERE `uname` = '$src_uname'";
$followers = mysqli_query($connection,$followers_query);
$followers = mysqli_fetch_row($followers);
$followers = $followers[0];
$follows_query = "SELECT `follows` FROM `users` WHERE `uname` = '$uname'";
$follows = mysqli_query($connection,$follows_query);
$follows = mysqli_fetch_row($follows);
$follows = $follows[0];
$followers = openssl_decrypt($followers,"AES-128-CBC",$followers_id);
$follows = openssl_decrypt($follows,"AES-128-CBC",$follows_id);
$followers = json_decode($followers,true);
$follows = json_decode($follows,true);
#Add the pov user to the search user's followers list ## NOT LOGGED IN USER
array_push($followers, $id);
#Add the searched user to the pov user's following list ## LOGGED IN USER
array_push($follows, $followers_id);
$followers = json_encode($followers);
$follows = json_encode($follows);
$followers = openssl_encrypt($followers,"AES-128-CBC",$followers_id);
$follows = openssl_encrypt($follows,"AES-128-CBC",$follows_id);
#Append new follow to the DB ## NOT LOGGED IN
$follow_q = "UPDATE `users` SET `followers` = '$followers' WHERE `uname` = '$src_uname'";
$connection->query($follow_q);
#Append nwe following to the DB ## LOGGED IN
$follow_q = "UPDATE `users` SET `follows` = '$follows' WHERE `uname` = '$uname'";
$connection->query($follow_q);
header("Location: ../public/$src_uname")
?>