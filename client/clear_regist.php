<?php
session_start();
unset($_SESSION['guardian']);
header('location:addUser.php');
