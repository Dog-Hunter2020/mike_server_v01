<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/8
 * Time: 上午10:39
 */

/*
 * description:添加一个全局变量
 *
 */
function addGlobalVar($name,$value){

    $GLOBALS[$name]=$value;

}



class Token{

    private static $instance;
    private $tokenList;

    private function  __construct(){

        /*
         * 这里如果全局变量里面没有token list 就新添加一个，这里的token是一个数据结构，保存的是登录用户的信息，具体细节参考Common/common/UserToken.php
         */

        if(!in_array("tokenList",$GLOBALS)){

            addGlobalVar("tokenList",array());

        }

        $this->tokenList=$GLOBALS['tokenList'];


    }

    public static function getInstance(){

        if(self::$instance==null){

            self::$instance=new Token();

            return self::$instance;
        }

        else{

            return self::$instance;

        }
    }


    function  addToken($userToken){

        $this->tokenList[$userToken->tokenId]=$userToken;


    }

    function setUserTokenByTokenId($tokenId,$key,$value){

        $userToken=$this->tokenList[$tokenId];
        $userToken[$key]=$value;

    }

    function deleteTokenByTokenId($tokenId){

        unset($this->tokenList[$tokenId]);

    }

    function destoryAllTokens(){

        unset($GLOBALS['tokenList']);

    }
}
