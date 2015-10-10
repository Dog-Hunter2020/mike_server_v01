<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/10
 * Time: 下午3:01
 */

namespace Common\Extend\Interact;


class UserTokenMaker {
    /*生成设备号
     *user+id
     */
    public function makeDeviceToken($userId){
        return 'user'.$userId;
    }

    /*
     * 传入的为用户id列表
     * 生成token串
     */
    public function makeDeviceTokens($userIdArray){
        $idStr='';
        for($i=0;$i<sizeof($userIdArray);$i++){
            if($i==0){
                $idStr.=$userIdArray[$i];
                continue;
            }
            $idStr=$idStr.','.$userIdArray[$i];
        }
        return $idStr;
    }
} 