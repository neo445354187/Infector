<?php if (!defined('THINK_PATH')) exit();?>// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | <?php echo ($TIME); ?> 
// |
// +----------------------------------------------------------------------

namespace <?php echo ($MOUDEL); ?>\Model;

use Think\Model;

/**
 * <?php echo ($TABLENAME); ?>Model
 */
class <?php echo ($TABLENAME); ?>Model extends Model{
	<?php if(($AUTOENABLE) == "1"): ?>/**
	 * 自动启用
	 */
	protected $_auto = [
	[ 'status', 1, self::MODEL_INSERT ]
	];<?php endif; ?>


	<?php if(!empty($NOTNULL)): ?>/**
	 * 自动验证规则
	 */
	 protected $_validate = [
	 <?php if(is_array($NOTNULL)): $i = 0; $__LIST__ = $NOTNULL;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>['<?php echo ($v["field"]); ?>', 'require', '<?php echo ($v["field"]); ?>不能为空' ],<?php endforeach; endif; else: echo "" ;endif; ?>
	 ];<?php endif; ?>


	/**
	 * 获取所有数据
	 */
	public function pull( $field = false ) {
		if ( $field ) {
			return $this->where( ['status' => 1] )->field( $field )->select();
		}else {
			return $this->where( ['status' => 1] )->select();
		}
	}

	/**
	 * 获取单个数据
	 */
	public function browse( $id, $field = false ) {
		if ( $field ) {
			return $this->where( ['<?php echo ($PK); ?>' => $id] )->field( $field )->find();
		}else {
			return $this->where( ['<?php echo ($PK); ?>' => $id] )->find();
		}
	}

	/**
	 * 更新单个数据
	 */
	public function update( $data = null ) {
		$this->create();

		if ( empty( $data['<?php echo ($PK); ?>'] ) ) {
			return $this->add( $data );
		}else {
			return $this->where( ['<?php echo ($PK); ?>' => $data['<?php echo ($PK); ?>']] )->save( $data );
		}
	}
}