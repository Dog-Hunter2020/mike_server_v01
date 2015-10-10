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
    public function index(){
        $this->ajaxReturn(1);
    }

} 