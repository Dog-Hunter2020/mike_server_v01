<?php
namespace Home\Controller;
use Think\Controller;
//print("hello");

/*
 * date: 2015/10/5
 * author: zfy
 * description: 完成user相关功能
 * 相关功能: 获取用户，增加和删除用户，修改用户信息（头像，密码，昵称，签名），注册新用户
 *
 */
class UserController extends \Think\Controller{

    private $userModel;


    public function __construct(){

//使用自定义model
        $this->userModel=D('User');

    }

    public function index(){



    }

    //获取user所有基本信息
    public function getUser($id){


        $userInfo=$this->userModel->getUser($id);



        return json_encode($userInfo);


    }
    /*
     * add new user
     *
     */
    public function addNewUser($userInfo){

        $this->userModel->addNewUser(new User($userInfo));

    }

    public function deleteUser($userName){

        $this->userModel->delete("name=>$userName");

    }



    public function setUserPassword(){


    }

    public function setUserSign(){

    }

    public function setUserIcon(){

    }

    public function register($userInfo){

    }

    public function setNickName($nickName){

    }

}

