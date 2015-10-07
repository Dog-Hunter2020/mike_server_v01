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

    }
    //
    public function getLatestReplies($userId,$fromTime,$toTime){
        $PostModel = new Model();
        $data = $PostModel ->query("");

    }

    public function getNextPraises($userId,$index,$num){

    }
    //
    public function getLatestPraises($userId,$fromTime,$toTime){

    }
    public function getNextMentionMes($userId,$index,$num){

    }
    public function getLatestMentionMes($userId,$fromTime,$toTime){

    }

}