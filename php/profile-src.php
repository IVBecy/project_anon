<?php 
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
#Username from URL query
$src_uname = e($_POST["src_name"]);
$_SESSION["src_uname"] = $src_uname;
#Redirect if the user searches themselves
if ($src_uname == $uname){
  header("Location: ./profile.php");
};
#cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#User query
$u_query = "SELECT `uname` FROM `users` WHERE `uname` = '$src_uname'";
$logged_name = mysqli_query($connection,$u_query);
$logged_name = mysqli_fetch_row($logged_name);
$logged_name = $logged_name[0];
##Getting projects for the user
$collection = [];
$p_query = "SELECT `title`,`report` FROM `posts` WHERE `uname` = '$src_uname'";
$projects = mysqli_query($connection,$p_query);
#Appending all the projects to one array
while ($row = mysqli_fetch_assoc($projects)) {
    array_push($collection,$row);
};
#Checking if we can show projects
if (count($collection) == 0){
  $show_projects_state = False;
  $usr = true;
  $msg = $src_uname." doesn't have any projects..."; 
  if ($src_uname != $logged_name){
    $usr = false;
    $msg = $src_uname." is not a user.";
  }
}
else{
  $usr = true;
  $show_projects_state = true;
  #Reverse the order of the array, so newest will be 1st
  $collection = array_reverse($collection);
}
#Following system
$followers_query = "SELECT `followers` FROM `users` WHERE `uname` = '$src_uname'";
$followers = mysqli_query($connection,$followers_query);
$followers = mysqli_fetch_row($followers);
$followers = $followers[0];
$follows_query = "SELECT `follows` FROM `users` WHERE `uname` = '$src_uname'";
$follows = mysqli_query($connection,$follows_query);
$follows = mysqli_fetch_row($follows);
$follows = $follows[0];
$followers = openssl_decrypt($followers,"AES-128-CBC",$src_uname);
$follows = openssl_decrypt($follows,"AES-128-CBC",$src_uname);
$followers = json_decode($followers,true);
$follows = json_decode($follows,true);
#Checking for already following
if ($usr === true){
  if (array_search($uname,$followers) !== false){
    $btn_val = "Unfollow";
    $script = "./unfollow.php";
  }else{
    $btn_val = "Follow";
    $script = "./follow.php";
  }
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
  <title>Project Anon - <?php echo $src_uname;?></title>
</head>
<body id="feed_bg">
  <div id="project-form-overlay"></div>
  <div id="menu-bar"></div>
 <div class="home-bar">
    <div class="align-right">
      <h4 id="uname"><?php echo $uname;?></h4>
      <div id="menu"></div>
    </div>
  </div>
  <br>
  <!-- PROJECTS -->
  <?php if ($show_projects_state === true && $usr === true){?>
    <div class="center-container">
      <h1><?php echo $src_uname;?></h1>
      <h4>Projects: <?php echo count($collection)?></h4>
      <h4>Followers: <?php echo count($followers)?></h4>
      <h4>Follows: <?php echo count($follows)?></h4>
      <form action="<?php echo $script?>" method="POST">
        <input type="submit" value="<?php echo $btn_val?>" class="follow-btn">
      </form>
    </div>
    <hr>
    <?php foreach($collection as $k){?>
    <div class="center-container">
      <div class="post">
        <div class="project" id="<?php echo $k["title"]?>">
          <i class="fas fa-ellipsis-h"></i>
          <h2 id="title"><?php echo $k["title"];?></h2>
          <p id="description" class="project-desc"><?php echo $k["report"];?></p>
        </div>
        <div class="post-actions">
          <div class="actions" id="star"><i class="fas fa-star"></i>Star</div>
          <div class="actions" id="comment"><i class="fas fa-comment-alt"></i>Comment</div>
        </div>
      </div>
    </div>
  <?php }}else{ if($usr === false){?>
    <div class="center-container">
      <h1><?php echo $msg?></h1>
    </div>
  <?php }else if($show_projects_state === false && $usr === true){?>
    <div class="center-container">
      <h1><?php echo $src_uname;?></h1>
      <h4>Projects: <?php echo count($collection)?></h4>
      <h4>Followers: <?php echo count($followers)?></h4>
      <h4>Follows: <?php echo count($follows)?></h4>
      <form action="<?php echo $script?>" method="POST">
        <input type="submit" value="<?php echo $btn_val?>" class="follow-btn">
      </form>
    </div>
    <hr>
    <div class="center-container"><?php echo $msg?></div>
  <?php } else{?>
    <div class="center-container"><?php echo $msg?></div>
  <?php }}?>
</body>
</html>