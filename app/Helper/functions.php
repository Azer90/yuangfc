<?php

 function Api_success( $message = '', $data = array()) {

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

/**
 * curl提交请求
 * @param $url 提交地址
 * @param array $data
 * @param bool $https
 * @return mixed
 */
function curl_post($url,$data=array(),$https=true)
{
    $curl = curl_init();                                    //创建一个新CURL资源 返回一个CURL句柄，出错返回 FALSE。
    curl_setopt($curl, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);           //构造来源
    curl_setopt($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);     //模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,300);                    //在发起连接前等待的时间，如果设置为0，则无限等待。
    curl_setopt($curl, CURLOPT_TIMEOUT, 300);                     //设置CURL允许执行的最长秒数
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);               //获取的信息以文件流的形式返回，而不是直接输出。
    if ($https) {                                                       //设置为https请求，不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $header[] = 'ContentType:application/json;charset=UTF-8';
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);               //用来设置HTTP头字段的数组
    curl_setopt($curl, CURLOPT_URL, $url);                          //设置请求地址
    curl_setopt($curl, CURLOPT_POST,true);                       //发送POST请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                  //发送的POST数据
    curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);                //启用时追踪句柄的请求字符串
    $result = curl_exec($curl);                               //执行CURL
    if(curl_errno($curl)){                                  //检查是否有错误发生
        echo 'Curl error: ' . curl_error($curl);                  //返回最后一次的错误号
    }
    curl_close($curl);                                     //关闭CURL 并且释放系统资源
    return $result;
}
