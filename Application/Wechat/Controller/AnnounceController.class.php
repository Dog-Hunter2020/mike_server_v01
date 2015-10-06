<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/6
 * Time: 下午12:26
 */

namespace Wechat\Controller;


class AnnounceController extends CommonController{

    function announce($user_id,$course_id,$content){

        //TODO

        //when the para is invalid
        if($user_id<0||$course_id<0){
            return array("status"=>2);
        }

        $courseModel=M('course');
        $userModel=M('user');
        //when the course or the user is invalid
        if(!($userModel->find(array("id"=>$user_id))&&$courseModel->find(array("id"=>$course_id)))){
            return array("status"=>3);
        }
//TODO


        $noticeModel=M('notice');
        $id=$noticeModel->add(array('id'=>0,'content'=>$content,'course_id'=>$course_id,'course_name'=>"",'user_id'=>$user_id,'posttime'=>date('Y-m-d H:i:s')));
        // print($id);
        if($id){
            $name=$courseModel->find(array('id'=>$course_id))['name'];
            //公告中添加课程名
            $noticeModel->where("id=$id")->save(array('course_name'=>$name));
            //todo 推送
//            push_course_announce($id,$content,$course_id);
            return array("status"=>1);

        }
        else{
            return array("status"=>0);
        }

    }
    //推送
//    function push_course_announce($id,$content,$course_id){
//        require_once(dirname(dirname(__FILE__)).'/controller/CourseController2.php');
//        $c=new CourseController2();
//        $name=$c->find(array("name"),array("id"=>$course_id));
//        $name=$name[0][0];
//        $message=new \bl\message\message();
//        $message->sender=array("id"=>$id,"name"=>"�屡����");
//        $message->receiver="students";
//        $message->time=date('Y-m-d H:i:s');
//        $message->inf_type=\enum\inf_type\inf_type::ANNOUNCE_TE;
//        $message->content=array("course_info"=>array("id"=>$course_id,"name"=>$name),"announce_info"=>array("id"=>$id,"content"=>$content));
//        $message->notification="$name �γ̹���: $content";
//        $tags=array(tag::STUDENT,"course_".$course_id);
//        push($message,$tags,array());
//
//    }
    function delete_announce($user_id,$course_id,$a_id){

        $noticeModel=M('notice');
        if($noticeModel->delete(array("course_id"=>$course_id,"id"=>$a_id,"user_id"=>$user_id))){

            return array("status"=>0);
        }
        else{
            return array("status"=>1);
        }


    }
    function get_course_announce_list($course_id)
    {
        $noticeModel =M('notice');
        $result = $noticeModel->find(array(), array("course_id" => $course_id));
        $result=$this->set_user_name($result);
        $result=$this->set_coursename($result);
        arsort($result);
        if ($result) {

            return array("status" => 1, "announces" => $result);

        } else {
            return array("status" => 0, "announces" => array());
        }
    }
    function set_user_name($result){

        $length=sizeof($result);
        $userModel=M('user');
        for($i=0;$i<$length;$i++){
            $u_name=$userModel->find(array("id"=>$result[$i]["user_id"]));
            $u_name=$u_name['name'];
            $result[$i]['user_name']=$u_name;

        }
        return $result;


    }


    function set_coursename($result){

        $length=sizeof($result);
        $courseModel=M('course');
        for($i=0;$i<$length;$i++){
            $u_name=$courseModel->find(array("id"=>$result[$i]["course_id"]));
            $u_name=$u_name['name'];
            $result[$i]['name']=$u_name;

        }
        return $result;


    }

    function get_user_announce_list($user_id){
        $noticeModel =D('Notice');
        $result = $noticeModel->getUserNoticeList($user_id);
        $result=$this->set_user_name($result);
        $result=$this->set_coursename($result);
        arsort($result);
        if ($result) {
            return json_encode(array("status" => 1, "announces" => $result));
        } else {
            return json_encode(array("status" => 0, "announces" => array()));
        }
    }
} 