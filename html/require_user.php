<?php
ob_start();
session_start();
if (!isset($_SESSION['userid'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo '401 Unauthorized';
    exit;
}