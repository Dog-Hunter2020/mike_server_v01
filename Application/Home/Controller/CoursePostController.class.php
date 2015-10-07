<?php

/*
 * 课堂提问相关操作
 */

namespace Home\Controller;
use Think\Controller;
class CoursePostController extends \Think\Controller{

    /*
     * description:获取某个课程下一定数目的帖子
     *
     * 获取的规则:根据帖子时间排序，最新的帖子有position为0 这里要获取的是beginPos＋num这一系列的帖子
     * return:List<Post>
     *
     * Post:
     * String postId,String userId,String authorName,String title,String content,int praise,int viewNum,String date,Strig userIcon
     */

    public function  getCoursePosts($courseId, $beginPos, $num){

    }

}