<?php

namespace Addons\QuickUpld;

use Common\Controller\Addon;

/**
 * QuickUpld插件
 *
 * @author NULL
 */

class QuickUpldAddon extends Addon{

    public $info = array(
        'name' => 'QuickUpld',
        'title' => 'QuickUpld',
        'description' => '后台快速上传图片',
        'status' => 1,
        'author' => 'NULL',
        'version' => '0.1'
    );

    public function install() {
        return true;
    }

    public function uninstall() {
        return true;
    }

    //实现的QuickUpld钩子方法
    public function QuickUpld( $param ) {
        $param['type'] = $param['type'] ? $param['type'] : 'id';
        $this->assign( 'addons_data', $param );
        $this->assign( 'addons_config', $this->getConfig() );
        $this->display( 'template' );
    }
}
