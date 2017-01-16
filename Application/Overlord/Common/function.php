<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

function int_to_string( &$data, $map=array( 'status'=>array( 1=>'正常', -1=>'删除', 0=>'禁用', 2=>'未审核', 3=>'草稿' ) ) ) {
    if ( $data === false || $data === null ) {
        return $data;
    }
    $data = (array)$data;
    foreach ( $data as $key => $row ) {
        foreach ( $map as $col=>$pair ) {
            if ( isset( $row[$col] ) && isset( $pair[$row[$col]] ) ) {
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 获取配置的类型
 *
 * @param string  $type 配置类型
 * @return string
 */
function get_config_type( $type=0 ) {
    $list = C( 'CONFIG_TYPE_LIST' );
    return $list[$type];
}

/**
 * 获取配置的分组
 *
 * @param string  $group 配置分组
 * @return string
 */
function get_config_group( $group=0 ) {
    $list = C( 'CONFIG_GROUP_LIST' );
    return $group?$list[$group]:'';
}

/**
 * 动态扩展左侧菜单,base.html里用到
 *
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
function extra_menu( $extra_menu, &$base_menu ) {
    foreach ( $extra_menu as $key=>$group ) {
        if ( isset( $base_menu['child'][$key] ) ) {
            $base_menu['child'][$key] = array_merge( $base_menu['child'][$key], $group );
        }else {
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 检测验证码
 *
 * @param integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify( $code, $id = 1 ) {
    $verify = new \Think\Verify();
    return $verify->check( $code, $id );
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr( $string ) {
    $array = preg_split( '/[,;\r\n]+/', trim( $string, ",;\r\n" ) );
    if ( strpos( $string, ':' ) ) {
        $value  =   array();
        foreach ( $array as $val ) {
            list( $k, $v ) = explode( ':', $val );
            $value[$k]   = $v;
        }
    }else {
        $value  =   $array;
    }
    return $value;
}

// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr( $string ) {
    if ( 0 === strpos( $string, ':' ) ) {
        // 采用函数定义
        return   eval( substr( $string, 1 ).';' );
    }
    $array = preg_split( '/[,;\r\n]+/', trim( $string, ",;\r\n" ) );
    if ( strpos( $string, ':' ) ) {
        $value  =   array();
        foreach ( $array as $val ) {
            list( $k, $v ) = explode( ':', $val );
            $value[$k]   = $v;
        }
    }else {
        $value  =   $array;
    }
    return $value;
}

/**
 * 系统非常规MD5加密方法
 *
 * @param string  $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5( $str, $key = 'ThinkUCenter' ) {
    return '' === $str ? '' : md5( sha1( $str ) . $key );
}

function get_username( $uid = 0 ) {
    static $list;
    if ( !( $uid && is_numeric( $uid ) ) ) { //获取当前登录用户名
        return session( 'user_auth.username' );
    }

    /* 获取缓存数据 */
    if ( empty( $list ) ) {
        $list = S( 'sys_active_user_list' );
    }
    /* 查找用户信息 */
    $key = "u{$uid}";
    if ( isset( $list[$key] ) ) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = $this->where( ['id' => $uid ] )->field( 'id,username,email,mobile,status' )->find();
        if ( $info && isset( $info[1] ) ) {
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count( $list );
            $max = C( 'USER_MAX_CACHE' );
            while ( $count-- > $max ) {
                array_shift( $list );
            }
            S( 'sys_active_user_list', $list );
        } else {
            $name = '';
        }
    }
    return $name;
}
