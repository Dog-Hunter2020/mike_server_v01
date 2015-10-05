<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/6
 * Time: 上午12:08
 */




assert_options(ASSERT_ACTIVE,1);//user assert()

assert_options(ASSERT_WARNING,1);//如果为1则为每个断言产生一个php warning

//assert_options(ASSERT_CALLBACK,null);//断言失败时调用回调函数，后面就是回调函数的名字

assert_options(ASSERT_BAIL,0);//断言失败时停止执行

assert_options(ASSERT_QUIET_EVAL,1);//在断言表达式求值时禁用 error_reporting

assert_options(ASSERT_CALLBACK,"handel_assert");

function handel_assert($file,$line_number,$code,$desc=null){

    echo func_get_args()[3];

    echo "Assert Error:  File:$file\nLine Number:$line_number\nCode:$code\n";

    if($desc){

        echo $desc;
    }


}

