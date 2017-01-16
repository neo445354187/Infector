<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace Overlord\Controller;

/**
 * 后台首页控制器
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录
     *
     */
    public function login( $username = null, $password = null, $verify = null ) {
        if ( IS_POST ) {
            // if ( !check_verify( $verify ) ) {
            //     $this->error( '验证码输入错误！' );
            // }

            $model = D( 'Staff' );
            $res = $model->login( $username, $password );
            if ( $res ) {
                $this->success( '登录成功！', U( 'Index/index' ) );
            } else {
                $this->error( $model->getError() );
            }
        } else {
            if ( is_login() ) {
                $this->redirect( 'Index/index' );
            }else {
                $config = S( 'DB_CONFIG_DATA' );
                if ( !$config ) {
                    $config = D( 'Config' )->lists();
                    S( 'DB_CONFIG_DATA', $config );
                }
                C( $config );

                $this->display();
            }
        }
    }

    /* 退出登录 */
    public function logout() {
        if ( is_login() ) {
            D( 'Staff' )->logout();
            session( '[destroy]' );
            $this->success( '退出成功！', U( 'login' ) );
        } else {
            $this->redirect( 'login' );
        }
    }

    public function verify() {
        $verify = new \Think\Verify();
        $verify->entry( 1 );
    }
}
