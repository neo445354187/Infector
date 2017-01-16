<?php
// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | 2016-05-12 10:50:05// |
// +----------------------------------------------------------------------

namespace Overlord\Model;


/**
 * OverallModel
 */
class OverallModel extends OverlordModel{

	protected $_auto = [
	['status', 1, self::MODEL_INSERT]
	];

	/**
	 * find all records
	 */
	public function pull( $field = false ) {
		if ( $field ) {
			return $this->where( ['status' => 1] )->field( $field )->select();
		}else {
			return $this->where( ['status' => 1] )->select();
		}
	}

	public function updata_content() {
		$data = I( 'post.' );
		if ( !$data ) {
			$this->error = '没有可更新的数据';
			return false;
		}
		foreach ( $data as $k => $v ) {
			$this->where( ['mark' => $k] )->save( ['content' => $v] );
		}
		return true;
	}

	/**
	 * find one singel record
	 */
	public function browse( $id, $field = false ) {
		if ( $field ) {
			return $this->where( ['id' => $id] )->field( $field )->find();
		}else {
			return $this->where( ['id' => $id] )->find();
		}
	}

	/**
	 * update one singel record
	 */
	public function update( $data = null ) {
		if ( !$data ) {
			$data = $this->create();
		}

		if ( '' == $data['mark'] ) {
			$this->error = 'mark不能为空';
			return false;
		}
		if ( '' == $data['title'] ) {
			$this->error = 'title不能为空';
			return false;
		}

		if ( empty( $data['id'] ) ) {
			if ( 0 < $this->where( ['mark' => $data['mark']] )->count() ) {
				$this->error = '标识符不能重复';
				return false;
			}
			return $this->add( $data );
		}else {
			return $this->where( ['id' => $data['id']] )->save( $data );
		}
	}
}
