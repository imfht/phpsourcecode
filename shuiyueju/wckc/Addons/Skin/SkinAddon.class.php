<?php

namespace Addons\Skin;

use Common\Controller\Addon;

require_once(ONETHINK_ADDON_PATH . 'Skin/Common/function.php');
/**空间换肤插件
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-2-28
 * Time: 上午10:54
 * @author 郑钟良<zzl@ourstu.com>
 */
class SkinAddon extends Addon
{
    public $info = array(
        'name' => 'Skin',
        'title' => '空间换肤',
        'description' => '用户自定义风格',
        'status' => 1,
        'author' => '想天科技-zzl(郑钟良)',
        'version' => '1.0'
    );
    public $custom_config = 'config.html';

    public function install()
    {
        $prefix = C("DB_PREFIX");
        $model = D();
        $sql = "REPLACE INTO `{$prefix}hooks` (`name`, `description`, `type`, `update_time`, `addons`) VALUES('setSkin', '设置个人皮肤', 2, 1425265259, 'Skin');";
        $model->execute($sql);
        $sql1="CREATE TABLE IF NOT EXISTS `{$prefix}user_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `model` varchar(30) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户配置信息表' AUTO_INCREMENT=1 ;";
        $model->execute($sql1);

        return true;
    }

    public function uninstall()
    {
        $prefix = C("DB_PREFIX");
        $sql = "DELETE FROM `{$prefix}hooks` WHERE `name`='setSkin';";
        D('')->execute($sql);
        $sql1="DELETE FROM `{$prefix}hooks` WHERE `name`='{$prefix}user_config';";
        D('')->execute($sql1);
        return true;
    }

    /**
     * 设置skin钩子代码
     * @param $param 相关参数
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setSkin($param)
    {
        $config=getAddonConfig();
        $this->assign('Skin_CanSet',$config['canSet']);
        $this->display(T('Addons://Skin@Skin/skin'));
    }

    /**
     * 站点头部钩子，加载换肤插件所需样式
     * @param array $param 相关参数
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function pageHeader($param)
    {
        $SkinsUrl = getRootUrl() . "Addons/Skin/Skins/";
        $config = getAddonConfig();
        if ($config['canSet'] == 0||$config['mandatory'] == 1) { //强制执行管理员设置的默认皮肤
            // 载入换肤插件默认样式
            echo '<link href="' . $SkinsUrl . $config['defaultSkin'] . '/style.css" data-role="skin_link" rel="stylesheet" type="text/css"/>';
        } else { //执行用户设置样式
            // 载入换肤插件用户样式
            $userSkin = getUserConfig();
            echo '<link href="' . $SkinsUrl . $userSkin['skin'] . '/style.css" data-role="skin_link" rel="stylesheet" type="text/css"/>';
        }
    }
}