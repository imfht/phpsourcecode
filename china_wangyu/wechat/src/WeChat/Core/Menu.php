<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;


/**
 * Class Menu  微信菜单类
 * @package WeChat\Core
 */
class Menu extends Base
{
    // 获取菜单
    private static $getMenuUrl = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=ACCESS_TOKEN';

    // 设置菜单
    private static $setMenuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';

    /**
     * 获取菜单
     * @inheritdoc 详细文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141014
     * @param string $accessToken
     * @return array|bool
     */
    public static function gain(string $accessToken)
    {
        // 拼装获取菜单链接
        $getMenuUrl = str_replace('ACCESS_TOKEN', $accessToken, static::$getMenuUrl);

        // 发送获取菜单，获取结果
        return self::get($getMenuUrl);
    }

    /**
     * 删除菜单
     * @inheritdoc 详细文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141015
     * @param string $accessToken
     * @return array|bool
     */
    public static function delete(string $accessToken)
    {
        // 拼装获取菜单链接
        $getMenuUrl = str_replace('ACCESS_TOKEN', $accessToken, static::$getMenuUrl);

        // 发送获取菜单，获取结果
        return self::get($getMenuUrl);
    }


    /**
     * 设置菜单
     * @inheritdoc 详细文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
     * @param string $accessToken
     * @param array $menu
        例如：$menu =[
                       [
                            'type'=> 'click', //
                            'name'=> '这是第一级button',
                            'list' => [
                               [
                                    'type'=> 'view',
                                    'name'=> '百度',
                                    'url' => 'http://www.baidu.com',
                                ]
                            ],
                       ],
                        [
                            'type'=> 'miniprogram',
                            'name'=> 'xx小程序',
                            'url' => 'http://www.baidu.com',
                            'appid' => 'asdasdas', 小程序APPID
                            'pagepath' => '/page/index/index', // 小程序页面链接
                        ]
                    ];

     * @return array
     */
    public static function set(string $accessToken, array $menu)
    {
        (!is_array($menu) or count($menu) < 1) && self::error('请设置正确的参数 $menu ~ !');

        // 组装参数
        $format_param['button'] = self::format($menu);

        // 替换token
        $setMenuUrl = str_replace('ACCESS_TOKEN', $accessToken, static::$setMenuUrl);

        // 生成菜单
        return self::post($setMenuUrl, json_encode($format_param, JSON_UNESCAPED_UNICODE));
    }


    /**
     * 格式化菜单数组
     * @param array $menu 菜单数组
     * @return array
     */
    public static function format(array $menu)
    {
        $button =[];
        foreach ($menu as $key => $val) {

            if (!isset($val['list'])) {
                $button[$key] = static::getTypeParam($val['type'],$val);
            } else {
                $button[$key]['name'] = $val['name'];
                $button[$key]['sub_button'] = static::format($val['list']);
            }
        }
        return $button;
    }


    /**
     * 获取自定义菜单参数
     * @param string $type  类型
     * @param array $item   数组
     * @return array
     */
    private static function getTypeParam(string $type,array $item)
    {
        switch (strtolower($type))
        {
            case 'click':
                return array(
                    'type' => 'click',
                    'name' => $item['name'],
                    'key' => $item['name'], // 关键词
                );
                break;
            case 'view':
                return array(
                    'type' => 'view',
                    'name' => $item['name'],
                    'url' => $item['url'],  // 原文链接
                );
                break;
            case 'miniprogram': // 小程序
                return array(
                    'type' => 'miniprogram',
                    'name' => $item['name'],    // 菜单名称
                    'url' => $item['url'],      // 小程序链接
                    'appid' => $item['appid'],      // 小程序APPID
                    'pagepath' => $item['pagepath'],    // 小程序页面路径
                );
                break;
        }
    }
}