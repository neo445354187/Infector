<?php
namespace Common\Util;

/**
 * [龙渊SDK-core  需要 PHP_VERSION > 5.4]
 *
 * @author Legend. <xcx_legender@qq.com>
 * @version 1.0
 */

class LYSDK {

	// const CLIENT_ID = '22c51853df2cd8fc'; // appid    请修改为应用appid
	// const CLIENT_SECRET = 'ee48bab45c77a5902a7f24ca9e784acc'; // appsecret 请修改为应用secret

	const RSA_PUBLIC_KEY = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDFK2EP+e1cdAwhdCHfsjlRi3jg
2CYZxBgccZw0B2Bq/alkPsJZC259G20A4bkX33V19zBe9xKruo13tDi309Z8dNKs
fSjjcu1mp1BGHnct9GY+kqjaaVhe7OS04J5wjJEgywsy9+Von8XvynTLawSHghMS
g9pUoQPxdOFd6zhp9QIDAQAB
-----END PUBLIC KEY-----'; // pub_key

	const URI_DEVELOP = 'http://api.sandbox.test.ilongyuan.cn'; // 开发环境
	const URI_PRODUCT = 'https://account.ilongyuan.cn'; // 正式环境

	protected $client_id;
	protected $client_secret;

	protected $user; // ['uid', 'username']
	protected $uri;
	protected $error;
	protected $debug;

	public function __construct( $client_id, $client_secret, $environment = 'dev', $debug = false ) {
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->uri = $environment == 'dev' ? self::URI_DEVELOP : self::URI_PRODUCT;
		$this->debug = $debug;
	}

	// 获取登陆授权码
	public function getAuthCode( $username, $password ) {
		$param = [
		'client_id' => $this->client_id,
		'username' => $username,
		'password' => $password,
		];
		$sign = $this->rsaSign( $param );
		$res = $this->doPost( $this->uri . '/Api/Oauth/quickLogin', ['sign' => $sign] );
		if ( $res['errno'] == 200 ) {
			return $res['data']['code'];
		} else {
			return null;
		}
	}

	// 根据授权码获取用户信息  包括token
	public function getUser( $code ) {
		$param = [
		'client_id' => $this->client_id,
		'client_secret' => $this->client_secret,
		'code' => $code,
		];
		$res = $this->doPost( $this->uri . '/Oauth/Game/access_token', $param );
		if ( $res['errno'] != 200 ) {
			return null;
		}
		$access_token = $res['data']['access_token'];
		$param = [
		'access_token' => $access_token,
		];
		$res = $this->doGet( $this->uri . '/Oauth/User/me', $param );
		if ( $res['errno'] != 200 ) {
			return null;
		}
		$data = $res['data'];
		$data['access_token'] = $access_token;
		return $data;
	}

	// 提供给服务器直接登录的接口
	public function login( $username, $password ) {
		$code = $this->getAuthCode( $username, $password );
		if ( $code ) {
			$user = $this->getUser( $code );
			if ( $user ) {
				return $user;
			}
		}
		return null;
	}

	public function getUserByUsername( $username ) {
		$param = ['username' => $username, 'client_id' => $this->client_id];
		$this->createSign( $param );
		$res = $this->doPost( $this->uri . '/App/ServerTool/userInfo', $param );
		return $res;
	}

	protected function createSign( &$param ) {
		ksort( $param );
		reset( $param );
		$param['sign'] = md5( http_build_query( $param ) . $this->client_secret );
	}

	// 支付回调验证
	public function orderCheckSign( $request = '' ) {
		if ( !$request ) {
			$request = $_REQUEST;
		}
		$sign = $request['sign'];
		unset( $request['sign'] );
		ksort( $request );
		reset( $request );
		return $sign === md5( http_build_query( $request ) . $this->client_id . $this->client_secret );
	}

	// function
	public function getError() {
		return $this->error;
	}

	public function doPost( $uri, $param ) {
		return $this->doHttpRequest( $uri, $param, 'POST' );
	}

	public function doGet( $uri, $param ) {
		return $this->doHttpRequest( $uri, $param, 'GET' );
	}

	public function rsaSign( $param ) {
		$str = json_encode( $param );
		openssl_public_encrypt( $str, $encrypted, self::RSA_PUBLIC_KEY );
		return base64_encode( $encrypted );
	}

	protected function doHttpRequest( $uri, $params, $method = 'GET', $header = '' ) {
		if ( is_array( $params ) ) {
			$params = http_build_query( $params );
		}
		$ch = curl_init();
		if ( 'GET' == strtoupper( $method ) ) {
			if ( !empty( $params ) ) {
				curl_setopt( $ch, CURLOPT_URL, $uri . "?" . $params );
			} else {
				curl_setopt( $ch, CURLOPT_URL, $uri );
			}
		} else {
			curl_setopt( $ch, CURLOPT_URL, $uri );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
		}

		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );

		if ( is_array( $header ) ) {
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		} else {
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Expect:' ) );
		}
		if ( stripos( $uri, 'https://' ) === 0 ) {
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		}
		$result = curl_exec( $ch );
		curl_close( $ch );
		if ( $this->debug ) {
			echo 'URI:' . $uri . '<br/>';
			echo 'PARAMS: ' . $params . '<br/>';
			echo 'RETURN: ' . $result . '<br/>';

		}
		$result = json_decode( $result, true );
		$this->error = [$result['errno'], $result['errinfo']];
		return $result;
	}
}
