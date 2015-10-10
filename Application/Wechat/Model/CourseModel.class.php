<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/6
 * Time: 下午12:40
 */

namespace Wechat\Model;
use Think\Model\RelationModel;

class CourseModel extends RelationModel{
    protected $table_name='course';

    protected $_link = array(

                        'Notice'=>array(

                        'mapping_type'=>self::HAS_MANY,

                        'mapping_name'=>'Notice',

                        'class_name'=>'Notice',

                        'foreign_key'=>'course_id',

                        ),

                        'User'=> array(

                            'mapping_type'=> self::MANY_TO_MANY,

                            'mapping_name'=>'User',

                            'class_name'=>'User',

                            'foreign_key'=>'course_id',

                            'relation_foreign_key'=>'user_id',

                            'relation_table'=>'user_course_relation',

                        ),
//
    );

} 