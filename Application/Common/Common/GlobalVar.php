<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/8
 * Time: 上午10:56
 */

function addGlobalVar($name,$value){

    $GLOBALS[$name]=$value;

}


addGlobalVar("tokenList",array());
