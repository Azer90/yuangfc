<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use EasyWeChat;
use Illuminate\Support\Facades\Log;
class WeChatController extends Controller
{
    private $app;
    public function __construct()
    {
        $this->app = EasyWeChat::officialAccount();
    }

    public function serve(){


        $this->app->server->push(function ($message) {
            $monolog = Log::getMonolog();
            $monolog->popHandler();
            Log::useFiles(storage_path('logs/wechat_ts.log'));
            Log::info($message);
            if(isset($message['Event'])){
                if($message['Event']=='SCAN'&&$message['EventKey']=='validate_logon'){
                    return "登录成功";
                }
            }


                return "您好！欢迎关注";


        });
        $this->menu();
        $response = $this->app->server->serve();

// 将响应输出
        return $response;
    }


    public function menu(){
        $buttons = [
            [
                "type" => "miniprogram",
                "name" => "宇昂房产",
                "url"=> "http://mp.weixin.qq.com",
                "appid"=>"wx0d754fd3f7b4a131",
                "pagepath"=>"pages/index/index",
            ],

        ];
        $this->app->menu->create($buttons);
    }




}