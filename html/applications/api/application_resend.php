<?php

include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

class ResendApiController extends ApiController{
    
    public function __construct() {
        parent::__construct(for: 'user');
    }
    
    

    public function onPost() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        
        $input_array = filter_input_array(INPUT_POST);
        $id = $input_array['id'];
        
        $application = $db->selectFirst(
                    "application",
                    ['user_id', 'status'],
                    "id = '" . $id . "'");
        if($_SESSION['userid'] !== $application['user_id']){
            echo self::forbidden();
            exit();
        }
        if ($application['status'] !== "Отправлено на доработку"){
            echo self::unprocessableEntity();
            exit();
        }
        $db->update("application", 
                ["status"], 
                ["Принято в обработку"],
                "id = '" . $id . "'");
        
        
        echo self::success();
    }
    

}

(new ResendApiController)->processRequest();