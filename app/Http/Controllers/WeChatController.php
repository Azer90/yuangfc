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
        //$this->menu();
        $response = $this->app->server->serve();

// 将响应输出
        return $response;
    }


    public function menu(){
        $buttons = [
            [
                "type" => "click",
                "name" => "今日歌曲",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://www.soso.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];
        $this->app->menu->create($buttons);
    }

}