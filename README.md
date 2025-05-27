# think-hotadmin
基于thinkphp8/adminlte3.2后台管理系统

## 项目clone到本地
```shell
git clone https://github.com/hotzhan/think-hotadmin.git
```

## 安装项目依赖
```shell
composer install
```

## 需要修改的配置文件
* config/mail.php 邮件服务器配置（HotAdmi专用）
* config/sms.php 短信网关配置（HotAdmin专用）
* 其它配置文件参考ThinkPHP6/8的文档即可

## 数据库文件
sql.sql导入到自己的网站mysql数据库

## 后台
* 后台地址 域名/adminxx
* 账户admin 后台密码123123
* adminxx这个可以在文件/config/app.php里设置，还可以设置后台对应域名
```
// 应用映射（自动多应用模式有效）
    'app_map'          => [
        'adminxx'=>'admin'
    ],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [
        'api.tp8.com'=>'api', // api应用对应域名
        //'admin2.tp8.com'=>'admin' // 后台对应域名
        //'www.tp8.com'=>'index' // 前台对应域名
    ],
```
