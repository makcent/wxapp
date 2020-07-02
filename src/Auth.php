<?php


namespace makcent\wxapp;


class Auth extends Helper
{
    /**
     * 登录凭证校验
     * @param $js_code
     * @return array
     */
    public static function  code2Session($js_code)
    {
        return static::policy(static::curl('https://api.weixin.qq.com/sns/jscode2session?'.http_build_query([
            'appid' => static::$APPID,
            'secret' => static::$SECRET,
            'js_code' => $js_code,
            'grant_type' => 'authorization_code',
        ])));
    }




}