<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/7
 * Time: 下午11:42
 */

/*
 *
 *
 * 使用的类: NJUSpider 构造参数 （教务网账号，密码）比如: $test=new NJUSpider("131250000","123456");
 *
 * 会用到的方法:
 *      getUserInfo()
 *
 *           description:获取用户在教务网上的信息
 *           return:NJUUserInfo(这是个对象)
 *
 *
 *
 *      getCurrentCourses()
 *
 *          description:获取用户的课程
 *          return:List<NJUCouseInfo>(这是个对象数组)
 *
 *
 *     getAllCourse()
 *
 *         description:获取全校的所有课程，且只获取当前学期，当前学期的定义参考CommonConfig::$LAST_TERM
 *         return:List<NJUCouseInfo>
 *
 *
 * tips:如果想把上面返回的对象数组或数组转化为二维数组或者数组，可以分别调用NJUSpider这个类里面的
 * formatCourse($courseArray)和formUserSource($user)方法，传入的参数分别为这两种对象，可以自己改写这两个方法
 *
 */