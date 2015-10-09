<?php

/*
 * 课堂提问相关操作
 */

namespace Home\Controller;
use Think\Controller;
use Home\Controller\CoursePostController;
class CourseQuestionController extends \Think\Controller{
    private $ClassQuestionModel = null;
    public  function __construct(){
        $this->ClassQuestionModel = M('class_question');
    }
    /*
     * description:老师布置新的问题
     * return:bool
     * para:array()
     * courseId(String), authorId(String), questionType(QuestionType), content(String), beginTime(long), lastTime(long)
     *
     * questionType:SingleChoiceQuestion|MultiChoiceQuestion|OtherQuestion
     * class_question 加一列 user_id
     */
    public function signQuestion($userId,$content,$courseId,$surviveTime){
        $ClassQuestionModel = D('class_question');
        $data['content'] = $content;
        $data['user_id'] = $userId;
        $data['course_id'] = $courseId;
        $data['survive_time'] = $surviveTime;
        $data['time'] = date('Y-m-d H:i:s',time());
        $ClassQuestionModel->add($data);
        $this->ajaxReturn($data,'JSON');
    }

    public function deleteQuestion($questionId){
        $this->ClassQuestionModel->delete($questionId);
    }
    /*
     * description:获取某个人的课程
     * return:array()
     */

    public function getQuestionBelongTo($userId){
        $condition['user_id'] = $userId;
        $classQuestionData = $this->ClassQuestionModel->where($condition)->select();
        $this->ajaxReturn($classQuestionData,'JSON');
    }

    /*
     * description:获取当前的问题(可能有多个)
     * return:array(CurrentQuestion)
     *
     * CurrentQuestion:
     * array(question,lefttime)
     *
     * question:
     * array(questionType,questionDetail)
     * 对应的type和detail分别为
     * SingleChoiceQuestion	courseId(String)	authorId(String)	questionType(QuestionType)	content(String) 	beginTime(long)	Ijoined(boolean)	choiceContents(List<String>)	correctChoice(int)	questionId(String)
     * MultiChoiceQuestion	courseId(String)	authorId(String)	questionType(QuestionType)	content(String) 	beginTime(long)	Ijoined(boolean)	choiceContents(List<String>)	correctChoices(List<int>)	questionId(String)
     * OtherQuestion	courseId(String)	authorId(String)	questionType(QuestionType)	content(String) 	beginTime(long)	Ijoined(boolean)	questionId(String)
     * 其中detail以数组的形式
     *
     * lefttime 剩余时间，以毫秒作为单位
     */
    public function getCurrentQuestions($courseId){
        $result = array();
        $condition['course_id'] = $courseId;
        $classQuestionData = $this->ClassQuestionModel->where($condition)->select();
        $now = time();
        foreach($classQuestionData as $classQuestion){
            $publishTime = $classQuestion['time'];
            $lastingTime = $now - strtotime($publishTime);
            $left_time = $classQuestion['survive_time'] - $lastingTime*1000;
            if($left_time >= 0){
                $result[] = array('content' => $classQuestion['content'],'question_id' => $classQuestion['id'], 'author_id' => $classQuestion['user_id'], 'survive_time' => $classQuestion['survive_time'], 'time' => $classQuestion['time'],'left_time' => $left_time);
            }
        }
        $this->ajaxReturn($result,'JSON');
    }
    /*
     * description:获取问题的当前统计信息
     * return:array(questionId(String), totalAnswerNum(int), correctAnswerNum(int), choiceDistribute(List<int>))
     * choiceDistribute指的是各个选项的数目
     *
     */
    public function getQuestionState($questionId){

    }
    /*
     * description:获取问题的回答统计情况
     * return:array()
     *
     * studentId(String), studentName(String), headIconUrl(String), answer(String)
     *
     */
    public function  getQuestionAnswers($questionId){
        $QuestionAnswerModel = M('question_answer');
        $condition['question_id'] = $questionId;
        $questionAnswerData = $QuestionAnswerModel->where($condition)->select();
        for($i=0;$i<count($questionAnswerData);$i++){
            $userId = $questionAnswerData[$i]['user_id'];
            $userData = CoursePostController::getUserDataById($userId);
            $questionAnswerData[$i]['user_name'] = $userData['name'];
            $questionAnswerData[$i]['icon_url'] = $userData['icon_url'];
        }
        $this->ajaxReturn($questionAnswerData,'JSON');
    }

    /*
     * description:获取该课程的历史问题(已经失效的问题)
     * return:array(question)
     *
     * question的定义参见getCurrentQuestion方法
     */
    public function getHistoryQuestions($courseId){
        $result = array();
        $condition['course_id'] = $courseId;
        $classQuestionData = $this->ClassQuestionModel->where($condition)->select();
        $now = time();
        foreach($classQuestionData as $classQuestion){
            $publishTime = $classQuestion['time'];
            $lastingTime = $now - strtotime($publishTime);
            $left_time = $classQuestion['survive_time'] - $lastingTime*1000;
            if($left_time < 0){
                $result[] = array('content' => $classQuestion['content'],'question_id' => $classQuestion['id'], 'author_id' => $classQuestion['user_id'], 'survive_time' => $classQuestion['survive_time'], 'time' => $classQuestion['time']);
            }
        }
        $this->ajaxReturn($result,'JSON');
    }
    /*
     * description:某个用户回答某个问题
     * return:bool
     */

    public function  answerQuestions($questionId,$studentId,$answer){
        //判断question有没有过期
        $now = time();
        $questionData = $this->ClassQuestionModel->find($questionId);
        $publishTime = $questionData['time'];
        $lastingTime = $now - strtotime($publishTime);
        if( $questionData['survive_time'] >= $lastingTime*1000){
            $QuestionAnswerModel = D('question_answer');
            $data['user_id'] = $studentId;
            $data['question_id'] = $questionId;
            $data['answer'] = $answer;
            $QuestionAnswerModel->add($data);
            $this->ajaxReturn(array('result' => 1),'JSON');
        }else{
            $this->ajaxReturn(array('result' => 0),'JSON');
        }


    }


}