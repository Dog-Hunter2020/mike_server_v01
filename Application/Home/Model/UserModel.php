<?php

/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/5
 * Time: 下午10:20
 *
 */
namespace Home\Model;
use Think\Model\RelationModel;

class UserModel extends RelationModel
{

    protected $table_name='user';

    protected $_link = array(
//
//                        'Profile'=>array(
//
//                        'mapping_type'=>HAS_ONE,
//
//                        'mapping_name'=>'Profile',
//
//                        'class_name'=>'Profile',
//
//                        'foreign_key'=>'user_id',
//
//                        ),
//
                        'School'=> array(

                        'mapping_type'=> self::BELONGS_TO,

                        'mapping_name'=>'School',

                        'class_name'=>'School',

                        'foreign_key'=>'school_id',

                        ),

                        'Department'=> array(

                            'mapping_type'=> self::BELONGS_TO,

                            'mapping_name'=>'Department',

                            'class_name'=>'Department',

                            'foreign_key'=>'department_id',

                        ),

                        'Major'=> array(

                            'mapping_type'=> self::BELONGS_TO,

                            'mapping_name'=>'Major',

                            'class_name'=>'Major',

                            'foreign_key'=>'major_id',

                        ),
//
//                        'Card'=> array(
//
//                        'mapping_type'=> HAS_MANY,
//
//                        'mapping_name'=>'Card',
//
//                        'class_name'=>'Card',
//
//                        'foreign_key'=>'user_id',
//
//                        ),

                        'Course'=> array(

                            'mapping_type'=> self::MANY_TO_MANY,

                            'mapping_name'=>'Course',

                            'class_name'=>'Course',

                            'foreign_key'=>'user_id',

                            'relation_foreign_key'=>'course_id',

                            'relation_table'=>'user_course_relation',

                        ),

    );


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