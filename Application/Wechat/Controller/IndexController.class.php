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

    private $teacherCreateTestUrl,$teacherTestOnUrl,$teacherTestResultUrl;
    private $keyTestResult='test_result';
    private $keyOpenId='openID';
    private $keyQuizId='quizID';
    private $keyIdentify='identify';


	public function _initialize(){
        $this->wechatWebController=new WechatWebController();
	}

//逻辑判断和跳转
    public function testForTeacher(){
    	//判断小测是否进行中
    	    $openid=I($this->keyOpenId);
            $identify=I($this->keyIdentify);
    		$testStatus=$this->wechatWebController->isTestOn($openid,$identify);
            switch($testStatus['status']){
                case $this->TEST_STATUS_CLOSED:
                    $this->testResult();
                    break;
                case $this->TEST_STATUS_NOTEXIST:
                    $this->createTest();
                    break;
                case $this->TEST_STATUS_ON:
                    $this->testIsOn();
                    break;
                default:
                    $this->display("<h1>未知错误！</h1>");

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

        }


        switch($submitStatus['status']){

        }


    }

    public function countForTeacher(){
//        判断点名是否进行中
    }

    public function countForStudent(){
//        判断学生是否已经点过名
    }

    public function announceForTeacher(){

    }

    public function announceForStudent(){

    }



//    这里是只需要跳转页面且需要传递参数的函数
    public function bind($openID){
        $this->assign('openID',$openID);
        $this->display('/bind');
    }

    public function studentTestClosed(){
        $this->display('/end_page');
    }

    public function studentTestSubmitted(){
        $this->show("<h style='text-align: center;margin-top: 10px'>已提交答案</h>");
    }

    public function createTest($openID, $quizID){
//        跳转到创建小测
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizID);
        $this->display('/teacher_create_test');
    }
    public function testIsOn($openID, $quizID){
//        正在进行
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizID);
        $this->display('/isTesting');
    }

    public function radioTest(){
//        跳转到单选
        $this->display('/radio_test');
    }

    public function testResult($openID, $quizID){
//        跳转到小测结果
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizID);
        $this->display('/teacher_test_result');
    }

    public function multipleTest(){
//        跳转到多选
        $this->display('/radio_test');
    }

    public function testDetail($openid,$quizid){
//      学生跳转到小测详情
        $result=array(
            $this->keyOpenId=>$openid,
            $this->keyQuizId=>$quizid
        );
        $this->assign($result);
        $this->display('/test_detail');
    }

    public function rollCall($openID, $quizID){
//        老师开始创建点名
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizID);
        $this->display('roll_call_teacher');
    }

    public function rollCallResult($openID, $quizID){
//        查看点名结果
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizID);
        $this->display('roll_call_result');
    }

    public function rollCallDetail($openID, $quizID){
//        学生查看点名详情
        $this->assign('openID',$openID);
        $this->assign('quizID',$quizID);
        $this->display('/roll_call');
    }

    public function announceForTeacher($openID){
//        老师发布公告
        $this->assign('openID',$openID);
        $this->display('/announce_teacher');
    }

    public function announceForStudent($openID){
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