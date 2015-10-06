<?php
namespace Wechat\Controller;
use Think\Controller;
//控制微信页面的跳转
class IndexController extends Controller {
	private $wechatWebController;
    private $TEST_STATUS_ON=1;
    private $TEST_STATUS_CLOSED=-1;
    private $TEST_STATUS_NOTEXIST=0;
    private $teacherCreateTestUrl,$teacherTestOnUrl,$teacherTestResultUrl;
    private $keyTestResult='test_result',

	public function __initialize(){
        $this->wechatWebController=new WechatWebController();
	}


    public function index(){
    	$this->assign('test','hehe');
    	$this->display('/teacher_create_test/?s=/openID/123');
    }

    public function testForTeacher(){
    	//judge is the test is on
    	    $openid=I('openid');
            $identify=I('identify');
            $this->wechatWebController=new WechatWebController();
    		$testStatus=$this->wechatWebController->isTestOn($openid,$identify);
            switch($testStatus['status']){
                case $this->TEST_STATUS_CLOSED:
                    $this->assign('test_result');
            }

    		$courselist=$this->wechatWebController->getCourseList($openid);
    		$this->assign('courselist',$courselist);
    		$this->display('/teacher_create_test');	
    	//
    }

    public function testForStudent(){

    }

    public function countForTeacher(){

    }

    public function countForStudent(){

    }

    public function announceForTeacher(){

    }

    public function announceForStudent(){

    }
}