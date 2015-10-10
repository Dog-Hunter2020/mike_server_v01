<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => __ROOT__ .'/Application/Wechat/View/Public',
        'IMG' => __ROOT__ .'/Application/Wechat/View/Public/Img',
        'JS' => __ROOT__ .'/Application/Wechat/View/Public/Js',
        'CSS' => __ROOT__ .'/Application/Wechat/View/Public/Css',
        'VIEW' => __ROOT__ .'/Application/Wechat/View',
        '__HOME__' => __ROOT__ .'/Application/Wechat',
    ),

//    'SHOW_PAGE_TRACE' =>true,
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => '127.0.0.1', // 服务器地址
    'DB_NAME'               => 'elearning_test',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => 'root',          // 密码
    'DB_PORT'               => '8889',        // 端口
    'DB_PREFIX'             => '',    // 数据库表前缀


    'CURRENT_TERM'=>20151,
    'LAST_TERM'=>20142,
    'BEFORE_TERM'=>20141,

    'ERROR_DB'=>"database error",
    'ERROR_USERNAME_PASSWORD_WRONG'=>"username or the password was wrong",
    'POST_PIC_PATH'=>'../post_pic/',

    'TEST_USERNAME'=>131250000,
    'TEST_PASSWORD'=>1234,

    'HEAD_ICONS'=>array(
        'http://i1.tietuku.com/9a3a60825015ecfcs.jpg',
        'http://i3.tietuku.com/2cf433b0dbb753cds.jpg',
        'http://i3.tietuku.com/9b9880945f2b45b9s.jpg',
        'http://i3.tietuku.com/344f10f13f9b0348s.jpg',
        'http://i3.tietuku.com/e4be941b6fd565dfs.jpg',
        'http://i3.tietuku.com/797d5a6151a55f62s.jpg',
        'http://i1.tietuku.com/5f787ffda8afa1fas.jpg',
        'http://i3.tietuku.com/60299b75f1958b45s.jpg',
        'http://i1.tietuku.com/c3daa32ab5ac31b8s.jpg',
        'http://i4.tietuku.com/6dc54c422275e6eas.jpg',
    )
    
);