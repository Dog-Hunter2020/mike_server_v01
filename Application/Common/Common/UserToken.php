<?php

/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/8
 * Time: 上午11:12
 */
class UserToken
{


    public $createdTime;
    public $state;//当前状态
    public $isGetCode=false;
    public $ipAddress;
    public $name;
    public $tokenId;
    public $userId;
    public $password;
    public $phoneNumber;
    public $schoolAccount;
    public $schoolAccountPassword;
    public $ifBindSchoolAccount;

    public function __construct(){

    }


}