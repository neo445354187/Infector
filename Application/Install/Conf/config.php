<?php
return array(
	'DB_TYPE'   => 'Pdo', // 数据库类型
	'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => 'fivech', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => 'root',  // 密码
	'DB_PORT'   => '3306', // 端口
	'DB_PREFIX' => 'fi_', // 数据库表前缀

	/* 模板相关配置 */
	'TMPL_PARSE_STRING' => array(
		'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
		'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
		'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
	)
);