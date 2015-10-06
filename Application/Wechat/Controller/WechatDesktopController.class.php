<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/6
 * Time: 下午1:03
 */

namespace Wechat\Controller;


class WechatDesktopController extends WechatController{
    private  $TOKEN_INVALID=0;
    //------------------------------------------
    //判断小测是否超时
    //判断小测是否超时，如果超时就清空临时表,1为超时，-1为未超时，0失败
    private function judgeTest($quiz_id){
        $quizModel=M('quiz');
        $quiz=$quizModel->find(array('id'=>$quiz_id));
        if(!$quiz){
            return WechatController::$ERROR;
        }
        switch($this->isTestOvertime($quiz_id)){
            case WechatController::$NOTOVERTIME:
                return WechatController::$NOTOVERTIME;
                break;
            case WechatController::$OVERTIME:
                //超时则删除
                if(!$this->clearQuizFromTemp($quiz_id)){
                    return WechatController::$FAIL;
                }
                return WechatController::$OVERTIME;
                break;
            case WechatController::$FAIL:
                return WechatController::$FAIL;
                break;
        }

    }
    //------------------------------------------------------------
    //返回token和时间(从1970年算起的时间)
    //设置Token
    function updateToken($token){
        $tokenModel=M('token');
        $tokenModel->where("id=1")->save(array('token'=>$token,'fetch_time'=>$this->timeToMysql(time())));
    }

    //获取Token
    function getToken(){
        $tokenModel=M('token');
        $now=time();
        $row=$tokenModel->find(array('id'=>1));
        if(!$row){
            return $this->TOKEN_INVALID;
        }
        $row=$row[0];
        $fetchtime=$row['fetch_time'];
        $sencends=$now-$this->mysqlToTime($fetchtime);
        if($sencends>=7200){
            //token过期,更新token
            return $this->TOKEN_INVALID;
        }else{
            return $row['token'];
        }

    }
    //------------------------------------------------------------
    function setToken($token,$time){
        $tokenModel=M('token');
        if($tokenModel->find(array('id'=>1))){
            $tokenModel->where('id=1')->save(array('token'=>$token,'fetch_time'=>$this->timeToMysql($time)));
        }else{
            $tokenModel->add(array('id'=>0,'token'=>$token,'fetch_time'=>$this->timeToMysql($time)));
        }
    }
    //存储新的token和时间
    //------------------------------------------------------------
    function getRole($openID){
        $userinfo=$this->getUserinfoByOpenid($openID);
        if($userinfo['identify']==1){
            return 1;
        }else if($userinfo['identify']==0){
            return 0;
        }else{
            return -1;
        }
    }
    //根据openID返回用户角色  0表示学生  1表示老师
    //------------------------------------------------------------
    function isTestExist($openID){
        $tempModel=M('quiz_temp_model');
        $quizs=$tempModel->select(array('openid'=>$openID));
        //过滤点名以及超时的小测
        foreach($quizs as $k=>$v){
            //判断是否超时
            $isOverTime = $this->judgeTest($v['quiz_id']);
            if ($isOverTime == WechatController::$OVERTIME) {
                unset($quizs[$k]);
                continue;
            }
            if($v['quiz_type']==WechatController::$QUIZ_TYPE_COUNT){
                unset($quizs[$k]);
            }
        }
        if($quizs){
            return 1;
        }else{
            return 0;
        }

    }
    //临时表是否存在该openID的测试  0不存在  1存在
    //------------------------------------------------------------
    function getTest($openID){
        $tempModel=M('quiz_temp_answer');
        $quizs=$tempModel->select(array('openid'=>$openID));
        $quizModel=M('quiz');
        $result=array();
        foreach($quizs as $key=>$value){
            //过滤点名
            if($value['quiz_type']==WechatController::$QUIZ_TYPE_COUNT){
                continue;
            }
            $course_id=$quizModel->find(array('course_id'),array('id'=>$value['quiz_id']))[0]['course_id'];
            if(!$course_id){
                $course_id=0;//如果未找到
            }
            $result[]=array(
                'quiz_id'=>$value['quiz_id'],
                'title'=>$value['quiz_title'],
                'content'=>$value['quiz_content'],
                'type'=>$value['quiz_type'],
                'course_id'=>$course_id,
            );
        }
        return $result;
    }
    //返回题目，描述，
    //------------------------------------------------------------
    function isCountExist($openID){
        $tempModel=M('quiz_temp_answer');
        $quiz=$tempModel->select(array('openid'=>$openID,'quiz_type'=>WechatController::$QUIZ_TYPE_COUNT));
        //判断是否超时
        foreach($quiz as $key=>$value) {
            $isOverTime = $this->judgeTest($value['quiz_id']);
            if ($isOverTime == WechatController::$OVERTIME) {
                unset($quiz[$key]);
            }
        }
        if($quiz){
            return 1;
        }else{
            return 0;
        }
    }
    //临时表是否存在该openID的点名  0不存在  1存在
    //------------------------------------------------------------
    function getCountTest($openID){
        $tempModel=M('quiz_temp_answer');
        $quizs=$tempModel->find(array(),array('openid'=>$openID,'quiz_type'=>WechatController::$QUIZ_TYPE_COUNT));
        $result=array();
        foreach($quizs as $key=>$value){
            $result[]=array(
                'quiz_id'=>$value['quiz_id'],
                'title'=>$value['quiz_title'],
                'content'=>$value['quiz_content'],
                'type'=>$value['quiz_type']
            );
        }
        return $result;
    }
    //返回题目，描述，网址

} 