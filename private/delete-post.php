<?php 
#Session (start and vars)
session_start();
# Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#getting some vars
include("./vars.php");
if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
  #Deletion cookie
  $delete_item = mysqli_real_escape_string($connection,e($_COOKIE["ToBeDeleted"]));
  #Delete the post from the DB
  $delete_query = "DELETE FROM `posts` WHERE `name_id` = '$id' AND `title` = '$delete_item'";
  if ($connection->query($delete_query) === true){}else{$connection->error;};
  #Create new csrf token
  createCSRF();
  #Delete cookie 
  setcookie("ToBeDeleted", NULL, 0);
  mysqli_close($connection);
  header("Location: ../public/profile.php");
};
?>