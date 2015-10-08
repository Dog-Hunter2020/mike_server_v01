<?php

/*
 * 帖子相关操作
 */
namespace Home\Controller;
use Think\Controller;

class PostController extends Controller{
    /*
     * description:添加新的问题
     * return:bool|new post`s id
     * postInfo:array()
     * String postId,String userId,String authorName,String title,String content,int praise,int viewNum,String date,Strig userIcon
     * 当$courseId为0说明在校内发的贴
     */

    public function postNewQuestion($userId,$postInfo,$courseId=0){
        assert($postInfo!=null,"post info can not be null!");


    }
    /*
     * description:根据id精确删除post
     * return:bool
     */

    public function deleteQuestion($postId){

    }
    /*
     * description:更新post的信息
     * return:bool
     * $postInfoType:CONTENT,PRAISE,VIEWNUM
     * 当type=PRAISE|VIEWNUM,postInfo不发挥作用
     *
     */





    public function updatePostInfo($postId,$postInfoType,$postInfo){

    }
    /*
     * description:邀请一些用户来回答某个问题
     * return:bool
     *
     * 这里涉及到推送信息
     *
     */

    public function invitateUserToAnswer($questionId,$userIdList){

    }
    /*
     * description:获取一定数目的post
     * return:array(postInfo)  postInfo的定义参考postNewQuestion
     * courseId=0表示校内
     */

    public function getSomePost($courseId,$startId=0,$number=1){


    }

    /*
     * description:获取最热门的帖子(根据回帖数目来判断热门程度)
     * return:array(postInfo) postInfo的定义参考postNewQuestion
     * courseId=0表示校内
     * startId表示选取的是大于某个startId的帖子中最热的帖子
     */
    public function getHotestPost($courseId,$startId,$number){

    }

    /*
    * description:获取最新的一些帖子(根据帖子的发表时间)
    * return:array(postInfo) postInfo的定义参考postNewQuestion
    * courseId=0表示校内
    * startId表示选取的是大于某个startId的帖子中最新的那些帖子
    */
    public function getNewestPost($courseId,$startId,$number){

    }
    /*
     * description:获取特定帖子的回复
     * return:array(replyInfo)
     * replyInfo:
     * String userId,String authorName,String content,String date,int praise,array(reply),String userIcon
     */

    public function getPostReplys($postId){

    }

    /*
     * description:获取某个post的详细信息
     * return:postInfo postInfo的定义参考postNewQuestion
     */
    public function getPost($postId){

    }

    /*
     * description:根据用户输入的信息查找相关的帖子
     * return:List<postInfo> postInfo的定义参考postNewQuestion
     *
     * $inContent  是否在内容中进行搜索，默认为是
     */

    public function searchPost($info,$inContent=1){

    }

}