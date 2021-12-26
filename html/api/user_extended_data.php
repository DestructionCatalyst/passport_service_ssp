<?php
include '../require_user.php';
include '../mysqldb.php';
header('Content-Type: application/json; charset=utf-8');

$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
if($request_method == 'GET'){
    $db = new MySQLDB("/usr/local/etc/db_config");
    $where_condition = "id = '".$_SESSION['userid']."'";

    $name_array = $db->selectFirst("user", 
            ["last_name", "first_name", "patronym"], 
            $where_condition);
    $full_name = $name_array['last_name'].' '
            .$name_array['first_name'].' '
            .$name_array['patronym'];

    $has_registration = 
            $db->tableContains('permanent_registration',
                    'user_'.$where_condition);
    $has_temp_registration = 
            $db->tableContains('temporary_registration',
                    'user_'.$where_condition);
    $has_passport = 
            $db->tableContains('passport',
                    'user_'.$where_condition);
    
    $result = [
        'full_name' => $full_name,
        'has_registration' => $has_registration,
        'has_temp_registration' => $has_temp_registration,
        'has_passport' => $has_registration,
        ];
    
    echo json_encode($result);
}
else {
    ob_start();
    header('HTTP/1.1 405 Method Not Allowed');
    echo '405 Method Not Allowed';
    exit;
}