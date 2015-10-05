<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/6
 * Time: 上午12:25
 */

require("assert.php");
function test($number){

    assert(false,"hello");print("test finish $number<5");
}

test(8);