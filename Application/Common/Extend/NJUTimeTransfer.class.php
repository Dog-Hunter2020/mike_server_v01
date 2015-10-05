<?php
namespace Common\Extend;
/**
* 
*/
class NJUTimeTransfer
{

	private $week=array(
        '周日','周一','周二','周三','周四','周五','周六'
    );
    private $class_time=array(
        "1-2"=>"08:00:00-09:50:00",
        "1-3"=>"08:00:00-11:00:00",
        "1-4"=>"08:00:00-12:00:00",
        "2-3"=>"09:00:00-10:50:00",
        "2-4"=>"09:00:00-12:00:00",
        "3-4"=>"10:10:00-12:00:00",
        "5-6"=>"14:00:00-15:50:00",
        "5-7"=>"14:00:00-17:00:00",
        "5-8"=>"14:00:00-18:00:00",
        "6-7"=>"15:00:00-17:00:00",
        "6-8"=>"15:00:00-18:00:00",
        "7-8"=>"16:10:00-18:00:00",
        "9-10"=>"18:30:00-20:20:00",
        "9-11"=>"18:30:00-21:30:00",

    );

    function __construct(){

    }

    //专门用于输出数组
    function p($array){
        foreach($array as $v){
            print_r($v);
            echo '<br/>';
        }
    }

    function classToTime($nju_course){
        if(strpos($nju_course,"1-2")!=false){
            return "08:00:00-09:50:00";
        }
        else if(strpos($nju_course,"1-3")!=false){
            return "08:00:00-11:00:00";
        }
        else if(strpos($nju_course,"1-4")!=false){
            return "08:00:00-12:00:00";
        }
        else if(strpos($nju_course,"2-3")!=false){
            return "09:00:00-10:50:00";
        }
        else if(strpos($nju_course,"2-4")!=false){
            return "09:00:00-12:00:00";
        }
        else if(strpos($nju_course,"3-4")!=false){
            return "10:10:00-12:00:00";
        }
        else if(strpos($nju_course,"5-6")!=false){
            return "14:00:00-15:50:00";
        }
        else if(strpos($nju_course,"5-7")!=false){
            return "14:00:00-17:00:00";
        }
        else if(strpos($nju_course,"5-8")!=false){
            return "14:00:00-18:00:00";
        }
        else if(strpos($nju_course,"6-7")!=false){
            return "15:00:00-17:00:00";
        }
        else if(strpos($nju_course,"6-8")!=false){
            return "15:00:00-18:00:00";
        }
        else if(strpos($nju_course,"7-8")!=false){
            return "16:10:00-18:00:00";
        }
        else if(strpos($nju_course,"9-10")!=false){
            return "18:30:00-20:20:00";
        }
        else if(strpos($nju_course,"9-11")!=false){
            return "18:30:00-21:30:00";
        }
    }
    //判断时间是否处于某个时间区间
    private function isBetweenSection($time,$section){
        $begin_end=explode('-',$section);
        if($time>=$begin_end[0] and $time<=$begin_end[1]){
            return true;
        }else{
            return false;
        }
    }
    //传入的time为时间
    function timeToClass($time){
        $sections=array();
        $result=array();
        $week=date('w',$time);
        $time=date('H:i:s',$time);
        foreach($this->class_time as $key=>$value){
            if($this->isBetweenSection($time,$value)){
                $sections[]=$key;
                $result[]=$this->week[$week].'第'.$key.'节';
            }
        }
        return $result;
    }


    //0代表周日
    function weekToTime($week){
        //课程日期

        if($week=="周一"){
            return 1;
        }
        else if($week=="周二"){
            return 2;
        }
        else if($week=="周三"){
            return 3;
        }
        else if($week=="周四"){
            return 4;
        }
        else if($week=="周五"){
            return 5;
        }
        else if($week=="周六"){
            return 6;
        }
        else if($week=="周日"){
            return 0;
        }
    }

    //哪些周包含该门课,0代表单周，1代表双周
    private function dateToTime($date){
        //即使是空字符，"">=0成立，空字符大于等于0返回1
        if($date=="单周"){
            return 0;
        }
        else if($date=="双周"){
            return 1;
        }
        else{
            //一个汉字算3个字符单位
            return substr($date,0,strlen($date)-3);
        }
    }

    //将课程周时间转换为具体的年月日
    function courseTimeToArray($courseinfo){
        $begin_time=date("Y-m-d",strtotime("2015-3-2"));
        $end_time=date("Y-m-d",strtotime("2015-7-5"));
        $allweeks=18;
        $dates=array();
        //如果是双周
        if($courseinfo[2]=="1"){
            for($i=2;$i<$allweeks;$i+=2){
                $dates[]=date("Y-m-d",strtotime($begin_time.''.($courseinfo[0]-1+($i-1)*7).' day'));
            }
        }else if($courseinfo[2]=="0"){
            for($i=1;$i<$allweeks;$i+=2){
                $dates[]=date("Y-m-d",strtotime($begin_time.''.($courseinfo[0]-1+($i-1)*7).' day'));
            }
        }else{
            $begin_end=explode("-",$courseinfo[2]);
            for($i=$begin_end[0];$i<$begin_end[1];$i++){
                $dates[]=date("Y-m-d",strtotime($begin_time.''.($courseinfo[0]-1+($i-1)*7).' day'));
            }
        }
        //将课程的周数转换为具体的日期
        $courseinfo[2]=$dates;
        return $courseinfo;
    }

    function njuTimeTransfer($courseInfo){
        $result=array();
        if(is_array($courseInfo)){
            for($i=0;$i<sizeof($courseInfo);$i++){
                $courseInfo[$i]["time_place"]=trim($courseInfo[$i]["time_place"]);
                $strarray=explode(" ",$courseInfo[$i]["time_place"]);
                //            print_r($strarray);
                // TODO 没有分析某些特殊的数组
                if(sizeof($strarray)%4==0){
                    $info=array();//
                    //[0]代表周几，[1]代表第几节,[2]代表第几周[3]地点[4]name
                    for($j=0;$j<sizeof($strarray);$j++){
                        if($j%4==0&&$j!=0){
                            $result[]=$info;
                        }
                        $info[$j%4]=$strarray[$j];
                        if($j%4==3){
                            $info[4]=$courseInfo[$i]["name"];
                            $info[5]=$courseInfo[$i]["course_id"];
                        }
                    }
                    $result[]=$info;
                }

            }
            //result的转换
            for($k=0;$k<sizeof($result);$k++){
                $result[$k][0]=$this->weekToTime($result[$k][0]);
                $result[$k][1]=$this->classToTime($result[$k][1]);
                $result[$k][2]=$this->dateToTime($result[$k][2]);
                $result[$k]=$this->courseTimeToArray($result[$k]);
            }
            //        p($result);
            return $result;
        }else{
            return "error in njuTimeTransfer";
        }

    }


    function classTableToActivities($courses){
        $activities=array();
        $course=array();
        foreach($courses as $v){
            $course['name']=$v[4];
            $course['place']=$v[3];
            $time=explode("-",$v[1]);
            foreach($v[2] as $day){
                $course["begin_time"]=$day.' '.$time[0];
                $course["end_time"]=$day.' '.$time[1];
                $activities[]=$course;
            }
        }
        return $activities;
    }

}

?>