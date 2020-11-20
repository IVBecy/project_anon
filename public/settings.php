<?php 
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
ob_start();
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
  <title>Project Anon - Settings</title>
</head>
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
  <div class="center-container" >
    <div class="settings-menu">
      <form method="POST" action="./settings.php" enctype="multipart/form-data">
        <h2>Account information</h2>
        <hr>
        <h4>Edit profile picture</h4>
        <input type="file" name="profile-img" accept=".png,.jpg,.jpeg"><br>
        <p>This picture will appear whenever someone looks up your profile.</p>
        <hr>
        <h4>Change username:</h4>
        <input type="text" name="uname" value="<?php echo $uname?>"><br>
        <p>Your name appears on your profile and on any post, that you have shared previously.</p>
        <hr>
        <h4>Change email</h4>
        <input type="email" name="email" placeholder="New email address"><br>
        <p>Your email is used for notifying you, of any changes regarding the platform or your account.</p>
        <hr>
        <h4 style="color:red">Delete your account</h4>
        <p style="margin:0">If you delete your account, there is no turning back.</p>
        <button type="button" id="delete-acc-btn" style="background-color:red">Delete account</button>
        <hr>
        <input type="hidden" name="csrftoken" value="<?php echo $_COOKIE["CSRF-Token"]?>"/>
        <?php 
        function update(){
          $msg = "";
          global $connection, $id, $followers;
          $uname = $_SESSION["uname"];
          # USERNAME
          if (isset($_POST["uname"])){
            $new_uname = mysqli_real_escape_string($connection,e($_POST["uname"]));
            #check for matching usernames in the BD
            $q = "SELECT `uname` FROM `users` WHERE `uname` = '$new_uname'";
            $db_name = mysqli_query($connection,$q);
            $db_name = mysqli_fetch_row($db_name);
            $db_name = $db_name[0];
            if ($db_name == $new_uname){
              if($new_uname == $uname){} 
              else{
                $msg .= "<p class='bg-danger' style='width:fit-content'>This username is taken, try something else</p>";
              }
            }
            else{
              #new uname for post
              $q = "SELECT `uname` FROM `posts` WHERE `uname` = '$uname'";
              $posts_name = mysqli_query($connection,$q);
              $posts_name = mysqli_fetch_row($posts_name);
              $posts_name = $posts_name[0];
              $q = "UPDATE `posts` SET `uname` = '$new_uname' WHERE `uname` = '$uname'";
              if ($connection->query($q) === true){}
              else{
                echo "<p class='bg-danger' style='width:fit-content'>Error: $connection->error</p>";
              }
              foreach($followers as $u){
                #get id of follower 
                $q = "SELECT `id` FROM `users` WHERE `uname` = '$u'";
                $follower_id = mysqli_query($connection,$q);
                $follower_id = mysqli_fetch_row($follower_id);
                $follower_id = $follower_id[0];
                #replace the follow name
                $q = "SELECT `follows` FROM `users` WHERE `uname` = '$u'";
                $follower_follows = mysqli_query($connection,$q);
                $follower_follows = mysqli_fetch_row($follower_follows);
                $follower_follows = $follower_follows[0];
                $follower_follows = openssl_decrypt($follower_follows,"AES-128-CBC",$follower_id);
                $follower_follows = json_decode($follower_follows,true);
                $pos = array_search($uname,$follower_follows);
                $follower_follows = array_replace($follower_follows, [$pos => $new_uname]);
                $follower_follows = json_encode($follower_follows);
                $follower_follows = openssl_encrypt($follower_follows,"AES-128-CBC",$follower_id);
                $q = "UPDATE `users` SET `follows` = '$follower_follows' WHERE `uname` = '$u'";
                if ($connection->query($q) === true){}
                else{
                  echo "<p class='bg-danger' style='width:fit-content'>Error: $connection->error</p>";
                }
                #replace the follower name
                $q = "SELECT `followers` FROM `users` WHERE `uname` = '$u'";
                $follower_followers = mysqli_query($connection,$q);
                $follower_followers = mysqli_fetch_row($follower_followers);
                $follower_followers = $follower_followers[0];
                $follower_followers = openssl_decrypt($follower_followers,"AES-128-CBC",$follower_id);
                $follower_followers = json_decode($follower_followers,true);
                if (in_array($uname,$follower_followers)){
                  $pos = array_search($uname,$follower_followers);
                  $follower_followers = array_replace($follower_followers, [$pos => $new_uname]);
                  $follower_followers = json_encode($follower_followers);
                  $follower_followers = openssl_encrypt($follower_followers,"AES-128-CBC",$follower_id);
                  $q = "UPDATE `users` SET `followers` = '$follower_followers' WHERE `uname` = '$u'";
                  if ($connection->query($q) === true){}
                  else{
                    echo "<p class='bg-danger' style='width:fit-content'>Error: $connection->error</p>";
                  }
                };
              };
              #new uname
              $q = "UPDATE `users` SET `uname` = '$new_uname' WHERE `uname` = '$uname'";
              if ($connection->query($q) === true){}
              else{
                echo "<p class='bg-danger' style='width:fit-content'>Error: $connection->error</p>";
              }
              $_SESSION["uname"] = $new_uname;
              $msg .= "<p class='bg-success' style='width:fit-content'>Username updated successfully</p>";
            }
          };
          # EMAIL
          if (isset($_POST["email"])){
            $new_email = mysqli_real_escape_string($connection,e($_POST["email"]));
            $email_query = "SELECT `email` FROM `users` WHERE `email` = '$new_email'";
            $db_email = mysqli_query($connection,$email_query);
            $db_email = mysqli_fetch_row($db_email);
            $db_email = $db_email[0];
            if ($new_email == $db_email && !empty($new_email)){
              $msg .= "<p class='bg-danger' style='width:fit-content'>This email is taken</p>";
            }
            else if (empty($new_email)){}
            else{
              $msg .= "<p class='bg-success' style='width:fit-content'>Email updated successfully</p>";
              $q = "UPDATE `users` SET `email` = '$new_email' WHERE `uname` = '$uname'";
              if ($connection->query($q) === true){}
              else{
                echo "<p class='bg-danger' style='width:fit-content'>Error: $connection->error</p>";
              }
            }
          };
          # PROFILE PIC
          if (!empty($_FILES["profile-img"])) {		
            if (empty($_FILES["profile-img"]["name"])){}
            else{ 
              $allowed = ["image/png", "image/jpg", "image/jpeg"];
              if (!in_array($_FILES["profile-img"]["type"], $allowed)){
                $msg .= "<p class='bg-danger' style='width:fit-content'>You are trying to upload a not allowed file type</p>";
              }
              else{
                $image = $_FILES["profile-img"]["tmp_name"];; 
                $image = base64_encode(file_get_contents(addslashes($image)));
                $q = "UPDATE `users` SET `img` = '$image' WHERE `uname` = '$uname'"; 
                if ($connection->query($q) === true){}
                else{
                  echo "<p class='bg-danger' style='width:fit-content'>Error: $connection->error</p>";
                }
                $msg .= "<p class='bg-success' style='width:fit-content'>New profile picture has been added</p>";
              }
            }
          }
          echo $msg;
        }
        if(isset($_POST['submit'])){
          if(hash_equals($_SESSION["csrf-token"], $_POST["csrftoken"])){
            #Create new csrf token
            createCSRF();
            update();
          }
          mysqli_close($connection);
        }
        ?>
        <input type="submit" value="Update" name="submit" style="background-color:#09D202;margin:0">
      </form>
    </div>  
  </div>
</body>
</html>