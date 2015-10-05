<?php
namespace Wechat\Controller;
use Think\Controller;
class CourseController extends CommonController {
    public function index(){
        
    }

    function findClassByTimeAndPlace($course_id,$time_place,$teacher){
	    $time_place=str_replace(' ', '', $time_place);

	}

	//$time为时间戳,course_id为课程编号，而非id
	function findClassByTime($course_id,$time,$teacher){
	    $courseModel=M('course');
	    $courses=$courseModel->select(array('course_id'=>$course_id));
	    $result=array();

	    $tran=new \Common\Extend\NJUTimeTransfer();
	    for($i=0;$i<sizeof($courses);$i++){
	        if(strpos($courses[$i]['teacher'],$teacher)!=false){
	            $sections=$tran->timeToClass($time);
	            foreach($sections as $k=>$value){
	                if(strpos(str_replace(' ','',$courses[$i]['time_place']),$value)){
	                    $result[]=$value;
	                }
	            }
	        }
	    }
	    return $result;
	}

	//通过时间地点判断两门课是否相同
	function isTheSameClassByTimePlace($course_one_time_place,$course_two_time_place){
	//        print_r('======='.$course_one_time_place.'======'.$course_two_time_place);
	//        print_r('--------');
	        //合并空格
	        $course_one_time_place=preg_replace("/[\s]+/is"," ",$course_one_time_place);
	        $course_two_time_place=preg_replace("/[\s]+/is"," ",$course_two_time_place);

	        $one_time_place_arr=explode('||',$course_one_time_place);
	        $two_time_place_arr=explode('||',$course_two_time_place);

	        //若长度不同则诶不同班级
	        if(sizeof($one_time_place_arr)!=sizeof($two_time_place_arr)){
	            return 0;
	        }

	        $arr_size=sizeof($one_time_place_arr);
	        //外循环为course_one内循环为course_two
	        for($i=0;$i<$arr_size;$i++){
	            for($j=0;$j<$arr_size;$j++){
	                //若数组相同则判断下一组,防止信息顺序不同，所以打散
	                if(!array_diff(explode(' ',$one_time_place_arr[$i]),explode(' ',$two_time_place_arr[$j]))){
	                    break;
	                }
	                //如果遍历course_two中未找到和course_one中time_place_arr[$i]相同数组，则为不同课程
	                if($j==($arr_size-1)){
	                    return 0;
	                }
	            }
	        }

	        return 1;
	}
}

?>