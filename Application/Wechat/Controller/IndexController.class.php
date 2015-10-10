<?php
namespace Wechat\Controller;
use Think\Controller;

//控制微信页面的跳转
class IndexController extends Controller {
	private $wechatWebController;
    private $TEST_STATUS_ON=1;
    private $TEST_STATUS_CLOSED=-1;
    private $TEST_STATUS_NOTEXIST=0;

    private $TEST_OVERTIME=1;
    private $TEST_NOTOVERTIME=-1;

    private $SUBMITTED=1;
    private $UNSUBMITTED=-1;

    private $END_PAGE_TYPE_NAME='type_name';

    private $ACTION_TEST='小测';
    private $ACTION_COUNT='点名';
    private $ACTION_BIND='绑定';
    private $ACTION_SIGN='签到';
    private $ACTION_SUBMIT='提交';


    private $teacherCreateTestUrl,$teacherTestOnUrl,$teacherTestResultUrl;
    private $keyTestResult='test_result';
    private $keyOpenId='openID';
    private $keyQuizId='quizID';
    private $keyIdentify='random';

    public function index(){
//        $spider=new \Common\Extend\NJU\spider\NJUSpider('131250043','19941026');
//        print_r($spider->getUserinfo());
//        $this->register();
//        $wechatUserModel=M('wechat_user');
//        echo $wechatUserModel->where(array('user_id'=>4))->delete();
    }


    public function wechatAPI(){
        $wechatObj = new \Wechat\Common\Extend\WechatCallbackapiTest();
        if(isset($_GET["echostr"])){
            $wechatObj->valid();
        }else{
            $wechatObj->responseMsg();
        }
    }


	public function _initialize(){
        $this->wechatWebController=new WechatWebController();
	}

//逻辑判断和跳转
    public function testForTeacher(){
    	//判断小测是否进行中
    	    $openid=I($this->keyOpenId);
            $identify=I($this->keyIdentify);
            $this->wechatWebController=new WechatWebController();
            $this->wechatWebController->judgeTest($identify);
    		$testStatus=$this->wechatWebController->isTestOn($openid,$identify);
            switch($testStatus['status']){
                case $this->TEST_STATUS_CLOSED:
                    $this->testResult($openid,$identify);
                    break;
                case $this->TEST_STATUS_NOTEXIST:
                    $this->createTest($openid,$identify);
                    break;
                case $this->TEST_STATUS_ON:
                    $this->testIsOn($openid,$identify);
                    break;
                default:
                    $this->error('操作失败，请稍候再试~');

            }
    }


    public function testForStudent(){
        //判断学生是否已经提交小测
        $openid=I($this->keyOpenId);
        $quizid=I($this->keyQuizId);
        $this->wechatWebController=new WechatWebController();
        $submitStatus=$this->wechatWebController->isSubmitted($openid,$quizid);
        $testStatus=$this->wechatWebController->isTestOvertime($quizid);
        switch($testStatus){
            case $this->TEST_OVERTIME:
                $this->wechatWebController->endTestById($quizid);
                $this->assign($this->END_PAGE_TYPE_NAME,$this->ACTION_TEST);
                $this->showEndPage();
                break;
            case $this->TEST_NOTOVERTIME:
                if($submitStatus['status']==$this->SUBMITTED){
                    $this->studentTestSubmitted();
                    break;
                }elseif($submitStatus['status']=$this->UNSUBMITTED){
                    $this->testDetail($openid,$quizid);
                    break;
                }else{
                    $this->error('操作失败，请稍后再试~');
                    break;
                }
            default:
                $this->error('操作失败，请稍候再试~');
                break;
        }

    }

    public function countForTeacher(){
//        判断点名是否进行中
        $openid=I($this->keyOpenId);
        $identify=I($this->keyIdentify);
        $this->wechatWebController=new WechatWebController();
        $testStatus=$this->wechatWebController->isTestOn($openid,$identify);
        switch($testStatus['status']){
            case $this->TEST_STATUS_CLOSED:
                $this->rollCallResult($openid,$identify);
                break;
            case $this->TEST_STATUS_NOTEXIST:
                $this->rollCall($openid,$identify);
                break;
            case $this->TEST_STATUS_ON:
                $this->rollcallIsOn($openid,$identify);
                break;
            default:
                $this->error('操作失败，请稍候再试~');

        }
    }

    public function countForStudent(){
//      判断学生是否已经点过名
        //判断学生是否已经提交小测
        $openid=I($this->keyOpenId);
        $quizid=I($this->keyQuizId);
        $this->wechatWebController=new WechatWebController();
        $submitStatus=$this->wechatWebController->isSubmitted($openid,$quizid);
        $testStatus=$this->wechatWebController->isTestOvertime($quizid);

        switch($testStatus){
            case $this->TEST_OVERTIME:
                $this->wechatWebController->endTestById($quizid);
                $this->assign($this->END_PAGE_TYPE_NAME,$this->ACTION_COUNT);
                $this->showEndPage();
//                $this->show("<h style='text-align: center;margin-top: 10px'>点名已结束</h>");
                break;
            case $this->TEST_NOTOVERTIME:
                if($submitStatus['status']==$this->SUBMITTED){
                    $this->assign('action',$this->ACTION_SIGN);
                    $this->showSuccessPage();
//                    $this->show("<h1 style='text-align: center;margin-top: 20px'>已成功签到</h1>");
                    break;
                }elseif($submitStatus['status']=$this->UNSUBMITTED){
                    $this->rollCallDetail($openid,$quizid);
                    break;
                }else{
                    $this->error('操作失败，请稍后再试~');
                    break;
                }
            default:
                $this->error('操作失败，请稍候再试~');
                break;
        }
    }

    public function announceForTeacher(){
        $openid=I($this->keyOpenId);
        $this->announceTeacher($openid);
    }

    public function announceForStudent(){
        $openid=I($this->keyOpenId);
        $this->announceStudent($openid);
    }


    public function bind(){
        $openid=I($this->keyOpenId);
        $this->toBindPage($openid);
    }

    public function register(){
        $this->wechatWebController=new WechatWebController();
        $bindStatus=$this->wechatWebController->bind(I('identify_id'),I('password'),I($this->keyOpenId));
        if($bindStatus==WechatController::$SUCCESS){
            $this->assign('action',$this->ACTION_BIND);
            $this->showSuccessPage();
        }elseif($bindStatus==WechatController::$JW_OVERTIME){
            $this->error('不好意思，教务网好像崩溃了，请稍后再试~');
        }else{
            $this->error('绑定失败，请确认您的账号和密码是否正确~');
//            $this->show("<h1 style='text-align: center;margin-top: 10px'>绑定失败</h1>");
        }
    }

    public function announceSingleForStudent(){
        $courseName=I('course_name');
        $teacherName=I('teacher_name');
        $posttime=I('posttime');
        $content=I('content');
        $this->singleAnnounce($courseName,$posttime,$teacherName,$content);
    }


	public function html(){
		$str = I('html');
		$this->display('/'.$str);
	}
	
	

//    这里是只需要跳转页面且需要传递参数的函数
    public function toBindPage($openID){
        $this->assign('openID',$openID);
        $this->display('/bind');
    }

    public function showEndPage(){
        $this->display('/end_page');
    }

    public function showSuccessPage(){
        $this->display('/success_page');
    }

    public function studentTestSubmitted(){
        $this->assign('action',$this->ACTION_SUBMIT);
        $this->showSuccessPage();
//        $this->show("<h1 style='text-align: center;margin-top: 20px'>已提交答案</h1>");
    }

    public function createTest($openID, $identify){
//        跳转到创建小测
        $this->assign('openID',$openID);
        $this->assign('quizID',$identify);
        $this->display('/teacher_create_test');
    }
    public function testIsOn($openID, $identify){
//        正在进行
        $this->assign('openID',$openID);
        $this->assign('quizID',$identify);
        $this->display('/isTesting');
    }

    public function radioTest(){
//        跳转到单选
        $this->display('/radio_test');
    }

    public function testResult($openID, $identify){
//        跳转到小测结果
        $this->assign('openID',$openID);
        $this->assign('quizID',$identify);
        $this->display('/teacher_test_result');
    }

    public function multipleTest(){
//        跳转到多选
        $this->display('/radio_test');
    }

    public function testDetail($openID,$quizid){
//      学生跳转到小测详情
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizid);
        $this->display('/test_detail');
    }

    public function rollcallIsOn($openID, $identify){
//        正在进行
        $this->assign('openID',$openID);
        $this->assign('quizID',$identify);
        $this->display('/isCounting');
    }

    public function rollCall($openID, $identify){
//        老师开始创建点名
        $this->assign('openID',$openID);
        $this->assign('quizID',$identify);
        $this->display('/roll_call_teacher');
    }

    public function rollCallResult($openID, $identify){
//        查看点名结果
        $this->assign('openID',$openID);
        $this->assign('quizID',$identify);
        $this->display('/roll_call_result');
    }

    public function rollCallDetail($openID, $quizID){
//        学生查看点名详情
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizID);
        $this->display('/roll_call');
    }

    public function announceTeacher($openID){
//        老师发布公告
        $this->assign('openID',$openID);
        $this->display('/announce_teacher');
    }

    public function announceStudent($openID){
//        学生看公告
        $this->assign('openID',$openID);
        $this->display('/announce_student');
    }

    public function singleAnnounce($course_name, $posttime, $teacher_name, $content){
//        单条公告
        $this->assign('course_name',$course_name);
        $this->assign('posttime',$posttime);
        $this->assign('teacher_name',$teacher_name);
        $this->assign('content',$content);
        $this->display('/announce_single');
    }
//    跳转函数End


//  这里是需要获得数据的函数
    public function getCourseList(){
//        获得课程列表，传入openid
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->getCourseList(I('openid'))));
    }

    public function createOtherTest(){
//        创建其他类型的小测，会传入参数
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->createTest($_POST['test_type'],$_POST['course_id'],$_POST['openid'],$_POST['test_title'],$_POST['option_count'],$_POST['test_duration'],$_POST['test_content'],$_POST['identify'])));
    }

    public function createRadioTest(){
//        创建单选类型小测，会传入参数
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->createTest($_POST['test_type'],$_POST['course_id'],$_POST['openid'],$_POST['test_title'],$_POST['option_count'],$_POST['test_duration'],$_POST['test_content'],$_POST['identify'])));
    }

    public function createMultipleTest(){
//        创建多选类型小测，会传入参数
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->createTest($_POST['test_type'],$_POST['course_id'],$_POST['openid'],$_POST['test_title'],$_POST['option_count'],$_POST['test_duration'],$_POST['test_content'],$_POST['identify'])));
    }

    public function getTestResult(){
//        获得小测结果，会传入参数
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->getTestResult($_POST['openid'],$_POST['identify'])));
    }

    public function endTest(){
//        结束小测，传入小测id
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->endTest($_POST['identify'])));
    }

    public function getTestDetail(){
//        学生获得小测详情，传入小测id
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->getTest($_POST['openid'],$_POST['quiz_id'])));

    }

    public function submitTest(){
//        学生提交小测答案，会传入参数
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->submitTest($_POST['test_id'],$_POST['submit_content'],$_POST['openid'])));

    }

    public function createRollCall(){
//        老师创建点名
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->beginCount($_POST['course_id'],$_POST['openid'],$_POST['location'],$_POST['duration'],$_POST['identify'])));

    }

    public function endRollCall(){
//        老师结束点名
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->endCount($_POST['openid'],$_POST['identify'])));

    }

    public function getRollCallResult(){
//        获得点名结果
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->getTestResult($_POST['openid'],$_POST['identify'])));

    }

    public function getRollCallDetail(){
//        学生获得点名详情
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->getTest($_POST['openid'],$_POST['quiz_id'])));

    }

    public function submitRollCall(){
//        学生提交点名位置
        $this->wechatWebController=new WechatWebController();
        print_r(json_encode($this->wechatWebController->submitCount($_POST['count_id'],$_POST['location'],$_POST['openid'])));

    }

    public function createAnnounce(){
//        老师发布公告
        $this->wechatWebController=new WechatWebController();
        print_r($this->wechatWebController->setAnnounce($_POST['openid'],$_POST['course_id'],$_POST['content']));

    }

    public function getAnnounces(){
//        学生获得最近公告
        $this->wechatWebController=new WechatWebController();
        $result=$this->wechatWebController->getAnnounce($_POST['openid']);
        print_r(json_encode($result));
//        if($result['status']==0){
//            $this->error('获取失败，请确认您是否已绑定教务网账号~');
//        }
    }

//    获取数据函数End
}