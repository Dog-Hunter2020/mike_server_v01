<?php

/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/5
 * Time: 下午10:20
 */
namespace Home\Model;
use Think\Model;

class DepartmentModel extends Model
{

    public  function getDepartmentInfo($department_name){

        $departmentModel=M("School");

        return $departmentModel->find(array("name=>$department_name"));

    }



}