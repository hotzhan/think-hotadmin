<?php
/*
 * 发送邮件配置
 * 作者：hotAdmin
 */

return [

    //-------------------phpmailer的设置-------------------start
    //可以配置多个服务器
    'servers' => [
        'qq_1' => [
            'mailer' => 'smtp', // pop3/imap/smtp/...
            'host' => 'smtp.qq.com',    //邮件服务器
            'auth' => true, //是否鉴权
            'username' => '123456789@qq.com',//qq邮箱
            'name' => '',//发送名称
            'password' => 'yzxxxxxxxxxxxxxxxxxxxx',//qq邮箱是使用授权码方式，非qq密码
            'encryption' => 'ssl',//加密方式 ssl/tsl等
            'port' => 465,  //邮件服务器端口
        ],
        'qq_2' => [
            'mailer' => 'smtp', // pop3/imap/smtp/...
            'host' => 'smtp.qq.com',    //邮件服务器
            'auth' => true, //是否鉴权
            'username' => '123123123@qq.com',//qq邮箱
            'name' => '',//发送名称
            'password' => 'zkxxxxxxxxxxxxxxx',//qq邮箱是使用授权码方式，非qq密码
            'encryption' => 'ssl',//加密方式 ssl/tsl等
            'port' => 465,  //邮件服务器端口
        ],
        '163' => [
            'mailer' => 'smtp',
            'host' => 'smtp.163.com',
            'auth' => true,
            'username' => 'xxxxxxx@163.com',
            'name' => '',//发送名称
            'password' => 'txxxxxxxxxxxxxxxxx',//163邮箱是使用授权码方式，非qq密码
            'encryption' => 'ssl',
            'port' => 465,
        ],
    ],

    'charset' => 'UTF-8',//邮件文本编码
    'ishtml' => true,//邮件文本内容是否开启html支持

    // debug 调试
    // 0：关闭调试
    // 1：show client -> server messages
    // 2：show client -> server and server -> client messages
    // 3：show connection status, client -> server and server -> client messages
    // 4：show all messages
    'debug' => 0,

    //-------------------phpmailer的设置-------------------end

    //其它设置

    //邮件发送验证码设置
    'verifycode'=>[
        'codenum' => 6,//验证码位数
        'expire' => 300,//验证码过期时间秒
        'limitcount' => 12,//0为不限制 ，如果频繁发送超过10条，需要等limittime时间后才能再发送
        'limittime' => 43200,//频繁发送，限制时间秒 12小时
    ],

];