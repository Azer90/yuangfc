<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use EasyWeChat;
class WeChatController extends Controller
{
    private $app;
    public function __construct()
    {
        $this->app = EasyWeChat::officialAccount();
    }

    public function serve(){


        $this->app->server->push(function ($message) {
            return "您好！欢迎关注";
        });
        $list = $this->app->menu->list();
        //dd($list);
        $response = $this->app->server->serve();

// 将响应输出
        return $response;
    }



}