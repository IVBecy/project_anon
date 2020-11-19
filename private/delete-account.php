<?php 
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
#Delete posts
$collection = [];
while ($row = mysqli_fetch_assoc($projects)) {
    array_push($collection,$row);
};
if (count($collection) > 0){
  $q = "DELETE FROM `posts` WHERE `uname` = '$uname'";
  $connection->query($q);
}
#Delete user's name from the array of followers
foreach($followers as $u){
  #get id of follower 
  $q = "SELECT `id` FROM `users` WHERE `uname` = '$u'";
  $follower_id = mysqli_query($connection,$q);
  $follower_id = mysqli_fetch_row($follower_id);
  $follower_id = $follower_id[0];
  #delete the follow name
  $q = "SELECT `follows` FROM `users` WHERE `uname` = '$u'";
  $follower_follows = mysqli_query($connection,$q);
  $follower_follows = mysqli_fetch_row($follower_follows);
  $follower_follows = $follower_follows[0];
  $follower_follows = openssl_decrypt($follower_follows,"AES-128-CBC",$follower_id);
  $follower_follows = json_decode($follower_follows,true);
  if (in_array($uname,$follower_follows)){
    $pos = array_search($uname,$follower_follows);
    unset($follower_follows[$pos]);
    $follower_follows = array_values($follower_follows);
    $follower_follows = json_encode($follower_follows);
    $follower_follows = openssl_encrypt($follower_follows,"AES-128-CBC",$follower_id);
    $q = "UPDATE `users` SET `follows` = '$follower_follows' WHERE `uname` = '$u'";
    $connection->query($q);
  }
  #delete the follower name
  $q = "SELECT `followers` FROM `users` WHERE `uname` = '$u'";
  $follower_followers = mysqli_query($connection,$q);
  $follower_followers = mysqli_fetch_row($follower_followers);
  $follower_followers = $follower_followers[0];
  $follower_followers = openssl_decrypt($follower_followers,"AES-128-CBC",$follower_id);
  $follower_followers = json_decode($follower_followers,true);
  if (in_array($uname,$follower_followers)){
    $pos = array_search($uname,$follower_followers);
    unset($follower_followers[$pos]);
    $follower_followers = array_values($follower_followers);
    $follower_followers = json_encode($follower_followers);
    $follower_followers = openssl_encrypt($follower_followers,"AES-128-CBC",$follower_id);
    $q = "UPDATE `users` SET `followers` = '$follower_followers' WHERE `uname` = '$u'";
    $connection->query($q);
  };
}
#Delete user's name from the array of follows
foreach($follows as $u){
  #get id of follow 
  $q = "SELECT `id` FROM `users` WHERE `uname` = '$u'";
  $follow_id = mysqli_query($connection,$q);
  $follow_id = mysqli_fetch_row($follow_id);
  $follow_id = $follow_id[0];
  #delete the follow name
  $q = "SELECT `followers` FROM `users` WHERE `uname` = '$u'";
  $follow_followers = mysqli_query($connection,$q);
  $follow_followers = mysqli_fetch_row($follow_followers);
  $follow_followers = $follow_followers[0];
  $follow_followers = openssl_decrypt($follow_followers,"AES-128-CBC",$follow_id);
  $follow_followers = json_decode($follow_followers,true);
  if (in_array($uname,$follow_followers)){
    $pos = array_search($uname,$follow_followers);
    unset($follow_followers[$pos]);
    $follow_followers = array_values($follow_followers);
    $follow_followers = json_encode($follow_followers);
    $follow_followers = openssl_encrypt($follow_followers,"AES-128-CBC",$follow_id);
    $q = "UPDATE `users` SET `follows` = '$follow_followers' WHERE `uname` = '$u'";
    $connection->query($q);
  }
};
#Delete the user's profile in the DB :(
$q = "DELETE FROM `users` WHERE `uname` = '$uname'";
$connection->query($q);
header("Location: ./logout.php");
?>