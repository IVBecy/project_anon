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
  $q = "DELETE FROM `posts` WHERE `name_id` = '$id'";
  $connection->query($q);
}
#Delete user's name from the array of followers
foreach($followers as $u){
  #delete the follow name
  $q = "SELECT `follows` FROM `users` WHERE `id` = '$u'";
  $follower_follows = mysqli_query($connection,$q);
  $follower_follows = mysqli_fetch_row($follower_follows);
  $follower_follows = $follower_follows[0];
  $follower_follows = openssl_decrypt($follower_follows,"AES-128-CBC",$u);
  $follower_follows = json_decode($follower_follows,true);
  if (in_array($id,$follower_follows)){
    $pos = array_search($id,$follower_follows);
    unset($follower_follows[$pos]);
    $follower_follows = array_values($follower_follows);
    $follower_follows = json_encode($follower_follows);
    $follower_follows = openssl_encrypt($follower_follows,"AES-128-CBC",$u);
    $q = "UPDATE `users` SET `follows` = '$follower_follows' WHERE `id` = '$u'";
    $connection->query($q);
  }
  #delete the follower name
  $q = "SELECT `followers` FROM `users` WHERE `id` = '$u'";
  $follower_followers = mysqli_query($connection,$q);
  $follower_followers = mysqli_fetch_row($follower_followers);
  $follower_followers = $follower_followers[0];
  $follower_followers = openssl_decrypt($follower_followers,"AES-128-CBC",$u);
  $follower_followers = json_decode($follower_followers,true);
  if (in_array($id,$follower_followers)){
    $pos = array_search($id,$follower_followers);
    unset($follower_followers[$pos]);    
    $follower_followers = array_values($follower_followers);
    $follower_followers = json_encode($follower_followers);
    $follower_followers = openssl_encrypt($follower_followers,"AES-128-CBC",$u);
    $q = "UPDATE `users` SET `followers` = '$follower_followers' WHERE `id` = '$u'";
    $connection->query($q);
  };
}
#Delete user's name from the array of follows
foreach($follows as $u){
  #delete the follow name
  $q = "SELECT `followers` FROM `users` WHERE `id` = '$u'";
  $follow_followers = mysqli_query($connection,$q);
  $follow_followers = mysqli_fetch_row($follow_followers);
  $follow_followers = $follow_followers[0];
  $follow_followers = openssl_decrypt($follow_followers,"AES-128-CBC",$u);
  $follow_followers = json_decode($follow_followers,true);
  if (in_array($id,$follow_followers)){
    $pos = array_search($id,$follow_followers);
    unset($follow_followers[$pos]);
    $follow_followers = array_values($follow_followers);
    $follow_followers = json_encode($follow_followers);
    $follow_followers = openssl_encrypt($follow_followers,"AES-128-CBC",$u);
    $q = "UPDATE `users` SET `follows` = '$follow_followers' WHERE `name_id` = '$u'";
    $connection->query($q);
  }
};
#Delete the user's profile in the DB :(
$q = "DELETE FROM `users` WHERE `id` = '$id'";
$connection->query($q);
header("Location: ./logout.php");
?>