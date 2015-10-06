<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/5
 * Time: 下午10:54
 */

namespace Wechat\Controller;

class LoginController extends CommonController{

    function index(){
        $model=D('User');
        dump($model->relation(true)->find(2));
    }

    function register($identify_id,$password,$status){

        $spider=new \Common\Extend\NJU\spider\NJUSpider($identify_id,$password);
        $userinfo=$spider->getUserinfo();
        if($userinfo->name==null){

            return C('ERROR_USERNAME_PASSWORD_WRONG');

        };
        $userModel=M('user');

        $result=$spider->formUserSource($userinfo);
        $result['password']=sha1(trim($password));
        $arr=range(0,9);
        shuffle($arr);
        $result['head_icon']=C('HEAD_ICONS')[$arr[0]];
        $user_id=$userModel->add($result);
        //解决注册和登陆返回的格式不一样的bug，array
        $status->userinfo=array($result);

        // /controller/addUser($userinfo);
        $courses=$spider->getCurrentCourses();
        $courseArray=$spider->formatCourse($courses);

        $status->courses=$courseArray;

        $length=sizeof($courseArray);
        $courseModel=M('user');
        $relationModel=M('user_course_relation');

        $courseController=new CourseController();
        for($i=0;$i<$length;$i++) {
            //todo 添加课程的筛选条件待测试
            $coursesIndb=$courseModel->select(array("course_id"=>$courseArray[$i]["course_id"]));

            //删除找到的结果中不符合条件的课
            foreach($coursesIndb as $k=>$v){
                if(!$courseController->isTheSameClassByTimePlace($courseArray[$i]['time_place'],$v['time_place'])){
                    unset($coursesIndb[$k]);
                    continue;
                }

                //若新导入的课中的老师包含数据库中的课的老师，则以新导入的课的老师为准，如果是包含关系，则update
                if(strpos($courseArray[$i]["teacher"],$v['teacher'])>=0){
                    $update=$courseModel->where(array('id'=>$v['id']))->save(array('teacher'=>$courseArray[$i]["teacher"]));
                    break;
                }

                //教师不同则定为不同的课,若数据库中的课的老师包含新导入的课中的老师，则以数据库中的课为准
                if(!(strpos($v['teacher'],$courseArray[$i]["teacher"])>=0)){
                    unset($coursesIndb[$k]);
                    continue;
                }

            }

            if(!$coursesIndb){

                $k = $courseModel->add($courseArray[$i]);


                //说明是新课，需要添加地点和时间

                $time_place=$courseArray[$i]['time_place'];
                $teacher=$courseArray[$i]['teacher'];
                $teacher_id=$userModel->find(array(),array("user_name"=>$teacher));
                if($teacher_id){

                    $teacher_id=$teacher_id[0]['id'];
                }
                else{
                    $teacher_id=-1;
                }


                $content="上课的时间和地点：".$time_place;
                $noticeModel=M('notice');
                $notice_id=$noticeModel->add(array('id'=>0,'content'=>$content,'course_id'=>$k,'course_name'=>"",'user_id'=>$teacher_id,'posttime'=>date('Y-m-d H:i:s')));

            }
            else {
                $k=$coursesIndb[0]['id'];
            }
            $root=0;
            //如果是老师则root为1
            if($result['identify']==1){
                $root=1;
            }
            if(!$relationModel->find(array(),array('user_id'=>$user_id,'course_id'=>$k))){
                $relationModel->add(array('user_id'=>$user_id,'course_id'=>$k,'root'=>$root));
            }
        }

    }

    function login($identify_id,$password){

        $status=new status();
        //测试账号
        $userModel=M('user');
        $userRelationModel=D('User');
        $courseController=new CourseController();
        if($identify_id==C('TEST_USERNAME') and $password==C('TEST_PASSWORD')){
            $status->status=1;
            $status->userinfo=$userModel->where("identify_id=$identify_id")->find();
            $status->courses=array();
            return $status->encode();
        }
//echo 'hello';

        $rs=$userModel->find(array("identify_id"=>$identify_id));

        //当没有找到相关用户
        if($rs==false){

            $this->register($identify_id,$password,$status);
            $status->status=1;
            return $status->encode();
        }
        //当找到了相关用户
        if(is_array($rs)&&sizeof($rs)!=0){

            if($rs['password']!==sha1(trim($password))){
                //如果是保存的密码错误则更新密码
                $spider=new \Common\Extend\NJU\spider\NJUSpider($identify_id,$password);
                $userinfo=$spider->getUserinfo();
                if($userinfo->name==null){
                    $status->status=2;
                    return $status->encode();
                }else{
                    //更新密码
                    $userModel->where("identify_id=$identify_id")->save(array('password'=>sha1(trim($password))));

                    //重新导入课程
                    $courseController->importCourse($rs['id'],$identify_id,$password);

                    $user=$userModel->find("identify_id=$identify_id");

                    $courses=$userRelationModel->relation(true)->find($user['id'])['Course'];
                    $userinfo=$userModel->where("identify_id=$identify_id")->find();
                    $status->status=1;
                    $status->courses=$courses;
                    $status->userinfo=$userinfo;

                    return $status->encode();

                }
            }else{
                //密码正确
                //重新导入课程
                $courseController->importCourse($rs['id'],$identify_id,$password);
                $user=$userModel->find("identify_id=$identify_id");
                $courses=$userRelationModel->relation(true)->find($user['id'])['Course'];
                $userinfo=$userModel->where("identify_id=$identify_id")->find();
                $status->status=1;
                $status->courses=$courses;
                $status->userinfo=$userinfo;

                return $status->encode();
            }


        }


    }

}


class status{

    public $status;
    public $userinfo;
    public $courses;

    function encode(){
        $list=array();
        $list['status']=$this->status;
        $list['userinfo']=$this->userinfo;
        $list['courses']=$this->courses;
        return $list;
    }

}