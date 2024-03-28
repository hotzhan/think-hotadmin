<?php
/**
 * jwt token 类
 * User: hotzhan
 * Date: 2024/3/26
 * Site: https://www.hotadmin.cn
 */


namespace app\api\logic;

use app\api\exception\ApiException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\Exception;

class Token
{
    protected $jwtKey = '_default_jew_key_';
    protected $jwtExp = 3600;
    protected $jwtRefreshExp = 604800;//7天

    protected $jewAlg = 'HS256';

    public function __construct()
    {
        $this->jwtKey = config('api.jwt_key') ?? $this->jwtKey;
        $this->jwtExp = config('api.jwt_exp') ?? $this->jwtExp;
        $this->jewAlg = config('api.jwt_alg') ?? $this->jewAlg;
    }
    public function createAccessToken(int $uid):string
    {
        return $this->createToken($uid, $this->jwtExp);
    }

    public function createRefreshToken(int $uid):string
    {
        return $this->createToken($uid, $this->jwtRefreshExp);
    }

    protected function createToken(int $uid, int $exp):string
    {
        $time = time();

        $playLoad = [
            'iss' => '',//签发者
            'aud' => '',//接收者
            'iat' => $time,//签发时间点
            'nbf' => $time,//生效时间点
            'exp' => $time + $exp,//过期时间点
            //'sub' => '',//所面向的用户
            //'jti' => '',//唯一标识
            //自定义数据
            'data'=>[
                'uid' => $uid,
            ]
        ];

        //签发JWT token
        $token = JWT::encode($playLoad, $this->jwtKey, $this->jewAlg);
        return $token;
    }

    /**
     * 验证jwt token
     * @param string $token
     * @return \stdClass
     * @throws ApiException
     */
    public function checkToken(string $token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtKey, $this->jewAlg));
            return $decoded;
        }
        catch (Exception $e)
        {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * 解包jwt token
     * @param string $token
     * @return \stdClass
     * @throws ApiException
     */
    public function decodeToken(string $token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtKey, $this->jewAlg));
            return $decoded;
        }
        catch (Exception $e)
        {
            throw new ApiException($e->getMessage());
        }
    }
}