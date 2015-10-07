<?php

/*
 * 课堂提问相关操作
 */

namespace Home\Controller;
use Think\Controller;
class CourseAnnounceController extends \Think\Controller{


    /*
     * description:获取某个课程一定数目的课程
     *
     * 获取的规则:根据公告时间排序，最新的公告有position为0 这里要获取的是beginPosition＋num这一系列的帖子
     * return:List<CourseAnnoucement>)
     *
     * CourseAnnoucement:
     *
     * annoucId(String)	courseId(String)	authorId(String)	authorName(String)	time(long)	title(String)	content(String)	onTop(boolean)
     *
     */
    public function  getAnnoucments($courseId, $beginPos, $num){

    }

    /*
     * description:添加新的课程公告
     * return:bool
     *
     * time为时间戳格式
     */
    public function newAnnouncement($courseId, $authorId, $time, $title, $content){

    }

    /*
     * description:使公告置顶
     * return:bool
     */
    public function putAnnoucOnTop($annoucId){

    }
}
