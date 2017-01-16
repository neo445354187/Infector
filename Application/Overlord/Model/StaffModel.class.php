<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace Overlord\Model;

/**
 * 会员模型
 */
class StaffModel extends OverlordModel {

	/* 用户模型自动验证 */
	protected $_validate = array(
		/* 验证用户名 */
		array( 'username', '1,30', -1, self::EXISTS_VALIDATE, 'length' ), //用户名长度不合法
		array( 'username', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback' ), //用户名禁止注册
		array( 'username', '', -3, self::EXISTS_VALIDATE, 'unique' ), //用户名被占用

		/* 验证密码 */
		array( 'password', '6,30', -4, self::EXISTS_VALIDATE, 'length' ), //密码长度不合法

		/* 验证邮箱 */
		array( 'email', 'email', -5, self::EXISTS_VALIDATE ), //邮箱格式不正确
		array( 'email', '1,32', -6, self::EXISTS_VALIDATE, 'length' ), //邮箱长度不合法
		array( 'email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback' ), //邮箱禁止注册
		array( 'email', '', -8, self::EXISTS_VALIDATE, 'unique' ), //邮箱被占用

		/* 验证手机号码 */
		array( 'mobile', '//', -9, self::EXISTS_VALIDATE ), //手机格式不正确 TODO:
		array( 'mobile', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback' ), //手机禁止注册
		array( 'mobile', '', -11, self::EXISTS_VALIDATE, 'unique' ), //手机号被占用
	);

	/* 用户模型自动完成 */
	protected $_auto = array(
		array( 'password', 'think_ucenter_md5', self::MODEL_BOTH, 'function', DATA_AUTH_KEY ),
		array( 'reg_time', NOW_TIME, self::MODEL_INSERT ),
		array( 'reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1 ),
		array( 'update_time', NOW_TIME ),
		array( 'status', 'getStatus', self::MODEL_BOTH, 'callback' ),
	);

	/**
	 * 检测用户名是不是被禁止注册
	 *
	 * @param string  $username 用户名
	 * @return boolean          ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyMember( $username ) {
		return true;
	}

	/**
	 * 检测邮箱是不是被禁止注册
	 *
	 * @param string  $email 邮箱
	 * @return boolean       ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyEmail( $email ) {
		return true;
	}

	/**
	 * 检测手机是不是被禁止注册
	 *
	 * @param string  $mobile 手机
	 * @return boolean        ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyMobile( $mobile ) {
		return true;
	}

	/**
	 * 根据配置指定用户状态
	 *
	 * @return integer 用户状态
	 */
	protected function getStatus() {
		return true;
	}

	/**
	 * 用户登录认证
	 *
	 * @param string  $username 用户名
	 * @param string  $password 用户密码
	 * @param integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	public function login( $username, $password, $type = 1 ) {
		$map = array();
		switch ( $type ) {
		case 1:
			$map['username'] = $username;
			break;
		case 2:
			$map['email'] = $username;
			break;
		case 3:
			$map['mobile'] = $username;
			break;
		case 4:
			$map['id'] = $username;
			break;
		default:
			return 0; //参数错误
		}

		/* 获取用户数据 */
		$user = $this->where( $map )->find();
		if ( is_array( $user ) && $user['status'] ) {
			/* 验证用户密码 */
			if ( think_ucenter_md5( $password, DATA_AUTH_KEY ) === $user['password'] ) {
				$this->updateLogin( $user['id'] ); //更新用户登录信息
				$this->autoLogin( $user );
				return true;
			} else {
				$this->error = '密码错误';
				return false;
			}
		} else {
			$this->error = '用户不存在或被禁用！';
			return false;
		}
	}

	/**
	 * 获取用户信息
	 *
	 * @param string  $uid         用户ID或用户名
	 * @param boolean $is_username 是否使用用户名查询
	 * @return array                用户信息
	 */
	public function info( $uid, $is_username = false ) {
		$map = array();
		if ( $is_username ) { //通过用户名获取
			$map['username'] = $uid;
		} else {
			$map['id'] = $uid;
		}

		$user = $this->where( $map )->field( 'id,username,email,mobile,status' )->find();
		if ( is_array( $user ) && $user['status'] = 1 ) {
			return array( $user['id'], $user['username'], $user['email'], $user['mobile'] );
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * 更新用户登录信息
	 *
	 * @param integer $uid 用户ID
	 */
	protected function updateLogin( $uid ) {
		$data = array(
			'id'              => $uid,
			'last_login_time' => NOW_TIME,
			'last_login_ip'   => get_client_ip( 1 ),
		);
		$this->save( $data );
	}

	/**
	 * 更新用户信息
	 *
	 * @param int     $uid      用户id
	 * @param string  $password 密码，用来验证
	 * @param array   $data     修改的字段数组
	 * @return true 修改成功，false 修改失败
	 * @author huajie <banhuajie@163.com>
	 */
	public function updateUserFields( $uid, $password, $data ) {
		if ( empty( $uid ) || empty( $password ) || empty( $data ) ) {
			$this->error = '参数错误！';
			return false;
		}

		//更新前检查用户密码
		if ( !$this->verifyUser( $uid, $password ) ) {
			$this->error = '验证出错：密码不正确！';
			return false;
		}

		//更新用户信息
		$data['password'] = think_ucenter_md5( $data['password'], DATA_AUTH_KEY );
		if ( !$data ) {
			return false;
		}
		return $this->where( array( 'id'=>$uid ) )->save( $data );
	}

	/**
	 * 验证用户密码
	 *
	 * @param int     $uid         用户id
	 * @param string  $password_in 密码
	 * @return true 验证成功，false 验证失败
	 * @author huajie <banhuajie@163.com>
	 */
	protected function verifyUser( $uid, $password_in ) {
		$password = $this->getFieldById( $uid, 'password' );
		if ( think_ucenter_md5( $password_in, DATA_AUTH_KEY ) === $password ) {
			return true;
		}
		return false;
	}

	public function updateInfo( $uid, $password, $data ) {
		if ( $this->updateUserFields( $uid, $password, $data ) !== false ) {
			$return['status'] = true;
		}else {
			$return['status'] = false;
			$return['info'] = $this->getError();
		}
		return $return;
	}

	/**
	 * 注销当前用户
	 *
	 * @return void
	 */
	public function logout() {
		session( 'user_auth', null );
		session( 'user_auth_sign', null );
	}

	/**
	 * 自动登录用户
	 *
	 * @param integer $user 用户信息数组
	 */
	private function autoLogin( $user ) {
		/* 记录登录SESSION和COOKIES */
		$auth = array(
			'uid'             => $user['id'],
			'username'        => $user['username'],
			'last_login_time' => $user['last_login_time'],
		);

		session( 'user_auth', $auth );
		session( 'user_auth_sign', data_auth_sign( $auth ) );
	}
}
