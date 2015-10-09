<?php

/*
 * 课堂提问相关操作
 */

namespace Home\Controller;
use Think\Controller;
use Think\Model;

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

    public function  getCoursePosts($courseId, $index, $num){
        $PostModel = new Model();
        $postData = $PostModel->query("select * from post where course_id = %d ORDER BY id DESC LIMIT %d,%d",array($courseId,$index,$num));
        for($i = 0;$i<count($postData);$i++){
            $userData = $this->getUserDataById($postData[$i]['user_id']);
            $postData[$i]['authorName'] = $userData['name'];
        }
        $this->ajaxReturn($postData,'JSON');

    }
    private function getUserDataById($userId = 0){
        $UserModel = M('user');
        $userData = $UserModel->find($userId);
        return $userData;
    }

}