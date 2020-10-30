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
  <link rel="stylesheet" href="../root/css/design.css">
  <link rel="stylesheet" href="../root/css/style.css">
  <script type="text/jsx" src="../root/js/index.js"></script>
  <title>Project Anon - Sign Up</title>
</head>
<body>
  <div class="center-container">
    <h1>Project Anon</h1>
    <h5>A social media platform developed for sharing projects.</h5>
  </div>
<?php
# cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
# Signing the user up, and adding data to the DB
# Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
# All the database data we need to connect
$DB_SERVER = "127.0.0.1:3307";
$DB_USERNAME = "root";
$DB_PASSWORD = ""; 
$DB_NAME = "anon";
# Connecting to the server
$connection = mysqli_connect($DB_SERVER,$DB_USERNAME,$DB_PASSWORD,$DB_NAME);
# on connection error
if($connection == false){
  echo "<h2>Something went wrong</h2>";
}
# Variables from the form
$uname =  mysqli_real_escape_string($connection, e($_POST['uname']));
$email = mysqli_real_escape_string($connection, e($_POST['email']));
$pass = mysqli_real_escape_string($connection, e($_POST['pass']));
# Hashing the password (bcrypt)
$hashed_password = password_hash($pass,PASSWORD_BCRYPT);
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
# Projects
$projects = [];
$projects = serialize($projects);
#Appending data to the Database
$append_query = "INSERT INTO `users` (uname,email,pass,projects) VALUES ('$uname','$email','$hashed_password','$projects')";
if ($connection->query($append_query) === TRUE) {
  $signed_up = true;
  $message = "";
}
## Check for the same uname
else if($logged_uname == $uname){
  $signed_up = false;
  $message = "This username is taken, try another one.";
} 
## Check for the same email
else if($logged_email == $email){
  $signed_up = false;
  $message = "This email is already in use.";
}
# Any error
else {
  $signed_up = false;
  $message = "Error: $append_query : $connection->error";
}
mysqli_close($connection);
?>
<?php if($signed_up == false){?>
  <div class="center-container">
  </div>
  <div class="center-container" id="background">
    <form method="POST" action="./signup.php">
      <input type="text" name="uname" placeholder="Username" minlength="4" required><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="pass" placeholder="Password" minlength="8" required><br>
      <p class="bg-danger"><?php echo $message; ?></p>
      <input type="submit" value="Sign Up" id="signup">
    </form>
    <p>Already a member?<br><a href="../root/index.html">Sign in</a></p>
  </div>
<?php }else{?>
  <div class="center-container" id="background">
    <h2>You are signed up as <?php echo $uname;?></h2>
    <p>Click the button and log in</p>
    <a href="../root/index.html"><button>Go to the login page</button></a>
  </div>
<?php } ?>
</html>