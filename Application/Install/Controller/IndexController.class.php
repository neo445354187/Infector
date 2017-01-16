<?php
namespace Install\Controller;

use Think\Controller;

class IndexController extends Controller{

	private $out = ['Addons', 'Config', 'Hooks', 'Member', 'Menu', 'Staff', 'Picture', 'News', 'NewsType','Overall'];

	public function index() {
		$model = M();
		$data = [];
		$tables = $model->query( 'SHOW TABLES' );
		if ( $tables ) {
			foreach ( $tables as $k => $v ) {
				$current = array_pop( $v );
				$table = str4Model( $current, C( 'DB_PREFIX' ) );
				if ( !in_array( $table, $this->out ) ) {
					$columns = $model->query( "SHOW FULL COLUMNS FROM {$current}" );
					array_push( $data, ['tn' => $table, 'tc' => $columns ] );
				}
			}
		}
		$this->tables = $data;

		return $this->display();
	}

	public function infect() {
		$data = I( 'data', NULL, 'html_entity_decode' );
		if ( empty( $data ) ) {
			$this->error( '没有可用的数据' );
		}
		$data = json_decode( $data, true );
		if ( empty( $data ) ) {
			$this->error( '没有可用的数据' );
		}

		$pk = '';
		$iSort = 0;
		$needUplod = '';
		$notNull = [];
		$needTPCss = '';
		$needTPScript = '';
		$needTPInit = '';
		//获取表字段和表字段备注的数组
		$columns = array();
		$comments = array();

		//准备变量
		foreach ( $data['field'] as $k => $v ) {
			if ( 1 == $v['key'] ) {
				$pk = $v['field'];
			}
			if ( 'sort' == $v['field'] ) {
				$iSort = 1;
			}

			$data['field'][$k]['output'] = '{$v.'.$v['field'].'}';
			$extra = explode( '|', $v['extra'] );
			if ( $extra[0] && 'UPLOADPIC' == $extra[0] ) {
				$needUplod = '<script type="text/javascript" src="__STATIC__/jquery.upload.js"></script>';
			}
			if ( $extra[0] && 'TIMEPICKER' == $extra[0] ) {
				$needTPCss = '<link rel="stylesheet" type="text/css" href="__STATIC__/datetimepicker/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="__STATIC__/datetimepicker/css/datetimepicker_blue.css">
<link rel="stylesheet" type="text/css" href="__STATIC__/datetimepicker/css/dropdown.css">';

				$needTPScript = '<script type="text/javascript" src="__STATIC__/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="__STATIC__/datetimepicker/js/bootstrap-datetimepicker.zh-CN.js"></script>';

				$needTPInit = '$(".timepicker").datetimepicker({
	format:"yyyy-mm-dd",
	minView:"2",
	autoclose:true,
	todayBtn:true
});';
			}
			$data['field'][$k]['comment'] = $extra;

			if ( 1 == $v['null'] ) {
				array_push( $notNull, ['field' => $v['field'], 'name' => ( isset( $extra[1] ) ? $extra[1] : $v['field'] )] );
			}
			$columns[] = "'".$v['field']."'";
			$comments[] = "'".( $extra[1] ? $extra[1] : $v['field'] )."'";
		}

		//时间
		$this->TIME = date( 'Y-m-d H:i:s', NOW_TIME );
		//模型名
		$this->TABLENAME = $data['table'];
		//主键
		$this->PK = $pk;
		//需要排序
		$this->ISORT = $iSort;
		//字段数据
		$this->FIELDS = $data['field'];
		//生成前端控制器
		$template = $this->fetch( 'Template:FrontController' );
		file_put_contents( APP_PATH.'Home/Controller/'.$this->TABLENAME.'Controller.class.php', "<?php\n".$template );

		//生成前端模型
		if ( $data['FM'] ) {
			$this->MOUDEL = 'Home';
			$this->AUTOENABLE = 0;
			$this->NOTNULL = $notNull;
			$template = $this->fetch( 'Template:FrontModel' );
			file_put_contents( APP_PATH.'Home/Model/'.$this->TABLENAME.'Model.class.php', "<?php\n".$template );
		}

		//生成前端模板文件文件夹
		__mkdir( APP_PATH.'Home/View/default/'.$this->TABLENAME );

		//生成后端控制器
		$this->export = intval( $data['export'] );
		$this->columns_str = implode( $columns, ', ' );
		$this->comments_str = implode( $comments, ', ' );
		$template = $this->fetch( 'Template:BackgroundController' );
		file_put_contents( APP_PATH.'Overlord/Controller/'.$this->TABLENAME.'Controller.class.php', "<?php\n".$template );

		//生成后端模型
		$this->MOUDEL = 'Overlord';
		$this->AUTOENABLE = intval( $data['autoEnable'] );
		$template = $this->fetch( 'Template:BackModel' );
		file_put_contents( APP_PATH.'Overlord/Model/'.$this->TABLENAME.'Model.class.php', "<?php\n".$template );
		
		//生成后端列表页面
		$export_btn = $this->export ? '<div class="fr"><a class="btn" href="{:U(\''.$this->TABLENAME.'/export\')}">导出EXCEL</a></div>' : '';
		$sort_btn = $iSort && $data['sort'] ? '<a class="btn" href="{:U(\''.$this->TABLENAME.'/sort\')}">排序</a>' : '';
		$template = file_get_contents( APP_PATH.'Install/View/Template/BackgroundIndexPage.html' );
		$template = str_replace( ['{$TABLE}', '{$TABLEHEADER}', '{$TABLEBODY}', '{$EXPORT}', '{$SORT}'],
			[$this->TABLENAME, create_table_header( $this->PK, $this->FIELDS ), create_table_body( $this->PK, $this->FIELDS ), $export_btn, $sort_btn], $template );
		__mkdir( APP_PATH.'Overlord/View/'.$this->TABLENAME );
		file_put_contents( APP_PATH.'Overlord/View/'.$this->TABLENAME.'/index.html', $template );
		if ( $iSort && $data['sort'] ) {
			file_put_contents( APP_PATH.'Overlord/View/'.$this->TABLENAME.'/sort.html', file_get_contents( APP_PATH.'Install/View/Template/SortPage.html' ) );
		}

		//生成后端编辑页面
		$template = file_get_contents( APP_PATH.'Install/View/Template/BackgroundEditPage.html' );
		$template = str_replace( ['{$TABLE}', '{$TPCSS}', '{$TPSCRIPT}', '{$TPINIT}', '{$UPLOAD}', '{$PK}', '{$BODY}'],
			[$this->TABLENAME, $needTPCss, $needTPScript, $needTPInit, $needUplod, $this->PK, create_field( $this->PK, $this->FIELDS ) ], $template );
		file_put_contents( APP_PATH.'Overlord/View/'.$this->TABLENAME.'/edit.html', $template );

		//插入菜单
		$model = M( 'Menu' );
		$count = $model->where( ['title' => $this->TABLENAME] )->count();
		if ( 1 > $count ) {
			$model->add( ['title' => $this->TABLENAME, 'url' => $this->TABLENAME.'/index'] );
		}

		$this->success();
	}
}
