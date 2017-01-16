<?php
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

namespace Overlord\Controller;

use Common\Controller\Controller;

/**
 * 后台首页控制器
 */
class OverlordController extends Controller {

    /**
     * 后台控制器初始化
     */
    protected function _initialize() {
        // 获取当前用户ID
        define( 'UID', is_login() );
        if ( !UID ) {
            $this->redirect( 'Public/login' );
        }
        /* 读取数据库中的配置 */
        $config = S( 'DB_CONFIG_DATA' );
        if ( !$config ) {
            $config = api( 'Config/lists' );
            S( 'DB_CONFIG_DATA', $config );
        }
        C( $config );

        // 是否是超级管理员
        define( 'IS_ROOT', is_administrator() );
        if ( !IS_ROOT && C( 'ADMIN_ALLOW_IP' ) ) {
            if ( !in_array( get_client_ip(), explode( ',', C( 'ADMIN_ALLOW_IP' ) ) ) ) {
                $this->error( '403:禁止访问' );
            }
        }

        // 检测访问权限
        $access = $this->accessControl();
        if ( $access === false ) {
            $this->error( '403:禁止访问' );
        }elseif ( $access === null ) {
            $dynamic = $this->checkDynamic();//检测分类栏目有关的各项动态权限
            if ( $dynamic === null ) {
                //检测非动态权限
                $rule  = strtolower( MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME );
                if ( !$this->checkRule( $rule, array( 'in', '1,2' ) ) ) {
                    $this->error( '未授权访问!' );
                }
            }elseif ( $dynamic === false ) {
                $this->error( '未授权访问!' );
            }
        }
        $this->assign( '__MENU__', $this->getMenus() );
    }

    /**
     * 权限检测
     *
     * @param string  $rule 检测的规则
     * @param string  $mode check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule( $rule, $type=AuthRuleModel::RULE_URL, $mode='url' ) {
        if ( IS_ROOT ) {
            return true;
        }
        static $Auth    =   null;
        if ( !$Auth ) {
            $Auth       =   new \Think\Auth();
        }
        if ( !$Auth->check( $rule, UID, $type, $mode ) ) {
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     *
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic() {
        if ( IS_ROOT ) {
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }

    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl() {
        if ( IS_ROOT ) {
            return true;//管理员允许访问任何页面
        }
        $allow = C( 'ALLOW_VISIT' );
        $deny  = C( 'DENY_VISIT' );
        $check = strtolower( CONTROLLER_NAME.'/'.ACTION_NAME );
        if ( !empty( $deny )  && in_array_case( $check, $deny ) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty( $allow ) && in_array_case( $check, $allow ) ) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string  $model 模型名称,供M函数使用的参数
     * @param array   $data  修改的数据
     * @param array   $where 查询时的where()方法的参数
     * @param array   $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    final protected function editRow( $model , $data, $where , $msg ) {
        $id    = array_unique( (array)I( 'id', 0 ) );
        $id    = is_array( $id ) ? implode( ',', $id ) : $id;
        $where = array_merge( array( 'id' => array( 'in', $id ) ) , (array)$where );
        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' , 'ajax'=>IS_AJAX ) , (array)$msg );
        if ( M( $model )->where( $where )->save( $data )!==false ) {
            $this->success( $msg['success'], $msg['url'], $msg['ajax'] );
        }else {
            $this->error( $msg['error'], $msg['url'], $msg['ajax'] );
        }
    }

    /**
     * 禁用条目
     *
     * @param string  $model 模型名称,供D函数使用的参数
     * @param array   $where 查询时的 where()方法的参数
     * @param array   $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function forbid( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！' ) ) {
        $data    =  array( 'status' => 0 );
        $this->editRow( $model , $data, $where, $msg );
    }

    /**
     * 恢复条目
     *
     * @param string  $model 模型名称,供D函数使用的参数
     * @param array   $where 查询时的where()方法的参数
     * @param array   $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function resume(  $model , $where = array() , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！' ) ) {
        $data    =  array( 'status' => 1 );
        $this->editRow(   $model , $data, $where, $msg );
    }

    /**
     * 条目假删除
     *
     * @param string  $model 模型名称,供D函数使用的参数
     * @param array   $where 查询时的where()方法的参数
     * @param array   $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function delete( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！' ) ) {
        $data['status']         =   -1;
        $data['update_time']    =   NOW_TIME;
        $this->editRow(   $model , $data, $where, $msg );
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus( $Model=CONTROLLER_NAME ) {
        $ids    = I( 'request.ids' );
        $status = I( 'request.status' );
        if ( empty( $ids ) ) {
            $this->error( '请选择要操作的数据' );
        }

        $map['id'] = array( 'in', $ids );
        switch ( $status ) {
        case -1 :
            $this->delete( $Model, $map, array( 'success'=>'删除成功', 'error'=>'删除失败' ) );
            break;
        case 0  :
            $this->forbid( $Model, $map, array( 'success'=>'禁用成功', 'error'=>'禁用失败' ) );
            break;
        case 1  :
            $this->resume( $Model, $map, array( 'success'=>'启用成功', 'error'=>'启用失败' ) );
            break;
        default :
            $this->error( '参数错误' );
            break;
        }
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus() {
        $store = session( 'ADMIN_MENU_LIST' );
        if ( !isset( $store[CONTROLLER_NAME] ) ) {
            $menus = [];
            // 获取主菜单
            $menus['main']  = M( 'Menu' )->where( ['pid' => 0, 'hide' => 0] )->order( 'sort asc' )->select();
            $menus['child'] = array(); //设置子节点

            //高亮主菜单
            $current = M( 'Menu' )->where( ['url'=>['LIKE', CONTROLLER_NAME.'/'.ACTION_NAME.'%']] )->field( 'id' )->find();
            if ( $current ) {
                $nav = D( 'Menu' )->getPath( $current['id'] );
                $nav_first_title = $nav[0]['pid'] ? $nav[0]['pid'] : $nav[0]['id'];

                foreach ( $menus['main'] as $key => $item ) {

                    // 获取当前主菜单的子菜单项
                    if ( $item['id'] == $nav_first_title ) {

                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = M( 'Menu' )->where( ['pid' => $item['id']] )->distinct( true )->field( '`group`' )->select();
                        if ( $groups ) {
                            $groups = array_column( $groups, 'group' );
                        }else {
                            $groups =   array();
                        }

                        //获取二级分类的合法url
                        $second_urls = M( 'Menu' )->where( ['pid' => $item['id'], 'hide' => 0] )->getField( 'id,url' );

                        // 按照分组生成子菜单树
                        foreach ( $groups as $g ) {
                            $map = array( 'group'=>$g );
                            $map['pid'] = $item['id'];
                            $map['hide'] = 0;
                            $menuList = M( 'Menu' )->where( $map )->field( 'id,pid,title,url,tip' )->order( 'sort asc' )->select();
                            $menus['child'][$g] = list_to_tree( $menuList, 'id', 'pid', 'operater', $item['id'] );
                        }
                    }
                }
            }
            $store[CONTROLLER_NAME] = $menus;
            session( 'ADMIN_MENU_LIST', $store );
        }
        return $store[CONTROLLER_NAME];
    }

    /**
     * 返回后台节点数据
     *
     * @param boolean $tree 是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes( $tree = true ) {
        static $tree_nodes = array();
        if ( $tree && !empty( $tree_nodes[(int)$tree] ) ) {
            return $tree_nodes[$tree];
        }
        if ( (int)$tree ) {
            $list = M( 'Menu' )->field( 'id,pid,title,url,tip,hide' )->order( 'sort asc' )->select();
            foreach ( $list as $key => $value ) {
                if ( stripos( $value['url'], MODULE_NAME )!==0 ) {
                    $list[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
            $nodes = list_to_tree( $list, $pk='id', $pid='pid', $child='operator', $root=0 );
            foreach ( $nodes as $key => $value ) {
                if ( !empty( $value['operator'] ) ) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset( $nodes[$key]['operator'] );
                }
            }
        }else {
            $nodes = M( 'Menu' )->field( 'title,url,tip,pid' )->order( 'sort asc' )->select();
            foreach ( $nodes as $key => $value ) {
                if ( stripos( $value['url'], MODULE_NAME )!==0 ) {
                    $nodes[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }

    protected function lists( $model, $where = array(), $order = '', $field = true, $size = 10 ) {
        if ( is_string( $model ) ) {
            $model = M( $model );
        }
        if ( !empty( $where ) ) {
            $total = $model->where( $where )->count();
        }else {
            $total = $model->count();
        }
        if ( 'Overlord' == MODULE_NAME ) {
            $listRows = 0 < C( 'LIST_ROWS' ) ? C( 'LIST_ROWS' ) : 10;
        }else {
            $listRows = $size;
        }
        $page = new \Think\Page( $total, $listRows, (array)I( 'request.' ) );
        if ( $total > $listRows ) {
            $page->setConfig( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
        }
        $p = $page->show();
        $this->assign( '_page', $p ? $p : '' );
        $this->assign( '_total', $total );
        return $model->where( $where )->order( $order )->field( $field )->page( I( 'get.p', 0 ).','.$listRows )->select();
    }
}