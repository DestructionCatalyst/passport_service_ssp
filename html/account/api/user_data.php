<?php
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/require_user.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';
header('Content-Type: application/json; charset=utf-8');

$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
if($request_method == 'GET'){
    $db = new MySQLDB("/usr/local/etc/db_config");
    $existing_data = $db->selectFirst("user", 
                        ["id", "first_name", "last_name", "patronym", "email", 
                         "phone_number", "sex", "birth_date", "birth_place"], 
                        "id = '".$_SESSION['userid']."'");
    echo json_encode($existing_data);
}
elseif ($request_method == 'POST') {
    $db = new MySQLDB("/usr/local/etc/db_config");

    $safePost = filter_input_array(INPUT_POST, [
         "last_name" => FILTER_SANITIZE_STRING,
         "first_name" => FILTER_SANITIZE_STRING,
         "patronym" => FILTER_SANITIZE_STRING,
         "email" => FILTER_VALIDATE_EMAIL,
         "phone_number" => FILTER_SANITIZE_STRING,
         "birth_date" => FILTER_SANITIZE_STRING,
         "birth_place" => FILTER_SANITIZE_STRING,
        ]);
    $existing_data = $db->update("user", 
                        array_keys($safePost), 
                        $safePost,
                        "id = '".$_SESSION['userid']."'");
    
}
else {
    ob_start();
    header('HTTP/1.1 405 Method Not Allowed');
    echo '405 Method Not Allowed';
    exit;
}