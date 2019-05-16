<?php

 function Api_Success( $message = '', $data = array()) {

    $result = array(
        'code' => 200,
        'message' => $message,
        'data' => $data
    );

     return response()->json($result);
}

function Api_error($message = '', $data = array()) {

    $result = array(
        'code' => 100,
        'message' => $message,
        'data' => $data
    );

    return response()->json($result);
}