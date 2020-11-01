<?php 
session_start();
$uname = $_SESSION["uname"];
# cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
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
# Getting the projects from the database
$p_query = "SELECT `projects` FROM `users` WHERE `uname` = '$uname'";
$projects = mysqli_query($connection,$p_query);
$projects = mysqli_fetch_row($projects);
$projects = $projects[0];
$projects = json_decode($projects,true);
# Checking if we can show projects
if (count($projects) == 0){
  $show_projects_state = False;
  $msg = "You don't have any projects..."; 
}
else{
  $show_projects_state = True;
}
?>
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
  <script type="text/jsx" src="../root/js/index.js"></script>
  <title>Project Anon - <?php echo $uname;?></title>
</head>
<body id="feed_bg">
  <div id="project-form-overlay"></div>
  <div id="menu-bar"></div>
  <div class="home-bar">
    <img src="/root/imgs/logo_anon.png" alt="logo">
    <div class="align-right">
      <h5 id="uname"><?php echo $uname;?></h5>
      <div id="menu"></div>
    </div>
  </div>
  <div class="center-container">
    <h1><?php echo $uname;?></h1>
    <h4>Number of projects: <?php echo count($projects)?></h4>
  </div>
  <hr>
  <!-- PROJECTS -->
  <?php if ($show_projects_state == True){
    foreach($projects as $title => $desc) {?>
    <div class="center-container">
      <div class="project" id=<?php echo $title?>>
        <i class="fas fa-ellipsis-h"></i>
        <h2><?php echo $title;?></h2>
        <p class="project-desc"><?php echo $desc;?></p>
      </div>
    </div>
  <?php }}else{?>
    <div class="center-container"><?php echo $msg?></div>
  <?php }?>
</body>
</html>