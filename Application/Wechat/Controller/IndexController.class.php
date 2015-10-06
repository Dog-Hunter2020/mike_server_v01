<?php
namespace Wechat\Controller;
use Think\Controller;
//控制微信页面的跳转
class IndexController extends Controller {
    public function index(){
        $this->show(C('ERROR_USERNAME_PASSWORD_WRONG'));
    }
}