<?php if (!defined('THINK_PATH')) exit();?>// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | <?php echo ($TIME); ?> 
// |
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * <?php echo ($TABLENAME); ?>Controller
 */
class <?php echo ($TABLENAME); ?>Controller extends HomeController {

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
        $this->data = $this->lists( '<?php echo ($TABLENAME); ?>', ['status' => 1], 'sort DESC', null, null );

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

        $model = D( '<?php echo ($TABLENAME); ?>' );
        $data = $model->browse( $id );
        if ( !$data ) {
            $this->error( '没有找到相关记录' );
        }
        $this->data = $data;

        $this->display();
    }
}