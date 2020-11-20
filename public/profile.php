<?php 
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
$_SESSION["src_uname"] = $uname;
# If not logged in, redirect to login page
if ($logged_in === false){
  http_response_code(404);
  header("Location: ./index.html");
  die();
}
#Getting projects for the user
$collection = [];
$p_query = "SELECT `title`,`report` FROM `posts` WHERE `name_id` = '$id'";
$projects = mysqli_query($connection,$p_query);
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
  <title>Project Anon - <?php echo $uname;?></title>
</head>
<script type="text/jsx">
  //follows and followers onclick
  const FollowersOverlay = () => {
    return(
      <div className="popup"> 
        <i className="fas fa-times-circle" style={{ fontSize: "30px" }}></i>
        <div className="user-social">
          <div className="center-container">
            <h2>Followers</h2>
            <div className="left-container">
              <?php foreach($followers as $f){
                $q = "SELECT `uname` FROM `users` WHERE `id` = '$f'";  
                $f_name = mysqli_query($connection,$q);
                $f_name = mysqli_fetch_row($f_name);
                $f_name = $f_name[0];
              ?>
                <div id="<?php echo $f_name?>"><span><?php echo $f_name?></span></div>
              <?php }?>
            </div>
          </div>
        </div>
      </div>
    )
  }
  const FollowsOverlay = () => {
    return(
      <div className="popup"> 
        <i className="fas fa-times-circle" style={{ fontSize: "30px" }}></i>
        <div className="user-social">
          <div className="center-container">
            <h2>Follows</h2>
            <div className="left-container">
              <?php foreach($follows as $f){
                $q = "SELECT `uname` FROM `users` WHERE `id` = '$f'";  
                $f_name = mysqli_query($connection,$q);
                $f_name = mysqli_fetch_row($f_name);
                $f_name = $f_name[0];
              ?>
                <div id="<?php echo $f_name?>"><span><?php echo $f_name?></span></div>
              <?php }?>
            </div>
          </div>
        </div>
      </div>
    )
  }
  const CommentOverlay = () => {
    return(
      <div className="popup"> 
        <i className="fas fa-times-circle" style={{ fontSize: "30px" }}></i>
          <div className="center-container">
            <h2>Comments</h2>
            <div className="left-container">
              <?php foreach($collection as $k){
                $comments_q = "SELECT `comments` FROM `posts` WHERE `name_id` = '$id' AND `title` = '$k[title]'";
                $comments = mysqli_query($connection,$comments_q);
                $comments = mysqli_fetch_row($comments);
                $comments = $comments[0];   
                $comments = openssl_decrypt($comments,"AES-128-CBC",$id);
                $comments = json_decode($comments,true);  
                if (count($comments) == 0){
                  echo "<span>There are no comments for this post.</span>";
                }else{
                  foreach($comments as $n => $c){
                    $q = "SELECT `uname` FROM `users` WHERE `id` = '$n'";  
                    $f_name = mysqli_query($connection,$q);
                    $f_name = mysqli_fetch_row($f_name);
                    $f_name = $f_name[0];
                    echo "
                      <h4><a href='./$f_name' style={{color:'black'}}>$f_name</a></h4>
                      <span>$c</span>
                    ";
                  }
                }
              ?>
              <form action="../private/comment.php" method="POST"><input type="text" placeholder="Comment" name="msg"/><input type="submit" value="Post comment"></input><input type="hidden" name="title" value="<?php echo $k["title"]?>"/></form>
              <?php }?> 
            </div>
          </div>
      </div>
    )
  }
  $(document).ready(() => {
    var followers_btn = document.getElementById("followers");
    var follows_btn = document.getElementById("follows");
    var overlay = document.getElementById("overlay");
    var commentBtn = document.getElementById("comment");
    if (followers_btn){
      followers_btn.onclick = () => {
        overlay.style.display = "block";
        ReactDOM.render(<FollowersOverlay/>,overlay)
        setTimeout(() => {
          var x = document.getElementsByClassName("fas fa-times-circle")[0];
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
          var x = document.getElementsByClassName("fas fa-times-circle")[0];
          if (x && overlay.style.display == "block") {
            x.onclick = () => {
              overlay.style.display = "none";
            };
          };
        },100)
      }
    }
    if (commentBtn){
       commentBtn.onclick = () => {
        overlay.style.display = "block";
        ReactDOM.render(<CommentOverlay/>,overlay)
        setTimeout(() => {
          var x = document.getElementsByClassName("fas fa-times-circle")[0];
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
      <div class="profile-img" id="dropdown-img">
        <?php echo $dir?>
      </div>
      <div id="menu"></div>
    </div>
  </div>
  <br>
  <div class="center-container">
    <div class="profile-card">
      <?php echo $dir?>
      <h1><?php echo $uname;?></h1>
      <h5>Projects: <?php echo count($collection)?></h5>
      <h5 id="followers">Followers: <?php echo count($followers)?></h5>
      <h5 id="follows">Follows: <?php echo count($follows)?></h5>
    </div>
  </div>
  <!-- PROJECTS -->
  <?php if ($show_projects_state == True){
    foreach($collection as $k) {
      $likes_q = "SELECT `likes` FROM `posts` WHERE `name_id` = '$id' AND `title` = '$k[title]'";
      $likes = mysqli_query($connection,$likes_q);
      $likes = mysqli_fetch_row($likes);
      $likes = $likes[0];   
      $likes = openssl_decrypt($likes,"AES-128-CBC",$id);
      $likes = json_decode($likes,true);     
    ?>
    <div class="center-container">
      <div class="post">
        <div class="project" id="<?php echo $k["title"]?>">
          <i class="fas fa-ellipsis-h"></i>
          <h2 id="title"><?php echo $k["title"];?></h2>
          <p id="description" class="project-desc"><?php echo $k["report"];?></p>
        </div>
        <div class="post-actions">
          <div class="actions" id="star"><i class="fas fa-star"></i>Star <?php echo "(".count($likes).")"?></div>
          <div class="actions" id="comment"><i class="fas fa-comment-alt"></i>Comment <?php echo "(".count($comments).")"?></div>
        </div>
      </div>
    </div>
  <?php }}else{?>
    <div class="center-container"><?php echo $msg?></div>
  <?php }?>
</body>
</html>