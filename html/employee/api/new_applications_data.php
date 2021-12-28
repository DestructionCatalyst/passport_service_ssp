<?php

include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

class EmployeeApplicationsApiController extends ApiController{
    
    public function __construct() {
        parent::__construct(for: 'employee');
    }
    
    public function onGet() {
        ApiController::loadFromDB("many", "application", 
                                ['id', 'status', 'application_date'], 
                                "employee_id IS NULL",
                                order_by: 'application_date, id');
    }
    
    public function onPost() {
        $db = MySQLDB::getInstance();
        
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $lock = filter_input(INPUT_POST, 'lock', FILTER_VALIDATE_BOOLEAN);
        
        if ($lock){
            $db->rawQuery("LOCK TABLES application WRITE;");
        }

        $status = $db->selectFirst(
                table: "application", 
                columns: '*', 
                where: "id = '" . $id . "'")['status'];

        // Для удобства моделирования
        sleep(5);

        if($status == 'Заполнено'){
            $db->update(
                table: "application", 
                columns: ['employee_id', 'status'], 
                values: [$_SESSION['employeeid'], "Принято в обработку"],
                where: "id = '" . $id . "'");
        }
        else{
            // Returns responce with 409 code
            echo self::conflict();
            exit;
        }
        
        if ($lock){
            $db->rawQuery("UNLOCK TABLES;");
        }
        echo self::success();
    }
}

(new EmployeeApplicationsApiController)->processRequest();

