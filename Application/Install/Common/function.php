<?php

function str4Model( $str, $prefix ) {
	$str = str_replace( $prefix, '', $str );
	$arr = explode( '_', $str );
	$new_str = '';
	foreach ( $arr as $v ) {
		$new_str .= ucfirst( $v );
	}
	return $new_str;
}

function __mkdir( $dir ) {
	if ( is_dir( $dir ) ) {
		return true;
	}
	if ( mkdir( $dir ) ) {
		return true;
	}
	return false;
}

function create_table_header( $pk, $field ) {
	$body = '';
	foreach ( $field as $k => $v ) {
		if ( 'status' != $v['field'] && $pk != $v['field'] ) {
			$body.='<th>'.( isset( $v['comment'][1] ) ? $v['comment'][1] : $v['field'] )."</th>\n\t\t\t";
		}
	}
	$body.='<th class="text-center">操作</th>';
	return $body;
}

function create_table_body( $pk, $field ) {
	$i = 0;
	$body = "<notempty name=\"list\">\n\t\t\t<volist name=\"list\" id=\"v\">\n\t\t\t\t<tr>\n\t\t\t\t\t";
	$body.= '<td><input class="ids" type="checkbox" name="id[]" value="{$v.'.$pk.'}"></td>';
	$body.= "\n\t\t\t\t\t";
	foreach ( $field as $k => $v ) {
		if ( 'status' != $v['field'] && $pk != $v['field'] ) {
			$i++;
			$body.='<td>{$v.'.$v['field']."}</td>\n\t\t\t\t\t";
		}
	}
	$body.="<td class=\"text-center\">\n\t\t\t\t\t<eq name=\"v.status\" value=\"1\">\n\t\t\t\t\t";
	$body.='<a href="{:U(\'state\', [\'id\'=>$v[\''.$pk.'\'],\'method\'=>\'forbid\'])}" class="ajax-get">禁用</a>';
	$body.="\n\t\t\t\t\t<else/>\n\t\t\t\t\t";
	$body.='<a href="{:U(\'state\', [\'id\'=>$v[\''.$pk.'\'],\'method\'=>\'resume\'])}" class="ajax-get">启用</a>';
	$body.="\n\t\t\t\t\t</eq> | ";
	$body.='<a href="{:U(\'edit\', [\'id\'=>$v[\''.$pk.'\']])}">编辑</a> | <a href="{:U(\'state\', [\'id\'=>$v[\''.$pk.'\'],\'method\'=>\'remove\'])}" class="confirm ajax-get">删除</a>';
	$body.="\n\t\t\t\t\t</td>\n\t\t\t\t</tr>\n\t\t\t</volist>\n\t\t<else/>\n\t\t\t";
	$body.= "<td colspan=\"".( $i + 2 )."\" class=\"text-center\">aOh!暂时还没有内容!</td>\n\t\t</notempty>";
	return $body;
}

function create_field( $pk, $field ) {
	$body = '';
	foreach ( $field as $k => $v ) {
		if ( 'status' != $v['field'] && $pk != $v['field'] ) {
			$require = '';
			if ( $v['null'] ) {
				$require = ' required';
			}
			switch ( $v['comment'][0] ) {
			case 'UPLOADPIC':
				$body.= "\n\t\t<div class=\"form-item\">
		      <label class=\"item-label\">图标 <span class=\"check-tips\">（没有可不传，推荐尺寸 24px x 27px）</span><a href=\"https://tinypng.com/\" target=\"_blank\">图片太大请压缩</a></label>
			  <div class=\"controls\">
		        <input type=\"file\" id=\"".$v['field']."\" class=\"picUpload\" accept=\"image/png,image/gif,image/jpeg\">\n\t\t\t\t";
				$body.= '<input type="hidden" name="'.$v['field'].'" id="'.$v['field'].'_val" value="{$data.'.$v['field'].'}">';
				$body.= '<label for="'.$v['field'].'" class="picUploadBox">上传文件</label>';
				$body.= "\n\t\t\t\t<div class=\"upload-img-box\">\n\t\t\t\t";
				$body.= "\n\t\t\t\t<div class=\"upload-pre-item\">";
				$body.= '<img src="<notempty name="data.'.$v['field'].'">{$data.'.$v['field'].'}</notempty>" alt="preview" id="'.$v['field'].'_val_preview">';
				$body.= "</div>\n\t\t\t\t
		        </div>
			   </div>
		      </div>
			  \n\t\t{:hook('QuickUpld', ['id' => '".$v['field']."', 'return' => '".$v['field']."_val', 'type' => 'path'])}";
				break;

			case 'RICHTEXT':
				$body.= "\n\t\t<div class=\"form-item\">
		      <label class=\"item-label\">".( isset( $v['comment'][1] ) ? $v['comment'][1] : $v['field'] )."</label>
		      <div class=\"controls\">
		        <label class=\"textarea\">\n\t\t\t\t";
				$body.= '<textarea name="'.$v['field'].'"'.$require.'>{$data.'.$v['field'].'}</textarea>';
				$body.= "\n\t\t\t\t";
				$body.= '{:hook(\'adminArticleEdit\', [\'name\'=>\''.$v['field'].'\',\'value\'=>$data[\''.$v['field'].'\']])} </label>';
				$body.= "\n\t\t\t</div>\n\t\t</div>";
				break;

			case 'TEXT':
				$body.= "\n\t\t<div class=\"form-item\">
		      <label class=\"item-label\">".( isset( $v['comment'][1] ) ? $v['comment'][1] : $v['field'] )."</label>
		      <div class=\"controls\">
		        <label class=\"textarea\">\n\t\t\t\t";
				$body.= '<textarea name="'.$v['field'].'"'.$require.'>{$data.'.$v['field'].'}</textarea>';
				$body.= "\n\t\t\t\t</label>\n\t\t\t</div>\n\t\t</div>";
				break;

			case 'TIMEPICKER':
				$body.= "\n\t\t<div class=\"form-item\">
		      <label class=\"item-label\">".( isset( $v['comment'][1] ) ? $v['comment'][1] : $v['field'] )."</label>
		      <div class=\"controls\">\n\t\t\t\t";
				$body.= '<input type="text" class="text input-large timepicker" name="'.$v['field'].'" value="{$data.'.$v['field'].'}" '.$require.'>';
				$body.= "\n\t\t\t</div>\n\t\t</div>";
				break;

			default:
				$body.= "\n\t\t<div class=\"form-item\">
		      <label class=\"item-label\">".( isset( $v['comment'][1] ) ? $v['comment'][1] : $v['field'] )."</label>
		      <div class=\"controls\">\n\t\t\t\t";
				$body.= '<input type="text" class="text input-large" name="'.$v['field'].'" value="{$data.'.$v['field'].'}" '.$require.'>';
				$body.= "\n\t\t\t</div>\n\t\t</div>";
				break;
			}
		}
	}
	return $body;
}
