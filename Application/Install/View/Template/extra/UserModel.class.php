<?php
// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | {$TIME}
// |
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;
use Common\Util\LYSDK2016;

/**
 * 用户模型
 */
class UserModel extends Model {

    const CLIENT_ID  = '1c7667ade4ef8602';
    const CLIENT_SECRET = '455fcb6cdd965e76876a9e191ebaeb09';

    private static $SDK = null;

    protected $_auto = [
    //[ 'reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1 ],
    [ 'reg_time', NOW_TIME, self::MODEL_INSERT ],
    [ 'status', 1, self::MODEL_INSERT ]
    ];

    public function login() {

        $account = I( 'account', '', 'trim' );
        $pwd = I( 'pwd', '', 'trim' );

        if ( empty( $account ) || empty( $pwd ) ) {
            $this->error = '账号 和 密码 不能为空';
            return false;
        }

        //sdk登录
        $res = $this->SDK()->login( $account, $pwd );
        if ( !$res ) {
            $err = $this->SDK()->getError();
            switch ( $err[0] ) {
            case 100:
                $this->error = '账号 或 密码不能为空';
                break;
            case 301:
                $this->error = '账号 或 密码错误';
                break;
            default:
                $this->error = '登录失败，'.$err[1];
                break;
            }
            return false;
        }

        //本地登录
        $row = $this->where( ['lyid' => $res['id']] )->field( 'id, lyid, nickname, status' )->find();
        if ( !$row ) {
            $data = $this->create();

            $data['lyid'] = $res['id'];
            $key = $this->add( $data );
            if ( false === $key ) {
                return false;
            }
            $row = ['id' => $key, 'lyid' => $res['id'], 'nickname' => $res['name'], 'status' => 1];
        }

        if ( 0 == $row['status'] ) {
            $this->error = '用户被禁用';
            return false;
        }

        unset( $row['status'] );
        session( 'user_auth', $row );

        return true;
    }

    private function SDK() {
        if ( null == static::$SDK ) {
            static::$SDK = new LYSDK2016( self::CLIENT_ID, self::CLIENT_SECRET, 'pro', false );
        }
        return static::$SDK;
    }
}
