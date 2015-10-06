<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/6
 * Time: 上午12:08
 * 相关功能:
 */
namespace Home\Controller;
use Think\Controller;
class UserAttentionListController extends \Think\Controller{



    public function getAttentionPeople($user_id){
        /*
         *
         */

    }
    /*
     *description： 获取关注的课程列表
     * return：id,course_name
     */

    public function getAttentionCourse($user_id){


    }

    public function getAttentionPost($user_id){

    }

    public function addAttentionItem($type,$userId,$itemId){

    }

    public function deleteAttentionItem($type,$userId,$itemId){

    }

    public function followed($userId,$followerId){

    }


}