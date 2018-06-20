<?php

/**
 * @param string $url post请求地址
 * @param array $params
 * @return mixed
 */


// 获取access_token
// 小程序、公众号获取access_token的方式一样
if (!function_exists("getAccessToken")) {
    function getAccessToken($arr)
    {

        $appid = $arr['appid'];
        $appsecret = $arr['appsecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";

        //memcache读取和缓存access_token
        $mem = new Memcached();
        $mem->addServer('172.18.176.170', '11211');

        $token = $mem->get($appid);//对appid加密
        if (!empty($token)) {
            //为空说明缓存中有token，输出token
            return $token;
        } else {
            //重新获取token
            $tokenArr = json_decode(http_request($url), 1);
            if (!isset($tokenArr['errcode'])) {
                $token = $tokenArr['access_token'];
                $mem->add($appid, $token,6500);//对appid加密
                //return $mem->get('token');
                return $token;
            }

        }
        //file_put_contents(APPPATH.'token.txt',$token);

    }
}

// 应用公共文件
// curl助手函数
if (!function_exists("http_request")) {
    function http_request($url,$data='')
    {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        if(!empty($data)){
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
        $str=curl_exec($ch);
        curl_close($ch);
        return $str;

    }
}
