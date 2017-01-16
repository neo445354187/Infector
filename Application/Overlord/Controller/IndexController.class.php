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
 *
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends OverlordController {

    /**
     * 后台首页
     *
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index() {
        if ( UID ) {
            $this->meta_title = '首页';
            $this->display();
        } else {
            $this->redirect( 'Public/login' );
        }
    }
}
