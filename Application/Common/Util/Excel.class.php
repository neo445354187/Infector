<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: susy.liang
// +----------------------------------------------------------------------

namespace Common\Util;

/**
 * +------------------------------------------------------------------------------
 * 基于PHPExcel类库进行excel文件处理
 * +------------------------------------------------------------------------------
 *
 * @category   ORG
 * @package  ORG
 * +------------------------------------------------------------------------------
 */

class Excel {
	
	public function __construct() {
		require __DIR__.'/Excel/PHPExcel.php';
		require __DIR__.'/Excel/PHPExcel/IOFactory.php';
		require __DIR__.'/Excel/PHPExcel/Cell.php';
	}

	/**
	 * 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
	 * 以下基本都不要修改
	 */
	public function read( $filename, $encode='utf-8' ) {

		$objReader = \PHPExcel_IOFactory::createReader( 'Excel5' );//\Lib\ORG\RBAC

		$objReader->setReadDataOnly( true );

		$objPHPExcel = $objReader->load( $filename );

		$objWorksheet = $objPHPExcel->getActiveSheet();

		$highestRow = $objWorksheet->getHighestRow();

		$highestColumn = $objWorksheet->getHighestColumn();

		$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString( $highestColumn );

		$excelData = array();
		for ( $row = 1; $row <= $highestRow; $row++ ) {

			for ( $col = 0; $col < $highestColumnIndex; $col++ ) {

				$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow( $col, $row )->getValue();

			}
		}
		return $excelData;
	}

	public function write( $data, $file ) {
		$file = $file.'_'.date( 'YmdHi' ).'.xlsx';
		$handler = new \PHPExcel();

		$handler
		->getProperties()
		->setCreator( 'iLongyuan' )
		->setLastModifiedBy( 'iLongyuan' )
		->setTitle( 'iLongyuan' )
		->setDescription( 'iLongyuan' );
		$handler->setActiveSheetIndex( 0 );
		$objActSheet = $handler->getActiveSheet();
		$i = 1;
		foreach ( $data as $row ) {
			$j = ord( 'A' );
			foreach ( $row as $key => $value ) {
				$objActSheet->setCellValue( chr( $j ).$i, $value );
				$j ++;
			}
			$i ++;
		}

		header( 'Content-Type: application/vnd.ms-excel' );
		header( 'Content-Disposition: attachment;filename="'.$file.'"' );
		header( 'Cache-Control: max-age=0' );

		$objWriter = \PHPExcel_IOFactory::createWriter( $handler, 'Excel2007' );
		$objWriter->save( 'php://output' );
	}
}
