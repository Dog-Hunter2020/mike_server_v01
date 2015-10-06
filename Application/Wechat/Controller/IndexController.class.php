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
    private $keyTestResult='test_result';


	public function _initialize(){
        $this->wechatWebController=new WechatWebController();
	}


    public function testForTeacher(){
    	//判断小测是否进行中
    	    $openid=I('openid');
            $identify=I('identify');
            $this->wechatWebController=new WechatWebController();
    		$testStatus=$this->wechatWebController->isTestOn($openid,$identify);
            switch($testStatus['status']){
                case $this->TEST_STATUS_CLOSED:
                    testResult();
                    break;
                case $this->TEST_STATUS_NOTEXIST:
                    createTest();
                    break;
                case $this->TEST_STATUS_ON:
                    testIsOn();
                    break;
                default:
                    $this->display("<h1>未知错误！</h1>");
            }
    }


    public function testForStudent(){
        //判断学生是否已经提交小测
    }

    public function countForTeacher(){
//        判断点名是否进行中
    }

    public function countForStudent(){
//        判断学生是否已经点过名
    }

//    这里是只需要跳转页面且需要传递参数的函数
    public function bind(){
        $this->display('/bind');
    }

    public function createTest(){
//        跳转到创建小测
        $courselist=$this->wechatWebController->getCourseList();
//        $this->assign('courselist',$courselist);
        $this->assign('openID',I('openID'));
        $this->assign('random',I('random'));
        $this->display('/teacher_create_test');
    }
    public function testIsOn(){
//        正在进行
        $this->display('/isTesting');
    }

    public function radioTest(){
//        跳转到单选
        $this->display('/radio_test');
    }

    public function testResult(){
//        跳转到小测结果
        $this->display('/teacher_test_result');
    }

    public function multipleTest(){
//        跳转到多选
        $this->display('/radio_test');
    }

    public function testDetail(){
//        学生跳转到小测详情
        $this->display('/test_detail');
    }

    public function rollCall(){
//        老师开始创建点名
        $this->display('roll_call_teacher');
    }

    public function rollCallResult(){
//        查看点名结果
        $this->display('roll_call_result');
    }

    public function rollCallDetail(){
//        学生查看点名详情
        $this->display('/roll_call');
    }

    public function announceForTeacher(){
//        老师发布公告
        $this->display('/announce_teacher');
    }

    public function announceForStudent(){
//        学生看公告
        $this->display('/announce_student');
    }

    public function singleAnnounce(){
//        单条公告
        $this->display('announce_single');
    }
//    跳转函数End


//  这里是需要获得数据的函数
    public function getCourseList(){
//        获得课程列表，传入openid
        print_r(json_encode($this->wechatWebController->getCourseList(I('openid'))));
    }

    public function createOtherTest(){
//        创建其他类型的小测，会传入参数
        echo I('courseId');
    }

    public function createRadioTest(){
//        创建单选类型小测，会传入参数
        print_r($_POST);
    }

    public function createMultipleTest(){
//        创建多选类型小测，会传入参数
        print_r($_POST);
    }

    public function getTestResult(){
//        获得小测结果，会传入参数
        print_r('234');
    }

    public function endTest(){
//        结束小测，传入小测id
    }

    public function getTestDetail(){
//        学生获得小测详情，传入小测id
    }

    public function submitTest(){
//        学生提交小测答案，会传入参数
    }

    public function createRollCall(){
//        老师创建点名
    }

    public function endRollCall(){
//        老师结束点名
    }

    public function getRollCallResult(){
//        获得点名结果
    }

    public function getRollCallDetail(){
//        学生获得点名详情
    }

    public function submitRollCall(){
//        学生提交点名位置
    }

    public function createAnnounce(){
//        老师发布公告
    }

    public function getAnnounces(){
//        学生获得最近公告
    }

//    获取数据函数End
}