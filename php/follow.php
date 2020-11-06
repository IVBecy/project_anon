<?php 
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
#Username from URL query
$src_uname = $_SESSION["src_uname"];
#cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#Following system
$followers_query = "SELECT `followers` FROM `users` WHERE `uname` = '$src_uname'";
$followers = mysqli_query($connection,$followers_query);
$followers = mysqli_fetch_row($followers);
$followers = $followers[0];
$follows_query = "SELECT `follows` FROM `users` WHERE `uname` = '$uname'";
$follows = mysqli_query($connection,$follows_query);
$follows = mysqli_fetch_row($follows);
$follows = $follows[0];
$followers = json_decode($followers,true);
$follows = json_decode($follows,true);
#Add the pov user to the search user's followers list ## NOT LOGGED IN USER
array_push($followers, $uname);
#Add the searched user to the pov user's following list ## LOGGED IN USER
array_push($follows, $src_uname);
$followers = json_encode($followers);
$follows = json_encode($follows);
#Append new follow to the DB ## NOT LOGGED IN
$follow_q = "UPDATE `users` SET `followers` = '$followers' WHERE `uname` = '$src_uname'";
$connection->query($follow_q);
#Append nwe following to the DB ## LOGGED IN
$follow_q = "UPDATE `users` SET `follows` = '$follows' WHERE `uname` = '$uname'";
$connection->query($follow_q);
header("Location: ./profile.php")
?>