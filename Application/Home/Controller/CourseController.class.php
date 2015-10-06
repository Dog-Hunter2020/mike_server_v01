<?php
/**
 * Created by PhpStorm.
 * User: kisstheraik
 * Date: 15/10/6
 * Time: 上午12:08
 * 相关功能:获取课程（课程名字｜课程id），修改课程信息（名字，图标），获取课程id列表（teacherId｜studentId），获取全校课程（起始id，数目）
 */
namespace Home\Controller;
use Think\Controller;
class CourseController extends \Think\Controller{

    /*
     * description: 获取课程信息(用id精确查询)
     * return: array()
     */

    public function getCourseInfo($courseId){

        assert($courseId>0,"courseId error!");



    }
    /*
     *description:更改课程的相关信息，信息种类在CourseInfoTypeEnum这个类中
     *return: bool
     *
     */
    public function setCourseInfo($courseId,$type,$content){

        assert($courseId!=-1,"courseId error!");
        assert($content!=null,"course info can not be null!");



    }
    /*
     * description:利用课程信息进行模糊查询
     * process:先模糊查询出ID list 再用id精确查询
     * return:array()
     *
     */

    public function getCoursesByInfo(){

    }
    /*
     * description:获取course表的fields
     *
     */

    public function getCourseTableFields(){

    }
    /*
     * description:获取以startId为开始id，number个的课程数目
     * return array(array())
     *
     */

    public function getCourseInfoRange($startId,$number){

        assert($startId>0&&$number>0,"startId or number error");

    }
    /*
     * description:添加新的课程
     * return:bool
     */
    public  function  addNewCourse($course){

    }
    /*
     * description:利用id进行精确删除
     * return:bool
     */

    public function deleteCourse($id){

    }










}