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
        $CourseNoticeModel = new Model();
        $data = $CourseNoticeModel->query("select count(*) as num from course_notice where course_id in (select course_id from user_course_relation where user_id = %d and `right`=1)",array($userId));
        $result['num'] = $data[0]['num'];
        $this->ajaxReturn($result,'JSON');
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
        $InteractModel = M('interact');
        $condition['receiver_id'] = $userId;
        $result['num'] = $InteractModel->where($condition)->count();
        $this->ajaxReturn($result,'JSON');
    }

    //为了能实时变化，index最好存储在服务器端，而num由用户自己控制
    public function getNextCourseNotices($userId,$index,$num){
        $CourseNoticeModel = new Model();
        $data = $CourseNoticeModel->query("select course_id, title, content, publish_time, user_id
                                            from course_notice where course_id in (select course_id from user_course_relation where user_id = %d and `right` = 1)
                                            ORDER BY id DESC LIMIT %d,%d",array($userId,$index,$num));
        $data = $this->handleCourseData($data);
        $this->ajaxReturn($data,'JSON');
    }
    public function getLatestCourseNotices($userId,$fromTime,$toTime){
        $CourseNoticeModel = new Model();
        $data = $CourseNoticeModel->query("select course_id, title, content, publish_time, user_id from course_notice where publish_time BETWEEN '%s' and '%s' and course_id in (select course_id from user_course_relation where user_id = %d and `right` = 1) ORDER BY id DESC",array($fromTime,$toTime,$userId));
        $data = $this->handleCourseData($data);
        $this->ajaxReturn($data,'JSON');
    }
    private function handleCourseData($data){
        for($x=0;$x<count($data);$x++){
            $UserModel = M('user');
            $userData = $UserModel->find($data[$x]['user_id']);
            $data[$x]['user_name'] = $userData['name'];

            $courseRary = M('course_rary');
            $condition['course_id'] = $data[$x]['course_id'];
            $condition['field_name'] = 'name';
            $courseNameData = $courseRary->where($condition)->select();
            if(count($courseNameData) == 0){
                $CourseInfoModel = new Model();
                $CourseInfo = $CourseInfoModel->query("select name from course_info where id in (SELECT course_info_id from course where id = %d)",array($data[$x]['course_id']));
                $data[$x]['course_name'] = $CourseInfo[0]['name'];
            }else{
                $data[$x]['course_name'] = $courseNameData[0]['field_content'];
            }
        }
        return $data;
    }

    public function getNextReplies($userId,$index,$num){
        $PostModel = new Model();
        $data = $PostModel->query("select user_id, `timestamp`, reply_to from post where reply_to in (select id from post where user_id = %d ) order by id DESC LIMIT %d,%d",array($userId,$index,$num));
        $data = $this->handleReplyData($data);
        $this->ajaxReturn($data,'JSON');
    }
    //
    public function getLatestReplies($userId,$fromTime,$toTime){
        $PostModel = new Model();
        $data = $PostModel ->query("select user_id, `timestamp`, reply_to from post where reply_to in (select id from post where user_id = %d ) and `timestamp` BETWEEN '%s' and '%s' ORDER BY id DESC ",array($userId,$fromTime,$toTime));
        $data = $this->handleReplyData($data);
        $this->ajaxReturn($data,'JSON');
    }
    private function handleReplyData($Data){
        for($i=0;$i<count($Data);$i++){
            $user_id = $Data[$i]['user_id'];
            $UserModel = M("user");
            $userData = $UserModel->find($user_id);
            $Data[$i]['user_name'] = $userData['nick_name'];
            $PostModel = M('post');
            $postData = $PostModel->find($Data[$i]['reply_to']);
            $Data[$i]['content'] = $postData['content'];
        }
        return $Data;

    }

    public function getNextPraises($userId,$index,$num){
        $data = $this->getNextInteractData($userId,$index,$num,'PRAISE');
        $data = $this->handleInteractData($data);
        $this->ajaxReturn($data,'JSON');
    }
    //
    public function getLatestPraises($userId,$fromTime,$toTime){
        $data = $this->getLatestInteractData($userId,$fromTime,$toTime,'PRAISE');
        $data = $this->handleInteractData($data);
        $this->ajaxReturn($data,'JSON');
    }
    private function handleInteractData($data){
        for($i=0;$i<count($data);$i++){
            $sender_id = $data[$i]['sender_id'];
            $object_id = $data[$i]['object_id'];
            $UserModel = M('user');
            $UserData = $UserModel->find($sender_id);
            $data[$i]['sender_name'] = $UserData['nick_name'];
            $PostModel = M('post');
            $postData = $PostModel->find($object_id);
            $data[$i]['post_content'] = $postData['content'];
        }
        return $data;
    }
    public function getNextMentionMes($userId,$index,$num){
        $data = $this->getNextInteractData($userId,$index,$num,'POST');
        $data = $this->handleInteractData($data);
        $this->ajaxReturn($data,'JSON');
    }
    public function getLatestMentionMes($userId,$fromTime,$toTime){
        $data = $this->getLatestInteractData($userId,$fromTime,$toTime,'POST');
        $data = $this->handleInteractData($data);
        $this->ajaxReturn($data,'JSON');
    }
    private function getNextInteractData($userId,$index,$num,$type){
        $InteractModel = new Model();
        $data = $InteractModel->query("select sender_id, time, object_id from interact where receiver_id = %d and type='%s' order by id DESC LIMIT %d,%d",array($userId,$type,$index,$num));
        return $data;
    }
    private function getLatestInteractData($userId,$fromTime,$toTime,$type){
        $InteractModel = new Model();
        $data = $InteractModel->query("select sender_id, time, object_id from interact where receiver_id = %d and type='%s' AND TIME BETWEEN '%s' and '%s' order by id DESC",array($userId,$type,$fromTime,$toTime));
        return $data;
    }

}