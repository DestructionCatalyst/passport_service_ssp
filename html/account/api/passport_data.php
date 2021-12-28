<?php
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';
include_once '../forms.php';

class PassportApiController extends ApiController{
    function __construct() {
        parent::__construct("user");
    }


    public function onGet() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        $existing_data = $db->selectFirst("passport", 
                            ["id", "series", "number", "issue_date", "issue_organ"], 
                            "user_id = '".$_SESSION['userid']."'");
        echo json_encode($existing_data);

    }
    
    public function onPost() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        
        if ($passportForm->validate()){
            // Save to DB
            $db = new MySQLDB("/usr/local/etc/db_config");
            $safePost = filter_input_array(INPUT_POST, [
                 "series" => FILTER_SANITIZE_NUMBER_INT,
                 "number" => FILTER_SANITIZE_NUMBER_INT,
                 "issue_organ" => FILTER_SANITIZE_STRING,
                 "issue_date" => FILTER_SANITIZE_STRING
                ]);
            $safePost += ["user_id" => $_SESSION['userid']];
            $existing_data = $db->addOrReplace("passport", 
                                array_keys($safePost), 
                                array_values($safePost),
                                "user_id");
            // code 200
            echo ApiController::success();
        }
        else {
            // code 422
            echo ApiController::unprocessableEntity();
        }
        
    }

}

(new PassportApiController)->processRequest();
