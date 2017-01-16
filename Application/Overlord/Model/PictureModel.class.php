<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Overlord\Model;

use Think\Upload;

/**
 * 图片模型
 * 负责图片的上传
 */

class PictureModel extends OverlordModel{
    /**
     * 自动完成
     *
     * @var array
     */
    protected $_auto = array(
        array( 'status', 1, self::MODEL_INSERT ),
        array( 'create_time', NOW_TIME, self::MODEL_INSERT ),
    );

    /**
     * 文件上传
     *
     * @param array   $files   要上传的文件列表（通常是$_FILES数组）
     * @param array   $setting 文件上传配置
     * @param string  $driver  上传驱动名称
     * @param array   $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function upload( $files, $setting, $driver = 'Local', $config = null ) {
        /* 上传文件 */
        $setting['callback'] = array( $this, 'isFile' );
        $setting['removeTrash'] = array( $this, 'removeTrash' );
        $Upload = new Upload( $setting, $driver, $config );
        $info   = $Upload->upload( $files );

        if ( $info ) { //文件上传成功，记录文件信息
            foreach ( $info as $key => &$value ) {
                /* 已经存在文件记录 */
                if ( isset( $value['id'] ) && is_numeric( $value['id'] ) ) {
                    continue;
                }

                /* 记录文件信息 */
                $value['path'] = $setting['rootPath'].$value['savepath'].$value['savename']; //在模板里的url路径
                if ( $this->create( $value ) && ( $id = $this->add() ) ) {
                    $value['id'] = $id;
                } else {
                    //TODO: 文件上传成功，但是记录文件信息失败，需记录日志
                    unset( $info[$key] );
                }
            }
            return $info; //文件上传成功
        } else {
            $this->error = $Upload->getError();
            return false;
        }
    }

    /**
     * 检测当前上传的文件是否已经存在
     *
     * @param array   $file 文件上传数组
     * @return boolean       文件信息， false - 不存在该文件
     */
    public function isFile( $file ) {
        if ( empty( $file['md5'] ) ) {
            throw new \Exception( '缺少参数:md5' );
        }
        /* 查找文件 */
        $map = array( 'md5' => $file['md5'], 'sha1'=>$file['sha1'], );
        return $this->field( true )->where( $map )->find();
    }

    public function removeTrash( $data ) {
        $this->where( array( 'id'=>$data['id'], ) )->delete();
    }
}
