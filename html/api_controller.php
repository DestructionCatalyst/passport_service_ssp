<?php

header('Content-Type: application/json; charset=utf-8');

/**
 * Description of api_view
 *
 * @author vladislav
 */
class ApiController {
    private $for;
    
    public function __construct($for) {
        $this->for = $for;
    }


    static function success() {
        return json_encode(
                [
                    'code' => 200,
                    'message' => "Success"
                ]);
    }
    
    static function unauthorized() {
        header('HTTP/1.1 401 Unauthorized');
        return json_encode(
                [
                    'code' => 401,
                    'message' => "Unauthorized"
                ]);
    }
    
    static function forbidden() {
        header('HTTP/1.1 403 Forbidden');
        return json_encode(
                [
                    'code' => 403,
                    'message' => "Forbidden"
                ]);
    }
    
    static function methodNotAllowed() {
        header('HTTP/1.1 405 Method Not Allowed');
        return json_encode(
                [
                    'code' => 405,
                    'message' => "Method Not Allowed"
                ]);
    }
    
    static function conflict() {
        header('HTTP/1.1 409 Conflict');
        return json_encode(
                [
                    'code' => 409,
                    'message' => "Conflict"
                ]);
    }
    
    static function unprocessableEntity() {
        header('HTTP/1.1 422 Unprocessable Entity');
        return json_encode(
                [
                    'code' => 422,
                    'message' => "Unprocessable Entity"
                ]);
    }
    
    static function internalServerError($err) {
        header('HTTP/1.1 500 Internal Server Error');
        return json_encode(
                [
                    'code' => 500,
                    'message' => "Internal Server Error",
                    'value' => $err
                ]);
    }
    
    static function loadFromDB($amount, $table, $columns, $where = "", $group_by = "",
            $having = "", $order_by = "", $limit = "")
    {
        include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';
        $db = new MySQLDB("/usr/local/etc/db_config");
        if ($amount === 1){
            $existing_data = $db->selectFirst($table, $columns, $where, $group_by, 
                $having, $order_by, $limit);
        }
        else{
            $existing_data = $db->select($table, $columns, $where, $group_by, 
                $having, $order_by, $limit);
        }
        echo json_encode($existing_data);
    }
            
    function onGet(){
        echo ApiController::methodNotAllowed();
        exit;
    }
    
    function onPost(){
        echo ApiController::methodNotAllowed();
        exit;
    }
    
    function onDelete(){
        echo ApiController::methodNotAllowed();
        exit;
    }
    
    function authorize(){
        session_start();
        
        $for = $this->for;
        if ($for === 'everyone'){
            // All good
        }
        else if ($for === 'authenticated'){
            self::authorizeAuthenticated();
        }
        else if ($for === 'user'){
            self::authorizeUser();
        }
        else if ($for === 'employee'){
            self::authorizeEmployee();
        }
        else {
            echo ApiController::internalServerError(
                    'Incorrect API configuration');
        }
    }
    
    static function authorizeAuthenticated(){
        if (!isset($_SESSION['userid']) && !isset($_SESSION['employeeid'])){
            echo ApiController::unauthorized();
            exit;
        }
    }
    
    static function authorizeUser(){
        if (isset($_SESSION['employeeid'])){
            echo ApiController::forbidden();
            exit;
        }
        elseif (!isset($_SESSION['userid'])) {
            echo ApiController::unauthorized();
            exit;
        }
    }
    
    static function authorizeEmployee(){
        if (isset($_SESSION['userid'])){
            echo ApiController::forbidden();
            exit;
        }
        elseif (!isset($_SESSION['employeeid'])) {
            echo ApiController::unauthorized();
            exit;
        }
    }


    function processRequest(){
        $request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        
        $this->authorize();
        
        try {
            if($request_method == 'GET'){
                $this->onGet();
            }
            elseif ($request_method == 'POST') {
                $this->onPost();
            }
            elseif ($request_method == 'DELETE') {
                $this->onDelete();
            }
            else {
                echo ApiController::methodNotAllowed();
                exit;
            }
        } catch (\Throwable $e) {
            echo ApiController::internalServerError(
                    $e->getMessage() . ' ' . 
                    $e->getFile() . ' ' . 
                    $e->getLine() . ' ' . 
                    $e->getTraceAsString());
            exit;
        }
        
    }
}
