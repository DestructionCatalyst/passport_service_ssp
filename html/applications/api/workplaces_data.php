<?php

include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

class WorkplacesApiController extends ApiController{
    public function __construct() {
        parent::__construct(for: 'user');
    }
    
    public function onGet() {
        self::loadFromDB("many", 
                "work_place", 
                "*", 
                "user_id = '" . $_SESSION['userid'] . "'");
    }
}

(new WorkplacesApiController())->processRequest();