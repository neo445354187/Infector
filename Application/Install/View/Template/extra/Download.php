<?php

public function download() {
	switch ( I( 'E' ) ) {
	case 'I':
	case 'A':
		$model = M( 'Overall' );
		$url = $model->where( ['mark' => ( 'I' == I( 'E' ) ? '' : '' ) ] )->getField( 'content' );
		if ( '' == $url ) {
			$this->error( '敬请期待', U( 'Index/index' ) );
		}
		//下载数+1
		$model->where( ['mark' => ''] )->setInc( 'content' );
		exit( '<meta http-equiv="refresh" content="0;url='.$url.'">' );

	default:
		if ( !IS_MOBILE ) {
			$this->error( '请使用移动设备', U( 'Index/index' ) );
		}

		$agent = $_SERVER['HTTP_USER_AGENT'];
		if ( empty( $agent ) || strpos( $agent, 'MicroMessenger' ) !== false ) {
			return $this->display();
		}

		$model = M( 'Overall' );
		if ( strpos( $agent, 'iPhone' ) !== false ) {
			$url = $model->where( ['mark' => '' ] )->getField( 'content' );
		}
		if ( strpos( $agent, 'Android' ) !== false ) {
			$url = $model->where( ['mark' => '' ] )->getField( 'content' );
		}
		if ( '' == $url ) {
			$this->error( '敬请期待', U( 'Index/index' ) );
		}
		$model->where( ['mark' => ''] )->setInc( 'content' );
		exit( '<meta http-equiv="refresh" content="0;url='.$url.'">' );
	}
}
