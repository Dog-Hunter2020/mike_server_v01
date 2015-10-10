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
	    $courses=$courseModel->where(array('course_id'=>$course_id))->select();
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

    //重新导入课程
    function importCourse($user_id,$identify_id,$password){

        $spider=new \Common\Extend\NJU\spider\NJUSpider($identify_id,$password);
        $userinfo=$spider->getUserinfo();
        $courses=$spider->getCurrentCourses();
        $courseArray=$spider->formatCourse($courses);
        if(is_array($courseArray)){
            $length=sizeof($courseArray);
        }
        else{
            $length=0;
        }

        $courseModel=M('course');
        $relationModel=M('user_course_relation');
        for($i=0;$i<$length;$i++) {
            //todo 添加课程的筛选条件待测试
            $course_id=$courseArray[$i]['course_id'];
            $coursesByCourseId=$courseModel->where(array("course_id"=>$course_id))->select();
            foreach($coursesByCourseId as $k=>$v){

                //教师不同则定为不同的课
                if(!(strpos($v['teacher'],$courseArray[$i]["teacher"])>=0)){
                    unset($coursesByCourseId[$k]);
                    continue;
                }

                if(!$this->isTheSameClassByTimePlace($courseArray[$i]['time_place'],$v['time_place'])){

                    unset($coursesByCourseId[$k]);
                }
            }
            $coursesByCourseId=array_values($coursesByCourseId);

            if(!$coursesByCourseId){

                $k = $courseModel->add($courseArray[$i]);
                echo $k;
                //说明是新课，需要添加地点和时间
            }
            else {
                $k=$coursesByCourseId[0]['id'];
            }
            $root=0;
            //如果是老师则root为1
            if($userinfo->identify==1){
                $root=1;
            }

            if(!$relationModel->where(array('user_id'=>$user_id,'course_id'=>$k))->find()){
                $relationModel->add(array('user_id'=>$user_id,'course_id'=>$k,'root'=>$root));
            }
        }

    }
}

?>