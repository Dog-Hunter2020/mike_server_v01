<?php
/**
 * Created by PhpStorm.
 * User: gaoyang
 * Date: 15/10/6
 * Time: 上午11:35
 */

namespace Wechat\Model;
use Think\Model\RelationModel;

class UserModel extends RelationModel{
    protected $table_name='user';

    protected $_link = array(
//
//                        'Profile'=>array(
//
//                        'mapping_type'=>HAS_ONE,
//
//                        'mapping_name'=>'Profile',
//
//                        'class_name'=>'Profile',
//
//                        'foreign_key'=>'user_id',
//
//                        ),
//
//                        'Dept'=> array(
//
//                        'mapping_type'=> BELONGS_TO,
//
//                        'mapping_name'=>'Dept',
//
//                        'class_name'=>'Dept',
//
//                        'foreign_key'=>'dept_id',
//
//                        ),
//
//                        'Card'=> array(
//
//                        'mapping_type'=> HAS_MANY,
//
//                        'mapping_name'=>'Card',
//
//                        'class_name'=>'Card',
//
//                        'foreign_key'=>'user_id',
//
//                        ),
                        
                        'Course'=> array(
                        
                        'mapping_type'=> self::MANY_TO_MANY,
                        
                        'mapping_name'=>'Course',
                        
                        'class_name'=>'Course',
                        
                        'foreign_key'=>'user_id',
                        
                        'relation_foreign_key'=>'course_id',
                        
                        'relation_table'=>'user_course_relation',
                        
                        ),

                    );
} 