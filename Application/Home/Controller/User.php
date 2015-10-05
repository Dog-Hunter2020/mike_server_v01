<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/5
 * Time: 下午10:49
 */

namespace Home\Controller;


class User
{

    private $base_info;
    private $school_info;
    private $department_info;

    public function __construct($userInfo){

    }

    public function  getSchoolInfo(){


    }

    public function getDepartmentInfo(){

    }
    
    public function getBaseInfo(){


    }

    public function encode(){

       return json_encode(array("base_info"=>$this->base_info,"school"=>school_info,"department"=>$this->department_info));
    }

}