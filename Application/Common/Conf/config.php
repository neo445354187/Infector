<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE'   => array( 'Addons' => ONETHINK_ADDON_PATH ), //扩展模块列表
    'DEFAULT_MODULE'       => 'Home',
    //'MODULE_ALLOW_LIST'    => array( 'Home', 'Admin' ),
    'MODULE_DENY_LIST'     => array( 'Common', 'Runtime', 'Api' ),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY'        => 'VZfT(yYW{SdL1UjXME29c-["Bm<,5#ACN]rkl/s|', //默认数据加密KEY

    /* 调试配置 */
    'SHOW_PAGE_TRACE'      => false,

    /* 用户相关设置 */
    'USER_MAX_CACHE'       => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR'   => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER'       => 'htmlspecialchars', //全局过滤函数

    /* 数据库配置 */
    'DB_TYPE'   => 'Pdo', // 数据库类型
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码
    'DB_PREFIX' => 'fi_', // 数据库表前缀
    'DB_DSN'    => 'mysql:host=127.0.0.1;dbname=fivech;charset=utf8',
    'DB_BIND_PARAM' => true
);
