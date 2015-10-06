<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/6
 * Time: 下午12:50
 */

namespace Wechat\Model;
use Think\Model;

class NoticeModel extends Model{

    function getUserNoticeList($user_id){
        $sql="select notice.* from notice,user_course_relation where notice.course_id=user_course_relation.course_id and user_course_relation.user_id=$user_id";
        return $this->query($sql);
    }

} 