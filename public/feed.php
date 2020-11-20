<?php
#Start session and set uname as a var
session_start();
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
# If not logged in, redirect to login page
if ($logged_in === false){
  http_response_code(404);
  header("Location: ./index.html");
  die();
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
  <link rel="stylesheet" href="../assets/css/design.css">
  <script type="text/jsx" src="../assets/js/index.js"></script>
  <title>Project Anon - Your feed</title>
</head>
<body id="feed_bg">
  <div id="overlay"></div>
  <div class="home-bar">
    <div class="align-right">
      <div class="profile-img" id="dropdown-img">
        <?php echo $dir?>
      </div>
      <div id="menu"></div>
    </div>
  </div>
  <br>
  <?php    
  #time
  $t = time();
  #collect
  $collection = [];
  #Get posts
  if ($follows){
    foreach($follows as $p){
      $post_query = "SELECT `name_id`,`title`,`report`,`time` FROM `posts` WHERE `name_id` = '$p' AND `time` <= '$t'";
      $post = mysqli_query($connection,$post_query);
      while ($row = mysqli_fetch_assoc($post)) {
          array_push($collection,$row);
      }
    }
    #Sort the array by time, so newest will appear on the top
    $time = array_column($collection, "time");
    array_multisort($time, SORT_DESC, $collection);
    #Restrict array to show max 300 posts
    array_slice($collection, 0, 300);
  }
  ?>
  <!--show posts-->
  <?php foreach($collection as $k){ 
      $q = "SELECT `uname` FROM `users` WHERE `id` = '$k[name_id]'";  
      $f_name = mysqli_query($connection,$q);
      $f_name = mysqli_fetch_row($f_name);
      $f_name = $f_name[0];    
    ?>
    <div class="center-container">
      <div class="post">
        <h6 class="posted-by">Posted by <a style="color:black" href="<?php echo "./".$f_name?>"><?php echo $f_name?></a></h6>
        <div class="project" id="<?php echo $k["title"]?>">
          <h2 id="title"><?php echo $k["title"];?></h2>
          <p id="description" class="project-desc"><?php echo $k["report"];?></p>
        </div>
        <div class="post-actions">
          <div class="actions" id="star"><i class="fas fa-star"></i>Star</div>
          <div class="actions" id="comment"><i class="fas fa-comment-alt"></i>Comment</div>
        </div>
      </div>
    </div>
  <?php }?>
</body>
</html>