<?php
// +----------------------------------------------------------------------
// | Infector
// +----------------------------------------------------------------------
// | 2016-09-19 11:26:35
// |
// +----------------------------------------------------------------------

namespace Overlord\Model;


/**
 * NewsTypeModel
 */
class NewsTypeModel extends OverlordModel{
    /**
     * 自动启用
     */
    protected $_auto = [
    [ 'status', 1, self::MODEL_INSERT ]
    ];

    public function getIdxList() {
        return $this->where( ['status' => 1] )->order( 'sort DESC' )->getField( 'id,title', true );
    }

    /**
     * 获取所有数据
     */
    public function pull( $field = false ) {
        if ( $field ) {
            return $this->where( ['status' => 1] )->field( $field )->select();
        }
        return $this->where( ['status' => 1] )->select();
    }

    /**
     * 获取单个数据
     */
    public function browse( $id, $field = false ) {
        if ( $field ) {
            return $this->where( ['id' => $id] )->field( $field )->find();
        }
        return $this->where( ['id' => $id] )->find();
    }

    /**
     * 更新单个数据
     */
    public function update( $data = null ) {
        if ( !$data ) {
            $data = $this->create();
        }

        if ( empty( $data['id'] ) ) {
            return $this->add( $data );
        }
        return $this->where( ['id' => $data['id']] )->save( $data );
    }

    public function tree( $tick = false, $range = false ) {
        $map = ['status' => 1];
        if ( $range ) {
            $map = $range;
        }
        $tree = D( 'Common/Tree' )->toFormatTree( $this->where( $map )->order( 'sort DESC' )->select() );
        if ( !$tick ) {
            $tree = array_merge( [0=>['id'=>0, 'title_show'=>'顶级菜单']], $tree );
        }
        return $tree;
    }
}
