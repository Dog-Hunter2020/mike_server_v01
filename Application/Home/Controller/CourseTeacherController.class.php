<?php

/*
 * 课程老师相关操作
 */

namespace Home\Controller;
use Think\Controller;
class CourseTeacherController extends \Think\Controller{
    /*
     * description:为课程添加老师
     * return:bool
     */

    public function setTeacher($courseId,$userId){

    }

    public function unsignTeacher($courseId,$userId){

    }

    /*
     * description:为某门课程添加助教
     * return:bool
     */
    public function addAssistant($courseId,$userIdList){

    }

    /*
     * description:为某门课程删除助教
     * return:bool
     *
     */

    public function deleteAssistant($courseId,$userName){

    }

}