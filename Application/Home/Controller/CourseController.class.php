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
use Think\Model;

class CourseController extends \Think\Controller{

    /*
     * description: 获取课程信息(用id精确查询)
     * return: array()
     * courseId(String)	courseName(String)	courseType(String)	academyName(String)	teacherIds(List<String>)	teacherNames(List<String>)	annoucement(CourseAnnoucement)	currentStudents(int)	outline(String)	teachContent(String)	references(List<String>)	assistantIds(List<String>)	assistantNames(List<String>)
     */

    public function getCourseDetail($courseId=0){

//        assert($courseId>0,"courseId error!");

        $FormCourse = M('course');
        $courseData = $FormCourse->find($courseId);

        $FormCourseInfo = M('course_info');
        $courseInfoData = $FormCourseInfo->find($courseData['course_info_id']);

        $courseInfoData['course_id'] = $courseId;
        $courseInfoData['semester'] = $courseData['semester'];
        $courseInfoData['grade'] = $courseData['grade'];
        $courseInfoData['time_place'] = $courseData['time_place'];

        $courseInfoData = $this->raryCourseInfo($courseInfoData);

        $this->ajaxReturn($courseInfoData,'JSON');
    }
    private function raryCourseInfo($courseInfo){
        $FormRary = M('course_rary');
        $condition['course_id'] = $courseInfo['course_id'];
        $courseRaryData = $FormRary->where($condition)->select();

        foreach($courseRaryData as $courseRary){
            $courseInfoData[$courseRary['field_name']] = $courseRary['field_content'];
        }
        return $courseInfo;
    }
    /*
     *description:更改课程的相关信息，信息种类在CourseInfoTypeEnum这个类中
     *return: bool
     *
     */
    public function setCourseInfo(){
        $courseId = $_POST['course_id'];
        $changes = $_POST['changes'];
        $data = array();
        foreach($changes as $key=>$value){
            if($key === 'semester' || $key === 'grade' || $key === 'time_place'){
                $data[$key]=$value;
            }
        }
        if(count(array_keys($data)) > 0){
            $data['id'] = $courseId;
            $CourseModel = M('course');
            $CourseModel->save($data);
        }
        $this->ajaxReturn($_POST,'JSON');
//        $arr=json_decode($_POST['changes'],true);
//        echo $arr;
    }
    /*
     * description:利用课程信息进行模糊查询
     * process:先模糊查询出ID list 再用id精确查询
     * return:array()
     *目前只是利用name进行查询
     */

    public function getCoursesByInfo($key){
        $CourseNameData = $this->search('name',$key);
        $this->ajaxReturn($CourseNameData,'JSON');

    }

    private function search($field_name,$key){
        $result = array();
        $CourseInfoModel = M('course_info');
        $map[$field_name] = array('like','%'.$key.'%');
        $CoursesData = $CourseInfoModel->where($map)->select();
        $CourseModel = M('course');
        $CourseRaryModel = M('course_rary');
        for($i=0;$i<count($CoursesData);$i++){
            $course_info_id = $CoursesData[$i]['id'];
            $field_content = $CoursesData[$i][$field_name];
            $condition['course_info_id'] = $course_info_id;
            $candidate_courses = $CourseModel->where($condition)->select();
            for($j=0;$j<count($candidate_courses);$j++){
                $condition1['course_id'] = $candidate_courses[$j]['id'];
                $condition1['field_name'] = $field_name;
                $courseRaryData = $CourseRaryModel->where($condition1)->select();
                if(count($courseRaryData) == 0){
                    $result[] = array($candidate_courses[$j]['id'] => $field_content);
                }else{
                    if(strstr($courseRaryData[0]['field_content'],$key)){
                        $result[] = array($candidate_courses[$j]['id'] => $field_content);
                    }
                }
            }
        }
        $condition2['field_name'] = $field_name;
        $condition2['field_content'] = array('like','%'.$key.'%');
        $courseRaryData = $CourseRaryModel->where($condition2)->select();
        for($i=0;$i<count($courseRaryData);$i++){
            if(!array_key_exists($courseRaryData[$i]['course_id'])){
                $result[] = array($courseRaryData[$i]['course_id'] => $courseRaryData[$i]['field_content']);
            }
        }
        $this->ajaxReturn($result,'JSON');

    }
    /*
     * description:获取course表的fields
     *
     */

    public function getCourseTableFields(){
        $fields = array('课程名称','课程内容','课程介绍','参考资料','上课时间地点','上课年级','课程学期');
        $this->ajaxReturn($fields,'JSON');

    }
    /*
     * description:获取以startId为开始id，number个的课程数目
     * return array(array())
     * 用index会加快访问速度且数据处理更加简单
     */

    public function getCourseInfoRange($index,$number){
        $result=array();
//        assert($startId>0&&$number>0,"startId or number error");
        $CourseModel = new Model();
        $CoursesData = $CourseModel->query("select * from course LIMIT %d,%d",array($index,$number));
        $FormCourseInfo = M('course_info');
        for($i=0;$i<count($CoursesData);$i++){
            $courseInfoData = $FormCourseInfo->find($CoursesData[$i]['course_info_id']);
            $courseInfoData['course_id'] = $CoursesData[$i]['id'];
            $courseInfoData['semester'] = $CoursesData[$i]['semester'];
            $courseInfoData['grade'] = $CoursesData[$i]['grade'];
            $courseInfoData['time_place'] = $CoursesData[$i]['time_place'];

            $courseInfoData = $this->raryCourseInfo($courseInfoData);
            $result[] = $courseInfoData;

        }
        $this->ajaxReturn($result,'JSON');

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



        $FormCourseRary = M('course_rary');
        $condition['course_id'] = $id;
        $FormCourseRary->where($condition)->delete();
        
        $FormCourse = M('course');
        $result = $FormCourse->delete($id);
        $return = array();
        if($result == false){
            $return['result'] = false;
        }else{
            $return['result'] = true;
        }
        $this->ajaxReturn($return,'JSON');
    }
    /*
     * description:获取user的课程的简介信息
     * return:List<CourseBriefInfos>
     *
     * CourseBriefInfos:
     *
     *courseId(String)	courseName(String)	academyName(String)	courseType(String)	teacherName(String)	courseImageUrl(String)
     *
     */

    public function  getMyCourseBriefInfos($studenId){

    }

    /*
     * description:获取某个学校的(beginPosition＋num)个课程的简介信息
     * return:List<CourseBriefInfos>
     *
     *
     *
     * CourseBriefInfos:
     *
     *courseId(String)	courseName(String)	academyName(String)	courseType(String)	teacherName(String)	courseImageUrl(String)
     *
     */
    public function  getAllCourses($schoolId,$beginPosition, $num){

    }

    /*
     * description:老师创建新的课程
     * return:bool
     */

    public function createNewCourse($createTeacherId, $courseName, $courseBrief, $teachContent, $teacherIdList, $assistantIdList){

    }

}