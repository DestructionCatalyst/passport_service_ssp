<?php

include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

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
        //TODO trancactions
        $db = MySQLDB::getInstance();
        
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        try {
            $db->autocommit(false);
            $db->rawQuery(
                "SET GLOBAL TRANSACTION ISOLATION LEVEL SERIALIZABLE;"
            );
            
            $db->beginTransaction();
            
            $db->update("application", 
                        ['employee_id', 'status'], 
                        [$_SESSION['employeeid'], "Принято в обработку"],
                        "id = '" . $id . "'");
            
            // Для удобства моделирования
            if ($_SESSION['employeeid'] == 1){
                sleep(10);
            }
            
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollback();
            echo self::conflict();
        }
        
        echo ApiController::success();
    }
}

(new EmployeeApplicationsApiController)->processRequest();

