<?php
namespace Wechat\Controller;
use Think\Controller;
class WechatController extends Controller {
    public function index(){
       print_r($this->getUsersInfoByCourse(1234,1,1));
    }

    public static $SUCCESS=1;
    public static $FAIL=0;
    public static $ERROR=-1;

    public static $OVERTIME=1;
    public static $NOTOVERTIME=-1;

    public static $SUBMITTED=1;
    public static $UNSUBMITTED=-1;

    public static $QUIZ_TYPE_COUNT='count';
    public static $QUIZ_TYPE_CHOICE='radio';
    public static $QUIZ_TYPE_MULTCHOICE='multiple';
    public static $QUIZ_TYPE_OTHER='other';

    public static $NOT_FIND_INFO=-1;

    public static $RADIO='radio';
    public static $MULTIPLE='multiple';
    public static $OTHER='other';


    //根据openid获取用户信息
    protected function getUserinfoByOpenid($openid){
    	$wechatUserModel=M('wechat_user');
        $condition['openid']=$openid;
        $the_one=$wechatUserModel->where($condition)->find();
        if(!$the_one){
            return WechatController::$FAIL;
        }
        $user_id=$wechatUserModel->find(array('openid'=>$openid))['id'];
        if($user_id){
        	$userModel=M('user');
            $userinfo=$userModel->where(array("id"=>$user_id))->find();
            return $userinfo;
        }else{
            //未找到用户
            return 'not find the user';
        }
    }

    //写入持久化表
//    function persistent($quiz_id){
//
//    }

    //清空临时表
    protected  function clearTemp($quiz_id){
        $tempModel=M('quiz_temp');
        $tempModel->delete(array('quiz_id'=>$quiz_id));
    }
    //查询临时表获取小测
//    function getQuiz($openid){
//        require_once(dirname(dirname(__FILE__)).'/controller/QuizTempController.php');
//    }


    //通过course信息获取用户，$time为时间戳,$course_id为课程编号
    protected  function getUsersInfoByCourse($course_id,$time,$teacher){
        $courseModel=new CourseController();
        $courses=$courseModel->findClassByTime($course_id,$time,$teacher);
        $users=array();
        foreach($courses as $k=>$course){
            $relationModel=M('user_course_relation');
            $userModel=M('user');
            $relations=$relationModel->select(array('course_id'=>$course['id']));
            foreach($relations as $key=>$value){
                $the_one=$userModel->find(array('id'=>$value['user_id']));
                if(!$the_one){
                    continue;
                }
                $users[]=$userModel->find(array(),array('id'=>$value['user_id']))[0];
            }
        }
        return $users;
    }
    //根据Id获取openid
    protected function getOpenidById($id){
        $wechatUserModel=M('wechat_user');
        $result=array();
        $user=$wechatUserModel->find(array('user_id'=>$id));
        if(!$user){
            return  WechatController::$FAIL;
        }
        return $user['openid'];
    }

    //根据自增id获取用户
    protected function getUsersByCourseid($course_id){
        $relationModel=M('user_course_relation');
        $userModel=M('user');

        $relations=$relationModel->select(array('course_id'=>$course_id));
        $users=array();
        foreach($relations as $key=>$value){
            $the_one=$userModel->find(array(),array('id'=>$value['user_id']));
            if(!$the_one){
                continue;
            }
            $users[]=$userModel->find(array('id'=>$value['user_id']));
        }
        return $users;
    }

    public function timeToMysql($time){
        return date('Y-m-d H:i:s',$time);
    }

    public function mysqlToTime($mysqlDate){
        return strtotime($mysqlDate);
    }

    //判断小测是否到时，1到时，-1未到时,0失败
    protected  function isTestOvertime($quiz_id){
        $quizModel=M('quiz');
        $quiz=$quizModel->find(array(),array('id'=>$quiz_id));
        if(!$quiz){
            return WechatController::$ERROR;
        }
        $endtime=strtotime($quiz['endtime']);
        if($endtime<=time()){
            return WechatController::$OVERTIME;
        }else{
            return WechatController::$NOTOVERTIME;
        }
    }

    protected function clearQuizFromTemp($quiz_id){
        $quizTempAnswerModel=M('quiz_temp_model');
        if($quizTempAnswerModel->delete(array('quiz_id'=>$quiz_id))){
            return WechatController::$SUCCESS;
        }else{
            return WechatController::$FAIL;
        }

    }

}