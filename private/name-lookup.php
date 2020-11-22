<?php
#Turn off all notices
error_reporting(E_ALL & ~E_NOTICE);
#Adding the script that connects to the DB
include("./connect.php");
include("./vars.php");
# Get the str parsed in at JS
$str = mysqli_real_escape_string($connection,e($_GET["str"]));
$output = "";
# Get all the usernames that start with the str
$q = "SELECT `uname` FROM `users` WHERE `uname` LIKE '$str%' LIMIT 10";
$names = mysqli_query($connection,$q);
$names = mysqli_fetch_row($names);
# Output names from query
if ($names != ""){
  foreach($names as $n){
    $output .= "$n";
  }
  echo "<h5><a href='./$output'>$output</a></h5>";
}else{
  $output =  "No match"; 
  echo $output;
}
?>