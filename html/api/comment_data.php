<?php

include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/api_controller.php';
include filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/mysqldb.php';

class CommentApiController extends ApiController{
    
    public function __construct() {
        parent::__construct(for: 'authenticated');
    }
    
    public function onGet() {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if ($id){
            ApiController::loadFromDB("many", "comment", 
                                ['id', 'stage', 'description', 'status', 
                                    'creation_date', 'last_change_date'], 
                                "application_id = '".$id."'"
                                . " AND status != 'Исправлено'",
                                order_by: 'stage');
        }
    }
    
    public function onPost() {
        $db = new MySQLDB("/usr/local/etc/db_config");
        
        $input_array = filter_input_array(INPUT_POST);
        $action = $input_array['action'];
        
        if($action === 'create'){
            self::authorizeEmployee();
            $comments = $input_array['comments'];
            if ($comments){
                foreach ($comments as $comment) {
                    $id = $db->selectFirst("comment", 
                            ["id"], 
                            "application_id = '" . $comment['application_id'] . "'"
                            . " AND stage = '" . $comment['stage'] . "'");
                    if($id){
                        $this->approve($db, $id['id']);
                    }
                }
                $db->insert('comment', 
                    array_keys($comments[0]), 
                    array_map('array_values', $comments));
                echo self::success();
            }
        }
        elseif ($action === 'fix') {
            self::authorizeUser();
            $comment_id = $input_array['comment_id'];
            $application = $db->selectFirst(
                    "comment JOIN application ON comment.application_id = application.id",
                    ['user_id', 'application.status'],
                    "comment.id = '" . $comment_id . "'");
            if($_SESSION['userid'] !== $application['user_id']){
                echo self::forbidden();
                exit();
            }
            if ($application['status'] !== "Отправлено на доработку"){
                echo self::unprocessableEntity();
                exit();
            }
            $db->update("comment", 
                    ["status", "last_change_date"], 
                    ["Внесены правки", date('Y-m-d')],
                    "id = '" . $comment_id . "'");
            echo self::success();
        }
        elseif ($action === 'approve') {
            self::authorizeEmployee();
            $comment_id = $input_array['comment_id'];
            $this->approve($db, $comment_id);
            echo self::success();
        }
        else{
            echo self::unprocessableEntity();
        }
    }
    
    function approve($db, $comment_id){
        $application = $db->selectFirst(
                    "comment JOIN application ON comment.application_id = application.id",
                    ['employee_id', 'application.status'],
                    "comment.id = '" . $comment_id . "'");
            if($_SESSION['employeeid'] !== $application['employee_id']){
                echo self::forbidden();
                exit();
            }
            if ($application['status'] !== "Принято в обработку"){
                echo self::unprocessableEntity();
                exit();
            }
            $db->update("comment", 
                    ["status"], 
                    ["Исправлено"],
                    "id = '" . $comment_id . "'");
    }
    

}

(new CommentApiController)->processRequest();