<?php if (!defined('THINK_PATH')) exit();?>// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | <?php echo ($TIME); ?> 
// |
// +----------------------------------------------------------------------

namespace Overlord\Controller;

<?php if(($export) == "1"): ?>use \Common\Util\Excel;<?php endif; ?>

/**
 * <?php echo ($TABLENAME); ?>控制器
 */
class <?php echo ($TABLENAME); ?>Controller extends OverlordController {

    /**
     * 列表
     */
    public function index() {
        $this->list = $this->lists( '<?php echo ($TABLENAME); ?>', ['status' => ['gt', -1]],'sort DESC', null,  null );

        $this->display();
    }

    /**
     * 添加数据
     */
    public function add() {
        $model = D( '<?php echo ($TABLENAME); ?>' );

        if ( IS_POST ) {
            $state = $model->update();
            if ( false === $state ) {
                $this->error( '新增数据失败，'.$model->getError() );
            }
            $this->success( '新增数据成功', U( 'index' ) );
        }

        $this->meta_title = '添加';
        <?php if(($ISORT) == "1"): ?>$this->data = [
        'sort' => $model->where( ['status' => 1] )->max( 'sort' ) + 1
        ];<?php endif; ?>

        $this->display( 'edit' );
    }

    /**
     * 编辑数据
     */
    public function edit() {
        $id = I( '<?php echo ($PK); ?>' );
        if ( empty( $id ) ) {
            $this->error( '请选择要编辑的数据' );
        }

        $model = D( '<?php echo ($TABLENAME); ?>' );

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
            $this->forbid( '<?php echo ($TABLENAME); ?>', $map );
            break;
        case 'resume':
            $this->resume( '<?php echo ($TABLENAME); ?>', $map );
            break;
        case 'remove':
            $this->delete( '<?php echo ($TABLENAME); ?>', $map );
            break;
        default:
            $this->error( '参数丢失' );
        }
    }

    <?php if(($ISORT) == "1"): ?>/**
     * 排序
     *
     */
    public function sort() {
        if ( IS_GET ) {
            $list = M( '<?php echo ($TABLENAME); ?>' )->where( ['status' => ['gt', -1]] )->field( 'id,title' )->order( 'sort DESC,id ASC' )->select();

            $this->assign( 'list', $list );
            $this->display();
        }else if ( IS_POST ) {
            $ids = explode( ',', I( 'post.ids' ) );
            $len = count( $ids );
            if ( 1 > $len ) {
                $this->error( '没有可以排序的数据' );
            }
            foreach ( $ids as $key=>$value ) {
                $res = M( '<?php echo ($TABLENAME); ?>' )->where( array( 'id'=>$value ) )->setField( 'sort', $len );
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
    }<?php endif; ?>


    <?php if(($export) == "1"): ?>/**
     * 导出数据
     *
     * @return [type] [description]
     */
    public function export() {
        $data[] = ['<?php echo ($comments_str); ?>'];
        $list = M( '<?php echo ($TABLENAME); ?>' )->field( '<?php echo ($columns_str); ?>' )->order( '<?php echo ($PK); ?> DESC' )->select();
        if ( !$list ) {
            $this->error( '没有可以导出的数据' );
        }

        $data = array_merge( $data, $list );
        return ( new Excel() )->write( $data, '<?php echo ($TABLENAME); ?>' );
    }<?php endif; ?>

}