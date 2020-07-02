<?php


namespace makcent\wxapp;

use PHPUnit\Framework\Constraint\Count;
use yii\base\BaseObject;

class Helper extends BaseObject
{
    public $appid;
    public $secret;
    protected static $ACCESS_TOKEN;
    public static $WX_HTTP = 'https://api.weixin.qq.com';

    /**
     * 调用实现方法
     * @param string $method
     * @param $params
     * @return mixed
     */
    public function call($method = '', array $params = [])
    {
        list($classname, $action) = explode('.', $method);

        $classname = "\\makcent\\wxapp\\{$classname}";
        if (class_exists($classname)) {
            return call_user_func_array([new $classname([
                'appid' => $this->appid,
                'secret' => $this->secret
            ]), $action], $params);
        }
        return parent::__call($name, $params);
    }

    /**
     * 格式化微信小程序响应
     * @param $json
     * @return array
     */
    public static function policy($json)
    {
        if (!isset($json['errcode']) || $json['errcode'] != 0) {
            return static::ret(1, 'fail', $json['result']);
        }
        return static::ret(0, 'ok', $json['result']);

    }


    /**
     * 单个请求
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function curl($url, $params = array())
    {
        $result = static::mutil(array(array( 'url' => $url, 'params' => $params)));
        return $result[0];
    }

    /**
     * 批量爬虫
     * @param $request
     * @return mixed
     */
    public static function mutil($request)
    {
        $curl_mutil = array();
        foreach ($request as $key => $param) {
            $curl_mutil[$key] = curl_init();
            curl_setopt($curl_mutil[$key], CURLOPT_URL, $param['url']);
            curl_setopt($curl_mutil[$key], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_mutil[$key], CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl_mutil[$key], CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl_mutil[$key], CURLOPT_SSL_VERIFYHOST, FALSE);
            //curl_setopt($curl_mutil[$key], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($curl_mutil[$key], CURLOPT_TIMEOUT, 30);
            if (isset($param['params']) && !empty($param['params'])) {
                curl_setopt($curl_mutil[$key], CURLOPT_POST, 1);
                curl_setopt($curl_mutil[$key], CURLOPT_POSTFIELDS, $param['params']);
            }
        }

        //添加批量资源
        $mutil_init = curl_multi_init();
        foreach ($curl_mutil as $key => $curl){
            curl_multi_add_handle($mutil_init, $curl);
        }

        //关联资源ID
        foreach ($curl_mutil as $key => $value) {
            $curl_mutil[(int)$value] = $key;
        }

        $active = null;
        do {
            while(($mrc = curl_multi_exec($mutil_init,$active)) == CURLM_CALL_MULTI_PERFORM);
            if($mrc != CURLM_OK){
                break;
            }
            while ($done = curl_multi_info_read($mutil_init)) {
                if (curl_errno($done['handle'])) {
                    $res = static::ret(1,'curl错误:' . curl_error($done['handle']));
                } else {
                    if (200 !== curl_getinfo($done['handle'], CURLINFO_HTTP_CODE)) {
                        $res = static::ret(1,'curl错误:' . curl_error($done['handle']));
                    }else{
                        $res = curl_multi_getcontent($done['handle']);
                        $json = json_decode($res, true);
                        $res = static::ret(0,'获取成功',is_array($json) ? $json : $res) ;
                    }
                }
                curl_multi_remove_handle($mutil_init, $done['handle']);
                curl_close($done['handle']);
                $request[$curl_mutil[(int)$done['handle']]] = $res;
            }

            if ($active > 0) {
                curl_multi_select($mutil_init);
            }
        }while($active);

        curl_multi_close($mutil_init);

        return $request;
    }

    /**
     * 请求响应格式
     * @param $status
     * @param $message
     * @param array $result
     * @return array
     */
    public static function ret($status, $message, $result = [])
    {
        return ['status' => $status, 'message' => $message, 'result' => $result];
    }

}