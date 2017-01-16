<?php
// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | 2017-01-16 20:09:05 
// |
// +----------------------------------------------------------------------

namespace Overlord\Model;

use Think\Model;

/**
 * UserModel
 */
class UserModel extends Model{
	

	

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
			return $this->where( ['id' => $id] )->field( $field )->find();
		}else {
			return $this->where( ['id' => $id] )->find();
		}
	}

	/**
	 * 更新单个数据
	 */
	public function update( $data = null ) {
		$this->create();

		if ( empty( $data['id'] ) ) {
			return $this->add( $data );
		}else {
			return $this->where( ['id' => $data['id']] )->save( $data );
		}
	}
}