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

    public function __construct($userTokenInfo){


        $this->state=$userTokenInfo['state'];
        $this->isGetCode=$userTokenInfo['isGetCode'];
        $this->ipAddress=$userTokenInfo['ipAddress'];
        $this->name=$userTokenInfo['name'];
        $this->userId=$userTokenInfo['userId'];
        $this->password=$userTokenInfo['password'];
        $this->phoneNumber=$userTokenInfo['phoneNumber'];
        $this->schoolAccount=$userTokenInfo['schoolAccount'];
        $this->schoolAccountPassword=$userTokenInfo['schoolAccountPassword'];
        $this->ifBindSchoolAccount=$userTokenInfo['ifBindSchoolAccount'];
        $this->createdTime=time();
        $this->generateTokenId();

    }

    public function generateTokenId(){

        $this->tokenId=sha1($this->createdTime.$this->name);


    }


}