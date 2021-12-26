<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function appendIfNotEmpty(&$head, $delimiter, $tail) {
    if ($tail) {
        $head = $head . '' . $delimiter . '' . $tail;
    }
}

function appendAll(&$head, $arr, $delimiter = ", ", $surround_with = "") {
    $i = 0;
    $item_count = count($arr);
    foreach ($arr as $item) {
        $head = $head . $surround_with . $item . $surround_with;
        if ($i != $item_count - 1) {
            $head = $head . $delimiter;
        }
        $i++;
    }
}

function toAssignment($name, $value, $surround_name = "`", $surround_value = "'")
{
    return $surround_name . $name . $surround_name . "="
            . $surround_value . $value . $surround_value;
}