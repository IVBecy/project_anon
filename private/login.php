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
  <title>Project Anon - Login</title>
</head>
<body>
  <div class="center-container">
    <h1>Project Anon</h1>
    <h5>A social media platform developed for sharing projects.</h5>
  </div>
<?php
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#Getting some vars
include("./vars.php");
# Variables from the form
$uname =  mysqli_real_escape_string($connection, e($_POST['uname']));
$pass = mysqli_real_escape_string($connection, e($_POST['pass']));
# Getting the username from the database
$query = "SELECT `uname` FROM `users` WHERE `uname` = '$uname'";
$logged_uname = mysqli_query($connection,$query);
$logged_uname = mysqli_fetch_row($logged_uname);
$logged_uname = $logged_uname[0];
# Getting the password from the database
$pass_query = "SELECT `pass` FROM `users` WHERE `uname` = '$uname'";
$logged_pass = mysqli_query($connection,$pass_query);
$logged_pass = mysqli_fetch_row($logged_pass);
$logged_pass = $logged_pass[0];
#Create new csrf token
createCSRF();
#Checking for the right data
if ($uname == $logged_uname) {
  if (password_verify($pass, $logged_pass)) {
    session_regenerate_id();
    $_SESSION["uname"] = $uname;
    header( "Location: ../public/feed.php" );
  }
  else{
    $logged_in = false;
    $message = "Wrong password, try again.";
  }
}
else{
  $logged_in = false;
  $message = "Wrong username.";
}
mysqli_close($connection);
?>
<?php if($logged_in == false){?>
  <div class="center-container">
  </div>
  <div class="center-container" id="background">
    <form method="POST" action="./login.php">
      <input type="text" name="uname" placeholder="Username" required><br>
      <input type="password" name="pass" placeholder="Password" required><br>
      <p class="bg-danger"><?php echo $message; ?></p>
      <input type="submit" value="Log in" id="signup">
    </form>
    <p>Not a member yet?<br><a href="../public/signup.html">Sign up</a></p>
  </div>
<?php }?>
</html>