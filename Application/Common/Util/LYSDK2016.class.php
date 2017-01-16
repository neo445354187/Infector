<?php
namespace Common\Util;

use Common\Util\LYSDK;

/**
 * [使用V2016新接口  需要 PHP_VERSION > 5.4]
 *
 * @author Legend. <xcx_legender@qq.com>
 * @version 1.2
 */

class LYSDK2016 extends LYSDK {

	// const CLIENT_ID = '22c51853df2cd8fc';

	// 弃用 请使用 LYSDK::getAuthCode
	// public function login($username, $password) {
	//  $param = [
	//   'username' => $username,
	//   'password' => $password,
	//   'client_id' => $this->client_id,
	//  ];
	//  $post = $this->rsaEncode($param);
	//  $res = $this->doPost($this->uri . '/V201601/Login/user', $post);
	//  // print_r($res);
	//  return $res;
	// }

	public function register( $username, $password ) {
		$param = [
		'username' => $username,
		'password' => $password,
		'client_id' => $this->client_id,
		];
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/Register/user', $post );
		return $res;
	}

	public function quickLogin( $pid, $username ) {
		$param = compact( 'pid', 'username' );
		$post['sign'] = $this->rsaEncode( $param );
		$post['client_id'] = $this->client_id;
		$res = $this->doPost( $this->uri . '/api/oauth/quickLogin', $post );
		return $res;
	}

	public function send_repass_code( $phone ) {
		$param = [
		'phone' => $phone,
		];
		$post['sign'] = $this->rsaEncode( $param );
		$post['version'] = '201602';
		$res = $this->doPost( $this->uri . '/api/user/send_repass_sms_code', $post );
		return $res;
	}

	public function repass( $phone, $verify_code, $password ) {
		$param = [
		'phone' => $phone,
		'verify_code' => $verify_code,
		'password' => $password,
		];
		$post['sign'] = $this->rsaEncode( $param );
		$post['version'] = '201602';
		$res = $this->doPost( $this->uri . '/api/user/repass', $post );
		return $res;
	}

	public function send_bindphone_code( $mobile, $demo = false ) {
		$is_demo = $demo ?  1 : 0;
		$param = compact( 'mobile', 'is_demo' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/sms/bind', $post );
		return $res;
	}

	public function bindmobile( $access_token, $mobile, $verify_code ) {
		$param = compact( 'access_token', 'mobile', 'verify_code' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/profile/bindPhone', $post );
		return $res;
	}

	public function send_unbindphone_code( $mobile, $demo = false ) {
		$is_demo = $demo ?  1 : 0;
		$param = compact( 'mobile', 'is_demo' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/sms/unbind', $post );
		return $res;
	}

	public function unbindPhone( $access_token, $verify_code ) {
		$param = compact( 'access_token', 'verify_code' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/profile/unbindPhone', $post );
		return $res;
	}

	public function setNickname( $access_token, $nickname ) {
		$param = compact( 'access_token', 'nickname' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/profile/setNickname', $post );
		return $res;
	}

	public function setUsername( $access_token, $username ) {
		$param = compact( 'access_token', 'username' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/profile/setUsername', $post );
		return $res;
	}

	public function setPassword( $access_token, $old_password, $password ) {
		$param = compact( 'access_token', 'old_password', 'password' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/profile/setPassword', $post );
		return $res;
	}

	public function setRecord( $mac, $datetime, $type, $username = '', $pid = '' ) {
		$param = compact( 'mac', 'datetime', 'type' , 'username', 'pid' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/record/set', $post );
		return $res;
	}

	public function getRecord( $mac ) {
		$param = compact( 'mac' );
		$post = $this->rsaEncode( $param );
		$res = $this->doPost( $this->uri . '/V201601/record/get', $post );
		return $res;
	}

	protected function rsaEncode( $param ) {
		$json = json_encode( $param );
		$pkey = openssl_pkey_get_public( self::RSA_PUBLIC_KEY );
		openssl_public_encrypt( $json, $encrypted, $pkey );
		return base64_encode( $encrypted );
	}
}
