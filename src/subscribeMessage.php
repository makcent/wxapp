<?php


namespace makcent\wxapp;


class subscribeMessage extends Helper
{
    /**
     * 发送订阅消息
     * @param $access_token
     * @param $request
     * @return array
     */
    public function send($access_token, $request)
    {
        return static::policy(static::curl(static::$WX_HTTP . '/cgi-bin/message/subscribe/send?'.http_build_query([
            'access_token' => $access_token,
        ]), $request));
    }


}