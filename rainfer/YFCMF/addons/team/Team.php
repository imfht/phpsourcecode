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
namespace addons\team;

use app\common\controller\Addons;

/**
 * 显示团队
 */
class Team extends Addons
{
    public $info = [
        'name'        => 'Team',
        'title'       => '团队&贡献者',
        'intro'       => '后台首页团队&贡献者显示',
        'description' => '后台首页团队&贡献者显示',
        'status'      => 1,
        'author'      => 'rainfer',
        'version'     => '0.1',
        'admin'       => '0'
    ];

    /**
     * @var array 插件钩子
     */
    public $hooks = [
        // 钩子名称 => 钩子说明
        'team' => '团队钩子'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 实现的team钩子方法
     * @return mixed
     * @throws
     */
    public function team()
    {
        $config = $this->getConfigValue();
        if (isset($config['display']) && $config['display']) {
            echo $this->fetch('team');
        }
    }
}
