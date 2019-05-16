<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
class WeChatController extends Controller
{
    public function serve(){

        $app = Factory::officialAccount();

        $response = $app->server->serve();

// 将响应输出
        return $response;
    }

}