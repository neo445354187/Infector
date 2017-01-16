<?php
// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | 2017-01-16 20:09:05 
// |
// +----------------------------------------------------------------------

namespace Overlord\Controller;


/**
 * User控制器
 */
class UserController extends OverlordController {

    /**
     * 列表
     */
    public function index() {
        $this->list = $this->lists( 'User', ['status' => ['gt', -1]],'sort DESC', null,  null );

        $this->display();
    }

    /**
     * 添加数据
     */
    public function add() {
        $model = D( 'User' );

        if ( IS_POST ) {
            $state = $model->update();
            if ( false === $state ) {
                $this->error( '新增数据失败，'.$model->getError() );
            }
            $this->success( '新增数据成功', U( 'index' ) );
        }

        $this->meta_title = '添加';
        
        $this->display( 'edit' );
    }

    /**
     * 编辑数据
     */
    public function edit() {
        $id = I( 'id' );
        if ( empty( $id ) ) {
            $this->error( '请选择要编辑的数据' );
        }

        $model = D( 'User' );

        if ( IS_POST ) {
            $state = $model->update();
            if ( false === $state ) {
                $this->error( '编辑数据失败，'.$model->getError() );
            }
            $this->success( '编辑数据成功', U( 'index' ) );
        }

        $this->meta_title = '编辑';
        $this->data = $model->browse( $id );

        $this->display();
    }

    /**
     * 更新数据状态
     */
    public function state( $method ) {
        $id = I( 'id' );
        $id = is_array( $id ) ? implode( ',', $id ) : $id;
        if ( !$id ) {
            $this->error( '请选择要编辑的数据' );
        }

        $map['id'] = ['IN', $id];
        switch ( strtolower( $method ) ) {
        case 'forbid':
            $this->forbid( 'User', $map );
            break;
        case 'resume':
            $this->resume( 'User', $map );
            break;
        case 'remove':
            $this->delete( 'User', $map );
            break;
        default:
            $this->error( '参数丢失' );
        }
    }

    

    
}