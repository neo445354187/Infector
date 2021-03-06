<?php
// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | 2017-01-16 20:09:05 
// |
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * UserController
 */
class UserController extends HomeController {

    /**
     * 构造函数
     */
    protected function _initialize() {
        parent::_initialize();
    }

    /**
     * 列表
     */
    public function index() {
        $this->data = $this->lists( 'User', ['status' => 1], 'sort DESC', null, null );

        $this->display();
    }

    /**
     * 单个记录
     */
    public function show() {
        $id = I( 'id' );
        if ( empty( $id )  ) {
            $this->error( '请选择要浏览的记录' );
        }

        $model = D( 'User' );
        $data = $model->browse( $id );
        if ( !$data ) {
            $this->error( '没有找到相关记录' );
        }
        $this->data = $data;

        $this->display();
    }
}