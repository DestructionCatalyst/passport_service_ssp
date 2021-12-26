<?php

function send_request($url, $data, $cookie){
    $url = 'http://site.local/' . $url;
    
    $headers = array(
        "Content-type: application/x-www-form-urlencoded\n",
        'Cookie: ' . $cookie
    );
    $options = array(
        'http' => array(
            'header'  => $headers,
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}