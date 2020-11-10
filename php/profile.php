<?php 
#Session (start and vars)
session_start();
$uname = $_SESSION["uname"];
#cross site scripting prevention
function e($str){
  return(htmlspecialchars($str, ENT_QUOTES, "UTF-8"));
}
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
#Getting projects for the user
$collection = [];
$p_query = "SELECT `title`,`report` FROM `posts` WHERE `uname` = '$uname'";
$projects = mysqli_query($connection,$p_query);
#ID
$id_q = "SELECT `id` FROM `users` WHERE `uname` = '$uname'";
$id = mysqli_query($connection,$id_q);
$id = mysqli_fetch_row($id);
$id = $id[0];
#Following system
$followers_query = "SELECT `followers` FROM `users` WHERE `uname` = '$uname'";
$followers = mysqli_query($connection,$followers_query);
$followers = mysqli_fetch_row($followers);
$followers = $followers[0];
$follows_query = "SELECT `follows` FROM `users` WHERE `uname` = '$uname'";
$follows = mysqli_query($connection,$follows_query);
$follows = mysqli_fetch_row($follows);
$follows = $follows[0];
$followers = openssl_decrypt($followers,"AES-128-CBC",$id);
$follows = openssl_decrypt($follows,"AES-128-CBC",$id);
$followers = json_decode($followers,true);
$follows = json_decode($follows,true);
#Appending all the projects to one array
while ($row = mysqli_fetch_assoc($projects)) {
    array_push($collection,$row);
};
#Checking if we can show projects
if (count($collection) == 0){
  $show_projects_state = False;
  $msg = "You don't have any projects..."; 
}
else{
  $show_projects_state = True;
  #Reverse the order of the array, so newest will be 1st
  $collection = array_reverse($collection);
}
#getting profile image if set
$prof_query = "SELECT `img` FROM `users` WHERE `uname` = '$uname'";
$prof_img = mysqli_query($connection,$prof_query);
$prof_img = mysqli_fetch_row($prof_img);
$prof_img = $prof_img[0];
if ($prof_img == ""){
  $dir = '<img src="../root/imgs/profile-img.png" alt="prof-img">';
  $prof_img_state = false;
}
else{
  $dir = '<img src="data:image/jpeg;base64,'.$prof_img.'"/>';
  $prof_img_state = true;
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
    <div class="align-right">
      <h4 id="uname"><?php echo $uname;?></h4>
      <div id="menu"></div>
    </div>
  </div>
  <br>
  <div class="center-container">
    <div class="profile-card">
      <?php echo $dir?>
      <h1><?php echo $uname;?></h1>
      <h5>Projects: <?php echo count($collection)?></h5>
      <h5>Followers: <?php echo count($followers)?></h5>
      <h5>Follows: <?php echo count($follows)?></h5>
    </div>
  </div>
  <!-- PROJECTS -->
  <?php if ($show_projects_state == True){
    foreach($collection as $k) {?>
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
  <?php }}else{?>
    <div class="center-container"><?php echo $msg?></div>
  <?php }?>
</body>
</html>