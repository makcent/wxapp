<?php


namespace makcent\wxapp;


class Auth extends Helper
{
    /**
     * 登录凭证校验
     * @param $js_code
     * @return array
     */
    public function  code2Session($js_code)
    {
        return static::policy(static::curl(static::$WX_HTTP.'/sns/jscode2session?'.http_build_query([
            'appid' => $this->appid,
            'secret' => $this->secret,
            'js_code' => $js_code,
            'grant_type' => 'authorization_code',
        ])));
    }

    /**
     * 获取小程序全局唯一后台接口调用凭据（access_token）
     * @return array
     */
    public function getAccessToken()
    {
        return static::policy(static::curl(static::$WX_HTTP.'/cgi-bin/token?'.http_build_query([
            'appid' => $this->appid,
            'secret' => $this->secret,
            'grant_type' => 'client_credential',
        ])));
    }

    /**
     * 获取该用户的 UnionId
     * @param $access_token
     * @param $openid
     * @return array
     */
    public function getPaidUnionId($access_token, $openid)
    {
        return static::policy(static::curl(static::$WX_HTTP.'/wxa/getpaidunionid?'.http_build_query([
            'access_token' => $access_token,
            'openid' => $openid,
        ])));
    }



}