<?php

/*
 * 课堂提问相关操作
 */

namespace Home\Controller;
use Think\Controller;
class CourseQuestionController extends \Think\Controller{
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
        $data['time'] = NOW_TIME;
        $ClassQuestionModel->add($data);
    }

    public function deleteQuestion($userId,$questionId){

    }
    /*
     * description:获取某个人的课程
     * return:array()
     */

    public function getQuestionBelongTo($userId){

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

    }

    /*
     * description:获取该课程的历史问题(已经失效的问题)
     * return:array(question)
     *
     * question的定义参见getCurrentQuestion方法
     */
    public function getHistoryQuestions($courseId){

    }
    /*
     * description:某个用户回答某个问题
     * return:bool
     */

    public function  answerQuestions($questionId,$studentId, $studentName, $answer){

    }


}