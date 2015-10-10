<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/5
 * Time: 下午10:50
 */

namespace Wechat\Controller;

class WechatWebController extends WechatController{
    public function index(){
        $this->getCourseList(1234);
    }

    private $result_array=array(
        'status'=>0
    );

    //关联注册
    function bind($identify_id,$password,$openid,$nickname=0,$headimgurl=0,$province=0,$city=0){

        $wechatUserModel=M('wechat_user');

        $loginController=new LoginController();
        //解析为对象
        $userRelationInfo=$loginController->login($identify_id,$password);
        if(!$userRelationInfo['userinfo']){
            return WechatController::$FAIL;
        }
        //测试教务网
        $spider=new \Common\Extend\NJU\spider\NJUSpider($identify_id,$password);
        $userinfo=$spider->getUserinfo();
        $isTimeout=$userinfo->name;//为空则超时

        $userinfo=$userRelationInfo['userinfo'];
        if($userinfo){
            $user=$wechatUserModel->where(array('openid'=>$openid))->find();
            if($user){
                $wechatUserModel->where(array('openid'=>$openid))->save(array('user_id'=>$userinfo['id'],'nickname'=>$nickname,'headimgurl'=>$headimgurl,'province'=>$province,'city'=>$city));
                if(!$isTimeout){
                    return WechatController::$JW_OVERTIME;
                }
                return WechatController::$SUCCESS;
            }
            //todo 暂时允许重复绑定
            $wechatUserModel->where(array('user_id'=>$userinfo['id']))->delete();
            if($wechatUserModel->add(array('openid'=>$openid,'user_id'=>$userinfo['id'],'nickname'=>$nickname,'headimgurl'=>$headimgurl,'province'=>$province,'city'=>$city))){
                if(!$isTimeout){
                    return WechatController::$JW_OVERTIME;
                }
                return WechatController::$SUCCESS;
            }

            if(!$isTimeout){
                return WechatController::$JW_OVERTIME;
            }

            return WechatController::$FAIL;
        }else{
            return WechatController::$FAIL;
        }

    }

    //设置小测,此处的time为时间戳,此处identify为小测标识
    private function setQuiz($course_id,$teacher_id,$title,$content,$type,$posttime,$endtime,$identify){
        $quizModel=M('quiz');

        if($quizModel->where(array('identify'=>$identify))->find()){
            return $this->result_array;
        }

        $quiz_id=$quizModel->add(array('course_id'=>$course_id,'teacher_id'=>$teacher_id,'title'=>$title,'content'=>$content,'type'=>$type,'posttime'=>$posttime,'endtime'=>$endtime,'identify'=>$identify));
        $tempModel=M('quiz_temp_answer');
        //todo
        $students=$this->getUsersByCourseid($course_id);
        $wechatUserModel=M('wechat_user');
        foreach($students as $k=>$student){
            //不包含题目发布者
            if($student['id']==$teacher_id){
                continue;
            }
            $wechat_user=$wechatUserModel->where(array('user_id'=>$student['id']))->find();
            if($wechat_user){
                $openid=$wechatUserModel->where(array('user_id'=>$student['id']))->find()['openid'];
                $tempModel->add(array('quiz_id'=>$quiz_id,'user_id'=>$student['id'],'answer'=>'','openid'=>$openid,'quiz_title'=>$title,'quiz_content'=>$content,'quiz_type'=>$type));
            }else{
                continue;
            }
        }
        if($quiz_id){
            $this->result_array['status']=WechatController::$SUCCESS;

        }

        return $this->result_array;
    }

    //结束小测
    private function endQuiz($quiz_id){
        $tempModel=M('quiz_temp_answer');
        $answerModel=M('quiz_answer');
        $temp_answers=$tempModel->where(array('quiz_id'=>$quiz_id))->select();
        //添加持久化->删除临时
        foreach($temp_answers as $k=>$v){
            $answerModel->add(array(
                'id'=>0,
                'user_id'=>$v['user_id'],
                'quiz_id'=>$v['quiz_id'],
                'answer'=>$v['answer']
            ));
            $tempModel->where(array('id'=>$v['id']))->delete();
        }
        $this->result_array['status']=1;
        return $this->result_array;
    }
    //
    private function setAnswer($id,$answer){
        $tempModel=M('quiz_temp_answer');
        return $tempModel->where("id=$id")->save(array('answer'=>$answer));
    }
    //todo 获取点名，小测结果
    private function getResult($quiz_id){
        $result=array(
            'status'=>0,
            'test_type'=>0,
            'test_title'=>0,
            'test_content'=>0,
            'result'=>array(
//                array(
//                  'choice'=>'a',
//                  'count'=>0,
//                  'users'=>array()
//                ),
            ),//
            'test_nonSubmit'=>array()//未提交名单
        );
        //获取题目信息
        $quizModel=M('quiz');
        //未找到
        if(!$quizModel->where(array('id'=>$quiz_id))->find()){
            return $this->result_array;
        }

        $quizInfo=$quizModel->where(array('id'=>$quiz_id))->find();

        //获取该课程所有学生
//         require_once(dirname(dirname(__FILE__)).'/controller/UserController2.php');
//         $user_controller=new UserController2();
        $course_id=$quizInfo['course_id'];
        $nonSubmitStudents=array();
        $all_students=$this->getUsersByCourseid($course_id);//所有名单
        //todo 如果没有找到问题
        if($quizInfo){
            $result['test_type']=$quizInfo['type'];
            $result['test_title']=$quizInfo['title'];
            $result['test_content']=$quizInfo['content'];
        }

        $test_content=json_decode($quizInfo['content'],TRUE);
        $test_type=$quizInfo['type'];
        $choices=array();//存储所有选项
        if($test_type==WechatController::$QUIZ_TYPE_CHOICE or $test_type==WechatController::$QUIZ_TYPE_MULTCHOICE){
            $count=array();//存储个选项数量
            $users=array();//存储各选项用户信息
            //解析答案
            foreach($test_content as $k=>$v){
                $choices[]=$k;
                $count[$k]=0;
                $users[$k]=array();
            }

            //判断选择该choice的人员以及数量
            $quizAnswerModel=M('quiz_answer');
            $all_answers=$quizAnswerModel->where(array('quiz_id'=>$quiz_id))->select();
            $userModel=M('user');
            foreach($all_answers as $k=>$answer){
                $user_id=$answer['user_id'];
                $user=$userModel->where(array('id'=>$user_id))->find();
                //删除已提交名单，生成未提交名单
                if(!$answer['answer']){
                    $nonSubmitStudents[]=$user['name'];
                    continue;
                }
                //如果是多选，解析答案
                $answer_content_arr=explode(',',$answer['answer']);
                //通过解析用户的答案添加答案相关新信息
                foreach($choices as $key=>$choice) {
                    if (in_array($choice, $answer_content_arr)) {
                        $count[$choice]++;
                        $name = $user['name'];
                        $users[$choice][] = $name;
                    }
                }
            }

            foreach($choices as $k){
                $single_choice_info=array();
                $single_choice_info['choice']=$k;
                $single_choice_info['count']=$count[$k];
                $single_choice_info['users']=$users[$k];
                $result['result'][]=$single_choice_info;
            }

        }else{
            //问答题
            $quizAnswerModel=M('quiz_answer');
            $all_answers=$quizAnswerModel->where(array('quiz_id'=>$quiz_id))->select();
            $userModel=M('user');
            foreach($all_answers as $k=>$answer) {
                $user_id = $answer['user_id'];
                $user = $userModel->where(array('id' => $user_id))->find();
                //生成未提交名单
                if (!$answer['answer']) {
                    $nonSubmitStudents[] = $user['name'];
                    continue;
                }
                $result['result'][]=$user['name'];
            }
        }
        $result['test_nonSubmit']=$nonSubmitStudents;
        $result['status']=1;
        return $result;

    }

    function getCourseList($openid){
        $user_info=$this->getUserinfoByOpenid($openid);
        $userRelationModel=D('User');
        $courses=$userRelationModel->relation(true)->find($user_info['id'])['Course'];
        if($user_info){
            $this->result_array['status']=1;
            $this->result_array['course_count']=sizeof($courses);
            $this->result_array['courses']=$courses;
        }
        //todo 出现问题后的返回信息
        return $this->result_array;
    }


    //
    function getTest($openid,$quiz_id){
        //获取题目信息
        $quizModel=M('quiz');

        if(!$quizModel->where(array('id'=>$quiz_id))->find()){
            return $this->result_array;
        }
        $quiz_info=$quizModel->where(array('id'=>$quiz_id))->find();
        //如果没有找到问题
        if(!$quiz_info){
            return $this->result_array;
        }else{
            $this->result_array['status']=1;
        }

        $this->result_array['test_type']=$quiz_info['type'];
        $this->result_array['test_title']=$quiz_info['title'];
        $this->result_array['test_content']=$quiz_info['content'];
        //数据库存储的为json数组
        $test_content=json_decode($quiz_info['content'],TRUE);
        //获取选项个数
        if($quiz_info['type']==WechatController::$QUIZ_TYPE_CHOICE or $quiz_info['type']==WechatController::$QUIZ_TYPE_MULTCHOICE){
            $this->result_array['option_count']=sizeof($test_content);
        }else{
            $this->result_array['option_count']=0;
        }

        return $this->result_array;
    }

    function getTestByOpenid($openid){
        $result=array(
            'status'=>0,
            'tests'=>array()
        );
        //获取题目信息
        $quizTempModel=M('quiz_temp_answer');
        $quizs=$quizTempModel->where(array('openid'=>$openid))->select();
        //如果没有找到问题
        if(!$quizs){
            return $this->result_array;
        }else{
            $this->result_array['status']=1;
        }
        foreach($quizs as $key=>$value){
            $quizTempModel=array();
            $quizTempModel['test_type']=$value['quiz_type'];
            $quizTempModel['test_title']=$value['quiz_title'];
            $quizTempModel['test_content']=$value['quiz_content'];
            //获取选项个数
            if($value['quiz_type']==WechatController::$QUIZ_TYPE_CHOICE or $value['quiz_type']==WechatController::$QUIZ_TYPE_MULTCHOICE){
                //数据库存储的为json数组
                $test_content=json_decode($value['quiz_content'],TRUE);
                $quizTempModel['option_count']=sizeof($test_content);
            }else{
                $quizTempModel['option_count']=0;
            }
            $result['tests'][]=$quizTempModel;
        }
        $result['status']=1;
        return $result;
    }

    function createTest($test_type,$course_id,$openid,$test_title,$option_count,$test_duration,$test_content,$identify){
        $wechatModel=M('wechat_user');

        if(!$wechatModel->where(array('openid'=>$openid))->find()){
            return $this->result_array;
        }
        $user=$wechatModel->where(array('openid'=>$openid))->find();
        $posttime=time();
        $endtime=$posttime+$test_duration;

        $result=$this->setQuiz($course_id,$user['user_id'],$test_title,$test_content,$test_type,$this->timeToMysql($posttime),$this->timeToMysql($endtime),$identify);

        return $result;
    }

    function getTestResult($openid,$identify){
        $quizModel=M('quiz');
        if(!$quizModel->where(array('identify'=>$identify))->find()){
            return $this->result_array;
        }
        $quiz=$quizModel->where(array('identify'=>$identify))->find();
        if(!$quiz){
            return $this->result_array;
        }
        $result=$this->getResult($quiz['id']);
        return $result;
    }

    function submitTest($test_id,$submit_content,$openid){
        $quizTempModel=M('quiz_temp_answer');

        if(!$quizTempModel->where(array('quiz_id'=>$test_id,'openid'=>$openid))->find()){
            return $this->result_array;
        }

        $quiz=$quizTempModel->where(array('quiz_id'=>$test_id,'openid'=>$openid))->find();
        $result=$this->setAnswer($quiz['id'],$submit_content);
        if($result){
            $this->result_array['status']=1;
        }
        return $this->result_array;
    }

    function endTest($identify){
        $quizModel=M('quiz');
        if(!$quizModel->where(array('identify'=>$identify))->find()){
            return $this->result_array;
        }
        $quiz_info=$quizModel->where(array('identify'=>$identify))->find();
        if($quiz_info){
            return $this->endQuiz($quiz_info['id']);
        }else{
            return $this->result_array;
        }
    }

    function endTestById($quiz_id){
        $this->endQuiz($quiz_id);
    }

    //1为小测开启,-1为关闭
    function isTestOn($openid,$identify){
        $this->clearResultArray();
        $quizModel=M('quiz');
        if(!$quizModel->where(array('identify'=>$identify))->find()){
            return $this->result_array;
        }
        $quiz_id=$quizModel->where(array('identify'=>$identify))->find()['id'];
        $quizTempModel=M('quiz_temp_answer');
        if($quizTempModel->where(array('quiz_id'=>$quiz_id))->find()){
            $result=array();
            $result['status']=1;
            return $result;
        }else{
            $result=array();
            $result['status']=-1;
            return $result;
        }
    }


//--------------
    //点名，和question共用一个table,点名时location为表中的title
//    private function setCount($course_id,$teacher_id,$location,$content=0,$type=0,$posttime,$endtime,$identify){
//        $this->setQuiz($course_id,$teacher_id,$location,$content,Wechat::$QUIZ_TYPE_COUNT,$posttime,$endtime,$identify);
//    }
//
//    function endCount($count_id){
//        return $this->endQuiz($count_id);
//    }
//
//    function submitCount($count_id,$openid){
//        return $this->submitTest($count_id,1,$openid);
//    }
//--------------


    function beginCount($course_id,$openid,$location,$duration,$identify){
        return $this->createTest(WechatController::$QUIZ_TYPE_COUNT,$course_id,$openid,WechatController::$QUIZ_TYPE_COUNT,0,$duration,$location,$identify);
    }

    function endCount($openid,$identify){
        return $this->endTest($identify);
    }

    function submitCount($count_id,$location,$openid){
        return $this->submitTest($count_id,$location,$openid);
    }
//---------
    //发布公告，老师,已json化
    function setAnnounce($openid,$course_id,$content){
        $userinfo=$this->getUserinfoByOpenid($openid);

        if(!$userinfo){
            return $this->result_array;
        }
        $openids=array();
        $students=$this->getUsersByCourseid($course_id);
        foreach($students as $key=>$value){
            $openid=$this->getOpenidById($value['id']);
            if($openid){
                $openids[]=$openid;
            }
        }
        //获取课程信息
        $courseModel=M('course');
        $course=$courseModel->where(array('id'=>$course_id))->find();
        if($course){
            $course_name=$course['name'];
        }

        $user_id=$userinfo['id'];
        $announceController=new AnnounceController();
        $result=$announceController->announce($user_id,$course_id,$content);
        //群发
        $wechat_api=new \Wechat\Common\Extend\WechatCallbackapiTest();
        $wechat_api->sendMessages($openids,$course_name,$userinfo['name'],$content);

        return $result;
    }
    // 获取公告，学生
    function getAnnounce($openid){
        $userinfo=$this->getUserinfoByOpenid($openid);
        if(!$userinfo){
            return $this->result_array;
        }
        $user_id=$userinfo['id'];
        $announceController=new AnnounceController();
        return $announceController->get_user_announce_list($user_id);
    }
//--------
    //判断小测是否超时，如果超时就清空临时表,1为超时，-1为未超时，0失败
    function judgeTest($identify){
        $quizModel=M('quiz');
        $quiz=$quizModel->where(array('identify'=>$identify))->find();
        if(!$quiz){
            return WechatController::$ERROR;
        }
        $quiz_id=$quiz['id'];

        switch($this->isTestOvertime($quiz_id)){
            case WechatController::$NOTOVERTIME:
                return WechatController::$NOTOVERTIME;
                break;
            case WechatController::$OVERTIME:
                //超时则删除
                $this->endQuiz($quiz_id);
                return WechatController::$OVERTIME;
                break;
            case WechatController::$FAIL:
                return WechatController::$FAIL;
                break;
        }

    }
//-------------------------------
    //判断学生是否已经提交小测,1为提交,-1为未提交
    function isSubmitted($openid,$quiz_id){
        $tempModel=M('quiz_temp_answer');
        $quiz=$tempModel->where(array('quiz_id'=>$quiz_id,'openid'=>$openid))->find();
        if(!$quiz){
            return $this->result_array;
        }
        $answer=$quiz['answer'];
        if($answer){
            $this->result_array['status']=WechatController::$SUBMITTED;
        }else{
            $this->result_array['status']=WechatController::$UNSUBMITTED;
        }
        return $this->result_array;
    }

    private function clearResultArray(){
        $this->result_array=array(
            'status'=>0
        );
    }

}