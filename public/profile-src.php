<?php 
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
#Username from URL query or form assig.
if ($_POST["src_name"]){
  $src_uname = mysqli_real_escape_string($connection,e($_POST["src_name"]));
  $src_uname = strtolower($src_uname);
}
else{
 $src_uname = mysqli_real_escape_string($connection,e($_GET["src_name"]));
 $src_uname = strtolower($src_uname);
}
$_SESSION["src_uname"] = $src_uname; 
#Redirect if the user searches themselves
if ($src_uname == $uname){
  header("Location: ./profile.php");
};
#Getting vars for the searched user
$src_query = "SELECT * FROM `users` WHERE `uname` = '$src_uname'";
$src_data = mysqli_query($connection,$src_query);
$src_data = mysqli_fetch_assoc($src_data);
$src_id  = $src_data["id"];
$_SESSION["src_id"] = $src_id;
$src_prof_img = $src_data["img"];
$src_followers = $src_data["followers"];
$src_followers = openssl_decrypt($src_followers,"AES-128-CBC",$src_id);
$src_followers = json_decode($src_followers,true);
$src_follows = $src_data["follows"];
$src_follows = openssl_decrypt($src_follows,"AES-128-CBC",$src_id);
$src_follows = json_decode($src_follows,true);
#see if the searched user is in the DB
$u_query = "SELECT `uname` FROM `users` WHERE `uname` = '$src_uname'";
$logged_src_name = mysqli_query($connection,$u_query);
$logged_src_name = mysqli_fetch_row($logged_src_name);
$logged_src_name = $logged_src_name[0];
#Getting projects for the user
$collection = [];
$p_query = "SELECT `title`,`report`,`prev_img` FROM `posts` WHERE `name_id` = '$src_id'";
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
  if ($src_uname != $logged_src_name){
    $usr = false;
    $msg = $src_uname." is not a user.";
    http_response_code(404);
    $err_msg = $src_uname." is not a user.";
    include("../errors/404.html");
    die();
  }
}
else{
  $usr = true;
  $show_projects_state = true;
  #Reverse the order of the array, so newest will be 1st
  $collection = array_reverse($collection);
}
#Checking for already following
if ($usr === true){
  if (in_array($id, $src_followers)){
    $btn_val = "Unfollow";
    $script = "../private/unfollow.php";
  }else{
    $btn_val = "Follow";
    $script = "../private/follow.php";
  }
}
# Showing profile picture
if ($src_prof_img == ""){
  $src_dir = '<img src="../assets/imgs/profile-img.png" alt="prof-img">';
  $prof_img_state = false;
}
else{
  $src_dir = '<img src="data:image/jpeg;base64,'.$src_prof_img.'"/>';
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
  <link rel="stylesheet" href="/../assets/css/design.css">
  <script type="text/jsx" src="/../assets/js/index.js"></script>
  <title>Project Anon - <?php echo $src_uname;?></title>
</head>
<script type="text/jsx">
  //follows and followers onclick
  const FollowersOverlay = () => {
    return(
      <div className="popup" id="f"> 
        <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
        <h2>Followers</h2>
        <hr/>
        <?php foreach($src_followers as $f){
          $q = "SELECT `uname` FROM `users` WHERE `id` = '$f'";  
          $f_name = mysqli_query($connection,$q);
          $f_name = mysqli_fetch_row($f_name);
          $f_name = $f_name[0];  
        ?>
          <div id="<?php echo $f_name?>"><a href="<?php echo "./$f_name"?>"><h4><?php echo $f_name?></h4></a></div>
        <?php }?>
      </div>
    )
  }
  const FollowsOverlay = () => {
    return(
      <div className="popup" id="f"> 
        <i className="fas fa-times" style={{ fontSize: "30px" }}></i>
        <h2>Follows</h2>
        <hr/>
        <?php foreach($src_follows as $f){
          $q = "SELECT `uname` FROM `users` WHERE `id` = '$f'";  
          $f_name = mysqli_query($connection,$q);
          $f_name = mysqli_fetch_row($f_name);
          $f_name = $f_name[0];    
        ?>
          <div id="<?php echo $f_name?>"><a href="<?php echo "./$f_name"?>"><h4><?php echo $f_name?></h4></a></div>
        <?php }?>
      </div>
    )
  }
  $(document).ready(() => {
    var followers_btn = document.getElementById("followers");
    var follows_btn = document.getElementById("follows");
    var overlay = document.getElementById("overlay");
    if (followers_btn){
      followers_btn.onclick = () => {
        overlay.style.display = "block";
        ReactDOM.render(<FollowersOverlay/>,overlay)
        setTimeout(() => {
          var x = document.getElementsByClassName("fas fa-times")[0];
          if (x && overlay.style.display == "block") {
            x.onclick = () => {
              overlay.style.display = "none";
            };
          };
        },100)
      }
    }
    if (follows_btn){
      follows_btn.onclick = () => {
        overlay.style.display = "block";
        ReactDOM.render(<FollowsOverlay/>,overlay)
        setTimeout(() => {
          var x = document.getElementsByClassName("fas fa-times")[0];
          if (x && overlay.style.display == "block") {
            x.onclick = () => {
              overlay.style.display = "none";
            };
          };
        },100)
      }
    }
  })
</script>
<body id="feed_bg">
  <div id="overlay"></div>
  <div id="menu-bar"></div>
 <div class="home-bar">
    <div class="align-right">
      <div class="profile-img" id="<?php echo $html_id?>">
        <?php echo $dir?>
      </div>
      <div id="menu"></div>
    </div>
  </div>
  <br>
  <!-- PROJECTS -->
  <?php if ($show_projects_state === true && $usr === true){?>
    <div class="center-container">
      <div class="profile-card">
        <?php echo $src_dir?>
        <h1><?php echo $src_uname;?></h1>
        <div class="user-info">
          <h4>Projects: <?php echo count($collection)?></h4>
          <h4 id="followers">Followers: <?php echo count($src_followers)?></h4>
          <h4 id="follows">Follows: <?php echo count($src_follows)?></h4>
        </div>
        <form action="<?php echo $script?>" method="POST">
          <input type="submit" value="<?php echo $btn_val?>" class="follow-btn">
        </form>
      </div>
    </div>
    <?php foreach($collection as $k){
      $likes_q = "SELECT `likes` FROM `posts` WHERE `name_id` = '$src_id' AND `title` = '$k[title]'";
      $likes = mysqli_query($connection,$likes_q);
      $likes = mysqli_fetch_row($likes);
      $likes = $likes[0];   
      $likes = openssl_decrypt($likes,"AES-128-CBC",$src_id);
      $likes = json_decode($likes,true);  
      if (in_array($id,$likes)){
        $star_txt = "Unstar";
      }else{
        $star_txt = "Star";
      }  
    ?>
    <div class="post">
      <div class="project" id="<?php echo $k["title"]?>">
        <h2 id="title"><?php echo $k["title"];?></h2>
        <p id="description" class="project-desc"><?php echo $k["report"];?></p>
        <?php if ($k["prev_img"]){echo'<img class="post-preview-img" src="data:image/jpeg;base64,'.$k["prev_img"].'"/>';}?>
      </div>
      <hr>
      <div class="post-actions">
        <?php if ($logged_in === true){
        ?>  
        <form action="../private/star.php" method="POST"><button class="actions" id="star"><i class="fas fa-star"></i><?php echo $star_txt," ","(".count($likes).")";?></button><input type="hidden" name="title" value="<?php echo $k["title"]?>" /></form>
        <?php } else{?>
        <a href="./index.html"><button class="actions" id="star"><i class="fas fa-star"></i>Star</button></a>
        <?php }?>
      </div>
    </div>
    <br>
    <br>
  <?php }}else{ if($usr === false){?>
    <div class="center-container">
      <h1><?php echo $msg?></h1>
    </div>
  <?php }else if($show_projects_state === false && $usr === true){?>
    <div class="center-container">
      <div class="profile-card">
        <?php echo $src_dir?>
        <h1><?php echo $src_uname;?></h1>
        <div class="user-info">
          <h4>Projects: <?php echo count($collection)?></h4>
          <h4 id="followers">Followers: <?php echo count($src_followers)?></h4>
          <h4 id="follows">Follows: <?php echo count($src_follows)?></h4>
        </div>
        <form action="<?php echo $script?>" method="POST">
          <input type="submit" value="<?php echo $btn_val?>" class="follow-btn">
        </form>
    </div>
    <div class="center-container"><?php echo $msg?></div>
  <?php } else{?>
    <div class="center-container"><?php echo $msg?></div>
  <?php }}?>
</body>
</html>