<?php
session_destroy();
unset($_SESSION['uname']);
header('Location: ../root/index.html');
?>