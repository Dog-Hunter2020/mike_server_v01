<?php
namespace Wechat\Controller;
use Think\Controller;
//控制微信页面的跳转
class IndexController extends Controller {
	private $wechatWebController;
	public function _initialize(){
        $this->wechatWebController=new WechatWebController();
	}


    public function index(){
    	$this->assign('test','hehe');
    	$this->display('/teacher_create_test');
    }

    public function testForTeacher(){
    	//judge is the test is on
    		$openid=I('openid');
    		$courselist=$this->wechatWebController->getCourseList($openid);
    		$this->assign('courselist',$courselist);
    		$this->display('/teacher_create_test');	
    	//
    }

    public function createOtherTest(){
        echo I('courseId');
    }

    public function radioTest(){
        $this->display('/radio_test');
    }

    public function createRadioTest(){
        print_r($_POST);
    }

    public function multipleTest(){
        $this->display('/radio_test');
    }

    public function createMultipleTest(){
        print_r($_POST);
    }

    public function testResult(){
        $this->display('/teacher_test_result');
    }

    public function getTestResult(){
        print_r('234');
    }
}