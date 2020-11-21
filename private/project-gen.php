<?php
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#getting some vars
include("./vars.php");
# Getting data from the form
$title =  mysqli_real_escape_string($connection, e($_POST['title']));
$desc =  mysqli_real_escape_string($connection, e($_POST['desc']));
echo $_FILES["preview-img"];
if (!empty($_FILES["preview-img"])) {		
  if (empty($_FILES["preview-img"]["name"])){}
  else{ 
    $allowed = ["image/png", "image/jpg", "image/jpeg"];
    if (!in_array($_FILES["preview-img"]["type"], $allowed)){
      $up_img = false;
    }
    else{
      $image = $_FILES["preview-img"]["tmp_name"];; 
      $image = base64_encode(file_get_contents(addslashes($image)));
      $up_img = true; 
    }
  }
}
$likes = [];
$likes = json_encode($likes);
$likes = openssl_encrypt($likes,"AES-128-CBC",$id);
#Check if title exist for user
$q = "SELECT `title` FROM `posts` WHERE `name_id` = '$id' AND `title` = '$title'";
$e_t = mysqli_query($connection,$q);
$e_t = mysqli_fetch_row($e_t);
$e_t = $e_t[0];
if ($e_t == $title || $title == ""){
  $append = false;
}else{
  $append = true;
}
#Insert data
if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"]) && $append === true){
  #Appending data to the Database
  $t = time();
  $append_query = "INSERT INTO `posts` (name_id,title,report,time,likes) VALUES ('$id','$title','$desc','$t','$likes')";
  $connection->query($append_query);
  # Append image
  if ($up_img === true){
    $q = "UPDATE `posts` SET `prev_img` = '$image' WHERE `name_id` = '$id' AND `title` = '$title'"; 
    if ($connection->query($q) === true){
      echo "Image is up";
    }else{
      echo $connection->error;
    }
  }
  #Create new csrf token
  createCSRF();
  mysqli_close($connection);
  //header("Location: ../public/profile.php");
}
?>