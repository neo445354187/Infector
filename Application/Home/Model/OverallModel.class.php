<?php
// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | 2016-05-12 10:50:05// |
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;

/**
 * OverallModel
 */
class OverallModel extends Model{

	/**
	 * find all records
	 */
	public function pull() {
		$data = S( 'Overall' );
		if ( !$data ) {
			$row = $this->where( ['status' => 1] )->order( 'sort DESC' )->field( 'mark,content' )->select();
			if ( $row ) {
				$data = [];
				foreach ( $row as $k => $v ) {
					$data[$v['mark']] = $v['content'];
				}
				S( 'Overall', $data );
			}
		}
		return $data;
	}

	/**
	 * get one singel key
	 */
	public function fetch( $field ) {
		$data = $this->pull();
		if ( !isset( $data[$field] ) ) {
			return false;
		}
		return $data[$field];
	}
}
