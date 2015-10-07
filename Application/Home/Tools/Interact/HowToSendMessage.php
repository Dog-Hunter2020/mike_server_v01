<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/7
 * Time: 下午8:36
 */

/*
 * MessagePush的说明  单例模式，MessagePush::getInstance()获取对象
 * 成员函数说明 $type有两个值 notification，message 前者相当于会触发手机振动那种消息，后者推送的消息是拿给app在后台处理的
 *            $notificationTitle="课间通知" 当type为notification的时候，这个就是那个消息推送框的名字
 *
 *            AndroidListcast （$type,$title,$content,$userTokens,$notificationTitle="课间通知"）
 *            这个方法把消息推送给知道名字的一系列设备
 *            $userTokens 要推送给的设备们的名字，英文逗号隔开，最多500个
 *
 *            AndroidBroadcast （$type,$title,$content,$notificationTitle="课间通知"）
 *            推送给所有android设备
 *
 *            AndroidGroupcast （$type,$title,$content,$tags,$notificationTitle="课间通知"）
 *            推送给具有某种属性的那些设备
 *            属性写在tags里面 例如  $tags=array(array("tags"=>"teacher"),array("tags"=>"student))
 *
 *
 *            AndroidUnicast 单播 （$type,$title,$content,$userToken,$notificationTitle="课间通知"）
 *            精确地推送给具有某个名字的设备
 *
 */

$test=\Home\Controller\Tools\MessagePush::getInstance();
$test->sendAndroidListcast("notification","测试推送标题","测试推送内容","设备1,设备2","测试notification框的名字");