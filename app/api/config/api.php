<?php
/**
 * User: hotadmin
 * Date: 2024/3/26
 * Site: https://www.hotadmin.cn
 */
 
return [
    'auth' => [
        'jwt_key' => env('api.jwt_key', '_jwt_default_key_'),
        //'jwt_typ' => env('api.jwt_typ', 'JWT'),,//默认就是JWT
        'jwt_alg' => env('api.jwt_alg', 'HS256'),//默认就是HS256
        'jwt_iss' => env('api.jwt_iss', request()->domain()),
        'jwt_aud' => env('api.jwt_aud', 'default_aud'),
        'jwt_exp' => env('api.jwt_exp', 3600),
        'jwt_refresh_exp' => env('api.jwt_exp', 604800),
    ]
];

