<?php

include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';

class EmployeeApiController extends ApiController{
    public function __construct() {
        parent::__construct(for: 'employee');
    }
    
    public function onGet() {
        ApiController::loadFromDB(
                1,
                "employee", 
                ["CONCAT(last_name, ' ', first_name, ' ', patronym) AS full_name"], 
                "id = '" . $_SESSION['employeeid'] . "'"
                );
    }
    
}

(new EmployeeApiController)->processRequest();
