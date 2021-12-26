<?php
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

class ApplicationsApiController extends ApiController{
    public function __construct() {
        parent::__construct(for: 'authenticated');
    }
    
    public function onGet() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($id){
            $existing_data = $db->select("application", 
                                '*', 
                                "id = '" . $id . "'");
            $application = $existing_data[0];
            $user_id = $application['user_id'];
            $employee_id= $application['employee_id'];
            if (isset($_SESSION['userid'])){
                if ($_SESSION['userid'] !== $user_id){
                    echo ApiController::forbidden();
                    exit;
                }
            }
            else if (isset($_SESSION['employeeid'])){
                if ($_SESSION['employeeid'] !== $employee_id){
                    echo ApiController::forbidden();
                    exit;
                }
            }
            else {
                //Should never get here, but just in case
                echo ApiController::unauthorized();
                exit;
            }
            
            $user = $db->select("user", 
                            ['id', 'first_name', 'last_name', 'patronym', 
                                'email', 'phone_number', 'sex', 
                                'birth_date', 'birth_place'], 
                            "id = '" . $user_id . "'")[0];
            
            $perm_reg = $db->select("permanent_registration", 
                                '*', 
                                "user_id = '" . $user_id . "'");
            $temp_reg = $db->select("temporary_registration", 
                                '*', 
                                "user_id = '" . $user_id . "'");
            $passport = $db->select("passport", 
                                '*', 
                                "user_id = '" . $user_id . "'")[0];
            $workplaces = $db->select("work_place", 
                                '*', 
                                "user_id = '" . $user_id . "'");
            $responce = [
                'user' => $user,
                'passport' => $passport,
                'application' => $application,
                'workplaces' => $workplaces
            ];
            if ($perm_reg){
                $responce += ['permanent_registration' => $perm_reg[0]];
            }
            if ($temp_reg){
                $responce += ['temporary_registration' => $temp_reg[0]];
            }
            echo json_encode($responce);
        }
    }
}

(new ApplicationsApiController)->processRequest();