<?php
session_start();
if (isset($_SESSION['userid'])) {
    header("Location: http://site.local/index.php");
    die();
}
if (!isset($_SESSION['employeeid'])) {
    header("Location: http://site.local/auth.php");
    die();
}


