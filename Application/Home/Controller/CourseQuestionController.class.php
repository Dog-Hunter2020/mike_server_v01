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
     */
    public function signQuestion($userId,$questionDetail,$courseId){

        assert($questionDetail!=null,"question cannot be null!");

    }

    public function deleteQuestion($userId,$questionId){

    }
    /*
     * description:获取某个人的课程
     * return:array()
     */

    public function getQuestionBelongTo($userId){

    }
}