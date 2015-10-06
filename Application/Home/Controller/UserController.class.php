<?php
namespace Home\Controller;
use Think\Controller;

class UserController extends \Think\Controller{

    private $userModel;


    public function __construct(){

        $this->userModel=D('User');

    }

    public function index(){



    }
    public function getUser($name){


        $userInfo=$this->userModel->getUser($name);

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

    public function login($userInfo){

    }



}