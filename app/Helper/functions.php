<?php

 function Api_Success( $message = '', $data = array()) {

    $result = array(
        'code' => 1000,
        'message' => $message,
        'data' => $data
    );

     return response()->json($result);
}

function Api_error($message = '', $data = array()) {

    $result = array(
        'code' => 1001,
        'message' => $message,
        'data' => $data
    );

    return response()->json($result);
}