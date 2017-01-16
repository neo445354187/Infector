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
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
class FileController extends OverlordController {

    /**
     * 上传图片
     *
     * @author huajie <banhuajie@163.com>
     */
    public function uploadPicture() {

        /* 返回标准数据 */
        $return = array( 'status' => 1, 'info' => '上传成功', 'data' => '' );

        /* 调用文件上传组件上传文件 */
        $Picture = D( 'Picture' );
        $pic_driver = C( 'PICTURE_UPLOAD_DRIVER' );
        $info = $Picture->upload(
            $_FILES,
            C( 'PICTURE_UPLOAD' ),
            C( 'PICTURE_UPLOAD_DRIVER' ),
            C( "UPLOAD_{$pic_driver}_CONFIG" )
        );

        /* 记录图片信息 */
        if ( $info ) {
            $return['status'] = 1;
            $return = array_merge( array_pop( $info ), $return );
        } else {
            $return['status'] = 0;
            $return['info']   = $Picture->getError();
        }

        /* 返回JSON数据 */
        $this->ajaxReturn( $return );
    }
}
