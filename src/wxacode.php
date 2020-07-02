<?php


namespace makcent\wxapp;


class wxacode extends Helper
{

    /**
     * 获取小程序二维码
     * @param $access_token
     * @param $request
     * @return array
     */
    public function createQRCode($access_token, $request)
    {
        return static::policy(static::curl(static::$WX_HTTP . '/cgi-bin/wxaapp/createwxaqrcode?'.http_build_query([
            'access_token' => $access_token,
        ]), $request));
    }


    /**
     * 获取小程序码，适用于需要的码数量较少的业务场景
     * @param $access_token
     * @param $request
     * @return array
     */
    public function get($access_token, $request)
    {
        return static::policy(static::curl(static::$WX_HTTP . '/wxa/getwxacode?'.http_build_query([
            'access_token' => $access_token,
        ]), $request));
    }

    /**
     * 获取小程序码，适用于需要的码数量极多的业务场景
     * @param $access_token
     * @param $request
     * @return array
     */
    public function getUnlimited($access_token, $request)
    {
        return static::policy(static::curl(static::$WX_HTTP . '/wxa/getwxacodeunlimit?'.http_build_query([
            'access_token' => $access_token,
        ]), $request));
    }





}