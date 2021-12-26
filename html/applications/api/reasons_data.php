<?php
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

class ApplicationsApiController extends ApiController{
    public function __construct() {
        parent::__construct(for: 'authenticated');
    }
    
    public function onGet() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        $values = $db->getEnumValues("application", "reason");
        echo json_encode($values);
    }
}

(new ApplicationsApiController)->processRequest();

