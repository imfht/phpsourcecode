<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use think\Model;

/**
 * 配置模型
 * @package app\admin\model
 */
class Options extends Model
{
    /*
     * 获取系统基本设置
     */
    public function getOption($name = '', $lang = 'zh-cn')
    {
        $option = cache($name . '_' . $lang);
        if (empty($option)) {
            self::where([['name', '=', $name], ['lang', '=', $lang]])->column('value', 'name');
        }
        return $option;
    }

    /**
     * 获取系统基本设置(组)
     * @param string $group
     * @param string $lang
     * @return array
     * @throws \think\Exception
     */
    public function getOptions($group = '', $lang = '')
    {
        $options = cache($group . '_' . $lang);
        if (empty($options)) {
            $where = [];
            if ($group) {
                $where[] = ['group', '=', $group];
            }
            if ($lang) {
                $where[] = ['lang', '=', $lang];
            }
            $options = self::where($where)->column('value', 'name');
            if (!$options) {
                $options = $this->getOptions($group, 'zh-cn');
                $options_all = self::where([['group', '=', $group], ['lang', '=', 'zh-cn']])->select();
                $this->addOptions($options_all, $lang);
            }
            cache($group . '_' . $lang, $options);
        }
        return $options;
    }

    /*
     * 设置系统基本设置
     */
    public function setOption($name = '', $value = '', $lang = 'zh-cn')
    {
        $where[] = ['name', '=', $name];
        if ($lang) {
            $where[] = ['lang', '=', $lang];
        }
        self::where($where)->update(['value' => $value]);
        cache($name . '_' . $lang, null);
    }

    /*
     * 设置系统基本设置(组)
     */
    public function setOptions($options = [], $lang = 'zh-cn')
    {
        if (is_array($options) && $options) {
            foreach ($options as $name => $option) {
                self::setOption($name, $option, $lang);
            }
        }
    }
    /**
     * 设置系统基本设置(组)
     * @param array $options
     * @param string $lang
     * @return bool
     * @throws \think\Exception
     */
    public function addOptions($options = [], $lang = 'zh-cn')
    {
        $list = [];
        if ($options) {
            foreach ($options as $name => $option) {
                $list[] = [
                    'name'=>$option['name'],
                    'value'=>$option['value'],
                    'group'=>$option['group'],
                    'lang'=>$lang
                ];
            }
            if ($this->saveAll($list)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    /*
     * 清设置缓存
     */
    public static function delCache($name = '', $group = '', $lang = 'zh-cn')
    {
        if ($name && $lang) {
            cache($name . '_' . $lang, null);
        }
        if ($group && $lang) {
            cache($group . '_' . $lang, null);
        }
    }
}
