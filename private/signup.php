<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--  Jquery link(s)  -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!--  React.js libraries  -->
  <script src="https://unpkg.com/react@16/umd/react.development.js" crossorigin></script>
  <script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js" crossorigin></script>
  <script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
  <!-- Font awesome kit  -->
  <script src="https://kit.fontawesome.com/b82b391bad.js" crossorigin="anonymous"></script>
  <!--  Bootstrap(s)  -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <!-- My scripts -->
  <link rel="stylesheet" href="../assets/css/design.css">
  <script type="text/jsx" src="../assets/js/index.js"></script>
  <title>Project Anon - Sign Up</title>
</head>
<body id="feed_bg">
  <div class="center-container">
    <h1>Project Anon</h1>
    <h5>A social media platform developed for sharing projects.</h5>
  </div>
<?php
#Turn off all notices
error_reporting(0);
#Adding the script that connects to the DB
include("./connect.php");
#get some vars
include("./vars.php");
# Variables from the form
$uname =  mysqli_real_escape_string($connection, e($_POST['uname']));
$uname = strtolower($uname);
$email = mysqli_real_escape_string($connection, e($_POST['email']));
$pass = mysqli_real_escape_string($connection, e($_POST['pass']));
# Getting the username from the database (IF IT IS PRESENT)
$query = "SELECT `uname` FROM `users` WHERE `uname` = '$uname'";
$logged_uname = mysqli_query($connection,$query);
$logged_uname = mysqli_fetch_row($logged_uname);
$logged_uname = $logged_uname[0];
# Getting the email from the database (IF IT IS PRESENT)
$email_query = "SELECT `email` FROM `users` WHERE `email` = '$email'";
$logged_email = mysqli_query($connection,$email_query);
$logged_email = mysqli_fetch_row($logged_email);
$logged_email = $logged_email[0];
# Hashing the password
$hashed_password = password_hash($pass,PASSWORD_BCRYPT);
#id 
$id = mt_rand();
$q = "SELECT `id` FROM `users` WHERE `id` = '$id'";
$logged_id = mysqli_query($connection,$q);
$logged_id = mysqli_fetch_row($logged_id);
$logged_id = $logged_id[0];
while ($logged_id == $id){
  $id = mt_rand();
  if ($logged_id != $id){
    break;
  }
}
#followers and follows array
$followers = [];
$follows = [];
$followers = json_encode($followers);
$follows = json_encode($follows);
$followers = openssl_encrypt($followers,"AES-128-CBC",$id);
$follows = openssl_encrypt($follows,"AES-128-CBC",$id);
# Check for the same email
if($logged_email == $email){
  $signed_up = false;
  $message = "This email is already in use.";
}
#Check for the same username
else if($logged_uname == $uname){
  $signed_up = false;
  $message = "This username is taken, try another one.";
}
else{
  $signed_up = true;
};
#Appending data to the DB, if email and username are not found in the DB
$append_query = "INSERT INTO `users` (id,uname,email,pass,followers,follows) VALUES ('$id','$uname','$email','$hashed_password','$followers','$follows')";
if ($signed_up == true) {
  $connection->query($append_query);
}
#Any error
else {
  if ($message){}
  else{
    $message = "Something went wrong, try again later.";
  };
}
mysqli_close($connection);
?>
<?php if($signed_up == false){?>
  <div class="center-container" id="background">
    <form method="POST" action="./signup.php">
      <input type="text" name="uname" placeholder="Username" minlength="4" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="pass" placeholder="Password" minlength="8" required><br>
      <p class="bg-danger"><?php echo $message; ?></p>
      <input type="submit" value="Sign Up" id="signup">
    </form>
    <p>Already a member?<br><a href="../public/index.html">Sign in</a></p>
  </div>
<?php }else{
  session_start();
  createCSRF();
  $_SESSION["uname"] = $uname;
  header("Location: ../public/profile.php");
}?>
</html>