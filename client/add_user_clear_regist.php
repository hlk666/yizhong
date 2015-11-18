<?php
session_start();
unset($_SESSION['guardian']);
header('location:add_user.php');
