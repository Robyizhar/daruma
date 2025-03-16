<?php
    session_start();
    include("../inc/design/head.php"); 
    include("../inc/design/header.php"); 
    include("../inc/design/nav.php"); 

    if (!isset($_SESSION['user_id'])) {
        echo "NO SESSION";
    } else {
        echo "WAS SESSION";
    }