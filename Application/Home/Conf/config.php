<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__IMG__'    => __ROOT__. '/Public/'.MODULE_NAME.'/default/images',
        '__CSS__'    => __ROOT__. '/Public/'.MODULE_NAME.'/default/css',
        '__JS__'     => __ROOT__. '/Public/'.MODULE_NAME.'/default/js',
        '__MIMG__'   => __ROOT__. '/Public/'.MODULE_NAME.'/mobile/images',
        '__MCSS__'   => __ROOT__. '/Public/'.MODULE_NAME.'/mobile/css',
        '__MJS__'    => __ROOT__. '/Public/'.MODULE_NAME.'/mobile/js',
    ),

    // 预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Article,OT\\TagLib\\Think',

    /* 主题设置 */
    'DEFAULT_THEME' =>  'default',  // 默认模板主题名称

    /* 数据缓存设置 */
    'DATA_CACHE_TYPE'   => 'File', // 数据缓存类型
    'DATA_CACHE_PREFIX' => 'home_', // 缓存前缀

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'home_', //session前缀
    'COOKIE_PREFIX'  => 'home_', // Cookie前缀 避免冲突

    'HTML_CACHE_ON'   => false, // 开启静态缓存
    'HTML_CACHE_TIME' => 36000,    //缓存存在时间10小时
    'HTML_FILE_SUFFIX' => '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES' => array(
        'Index:index' => array( '{:module}{:controller}{:action}_{|is_mobile_ui}' )
    )
);
