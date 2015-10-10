<?php
return array(
	//'配置项'=>'配置值'
 //数据库配置
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => '127.0.0.1', // 服务器地址
    'DB_NAME'               => 'elearning_test',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => 'root',          // 密码
    'DB_PORT'               => '8889',        // 端口
    'DB_PREFIX'             => '',    // 数据库表前缀
	//点语法默认解析
	'TMPL_VAR_IDENTIFY' => 'array',
	//模板路径
	// 'TMPL_FILE_DEPR' => '_',
    'URL_HTML_SUFFIX'=>'',//U方法尾静态后缀

    'RETURN_TYPE'=>'JSON',
    'DEFAULT_VALUE_NONE'=>0,

    'DEFAULT_MODULE'=> 'Wechat',

    'TABLE_CLASS_QUESTION'  => 'class_question',
    'TABLE_COURSE'  => 'course',
    'TABLE_COURSE_ATTENTION'  => 'course_attention',
    'TABLE_COURSE_INFO'  => 'course_info',
    'TABLE_COURSE_NOTICE'  => 'course_notice',
    'TABLE_COURSE_RARY'  => 'course_rary',
    'TABLE_DEPARTMENT'  => 'department',
    'TABLE_MAJOR'  => 'major',
    'TABLE_POST'  => 'post',
    'TABLE_POST_ATTENTION'  => 'post_attention',
    'TABLE_QUESTION_ANSWER'  => 'question_answer',
    'TABLE_SCHOOL'  => 'school',
    'TABLE_USER'=>'user',
    'TABLE_USER_ATTENTION'=>'user_attention',

    'DEFAULT_TIMEZONE'=>'Asia/Shanghai',
    'CURRENT_TERM'=>20151



);