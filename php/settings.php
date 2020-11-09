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
#ID
$id_name = "SELECT `id` FROM `users` WHERE `uname` = '$uname'";
$name_id = mysqli_query($connection,$id_name);
$name_id = mysqli_fetch_row($name_id);
$name_id = $name_id[0];
#getting email
$email_query = "SELECT `email` FROM `users` WHERE `uname` = '$uname'";
$email = mysqli_query($connection,$email_query);
$email = mysqli_fetch_row($email);
$email = $email[0];
#getting profile image if set
$prof_query = "SELECT `profile-img` FROM `users` WHERE `uname` = '$uname'";
$prof_img = mysqli_query($connection,$prof_query);
$prof_img = mysqli_fetch_row($prof_img);
$prof_img = $prof_img[0];
if ($prof_img == ""){
  $prof_img_state = false;
}
else{
  $prof_img_state = true;
}
#Array of edited stuff
$edits = [];
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
  <title>Project Anon - Settings</title>
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
    <h1>Settings menu</h1>
  </div>
  <div class="center-container" >
    <div class="settings-menu">
      <div class="settings-imgs">
        <?php if($prof_img_state == true){
        echo "<img src='$prof_img'>";
        }
        else{
          echo "<img src='../root/imgs/profile-img.png'><br><p>(Default)</p>";
        }      
        ?>
      </div>
      <span>Profile picture:</span>
      <form method="POST" action="./settings.php">
        <input type="file" name="profile-img" accept=".png,.jpg,.jpeg" enctype="multipart/form-data"><br>
        <p>This picture will appear whenever someone looks up your profile.</p>
        <span>Username:</span>
        <input type="text" name="uname" value="<?php echo $uname?>"><br>
        <p>Your name appears on your profile and on any post, that you have shared previously.</p>
        <span>Email:</span>
        <input type="email" name="email" value="<?php echo $email?>"><br>
        <p>Your email is used for notifying you, of any changes regarding the platform or your account.</p>
        <?php 
        function update(){
          global $connection;
          $uname = $_SESSION["uname"];
          #Checking what has been changed
          if (isset($_POST["uname"])){
            $new_uname = mysqli_real_escape_string($connection,e($_POST["uname"]));
            #new uname for post
            $q = "SELECT `uname` FROM `posts` WHERE `uname` = '$uname'";
            $posts_name = mysqli_query($connection,$q);
            $posts_name = mysqli_fetch_row($posts_name);
            $posts_name = $posts_name[0];
            $q = "UPDATE `posts` SET `uname` = '$new_uname' WHERE `uname` = '$uname'";
            #$connection->query($q);
            #follows of the user
            $q = "SELECT `follows` FROM `users` WHERE `uname` = '$uname'";
            $user_follows = mysqli_query($connection,$q);
            $user_follows = mysqli_fetch_row($user_follows);
            $user_follows = $user_follows[0];
            $user_follows = openssl_decrypt($user_follows,"AES-128-CBC",$name_id);
            $user_follows = json_decode($user_follows,true);
            #new uname for all the followers
            $q = "SELECT `followers` FROM `users` WHERE `uname` = '$uname'";
            $followers = mysqli_query($connection,$q);
            $followers = mysqli_fetch_row($followers);
            $followers = $followers[0];
            $followers = openssl_decrypt($followers,"AES-128-CBC",$name_id);
            echo $followers;
            $followers = json_decode($followers,true);
            foreach($followers as $u){
              #get id of follower 
              $q = "SELECT `id` FROM `users` WHERE `uname` = '$u'";
              $id = mysqli_query($connection,$q);
              $id = mysqli_fetch_row($id);
              $id = $id[0];
              #replace the follow name
              $q = "SELECT `follows` FROM `users` WHERE `uname` = '$u'";
              $follows = mysqli_query($connection,$q);
              $follows = mysqli_fetch_row($follows);
              $follows = $follows[0];
              $follows = openssl_decrypt($follows,"AES-128-CBC",$id);
              $follows = json_decode($follows,true);
              $pos = array_search($uname,$follows);
              $follows = array_replace($follows, [$pos => $new_uname]);
              $follows = json_encode($follows);
              echo $follows;
              $follows = openssl_encrypt($follows,"AES-128-CBC",$id);
              $q = "UPDATE `users` SET `follows` = '$follows' WHERE `uname` = '$u'";
              #$connection->query($q);
            };
            #new uname
            $q = "UPDATE `users` SET `uname` = '$new_uname' WHERE `uname` = '$uname'";
            #$connection->query($q);
            $_SESSION["uname"] = $new_uname;
          };
          if ($_POST["email"] != $email){
            $new_email = mysqli_real_escape_string($connection,e($_POST["email"]));
            $q = "UPDATE `users` SET `email` = '$new_email' WHERE `uname` = '$uname'";
            $connection->query($q);
          };
          if (isset($_FILES["profile-img"])) {
            $image = addslashes(file_get_contents($_FILES["profile-img"]["tmp_name"]));
            $q = "INSERT INTO `users` (img) VALUES ('$image')"; 
          }
        }
        if(isset($_POST['submit'])){
          update();
          echo "<p class='bg-success' style='width:fit-content'>Profile has been updated successfully</p>";
          mysqli_close($connection);
        }
        ?>
        <input type="submit" value="Update" name="submit" style="background-color:#09D202;">
      </form>
    </div>  
  </div>
</body>
</html>