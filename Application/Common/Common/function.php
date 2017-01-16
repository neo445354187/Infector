<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// OneThink常量定义
const ONETHINK_VERSION    = '1.0.131218';
const ONETHINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 *
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login() {
    $user = session( 'user_auth' );
    if ( empty( $user ) ) {
        return 0;
    } else {
        return session( 'user_auth_sign' ) == data_auth_sign( $user ) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 *
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator( $uid = null ) {
    $uid = is_null( $uid ) ? is_login() : $uid;
    return $uid && ( intval( $uid ) === C( 'USER_ADMINISTRATOR' ) );
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 *
 * @param string  $str  要分割的字符串
 * @param string  $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr( $str, $glue = ',' ) {
    return explode( $glue, $str );
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 *
 * @param array   $arr  要连接的数组
 * @param string  $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str( $arr, $glue = ',' ) {
    return implode( $glue, $arr );
}

/**
 * 字符串截取，支持中文和其他编码
 *
 * @static
 * @access public
 * @param string  $str     需要转换的字符串
 * @param string  $start   开始位置
 * @param string  $length  截取长度
 * @param string  $charset 编码格式
 * @param string  $suffix  截断显示字符
 * @return string
 */
function msubstr( $str, $start=0, $length, $charset='utf-8', $suffix=true ) {
    if ( function_exists( 'mb_substr' ) ) {
        $slice = mb_substr( $str, $start, $length, $charset );
    }else if ( function_exists( 'iconv_substr' ) ) {
            $slice = iconv_substr( $str, $start, $length, $charset );
            if ( false === $slice ) {
                $slice = '';
            }
        }else {
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all( $re[$charset], $str, $match );
        $slice = join( "", array_slice( $match[0], $start, $length ) );
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 系统加密方法
 *
 * @param string  $data   要加密的字符串
 * @param string  $key    加密密钥
 * @param int     $expire 过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt( $data, $key = '', $expire = 0 ) {
    $key  = md5( empty( $key ) ? C( 'DATA_AUTH_KEY' ) : $key );
    $data = base64_encode( $data );
    $x    = 0;
    $len  = strlen( $data );
    $l    = strlen( $key );
    $char = '';

    for ( $i = 0; $i < $len; $i++ ) {
        if ( $x == $l ) $x = 0;
        $char .= substr( $key, $x, 1 );
        $x++;
    }

    $str = sprintf( '%010d', $expire ? $expire + time():0 );

    for ( $i = 0; $i < $len; $i++ ) {
        $str .= chr( ord( substr( $data, $i, 1 ) ) + ( ord( substr( $char, $i, 1 ) ) )%256 );
    }
    return str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), base64_encode( $str ) );
}

/**
 * 系统解密方法
 *
 * @param string  $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string  $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt( $data, $key = '' ) {
    $key    = md5( empty( $key ) ? C( 'DATA_AUTH_KEY' ) : $key );
    $data   = str_replace( array( '-', '_' ), array( '+', '/' ), $data );
    $mod4   = strlen( $data ) % 4;
    if ( $mod4 ) {
        $data .= substr( '====', $mod4 );
    }
    $data   = base64_decode( $data );
    $expire = substr( $data, 0, 10 );
    $data   = substr( $data, 10 );

    if ( $expire > 0 && $expire < time() ) {
        return '';
    }
    $x      = 0;
    $len    = strlen( $data );
    $l      = strlen( $key );
    $char   = $str = '';

    for ( $i = 0; $i < $len; $i++ ) {
        if ( $x == $l ) $x = 0;
        $char .= substr( $key, $x, 1 );
        $x++;
    }

    for ( $i = 0; $i < $len; $i++ ) {
        if ( ord( substr( $data, $i, 1 ) )<ord( substr( $char, $i, 1 ) ) ) {
            $str .= chr( ( ord( substr( $data, $i, 1 ) ) + 256 ) - ord( substr( $char, $i, 1 ) ) );
        }else {
            $str .= chr( ord( substr( $data, $i, 1 ) ) - ord( substr( $char, $i, 1 ) ) );
        }
    }
    return base64_decode( $str );
}

/**
 * 数据签名认证
 *
 * @param array   $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign( $data ) {
    //数据类型检测
    if ( !is_array( $data ) ) {
        $data = (array)$data;
    }
    ksort( $data ); //排序
    $code = http_build_query( $data ); //url编码并生成query字符串
    $sign = sha1( $code ); //生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 *
 * @access public
 * @param array   $list   查询结果
 * @param string  $field  排序的字段名
 * @param array   $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by( $list, $field, $sortby='asc' ) {
    if ( is_array( $list ) ) {
        $refer = $resultSet = array();
        foreach ( $list as $i => $data )
            $refer[$i] = &$data[$field];
        switch ( $sortby ) {
        case 'asc': // 正向排序
            asort( $refer );
            break;
        case 'desc':// 逆向排序
            arsort( $refer );
            break;
        case 'nat': // 自然排序
            natcasesort( $refer );
            break;
        }
        foreach ( $refer as $key=> $val )
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 把返回的数据集转换成Tree
 *
 * @param array   $list  要转换的数据集
 * @param string  $pid   parent标记字段
 * @param string  $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree( $list, $pk='id', $pid = 'pid', $child = '_child', $root = 0 ) {
    // 创建Tree
    $tree = array();
    if ( is_array( $list ) ) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ( $list as $key => $data ) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ( $list as $key => $data ) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ( $root == $parentId ) {
                $tree[] =& $list[$key];
            }else {
                if ( isset( $refer[$parentId] ) ) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param array   $tree  原来的树
 * @param string  $child 孩子节点的键
 * @param string  $order 排序显示的键，一般是主键 升序排列
 * @param array   $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list( $tree, $child = '_child', $order='id', &$list = array() ) {
    if ( is_array( $tree ) ) {
        $refer = array();
        foreach ( $tree as $key => $value ) {
            $reffer = $value;
            if ( isset( $reffer[$child] ) ) {
                unset( $reffer[$child] );
                tree_to_list( $value[$child], $child, $order, $list );
            }
            $list[] = $reffer;
        }
        $list = list_sort_by( $list, $order, $sortby='asc' );
    }
    return $list;
}

/**
 * 格式化字节大小
 *
 * @param number  $size      字节数
 * @param string  $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes( $size, $delimiter = '' ) {
    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
    for ( $i = 0; $size >= 1024 && $i < 5; $i++ ) $size /= 1024;
    return round( $size, 2 ) . $delimiter . $units[$i];
}

/**
 * 处理插件钩子
 *
 * @param string  $hook   钩子名称
 * @param mixed   $params 传入参数
 * @return void
 */
function hook( $hook, $params=array() ) {
    \Think\Hook::listen( $hook, $params );
}

/**
 * 获取插件类的类名
 *
 * @param strng   $name 插件名
 */
function get_addon_class( $name ) {
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 *
 * @param string  $name 插件名
 */
function get_addon_config( $name ) {
    $class = get_addon_class( $name );
    if ( class_exists( $class ) ) {
        $addon = new $class();
        return $addon->getConfig();
    }else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param string  $url   url
 * @param array   $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url( $url, $param = array() ) {
    $url        = parse_url( $url );
    $case       = C( 'URL_CASE_INSENSITIVE' );
    $addons     = $case ? parse_name( $url['scheme'] ) : $url['scheme'];
    $controller = $case ? parse_name( $url['host'] ) : $url['host'];
    $action     = trim( $case ? strtolower( $url['path'] ) : $url['path'], '/' );

    /* 解析URL带的参数 */
    if ( isset( $url['query'] ) ) {
        parse_str( $url['query'], $query );
        $param = array_merge( $query, $param );
    }

    /* 基础参数 */
    $params = array(
        '_addons'     => $addons,
        '_controller' => $controller,
        '_action'     => $action,
    );
    $params = array_merge( $params, $param ); //添加额外参数

    return U( 'Addons/execute', $params );
}

/**
 * 时间戳格式化
 *
 * @param int     $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format( $time = NULL, $format='Y-m-d H:i' ) {
    $time = $time === NULL ? NOW_TIME : intval( $time );
    return date( $format, $time );
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 *
 * @param string  $name 格式 [模块名]/接口名/方法名
 * @param array|string $vars 参数
 */
function api( $name, $vars=array() ) {
    $array     = explode( '/', $name );
    $method    = array_pop( $array );
    $classname = array_pop( $array );
    $module    = $array? array_pop( $array ) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if ( is_string( $vars ) ) {
        parse_str( $vars, $vars );
    }
    return call_user_func_array( $callback, $vars );
}

/**
 * 根据条件字段获取指定表的数据
 *
 * @param mixed   $value     条件，可用常量或者数组
 * @param string  $condition 条件字段
 * @param string  $field     需要返回的字段，不传则返回整个数据
 * @param string  $table     需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field( $value = null, $condition = 'id', $field = null, $table = null ) {
    if ( empty( $value ) || empty( $table ) ) {
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M( ucfirst( $table ) )->where( $map );
    if ( empty( $field ) ) {
        $info = $info->field( true )->find();
    }else {
        $info = $info->getField( $field );
    }
    return $info;
}

if ( !function_exists( 'array_column' ) ) {
    function array_column( array $input, $columnKey, $indexKey = null ) {
        $result = array();
        if ( null === $indexKey ) {
            if ( null === $columnKey ) {
                $result = array_values( $input );
            } else {
                foreach ( $input as $row ) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if ( null === $columnKey ) {
                foreach ( $input as $row ) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ( $input as $row ) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}
