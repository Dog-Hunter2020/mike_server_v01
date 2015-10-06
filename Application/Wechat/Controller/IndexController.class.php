<?php
namespace Wechat\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$this->assign('test','hehe');
    	$this->display('/teacher_create_test/?s=/openID/123');
    }
}