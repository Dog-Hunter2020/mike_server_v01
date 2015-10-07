<?php

/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/5
 * Time: 下午10:20
 *
 */
namespace Home\Model;
use Think\Model;

class UserModel extends Model
{



    private $school;
    private $department;
    private $userInfo;

    public function __construct(){

        $this->userInfo=array();


    }

    public function getUser($name){

        $user_id=$this->find(array("name=>$name"));


        $this->userInfo['base_info']=$this->getUserBaseInfo($name);
        $this->userInfo['school_info']=$this->school->getSchool($user_id);
        $this->department['department_info']=$this->department->getDepartment($user_id);

        return $this->userInfo;

    }

    public function getUserBaseInfo($name){


        return $this->find(array("name=$name"));


    }

    public function addUserCourseRelation($user_id,$course_id){

        assert($user_id>0,"user_id is being smaller than 0 ");
        assert($course_id>0,"course_id is being small than 0");

        $userCourseRelationModel=M("user_course_relation");
        $result=$this->$userCourseRelationModel->add(array("user_id"=>$user_id,"course_id"=>$course_id));


        if($result!==false){

        }


    }
    /*
     * add user baseinfo to database
     *
     */
    public function addNewUser($userInfo){



    }

    public function addUserAttentionItem(){


    }







}