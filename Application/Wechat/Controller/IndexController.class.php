<?php
namespace Wechat\Controller;
use Think\Controller;
//控制微信页面的跳转
class IndexController extends Controller {
	private $wechatWebController=new wechatWebController();
	public function __init(){

	}


    public function index(){
    	$this->assign('test','hehe');
    	$this->display('/teacher_create_test/?s=/openID/123');
    }

    public function testForTeacher(){
    	//judge is the test is on
    		$openid=I('openid');
    		$courselist=$this->wechatWebController->getCourseList($openid);
    		$this->assign('courselist',$courselist);
    		$this->display('/teacher_create_test');	
    	//
    }
}