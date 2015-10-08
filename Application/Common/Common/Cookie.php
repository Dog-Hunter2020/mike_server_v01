<?php

/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/8
 * Time: 下午12:23
 */
class Cookie
{
    /*
     * name 必需。规定 cookie 的名称。
     * value 必需。规定 cookie 的值。
     * expire 可选。规定 cookie 的有效期。
     * path 可选。规定 cookie 的服务器路径。
     * domain 可选。规定 cookie 的域名。
     * secure 可选。规定是否通过安全的 HTTPS 连接来传输 cookie。
     */

    public static function sendCookie($name, $value, $expire=null, $path=null, $domain=null, $secure=null){

        setcookie($name, $value, $expire, $path, $domain, $secure);

    }

}