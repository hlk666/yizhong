<?php
session_start();
unset($_SESSION['guardian']);
unset($_SESSION['param']);
header('location:add_user.php');
