<?php

/*
 * 课堂提问相关操作
 */

namespace Home\Controller;
use Think\Controller;
class CourseRollController extends \Think\Controller{


    /*
     * description:获取最近一次点名的统计情况
     * return:RollRecord
     *
     * namingId(String)	teacherId(String)	beginTime(long)	lastTime(long)	signInNum(int)	absentNames(List<String>)	absentIds(List<String>)
     *
     */
    public function  getCurrentRollRecord($courseId){

    }

    /*
     * description:获取这门课所有的点名情况
     * return:List<RollRecord>
     *
     * RollRecord:
     * namingId(String)	teacherId(String)	beginTime(long)	lastTime(long)	signInNum(int)	absentNames(List<String>)	absentIds(List<String>)
     *
     */
    public function  getHistoryRollRecord($courseId){

    }
    /*
     * description:发布一次点名
     * lastTime以毫秒计数
     *
     * return:long (开始时间戳，一个date类型的时间可以转化为时间戳)
     */

    public function beginCallRoll($courseId, $teacherId, $lastTime){

    }

    /*
     * description:获取用户在当前点名中的签到情况
     * return:CourseSignInRecord
     *
     * CourseSignInRecord:
     * beginTime(long)	lastTime(long)	teacherId(String)	teacherName(String)	hasSignIn(boolean)
     */
    public function getCurrentSignRecord($studentId, $courseId){

    }

    /*
     * description:获取用户在该门课程下的所有签到情况
     * return:List<CourseSignInRecord>
     *
     * CourseSignInRecord:
     * beginTime(long)	lastTime(long)	teacherId(String)	teacherName(String)	hasSignIn(boolean)
     */

    public function getHistorySignRecords($studentId, $courseId){


    }
    /*
     * description:用户签到
     * return:bool
     */

    public function courseSign($studentId, $rollId){

    }



}
