<?php
/**
 * 前后台公用模型
 *
 * @author liuzuochao <you@mail.com>
 *
 */
namespace Common\Model;

class Model extends \Think\Model
{
    /**
     * [_condition 返回条件，为什么不写属性呢，因为属性数组里，没法放闭包]
     * @return [type]  [
     *       'number' => function($num){ return "";},
     *       'email' => 'strval|md5',
     *       'phone' => [__CLASS__, 'test']
     *   ]
     */
    protected function _condition()
    {
        return [];
    }

    /**
     * [createCondition 过滤搜索条件]
     * @author liuzuochao
     * @param  string $map [description]
     * @return [type]        [description]
     */
    public function createCondition($map = [])
    {
        $map === [] && $map = I('post.');
        $condition          = $this->_condition();
        foreach ($condition as $key => $cond) {
            //判断键中是否有连接查询
            $map_key = explode('.', $key)[1] ?: $key;
            
            if (!isset($map[$map_key]) || empty($map[$map_key])) {
                unset($condition[$key]);
                continue;
            }

            if ($cond instanceof \Closure) {
                $condition[$key] = $cond($map[$map_key]);
            } elseif (is_array($cond)) {
                $condition[$key] = call_user_func([$cond[0], $cond[1]], $map[$map_key]);
            } else {
                $funcs = explode('|', $cond);
                $temp  = $map[$map_key];
                foreach ($funcs as $func) {
                    $temp = call_user_func($func, $temp);
                }
                $condition[$key] = $temp;
            }
        }
        return $condition;
    }

}
