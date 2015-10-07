<?php
/**
 * Created by PhpStorm.
 * User: I322233
 * Date: 10/7/2015
 * Time: 9:06 AM
 */

namespace Home\Controller;
use Think\Controller;
use Think\Model;


class MessageController extends Controller
{
    //获取某用户所选的课程的所有公告数量
    public function getTotalCourseNoticeNum($userId=0){

    }

    //获取所有回复数量，暂时理解为回复该用户发的所有帖子（不管是主题贴还是回复）的数量
    public function getTotalReplyNum($userId=0){
        $PostModel = new Model();
        $data = $PostModel->query("select count(*) as num from post where reply_to in (select id from post where user_id = %d)",array($userId));
        $result['num'] = $data[0]['num'];
        $this->ajaxReturn($result,'JSON');
    }
    public function getTotalPraiseNum($userId=0){
        $PostModel = new Model();
        $data = $PostModel->query("select SUM(like_count) as praise_num from post where user_id = %d",array($userId));
        $result['num'] = $data[0]['praise_num'];
        $this->ajaxReturn($result,'JSON');
    }
    //提到我的 可能要再建一张表
    public function getTotalMentionMeNum($userId=0){

    }

    //???
    public function getLatestReplies($userId,$fromTime,$toTime){
        $PostModel = new Model();
        $data = $PostModel ->query("");

    }

    //
    public function getLatestPraises(){

    }

}