<?php
session_start();
unset($_SESSION['uname']);
session_destroy();
header('Location: ../root/index.html');
?>