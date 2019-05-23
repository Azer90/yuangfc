<?php
namespace  App\Libs;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
class Sms {

    public function send($mobile,$code){

        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => storage_path('/logs/easy-sms.log'),
                ],
                'yunpian' => [
                    'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                ],
                'aliyun' => [
                    'access_key_id' => 'LTAIy8EZpHzDCvl0',
                    'access_key_secret' => '2vpuGRpfF9jZL1XH4GH5dFionqQxeD',
                    'sign_name' => '宇昂房产',
                ],
                //...
            ],
        ];

        $easySms = new EasySms($config);

        try{
            $easySms->send($mobile, [
                'content'  => '您的验证码为:'.$code,
                'template' => 'SMS_166080682',
                'data' => [
                    'code' => $code
                ],['aliyun']
            ]);
            return 'success';
            }catch (NoGatewayAvailableException $e){
                    return $e->getExceptions();
             }

    }
}