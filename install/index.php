<?php
//Admin Check
session_start();
$_SESSION['right_enter'] = 1;
header("Location: ../index.php?a=index&m=Index&g=Install"); 
?>