<?php

/*
 * 帖子相关操作
 */
namespace Home\Controller;
use Think\Controller;

class PostController extends Controller{

    private $POST_INFO_TYPE_PRAISE='PRAISE';
    private $POST_INFO_TYPE_VIEWNUM='VIEWNUM';
    private $INTERACT_TYPE_POST='POST';
    private $INTERACT_TYPE_PRAISE='PRAISE';
    /*
     * description:添加新的问题
     * return:bool|new post`s id
     * postInfo:json array()
     * String postId,String userId,String authorName,String title,String content,int praise,int viewNum,String date,String userIcon
     * 当$courseId为0说明在校内发的贴
     */

    //todo userIcon无用，authorName无用，reply_to缺少
    public function postNewQuestion($userId,$postInfo,$courseId=0){
        assert($postInfo!=null,"post info can not be null!");

        $postInfo=json_decode($postInfo,true);

        $postModel=M('post');

        $data['id']=$postInfo['postId'];
        $data['title']=$postInfo['title'];
        $data['content']=$postInfo['content'];
        $data['user_id']=$userId;
        $data['timestamp']=$postInfo['date'];
        $data['reply_to']=C('DEFAULT_VALUE_NONE');
        $data['watch_count']=$postInfo['viewNum'];
        $data['course_id']=$courseId;
        $data['like_count']=$postInfo['praise'];

        $this->ajaxReturn($postModel->add());

    }

    private function formatPostFromClientToDb($post){
        $result['id']=$post['postId'];
        $result['timestamp']=$post['date'];
        $result['watch_count']=$post['viewNum'];
        $result['like_count']=$post['praise'];
        $result['title']=$post['title'];
        $result['content']=$post['content'];
        return $result;
    }
    /*
     * description:根据id精确删除post
     * return:bool
     */

    public function deleteQuestion($postId){
        $postModel=M('post');
        if($postModel->where(array('id'=>$postId))->delete()>=0){
            $this->ajaxReturn(true);
        }
        $this->ajaxReturn($postModel->where(array('id'=>$postId))->delete());
    }
    /*
     * description:更新post的信息
     * return:bool
     * $postInfoType:CONTENT,PRAISE,VIEWNUM
     * 当type=PRAISE|VIEWNUM,postInfo不发挥作用
     *
     */

    public function updatePostInfo($postId,$postInfoType,$postInfo){
        $postModel=M('post');
        switch($postInfoType){
            case $this->POST_INFO_TYPE_PRAISE:
                if($postModel->where(array('id'=>$postId))->setInc('like_count',1)>=0){
                    $this->ajaxReturn(true);
                }else{
                    $this->ajaxReturn(false);
                }
                break;
            case $this->POST_INFO_TYPE_VIEWNUM:
                if($postModel->where(array('id'=>$postId))->setInc('watch_count',1)>=0){
                    $this->ajaxReturn(true);
                }else{
                    $this->ajaxReturn(false);
                }
                break;
            default:
                if($postModel->where(array('id'=>$postId))->save($this->formatPostFromClientToDb($postInfo))>=0){
                    $this->ajaxReturn(true);
                }else{
                    $this->ajaxReturn(false);
                }
                break;
        }

    }
    /*
     * description:邀请一些用户来回答某个问题
     * return:bool
     *
     * 这里涉及到推送信息
     *
     */
    //todo 推送设备号
    public function inviteUserToAnswer($senderId,$questionId,$userIdList){
        $userIdList=json_decode($userIdList,true);

        $messagePusher=\Home\Controller\Tools\MessagePush::getInstance();
        $tokenMaker=new \Common\Extend\Interact\UserTokenMaker();
        $idsStr=$tokenMaker->makeDeviceTokens($userIdList);
        $messagePusher->sendAndroidBroadcast("notification","新的邀请","有问题需要您来回答",$idsStr,"课间");

        $interactModel=M('interact');

        foreach($userIdList as $id){
            $data['sender_id']=$senderId;
            $data['receiver_id']=$id;
            $data['object_id']=$questionId;
            $data['type']=$this->INTERACT_TYPE_POST;
            $data['time']=date('Y-m-d H:i:s',time());
            $interactModel->add($data);
        }

        $this->ajaxReturn(true);


    }

    /*
     * description:获取一定数目的post
     * startId不在返回的列表中
     * return:array(postInfo)  postInfo的定义参考postNewQuestion
     * courseId=0表示校内
     */

    public function getSomePost($courseId,$startId=0,$number=1){
        $postArray=array();
        $postModel=M('post');
        $postArray=$postModel->where("course_id=$courseId and id>$startId")->limit($number)->select();
        foreach($postArray as $k=>$value){
            $postArray[$k]=$this->formatPostFromDbToClient($postArray[$k]);
        }

        $this->ajaxReturn($postArray);
    }

    private function formatPostFromDbToClient($post){
        $post['postId']=$post['id'];
        $post['date']=$post['timestamp'];
        $post['viewNum']=$post['watch_count'];
        $post['praise']=$post['like_count'];
        return $post;
    }

    /*
     * description:获取最热门的帖子(根据回帖数目来判断热门程度)
     * return:array(postInfo) postInfo的定义参考postNewQuestion
     * courseId=0表示校内
     * startId表示选取的是大于某个startId的帖子中最热的帖子
     */
    //todo 筛选规则应有两个，以时间为第一筛选
    public function getHotestPost($courseId,$startId,$number){
        $postModel=M('post');
        $sql="select 'reply_to',count(*) from post group by 'repy_to' HAVING 'id'>$startId and 'course_id'=$courseId ORDER BY count(*) limit $number";
        $postIds=$postModel->field("reply_to,count(*)")->group('reply_to')->having("id>$startId and course_id=$courseId")->order("count(*)")->limit($number)->select();
        $postArray=array();
        foreach($postIds as $id){
            $postArray[]=$postModel->find($id);
        }
        foreach($postArray as $k=>$value){
            $postArray[$k]=$this->formatPostFromDbToClient($postArray[$k]);
        }
        $this->ajaxReturn($postArray);
    }

    /*
    * description:获取最新的一些帖子(根据帖子的发表时间)
    * return:array(postInfo) postInfo的定义参考postNewQuestion
    * courseId=0表示校内
    * startId表示选取的是大于某个startId的帖子中最新的那些帖子
    */
    public function getNewestPost($courseId,$startId,$number){
        $postModel=M('post');
        $postArray=$postModel->where("course_id=%d and id>%d",$courseId,$startId)->order('timestamp')->limit($number)->select();
        foreach($postArray as $k=>$value){
            $postArray[$k]=$this->formatPostFromDbToClient($postArray[$k]);
        }
        $this->ajaxReturn($postArray);
    }
    /*
     * description:获取特定帖子的回复
     * return:array(replyInfo)
     * replyInfo:
     * String userId,String authorName,String content,String date,int praise,array(reply),String userIcon
     */

    public function getPostReplys($postId){
        $postModel=M('post');
        $post=$postModel->where(array('id'=>$postId))->find();
        $userModel=M('user');
        $author=$userModel->where(array('id'=>$post['user_id']))->find();
        $authorName=$$author['name'];
        $userIcon=$author['icon_url'];
        $replies=$postModel->where(array('reply_to'=>$postId))->select();
        $post['authorName']=$authorName;
        $post['userIcon']=$userIcon;
        $post['replies']=$replies;
        $post=$this->formatPostFromDb($post);
        $this->ajaxReturn($post);
    }

    private function formatPostFromDb($post){
        $post['userId']=$post['user_id'];
        $post['date']=$post['timestamp'];
        $post['praise']=$post['like_count'];
        return $post;
    }

    /*
     * description:获取某个post的详细信息
     * return:postInfo postInfo的定义参考postNewQuestion
     */
    public function getPost($postId){
        $postModel=M('post');
        $post=$postModel->find($postId);
        $this->ajaxReturn($post);
    }

    /*
     * description:根据用户输入的信息查找相关的帖子
     * return:List<postInfo> postInfo的定义参考postNewQuestion
     *
     * $inContent  是否在内容中进行搜索，默认为是
     */

    public function searchPost($info,$inContent=1){
        $result=array();
        $postModel=M('post');
        if($inContent){
            $result=$postModel->where("content like %$info% or title like %$info%")->select();
        }else{
            $result=$postModel->where("title like %$info%")->select();
        }
        $this->ajaxReturn($result);
    }

}