<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/10
 * Time: 下午2:20
 */

namespace Home\Controller;

use Think\Controller;
//测试
class IndexController extends Controller{
    public function index($post,$haha){
        print_r(json_decode($post,true));
        print_r(json_encode(array(1,"heh")));
        echo $haha;
    }

} 