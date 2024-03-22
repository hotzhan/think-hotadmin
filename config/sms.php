<?php
/*
 * 视图配置
 * 作者：hotAdmin
 */

return [
    //easy-sms配置
    'easysms' => [
        'config'=>[
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'yunpian','aliyun',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => app()->getRuntimePath() . 'sms' . DIRECTORY_SEPARATOR. 'easy-sms.log',// /runtime/sms/easy-sms.log
                ],
                'yunpian' => [
                    'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                ],
                'aliyun' => [
                    'access_key_id' => 'LTxxxxxxxxxxxxxxxxxxxxxxxxx',
                    'access_key_secret' => 'omDxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                    'sign_name' => '阿里云短信测试',
                ],
                //...
            ],
        ],
    ],

    //其它设置
    'verifycode'=>[
        'codenum' => 6,//验证码位数
        'expire' => '300',//验证码过期时间秒
        'limitcount' => 8,//如果频繁发送超过5条，需要等limittime时间后才能再发送
        'limittime' => '43200',//频繁发送，限制时间秒 12小时
    ],
];