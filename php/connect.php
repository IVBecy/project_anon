<?php
# All the database data we need to be able to connect
$DB_SERVER = "127.0.0.1:3307";
$DB_USERNAME = "root";
$DB_PASSWORD = ""; 
$DB_NAME = "anon";
# Connecting to the server
$connection = mysqli_connect($DB_SERVER,$DB_USERNAME,$DB_PASSWORD,$DB_NAME);
# on connection error
if($connection == false){
  $msg = "Something went wrong";
}
?>