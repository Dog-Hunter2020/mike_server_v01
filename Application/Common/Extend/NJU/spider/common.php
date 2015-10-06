<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/1/27
 * Time: 下午9:16
 */
namespace Common\Extend\NJU\spider;

class CurlSpider{
    //访问的网址，post的数据，发送的cookie，是否需要存取返回的头信息中的cookie，http头中的reffer值
    public function curl_request($url,$post='',$cookie='', $returnCookie=0,$reffer='')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if($reffer){
            curl_setopt($curl, CURLOPT_REFERER, $reffer);
        }
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        if($returnCookie){
            curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        } else {
            return $data;//此时data中的第一个值为seesionID，即cookie
        }
    }

}

//需要实现的公共方法
interface method{
    function getCurrentClassTable();

    function getGrade();

}
