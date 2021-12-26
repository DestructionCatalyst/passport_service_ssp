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
                                "employee_id = '".$_SESSION['employeeid']."'"
                                . " AND status != 'Паспорт выдан'"
                                . " AND status != 'В оформлении отказано'",
                                order_by: 'application_date, id');
    }
    
    public function onPost() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $newStatus = filter_input(INPUT_POST, 'new_state', FILTER_SANITIZE_STRING);
        
        $data = $db->selectFirst('application', ['employee_id', 'status'], 
                "id='" . $id . "'");
        
        $status = $data['status'];
        $employee_id = $data['employee_id'];
        if ($_SESSION['employeeid'] !== $employee_id){
            echo ApiController::forbidden();
            exit;
        }
        
        include 'state_change.php';
        
        //var_dump($newStatus);
        //var_dump($possibleNextStates[$status]);
        
        if(in_array($newStatus, $possibleNextStates[$status])){
            $db->update("application", 
                        ['employee_id', 'status'], 
                        [$_SESSION['employeeid'], $newStatus],
                        "id = '" . $id . "'");
            echo ApiController::success();
        }
        else{
            echo ApiController::unprocessableEntity();
        }
    }
    

}

(new EmployeeApplicationsApiController)->processRequest();