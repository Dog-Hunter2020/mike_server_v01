<?php

/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/5
 * Time: 下午10:20
 */
namespace Home\Model;
use Think\Model;

class SchoolModel extends Model
{

    public  function getSchoolInfo($user_id){

        $schoolModel=M("School");
        $condition=array("user_id"=>$user_id);
        return $schoolModel->where($condition)->find();

    }

    public function addSchoolInfo($school_info){

    }







}