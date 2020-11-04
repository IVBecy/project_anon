<?php
#Destroy the session, remove vars and redirect
session_start();
unset($_SESSION['uname']);
session_destroy();
header('Location: ../root/index.html');
?>