<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty() {
		$this->redirect( 'Index/index' );
	}

	protected function _initialize() {
		/* 读取站点配置 */
		$config = S( 'DB_CONFIG_DATA' );
		if ( !$config ) {
			$config = api( 'Config/lists' );
			S( 'DB_CONFIG_DATA', $config );
		}
		C( $config );
	}

	protected function lists( $model, $where=array(), $order='', $base = array( 'status'=>array( 'egt', 0 ) ), $field=true, $size=10 ) {
		$options = array();
		$REQUEST = (array)I( 'request.' );
		if ( is_string( $model ) ) {
			$model = M( $model );
		}

		$OPT = new \ReflectionProperty( $model, 'options' );
		$OPT->setAccessible( true );

		$pk  = $model->getPk();
		if ( $order===null ) {
			//order置空
		}else if ( isset( $REQUEST['_order'] ) && isset( $REQUEST['_field'] ) && in_array( strtolower( $REQUEST['_order'] ), array( 'desc', 'asc' ) ) ) {
				$options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
			}else if ( $order==='' && empty( $options['order'] ) && !empty( $pk ) ) {
				$options['order'] = $pk.' desc';
			}else if ( $order ) {
				$options['order'] = $order;
			}
		unset( $REQUEST['_order'], $REQUEST['_field'] );

		$options['where'] = array_filter( array_merge( (array)$base, /*$REQUEST,*/ (array)$where ), function( $val ) {
				if ( $val===''||$val===null ) {
					return false;
				}else {
					return true;
				}
			} );
		if ( empty( $options['where'] ) ) {
			unset( $options['where'] );
		}
		$options = array_merge( (array)$OPT->getValue( $model ), $options );
		$total   = $model->where( $options['where'] )->count();

		if ( isset( $REQUEST['r'] ) ) {
			$listRows = (int)$REQUEST['r'];
		}else {
			$listRows = $size;
		}
		$page = new \Think\Page( $total, $listRows, $REQUEST );
		if ( $total>$listRows ) {
			$page->setConfig( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
		}
		$p = $page->show();
		$this->assign( '_page', $p? $p: '' );
		$this->assign( '_total', $total );
		$options['limit'] = $page->firstRow.','.$page->listRows;

		$model->setProperty( 'options', $options );
		return $model->field( $field )->select();
	}
}
