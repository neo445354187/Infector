<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

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

function is_mobile_ui() {
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if ( isset ( $_SERVER['HTTP_X_WAP_PROFILE'] ) ) {
		define( IS_MOBILE, true );
		return true;
	}

	//此条摘自TPM智能切换模板引擎，适合TPM开发
	if ( isset ( $_SERVER['HTTP_CLIENT'] ) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'] ) {
		define( IS_MOBILE, true );
		return true;
	}
	//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if ( isset ( $_SERVER['HTTP_VIA'] ) ) {
		if ( stristr( $_SERVER['HTTP_VIA'], 'wap' ) ) {
			define( IS_MOBILE, true );
			return true;
		}else {
			define( IS_MOBILE, false );
			return false;
		};
	}

	//判断手机发送的客户端标志,兼容性有待提高
	if ( isset ( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$clientkeywords = array(
			'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
		);
		//从HTTP_USER_AGENT中查找手机浏览器的关键字
		if ( preg_match( "/(" . implode( '|', $clientkeywords ) . ")/i", strtolower( $_SERVER['HTTP_USER_AGENT'] ) ) ) {
			define( IS_MOBILE, true );
			return true;
		}
	}
	//协议法，因为有可能不准确，放到最后判断
	if ( isset ( $_SERVER['HTTP_ACCEPT'] ) ) {
		// 如果只支持wml并且不支持html那一定是移动设备
		// 如果支持wml和html但是wml在html之前则是移动设备
		if ( ( strpos( $_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml' ) !== false ) && ( strpos( $_SERVER['HTTP_ACCEPT'], 'text/html' ) === false || ( strpos( $_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml' ) < strpos( $_SERVER['HTTP_ACCEPT'], 'text/html' ) ) ) ) {
			define( IS_MOBILE, true );
			return true;
		}
	}

	define( IS_MOBILE, false );
	return false;
}
