<?php 
#Session (start and vars)
session_start();
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("../private/connect.php");
#Getting some vars
include("../private/vars.php");
#Username to like post for
$src_uname = $_SESSION["src_uname"];
#Post title
$like_title = $_POST["title"];
$msg = $_POST["msg"];
$src_id = $_SESSION["src_id"];
#Getting comments from the posts table
$src_query = "SELECT `comments` FROM `posts` WHERE `name_id` = '$src_id' AND `title` = '$like_title'";
$comments = mysqli_query($connection,$src_query);
$comments = mysqli_fetch_row($comments);
$comments = $comments[0];
$comments = openssl_decrypt($comments,"AES-128-CBC",$src_id);
$comments = json_decode($comments,true);
$comments[$id] = mysqli_real_escape_string($connection,e($msg));
$comments = json_encode($comments);
$comments = openssl_encrypt($comments,"AES-128-CBC",$src_id);
$append_q = "UPDATE `posts` SET `comments` = '$comments'  WHERE `name_id` = '$src_id' AND `title` = '$like_title'";
$connection->query($append_q);
header("Location: ../public/$src_uname");
?>