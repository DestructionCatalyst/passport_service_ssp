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
        
        mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
        
        $this->conn = new mysqli($config_arr[0],
                $config_arr[1], $config_arr[2], $config_arr[3]);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        $this->debug = $debug;
    }
    
    static function getInstance() {
        if (!isset(self::$db)){
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
            $having = "", $order_by = "", $limit = "") {
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
        $SQLstring = $SQLstring . ';';
        $this->echoIfDebug($SQLstring);
        $result = $this->rawQuery($SQLstring);
        return $result;
    }

    function selectFirst($table, $columns, $where = "", $group_by = "",
            $having = "", $order_by = "", $limit = "") {
        $result = $this->_select($table, $columns, $where, 
                $group_by, $having, $order_by, $limit);
        if ($result) {
            return $result->fetch_array(MYSQLI_ASSOC);
        }
        return $result;
    }
    
    function select($table, $columns, $where = "", $group_by = "",
            $having = "", $order_by = "", $limit = "") {
        $result = $this->_select($table, $columns, $where, 
                $group_by, $having, $order_by, $limit);
        $values = [];
        if ($result) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                array_push($values, $row);
            }
        }
        return $values;
    }

    function tableContains($table, $where = "") {
        $SQLstring = "SELECT * FROM " . $table . " WHERE " . $where . ";";
        $this->echoIfDebug($SQLstring);
        $result = $this->rawQuery($SQLstring);
        return $result and $result->num_rows > 0;
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
        
        return $this->conn->insert_id;
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
        . "WHERE Field = '{$field}'" )->fetch_row()[1];
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        return $enum;
    }
    
    function beginTransaction(int $flags = 0, ?string $name = null){
        return $this->conn->begin_transaction($flags, $name);
    }
    
    function commit(int $flags = 0, ?string $name = null){
        return $this->conn->commit($flags, $name);
    }
    
    function rollback(int $flags = 0, ?string $name = null){
        return $this->conn->rollback($flags, $name);
    }
    
    function autocommit(bool $enable){
        return $this->conn->autocommit($enable);
    }

}
