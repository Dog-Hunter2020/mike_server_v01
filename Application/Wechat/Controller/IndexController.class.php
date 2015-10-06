<?php
namespace Wechat\Controller;
use Think\Controller;
//控制微信页面的跳转
class IndexController extends Controller {
    public function index(){
    	$this->assign('test','hehe');
    	$this->display('/teacher_create_test/?s=/openID/123');
    }
}