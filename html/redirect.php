<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

function redirect($url){
    echo '<script type="text/JavaScript"> '
        . 'window.location.replace('
        . '"'.$url.'");'
        . ' </script>';
}
