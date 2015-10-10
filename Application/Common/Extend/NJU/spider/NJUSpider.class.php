<?php
namespace Common\Extend\NJU\spider;

//error_reporting(0);
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/7/22
 * Time: 下午3:21
 */

class Department{
    //院系代号
    public $number;
    //院系名称
    public $name;
    //专业
    public $specials=array(
//        array(
//            'number'=>0,
//            'name'=>''
//        )
    );
}
//全校课程的数据结构
class NJUCouseInfo{
    //学期
    public $term;
    //年级
    public $grade;
    //编号
    public $number;
    //课程名称
    public $name;
    //性质 通修等
    public $nature;
    //开课院系
    public $faculty;
    //上课院系
    public $department;
    //学分
    public $credit;
    //学时
    public $period;
    //校区
    public $squre;
    //教师
    public $teacher;
    //上课时间地点
    public $time_place;
    //课程概述=教学目标
    public $description;
    //教学内容
    public $teaching_content;
    //-----教师专有属性-----
    //班级名字
    public $class_name;
    //授课对象
    public $object;

    public function __construct(){
        $this->name=0;
        $this->class_name=0;
        $this->credit=0;
        $this->department=0;
        $this->description=0;
        $this->faculty=0;
        $this->grade=0;
        $this->nature=0;
        $this->number=0;
        $this->object=0;
        $this->period=0;
        $this->squre=0;
        $this->teacher=0;
        $this->teaching_content=0;
        $this->time_place=0;
    }
}


class NJUUserInfo{
    //学号
    public $identify_id;
    //姓名
    public $name;
    //院系
    public $faculty;
    //专业
    public $speciality;
    //身份
    public $identify;

    public function __construct(){
        $this->faculty=0;
        $this->speciality=0;
        $this->identify=0;
    }
}




class NJUSpider extends CurlSpider{
    public static $ROLE_TEACHER='教师';
    public static $ROLE_STUDENT='学生';

    private $LAST_TERM=20142;

    private $identify_id,$password,$cookie,$postdata,$identify,$name;
    private $index_html;
    //网址的头部需要一致方可生效
    public static $url_login="http://desktop.nju.edu.cn:8080/jiaowu/login.do";
    //学生网址
    public static $url_current_classtable = "http://desktop.nju.edu.cn:8080/jiaowu/student/teachinginfo/courseList.do?method=currentTermCourse";
    public static $url_all_course_test="http://desktop.nju.edu.cn:8080/jiaowu/student/teachinginfo/allCourseList.do?method=getCourseList&curTerm=20151&curSpeciality=020&curGrade=2015";
    public static $url_all_course_choose_page="http://desktop.nju.edu.cn:8080/jiaowu/student/teachinginfo/allCourseList.do?method=getTermAcademy";
    public static $url_userinfo="http://desktop.nju.edu.cn:8080/jiaowu/student/studentinfo/studentinfo.do?method=searchAllList";
    public static $url_allcourse_index='http://desktop.nju.edu.cn:8080/jiaowu/student/teachinginfo/allCourseList.do?method=getTermAcademy';
    //教师网址
    public static $url_teacher_current_classtable='http://desktop.nju.edu.cn:8080/jiaowu/teacher/courseinfo/classStudentList.do';
    public static $url_teacher_current_classtable_request='http://desktop.nju.edu.cn:8080/jiaowu/teacher/courseinfo/courseList.do';
    //课程网址
    public static $url_course_detail='http://desktop.nju.edu.cn:8080/jiaowu/student/elective/courseList.do?method=getCourseInfoM&courseNumber=00000040&classid=0';

    private $hosts=array('jwas2','jwas3');

    function __construct($identify_id,$password){
        $this->identify_id=$identify_id;
        $this->password=$password;
        //登陆需要提供的参数
        $this->postdata['userName']=$identify_id;
        $this->postdata['password']=$password;
        //登陆获取cookie以及身份和老师
        $this->login();
    }


    private function changeHost($host){

       $this->url_login="http://".$host.".nju.edu.cn:8080/jiaowu/login.do";
        //学生网址
       $this->url_current_classtable = "http://".$host.".nju.edu.cn:8080/jiaowu/student/teachinginfo/courseList.do?method=currentTermCourse";
       $this->url_all_course_test="http://".$host.".nju.edu.cn:8080/jiaowu/student/teachinginfo/allCourseList.do?method=getCourseList&curTerm=20151&curSpeciality=020&curGrade=2015";
       $this->url_all_course_choose_page="http://".$host.".nju.edu.cn:8080/jiaowu/student/teachinginfo/allCourseList.do?method=getTermAcademy";
       $this->url_userinfo="http://".$host.".nju.edu.cn:8080/jiaowu/student/studentinfo/studentinfo.do?method=searchAllList";
       $this->url_allcourse_index='http://'.$host.'.nju.edu.cn:8080/jiaowu/student/teachinginfo/allCourseList.do?method=getTermAcademy';
        //教师网址
       $this->url_teacher_current_classtable='http://'.$host.'.nju.edu.cn:8080/jiaowu/teacher/courseinfo/classStudentList.do';
       $this->url_teacher_current_classtable_request='http://'.$host.'.nju.edu.cn:8080/jiaowu/teacher/courseinfo/courseList.do';
        //课程网址
       $this->url_course_detail='http://'.$host.'.nju.edu.cn:8080/jiaowu/student/elective/courseList.do?method=getCourseInfoM&courseNumber=00000040&classid=0';

    }

    private function login(){
        $index=$this->curl_request(NJUSpider::$url_login,$this->postdata,'',1);
        $this->cookie=$index['cookie'];
        $this->index_html=$index['content'];

        if(!$this->index_html){
            foreach($this->hosts as $host){
                $this->changeHost($host);
                $index=$this->curl_request(NJUSpider::$url_login,$this->postdata,'',1);
                $this->cookie=$index['cookie'];
                $this->index_html=$index['content'];
                if($this->index_html){
                    break;
                }
            }
        }
        //获取身份
        $doc = new \DOMDocument();
        $doc->loadHTML($index['content']);
        $xpath = new \DOMXPath($doc);
        $info=$xpath->query('//div[@id="UserInfo"]');
        $str=$info->item(0)->nodeValue;
        $str=str_replace("    当前身份", "", $str);
        $arr=explode('：',$str);
        $identify=$arr[sizeof($arr)-1];
        $this->identify=$identify;
        $this->name=$arr[1];
    }

    //获取目标界面html源码
    private function getHTML($login,$page,$post=''){

        $html_classtable=$this->curl_request($page,$post,$this->cookie);
//        $html_classtable=$this->removeWhite($html_classtable);
        return $html_classtable;
    }

    //加载html页面
    private function loadHtml($html){
        $doc = new \DOMDocument();
        $meta = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
        $doc->loadHTML($meta.$html);
//      print_r($html);
        $xpath = new \DOMXPath($doc);
        return $xpath;
    }

    //输出
    public function pout($arr){
        foreach($arr as $key=>$value){
            print_r($value.'<br>');
        }
    }

    //写文件
    private function writeFile($html){
        $f=fopen('log.txt','w');
        fwrite($f,$html);
        fclose($f);
    }

    //清除HTML代码、空格、回车换行符
    public function remove_html_tag($str){
        //trim 去掉字串两端的空格
        //strip_tags 删除HTML元素
        $str = trim($str);
        $str = @preg_replace('/<script[^>]*?>(.*?)<\/script>/si', '', $str);
        $str = @preg_replace('/<style[^>]*?>(.*?)<\/style>/si', '', $str);
//        $str = @strip_tags($str,"");
        $str = @ereg_replace("\t","",$str);
        $str = @ereg_replace("\r\n","",$str);
        $str = @ereg_replace("\r","",$str);
        $str = @ereg_replace("\n","",$str);
//        $str = @ereg_replace(" ","",$str);
        $str = @ereg_replace("&nbsp;"," ",$str);
        $str=preg_replace("/[\s]+/is"," ",$str);
        return trim($str);
    }

    //清除空行
    public function removeWhite($str){
//        $this->writeFile($str);
        $str = trim($str);
        $str=str_replace(array('<br>','</br>','<br/>'),'||',$str);
        $str = str_replace("\t"," ",$str);
        $str = str_replace("\r\n"," ",$str);
        $str = str_replace("\r"," ",$str);
        $str = str_replace("\n"," ",$str);
//        $str = @ereg_replace(" ","",$str);
        $str = str_replace("&nbsp;"," ",$str);
        $str=preg_replace("/[\s]+/is"," ",$str);
        return trim($str);
    }

    //检测表格列数
    protected function tableSizeDetection($domxpath){
        $table_size=10;

        $theads=$domxpath->query('//table/thead/tr/td');
        if($theads->length>0){
            $table_size=$theads->length;
        }

        $ths=$domxpath->query('//th');
        if($theads->length>0){
            $table_size=$theads->length;
        }

        return $table_size;
    }
    //获取所有院系和专业,解析网页中的js
    private function getAllDepartmentAndSpecials($html){
        $allDepatments=array();
        //解析院系
        $preg_department="/var academys = (.*?)CreateSelect/is";
        preg_match_all($preg_department,$html,$info_one);
        $info_one=$info_one[0];
        $info_one=str_replace('var academys = "','',$info_one);
        $info_one=str_replace('CreateSelect','',$info_one);
//        print_r($info_one);
        $info_two=explode('||',$info_one[0]);
        foreach($info_two as $k=>$v){
            $split_department_and_spe=explode('{{',$v);
            $deparment=new Department();
            $deparment_info=explode('_',$split_department_and_spe[0]);
            $deparment->number=$deparment_info[0];
            $deparment->name=$deparment_info[1];
            $spe_str=str_replace('**','',$split_department_and_spe[1]);
            $spes_info=explode('((',$spe_str);
            unset($spes_info[sizeof($spes_info)-1]);
            foreach($spes_info as $number=>$spesStr){
                $speInfo=explode('%',$spesStr);
                $deparment->specials[]=array(
                    'number'=>$speInfo[0],
                    'name'=>$speInfo[1]
                );
            }
            $allDepatments[]=$deparment;
//
//            print_r($deparment);
//            echo '<br>';
        }

        return $allDepatments;
    }

    //获取全校课程界面中的selection
    private function getAllcoursesSelections(){
        $termList=array();
        $gradeList=array();
        $departmentList=array();
        $specialList=array();
        $html=$this->getHTML(NJUSpider::$url_login,NJUSpider::$url_allcourse_index);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        //*[@id="academySelect"]
//        $this->writeFile($html);
        $termOptions = $xpath->query('//select[@id="termList"]/option/@value');
        $gradeOptions = $xpath->query('//select[@id="gradeList"]/option/@value');

        $departments=$this->getAllDepartmentAndSpecials($html);

        for($i=0;$i<$termOptions->length;$i++){
            $item=$termOptions->item($i)->nodeValue;
            $termList[]=$item;
        }

        for($i=0;$i<$gradeOptions->length;$i++){
            $item=$gradeOptions->item($i)->nodeValue;
            $gradeList[]=$item;
        }

        array_splice($termList,0,1);
        array_splice($gradeList,0,1);

        return array(
            'termList'=>$termList,
            'gradeList'=>$gradeList,
            'departmentList'=>$departments
        );
    }

    //合成课程详情页面
    private function echoDetailPage($courseNumber){
        return 'http://desktop.nju.edu.cn:8080/jiaowu/student/elective/courseList.do?method=getCourseInfoM&courseNumber='.$courseNumber.'&classid=0';
    }

    //合成全校课程页面
    private function echoAllCoursePage($term,$speId,$grade){
        $page='http://desktop.nju.edu.cn:8080/jiaowu/student/teachinginfo/allCourseList.do?method=getCourseList&curTerm='.$term.'&curSpeciality='.$speId.'&curGrade='.$grade;
        return $page;
    }

    //获取所有扒取页面所有课程信息
    private function getSchoolAllCourses(){
        $result=array();
        $selections=$this->getAllcoursesSelections();
        //组合学期，年级，院系三个筛选条件形成网页
        $i=0;
//        $courseController=new CourseController2();
        foreach($selections['termList'] as $term){
            //只扒取当前学期
            if($term==$this->LAST_TERM){
                return;
            }
            foreach($selections['gradeList'] as $grade){
                foreach($selections['departmentList'] as $department){
                    foreach($department->specials as $special){
                        $page=$this->echoAllCoursePage($term,$special['number'],$grade);
                        $html=$this->getHTML('',$page);
                        //属于本年级和本专业的课程
                        $courses=$this->allCourseHtmlToObject($html);
                        foreach($courses as $course){
                            $detail_page=$this->echoDetailPage($course->number);
                            $detail_html=$this->getHTML('',$detail_page);
                            $detail=$this->getCourseDetail($detail_html);

//                            $this->writeFile($detail_html);
//                            $this->writeFile(implode('----->>>>>',$detail));

                            $course->grade=$grade;
                            $course->department=$department->name;
                            $course->term=$term;
                            $course->description=$detail['description'];
                            $course->teaching_content=$detail['teaching_content'];
                            $course->tip=$special['name'];
                            $course->reference=$detail['reference'];
                            $result[]=$course;
//                            echo '\n';
                            $one=array(
                                'id'=>0,
                                'course_id'=>mysql_real_escape_string($course->number),
                                'name'=>mysql_real_escape_string($course->name),
                                'teacher'=>mysql_real_escape_string($course->teacher),
                                'time_place'=>mysql_real_escape_string($course->time_place),
                                'school'=>'南京大学',
                                'term'=>mysql_real_escape_string($course->term),
                                'grade'=>mysql_real_escape_string($course->grade),
                                'department'=>mysql_real_escape_string($course->department),
                                'description'=>mysql_real_escape_string($course->description),
                                'teaching_content'=>mysql_real_escape_string($course->teaching_content),
                                'reference'=>mysql_real_escape_string($course->reference),
                                'nature'=>mysql_real_escape_string($course->nature),
                                'tip'=>mysql_real_escape_string($course->tip),
                            );
//                            $rs=$courseController->add($one);
//                            print_r(++$i);
//                            echo '/---';
//                            return $result;
                        }

                    }
                }
            }
        }
        return $result;
    }
    //扒取课程概要等信息
    private function getCourseDetail($html){
        $result=array();
        //正则匹配
        $html=str_replace('<br/>','',$html);
        $html=$this->remove_html_tag($html);

//        $this->writeFile($html);
        $preg_courseinfo="/<\/div>(.*?)<\/br><\/br>/is";
        preg_match_all($preg_courseinfo,$html,$arr);
//        print_r($arr);
        $result['description']=$this->removeWhite($arr[1][6]);
        $result['reference']=$this->removeWhite($arr[1][4]);
        $result['teaching_content']=$this->removeWhite($arr[1][5]);
//        print_r($result);
        return $result;
    }

    //扒取课程信息
    private function allCourseHtmlToObject($html_classtable){
        $html_classtable=$this->removeWhite($html_classtable);
        $doc = new \DOMDocument();
        $doc->loadHTML($html_classtable);
        $xpath = new \DOMXPath($doc);
        $tablerows = $xpath->query('//tr[@align]/td');
        $result=array();
        $course=new NJUCouseInfo;
        for($i=0;$i<$tablerows->length;$i++){
            $row=$tablerows->item($i);
            if($i>=1){
                $course_content=$row->nodeValue;
//                print_r($course_content);
                switch($i%9){
                    case 1:
                        $course=new NJUCouseInfo;
                        $course->number=$course_content;
                        break;
                    case 2:
                        $course->name=$course_content;
                        break;
                    case 3:
                        $course->nature=$course_content;
                        break;
                    case 4:
                        $course->faculty=$course_content;
                        break;
                    case 5:
                        $course->credit=$course_content;
                        break;
                    case 6:
                        $course->period=$course_content;
                        break;
                    case 7:
                        $course->squre=$course_content;
                        break;
                    case 8:
                        $course->teacher=$course_content;
                        break;
                    case 0:
                        $course->time_place=$course_content;
                        $result[]=$course;
                        break;
                    default:
                        break;
                }
//                echo '</br>';
            }
        }
//        print_r($result);
        return $result;
    }



    private function CurrentCourseHtmlToObject($html){
//        $this->writeFile($html);
        $html=$this->removeWhite($html);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $tablerows = $xpath->query('//tr[@align]/td');

        $thead_size=10;

        $theads=$xpath->query('//th');
        if($theads->length>0){
            $thead_size=$theads->length;
        }

        $result=array();
        $course=new NJUCouseInfo;
        for($i=0;$i<$tablerows->length;$i++){
            $row=$tablerows->item($i);

            if($i>=2){
                $course_content=$row->nodeValue;
                switch($i%$thead_size){
                    case 2:
                        $course=new NJUCouseInfo;
                        $course->number=$course_content;
                        break;
                    case 4:
                        $course->name=$course_content;
                        break;
                    case 5:
                        $course->squre=$course_content;
                        break;
                    case 6:
                        $course->teacher=$course_content;
                        break;
                    case 7:
                        $course_content=$this->removeWhite($course_content);
                        $course->time_place=$course_content;
                        $result[]=$course;
                        break;
                    default:
                        break;
                }
            }
        }
        return $result;
    }

    private function teacherCurrentCourseHtmlToObject($html){
      $doc = new \DOMDocument();
      $html=$this->removeWhite($html);
      $meta = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
      $doc->loadHTML($meta.$html);
//      print_r($html);
      $xpath = new \DOMXPath($doc);
      $tablerows=$xpath->query("//tbody/tr/td");

        $table_size=10;
        $theads=$xpath->query('//table/thead/tr/td');
        if($theads->length>0){
            $table_size=$theads->length;
        }

      $result=array();
      $course=new NJUCouseInfo;
        for($i=0;$i<$tablerows->length;$i++){
            $row=$tablerows->item($i);
            $course_content=$row->nodeValue;
            $course_content=$this->removeWhite($course_content);
            switch($i%$table_size){
                case 0:
                    $course=new NJUCouseInfo;
                    $course->number=$course_content;
                    break;
                case 1:
                    $course->name=$course_content;
                    break;
                case 2:
                    $course->class_name=$course_content;
                    break;
                case 3:
                    $course->object=$course_content;
                    break;
                case 4:

                    $course->time_place=$course_content;
                    $course->teacher=$this->name;
                    $result[]=$course;
                    break;
                default:
                    break;
            }

    }
        return $result;
    }


    private function UserinfoHtmlToObject($html){
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $tablerows = $xpath->query('//tr[@height]/td[@class]');
        $user=new NJUUserInfo;
        for($i=0;$i<$tablerows->length;$i++){
            $row=$tablerows->item($i);
//            print_r($row);
//            echo '</br>';
            if($row->nodeValue=='学号'){
                $user->identify_id=$tablerows->item($i+1)->nodeValue;
            }
            if($row->nodeValue=='姓名'){
                $user->name=$tablerows->item($i+1)->nodeValue;
            }
            if($row->nodeValue=='所在院系'){
                $user->faculty=$tablerows->item($i+1)->nodeValue;
            }
            if($row->nodeValue=='所在专业'){
                $user->speciality=$tablerows->item($i+1)->nodeValue;
            }

        }
        //获取身份
        $info=$xpath->query('//div[@id="UserInfo"]');
        $str=$info->item(0)->nodeValue;
        $str=str_replace("    当前身份", "", $str);

        $arr=explode('：',$str);
        $identify=$arr[sizeof($arr)-1];
        $user->identify=$identify;
        $user->name=$arr[1];
        return $user;
    }

    private function teacherinfoToObject(){
        $user=new NJUUserInfo;
        $user->identify=$this->identify;
        $user->identify_id=$this->identify_id;
        $user->name=$this->name;
        return $user;
    }


    //返回的为课程对象数组
    public function getCurrentCourses(){
        $result=array();

        if($this->identify==NJUSpider::$ROLE_STUDENT) {
            $html=$this->getHTML(NJUSpider::$url_login,NJUSpider::$url_current_classtable);
            //TODO 如若出错返回0
//        if(){
//            return 0;
//        }
            $result=$this->CurrentCourseHtmlToObject($html);
        }elseif($this->identify==NJUSpider::$ROLE_TEACHER){
            $data=array(
                'method'=>'getCourseList'
            );
            $html=$this->getHTML(NJUSpider::$url_login,NJUSpider::$url_teacher_current_classtable_request,$data);
            $result=$this->teacherCurrentCourseHtmlToObject($html);
        }

        return $result;
    }

    public function getUserinfo(){
        $result=new NJUUserInfo();
        if($this->identify==NJUSpider::$ROLE_STUDENT){
            $html=$this->getHTML(NJUSpider::$url_login,NJUSpider::$url_userinfo);
            $result=$this->UserinfoHtmlToObject($html);
        }elseif($this->identify==NJUSpider::$ROLE_TEACHER){
            $result=$this->teacherinfoToObject();
        }

        return $result;
    }

    public function getAllCourse(){
//        echo 23;
        $allCourses=$this->getSchoolAllCourses();
        return $allCourses;
    }

    //将对象转化为数组
    public function formUserSource($user){
        $result=array();

        // {$userInfo['id']},'{$userInfo['name']}','{$userInfo['department']}','{$userInfo['head_icon']}','{$userInfo['identify']}','{$userInfo['identify_id']}',{$userInfo['school_id']})";
        $result['id']=0;
        $result['name']=$user->name;
        $result['department']=$user->faculty;
        $result['head_icon']='--';
        switch($user->identify){
            case "教师":
                $result['identify']=1;
                break;
            case "学生":
                $result['identify']=0;
                break;
            default :
                $result['identify']=1;
                break;
        }

        $result['identify_id']=$user->identify_id;
        $result['password']=sha1("12345");
        $result['school_id']='南京大学';
        $result['user_name']=$user->name;
        $result['signal_text']="This guy is lazy....";
        $result['speciality']=$user->speciality;
        return $result;

    }


    public function formatSignalCourse($course){

        $result=array();
        $result['id']=0;
        $result['course_id']=$course->number;
        $result['name']=$course->name;
        $result['teacher']=$course->teacher;
        $result['time_place']=$course->time_place;
        $result['school_id']='南京大学';
        $result['department_id']=0;

        return $result;
    }

    function formatCourse($courseArray){
        $size=sizeof($courseArray);
        $result=array();
        if($size<=0){
            return false;
        }
        for($i=0;$i<$size;$i++){
            $result[$i]=$this->formatSignalCourse($courseArray[$i]);
        }
        return $result;
    }


}





