<?php
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

class ApplicationsApiController extends ApiController{
    
    public function __construct() {
        parent::__construct(for: 'user');
    }


    public function onGet() {
        $db = new MySQLDB("/usr/local/etc/db_config"); 
        $existing_data = $db->select("application", 
                            ['id', 'status', 'application_date', 
                                'RANK() OVER(PARTITION BY user_id ORDER BY application_date, id) AS number'], 
                            "user_id = '".$_SESSION['userid']."'",
                            order_by: 'number');
        echo json_encode($existing_data);
    }
    
    public function onPost() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        include '../forms.php';
        $post_array = filter_input_array(INPUT_POST);
        if ($applicationForm->validate_input(
                $post_array['reason_and_limitations']))
        {
            foreach ($post_array['workplaces'] as $workplace) {
                if (!($workPlaceForm->validate_input($workplace))){
                    echo ApiController::unprocessableEntity();
                    exit;
                }
            }
            $applicationId = $db->insert("application", 
                ['reason', 'user_id', 'application_date'], 
                [[
                    $post_array['reason_and_limitations']['reason'],
                    $_SESSION['userid'],
                    date('Y-m-d')
                ]]);
            if (isset($post_array['workplaces'])){
                $db->delete("work_place", "user_id = '" . $_SESSION["userid"] . "'");
                foreach ($post_array['workplaces'] as $workplace) {
                    $workplace += ["user_id" => $_SESSION['userid']];
                    if (!$workplace['unemployment_date']){
                        unset($workplace['unemployment_date']);
                    }
                    $db->insert("work_place", 
                                array_keys($workplace), 
                                [array_values($workplace)]);
                }
            }
            
            echo ApiController::success();
        }
        else {
            echo ApiController::unprocessableEntity();
        }
        
    }

}

(new ApplicationsApiController)->processRequest();
