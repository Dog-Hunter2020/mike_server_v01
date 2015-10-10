<?php
namespace Home\Controller;
use Think\Controller;
use Home\Controller\Tools\Spider;
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
    private $userSpider;


    public function __construct(){

//使用自定义model
        $this->userModel=D('User');
//        $this->userSpider=new \Common\Extend\NJU\spider\NJUSpider($identifyId,$password);

    }

    public function index(){



    }

    //获取user所有基本信息
    /*
     * return: array()
     *
     *String name,String icon,array() schoolInfo,array() departmentInfo,
     * String nick_name,String identify,String gender,
     * String grade,String sign,String background_icon_path,
     * String id
     *
     * 其中
     * school:String id,String name,String number
     * departmrnt:String id,String schoolId,String name,String number
     *
     *
     */
    private function formatUserInfo($userInfo){
        $userInfo['icon']=$userInfo['icon_url'];
        $userInfo['background_icon_path']=$userInfo['background_icon_url'];
        return $userInfo;
    }

    private function formatDepartmentInfo($departmentInfo){
        $departmentInfo['schooId']=$departmentInfo['school_id'];
        $departmentInfo['number']=$departmentInfo['department_num'];
        return $departmentInfo;
    }

    public function getUser($id){
//        $userModel=D('User');
//        $userInfo=$userModel->relation(true)->where(array('id'=>$id))->find();

        $userModel=M('user');
        $userInfo=$userModel->where(array('id'=>$id))->find();
        $schoolModel=M('school');
        $schoolInfo=$schoolModel->where(array('id'=>$userInfo['school_id']))->find();
        $departmentModel=M('deparment');
        $departmentInfo=$departmentModel->where(array('id'=>$userInfo['department_id']))->find();

        $userInfo['schoolInfo']=$schoolInfo;
        $userInfo['departmentInfo']=$this->formatDepartmentInfo($departmentInfo);
        $userInfo=$this->formatUserInfo($userInfo);

        $this->ajaxReturn($userInfo,'JSON');


    }
    /*
     * description:用userToken进行登录
     * return:bool
     *
     * 获取的userTokenId会通过cookie传到客户端
     *
     */

    public function  login($userToken){

        $token=new \UserToken($userToken);

        \Cookie::sendCookie("tokenId",$token->tokenId);

    }


    /*
     * description:根据信息进行模糊查找
     * return :array()  String id,String name,String icon
     * $info 的种类:name,nick_name
     * 步骤:先模糊查找出userId,再根据id查info
     */
    public function getUsersByInfo($info){

    }
    /*
     * description:add new user
     * return:bool
     * $userInfo
     *
     *String name,String icon,String schoolName,String departmentName,
     * String nick_name,String identify,String gender,
     * String grade,String sign,String background_icon_path,
     * String id,String schoolaccount,String schoolAccountPsd
     *
     */
    public function addNewUser($userInfo){

        $this->userModel->addNewUser(new User($userInfo));

    }
    /*
     * description:根据用户信息判断user是否已经存在
     * userInfoType:PHONE_NUMBER|NICK_NAME|SCHOOL_NUMBER(学号)
     * return:bool
     */

    public function wheatherUserExist($userInfoType="NICK_NAME",$userInfo){

        assert($userInfo!=null,"user info can not be null!");

    }



//    public function deleteUser($userName){
//
//        $this->userModel->delete("name=>$userName");
//
//    }



    /*
     * description:修改用户信息
     * return:bool
     * $userInfoType:PASSWORD|SIGN_TEXT|ICON|NICKNAME
     */
    public function resetUserInfo($userId,$userInfoType,$newUserInfo){

    }

    /*
     * description:用户注册，没有schoolAccount的时候不用爬课程
     * return:user(和getUser方法里面的user结构一样)
     * userInfo和addNewUser一样
     *
     */
    public function register($userInfo){


        assert($userInfo!=null,"user info can not be null");

        //$this->userSpider->getUserInfo();


    }






}

