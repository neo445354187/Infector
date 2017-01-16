<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Addons\EditorForAdmin\Controller;

use Overlord\Controller\AddonsController;
use Think\Upload;

class UploadController extends AddonsController{

	public $uploader = null;

	public function ke_upimg() {
		/* 返回标准数据 */
		$return  = array( 'error' => 0, 'info' => '上传成功', 'data' => '' );
		$img = $this->upload();
		/* 记录附件信息 */
		if ( $img ) {
			$return['url'] = $img['fullpath'];
			unset( $return['info'], $return['data'] );
		} else {
			$return['error'] = 1;
			$return['message'] = session( 'upload_error' );
		}

		/* 返回JSON数据 */
		exit( json_encode( $return ) );
	}

	/* 上传图片 */
	private function upload() {
		session( 'upload_error', null );

		/* 调用文件上传组件上传文件 */
		$pic_driver = C( 'PICTURE_UPLOAD_DRIVER' );
		$this->uploader = new Upload( C( 'EDITOR_UPLOAD' ), C( 'PICTURE_UPLOAD_DRIVER' ), C( "UPLOAD_{$pic_driver}_CONFIG" ) );
		$info = $this->uploader->upload( $_FILES );
		if ( $info ) {
			$url = C( 'EDITOR_UPLOAD.rootPath' ).$info['imgFile']['savepath'].$info['imgFile']['savename'];
			$info['fullpath'] = $url;
		}else {
			session( 'upload_error', $this->uploader->getError() );
		}

		return $info;
	}
}
