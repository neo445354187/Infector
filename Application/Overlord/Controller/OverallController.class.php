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
 * OverallController
 */
class OverallController extends OverlordController {

    /**
     * all records list
     */
    public function index() {
        if ( IS_POST ) {
            $status = D( 'Overall' )->updata_content();
            if ( false === $status ) {
                $this->error( '更新失败' );
            }
            $this->success( '更新完成' );
        }

        $this->meta_title = '全局';
        $this->data = $this->lists( 'Overall', ['status' => 1], 'sort DESC' );
        $this->display();
    }

    public function create() {
        $row = $this->lists( 'Overall',['status' => 1], 'sort DESC',  'id, mark, type, title, sort' ,null);
        if ( $row ) {
            $row = int_to_string( $row, ['type'=>['0'=>'普通', '1'=>'图片', '2'=>'多行文本']] );
            $this->list = $row;
        }
        $this->display();
    }

    /**
     * add a new record
     */
    public function add() {
        $model = D( 'Overall' );
        if ( IS_POST ) {
            $state = $model->update();
            if ( false === $state ) {
                $this->error( '新增数据失败，'.$model->getError() );
            }
            S( 'Overall', null );
            $this->success( '新增数据成功', U( 'create' ) );
        }

        $this->meta_title = '添加';
        $this->data = [
        'sort' => $model->where( ['status' => 1] )->max( 'sort' ) + 1
        ];
        $this->display( 'edit' );
    }

    /**
     * edit one singel record
     */
    public function edit() {
        $id = I( 'id' );
        if ( empty( $id ) ) {
            $this->error( '请选择要编辑的数据' );
        }

        $model = D( 'Overall' );
        if ( IS_POST ) {
            $state = $model->update();
            if ( false === $state ) {
                $this->error( '编辑数据失败，'.$model->getError() );
            }
            S( 'Overall', null );
            $this->success( '编辑数据成功', U( 'create' ) );
        }

        $this->meta_title = '编辑';
        $this->data = $model->browse( $id );

        $this->display();
    }

    /**
     * delete, forbid or resume one singel record
     */
    public function state( $method ) {
        $id = I( 'id' );
        $id = is_array( $id ) ? implode( ',', $id ) : $id;
        if ( !$id ) {
            $this->error( '请选择要编辑的数据' );
        }

        $map['id'] = ['IN', $id];
        switch ( strtolower( $method ) ) {
        case 'remove':
            $this->delete( 'Overall', $map );
            break;

        default:
            $this->error( '参数丢失' );
        }
    }

    public function sort() {
        if ( IS_GET ) {
            $list = M( 'Overall' )->where( ['status' => ['gt', -1]] )->field( 'id,title' )->order( 'sort DESC,id ASC' )->select();

            $this->assign( 'list', $list );
            $this->display();
        }else if ( IS_POST ) {
                $ids = explode( ',', I( 'post.ids' ) );
                $len = count( $ids );
                if ( 1 > $len ) {
                    $this->error( '没有可以排序的数据' );
                }
                foreach ( $ids as $key=>$value ) {
                    $res = M( 'Overall' )->where( array( 'id'=>$value ) )->setField( 'sort', $len );
                    $len--;
                }
                if ( $res !== false ) {
                    $this->success( '排序成功！' );
                }else {
                    $this->eorror( '排序失败！' );
                }
            }else {
            $this->error( '非法请求！' );
        }
    }
}
