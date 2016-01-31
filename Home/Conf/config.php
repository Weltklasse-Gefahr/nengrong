<?php
return array(
	//'配置项'=>'配置值'
	    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '172.16.100.2', // 服务器地址
    'DB_NAME'               =>  'nengrongweb',          // 数据库名
    'DB_USER'               =>  'enetf',      // 用户名
    'DB_PWD'                =>  'enetf.123',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'ENF_',    // 数据库表前缀
    'DB_CHARSET'            =>  'utf8',  // 字符集

    //邮件配置
    'THINK_EMAIL' => array(
        'SMTP_HOST'   => 'smtp.qq.com', //SMTP服务器
        'SMTP_PORT'   => '465', //SMTP服务器端口
        'SMTP_USER'   => 'qianqiang1989@qq.com', //SMTP服务器用户名
        'SMTP_PASS'   => 'khhaktresduxbihe', //SMTP服务器密码
        'FROM_EMAIL'  => 'qianqiang1989@qq.com', //发件人EMAIL
        'FROM_NAME'   => '能融网', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
    ),

);