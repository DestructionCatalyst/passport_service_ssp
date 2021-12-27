<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of mysql_db
 *
 * @author vladislav
 */
include_once 'string_utils.php';

//error_reporting(E_ERROR | E_PARSE);


class MySQLDB {

    public static $db;
    private $conn;
    private $debug;

    function __construct(string $config_file_path, $debug=false) {
        $myfile = fopen($config_file_path, "r")
                or die("Unable to open database configuration!");
        $config_str = fread($myfile, filesize("$config_file_path"));
        fclose($myfile);

        $config_arr_raw = explode(";", $config_str);
        $config_arr = array_map('trim', $config_arr_raw);
        
        try{
            $this->conn = new PDO("mysql:host=$config_arr[0];dbname=$config_arr[3]", 
                $config_arr[1], 
                $config_arr[2]);
        }
        catch (PDOException $exception){    
            die("Connection failed: " . $exception->getMessage());
 
        }
        
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        //mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
        
        //$this->conn = new mysqli($config_arr[0],
        //        $config_arr[1], $config_arr[2], $config_arr[3]);

        
        
        $this->debug = $debug;
    }
    
    static function getInstance() {
        if (!isset(self::$db)){
            //echo "Created";
            self::$db = new MySQLDB('/usr/local/etc/db_config');
        }
        return self::$db;
    }
            
    function echoIfDebug($str){
        if ($this->debug){
            echo $str.'</br>';
        }
            
    }

    function rawQuery($query) {
        $result = $this->conn->query($query);
        return $result;
    }
    
    private function _select($table, $columns, $where = "", $group_by = "",
            $having = "", $order_by = "", $limit = "", $for = "") {
        $SQLstring = "SELECT ";
        if ($columns == '*'){
            $SQLstring = $SQLstring . ' *';
        }
        else{
            appendAll($SQLstring, $columns, ", ", "");
        }
        $SQLstring = $SQLstring . " FROM " . $table;
        appendIfNotEmpty($SQLstring, " WHERE ", $where);
        appendIfNotEmpty($SQLstring, " GROUP BY ", $group_by);
        appendIfNotEmpty($SQLstring, " HAVING ", $having);
        appendIfNotEmpty($SQLstring, " ORDER BY ", $order_by);
        appendIfNotEmpty($SQLstring, " LIMIT ", $limit);
        appendIfNotEmpty($SQLstring, " FOR ", $for);
        $SQLstring = $SQLstring . ';';
        $this->echoIfDebug($SQLstring);
        $result = $this->rawQuery($SQLstring);
        return $result;
    }

    function selectFirst($table, $columns, $where = "", $group_by = "",
            $having = "", $order_by = "", $limit = "", $for = "") {
        $result = $this->_select($table, $columns, $where, 
                $group_by, $having, $order_by, $limit, $for);
        if ($result) {
            return $result->fetch();
        }
        return $result;
    }
    
    function select($table, $columns, $where = "", $group_by = "",
            $having = "", $order_by = "", $limit = "", $for = "") {
        $result = $this->_select($table, $columns, $where, 
                $group_by, $having, $order_by, $limit, $for);
        if ($result) {
            return $result->fetchAll();
        }
        
    }

    function tableContains($table, $where = "") {
        $SQLstring = "SELECT * FROM " . $table . " WHERE " . $where . ";";
        $this->echoIfDebug($SQLstring);
        $result = $this->rawQuery($SQLstring);
        return $result and $result->rowCount() > 0;
    }

    function insert($table, $columns, $values) {
        $SQLstring = "INSERT INTO " . $table . " (";
        appendAll($SQLstring, $columns, ", ", "`");
        $SQLstring = $SQLstring . ") VALUES ";
        $i = 0;
        $values_length = count($values);
        foreach ($values as $tuple) {
            $SQLstring = $SQLstring . "(";
            appendAll($SQLstring, $tuple, ", ", "'");
            $SQLstring = $SQLstring . ")";
            if ($i != $values_length - 1) {
                $SQLstring = $SQLstring . ", ";
            }
            $i++;
        }
        $SQLstring = $SQLstring . ';';
        $this->echoIfDebug($SQLstring);
        $this->rawQuery($SQLstring);
    }

    function update($table, $columns, $values, $where) {
        $SQLstring = "UPDATE " . $table . " SET ";
        $assignment_list = array_map('toAssignment', $columns, $values);
        appendAll($SQLstring, $assignment_list, ", ", "");
        appendIfNotEmpty($SQLstring, " WHERE ", $where);
        $SQLstring = $SQLstring . ";";
        $this->echoIfDebug($SQLstring);
        $this->rawQuery($SQLstring);
    }

    function addOrReplace($table, $columns, $values, $index) {
        $i = array_search($index, $columns);
        if ($i !== false) {
            $where = "`" . ($columns[$i]) . "` = '" . ($values[$i]) . "'";
            $this->echoIfDebug($where);
            if ($this->tableContains($table, $where)) {
                $this->update($table, $columns, $values, $where);
            } else {
                $this->insert($table, $columns, array($values));
            }
        } else {
            die("Index not found!");
        }
    }

    function delete($table, $where) {
        $SQLstring = "DELETE FROM " . $table;
        appendIfNotEmpty($SQLstring, " WHERE ", $where);
        $this->echoIfDebug($SQLstring);
        $this->rawQuery($SQLstring);
    }
    
    function getEnumValues($table, $field )
    {
        $type = $this->rawQuery( "SHOW COLUMNS FROM {$table} "
        . "WHERE Field = '{$field}'" )->fetch()['Type'];
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        return $enum;
    }
    
    function beginTransaction(){
        return $this->conn->beginTransaction();
    }
    
    function commit(){
        return $this->conn->commit();
    }
    
    function rollback(){
        return $this->conn->rollBack();
    }
    
    function autocommit(bool $enable){
        return $this->conn->autocommit($enable);
    }

}
