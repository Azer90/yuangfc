<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use EasyWeChat;
class WeChatController extends Controller
{
    public function serve(){

        $app = EasyWeChat::officialAccount();

        $response = $app->server->serve();

// 将响应输出
        return $response;
    }

}