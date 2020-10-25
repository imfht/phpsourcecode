<?php

/**
 * 模块model文件.
 *
 * @author banyanCheung <banyan@ibos.com.cn>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2008-2013 IBOS Inc
 */
/**
 * 模块表的模型处理类
 * @package application.core.model
 * @version $Id: Module.php 2877 2014-03-24 06:10:38Z zhangrong $
 */

namespace application\core\model;

use application\core\utils\ArrayUtil;
use application\core\utils\Cache;
use application\core\utils\Ibos;
use application\modules\dashboard\model\Menu;

class Module extends Model
{
    /**
     * @param string $className
     * @return Module
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{module}}';
    }

    /**
     * 获得模块名称
     * @staticvar null $modules 所有模块缓存
     * @param string $moduleName 模块标识
     * @return string
     */
    public function fetchNameByModule($moduleName)
    {
        static $modules = null;
        if (!$modules) {
            $modules = $this->fetchAllEnabledModule();
        }
        $module = isset($modules[$moduleName]) ? $modules[$moduleName] : $this->fetchByAttributes(array('module' => $moduleName));
        return (is_array($module) && isset($module['name'])) ? $module['name'] : '';
    }

    /**
     * 查找所有非系统模块
     * @return type
     */
    public function fetchAllNotCoreModule()
    {
        $modules = $this->fetchAllSortByPk('module', array(
                'condition' => "`iscore` = 0 AND `disabled` = 0",
                'order' => '`sort` ASC',
            )
        );
        return $modules;
    }

    /**
     * 查找所有非系统与非辅助模块
     * @return array
     */
    public function fetchAllClientModule()
    {
        $modules = $this->fetchAllSortByPk('module', array(
                'condition' => "`iscore` = 0 AND `disabled` = 0 AND `category` != ''",
                'order' => '`sort` ASC',
            )
        );
        return $modules;
    }

    /**
     * 获取所有可用的模块
     * @return array
     */
    public function fetchAllEnabledModule()
    {
        $module = Cache::get('module');
        if ($module == false) {
            $criteria = array(
                'condition' => '`disabled` = 0',
                'order' => '`sort` ASC'
            );
            $module = $this->fetchAllSortByPk('module', $criteria);
            Cache::set('module', $module);
        }
        return $module;
    }

    public function findAllEnabledModuleArray()
    {
        return Ibos::app()->db->createCommand()
            ->select()
            ->from($this->tableName())
            ->where(" `disabled` = '0' ")
            ->order(" sort ASC ")
            ->queryAll();
    }

    /**
     * 检查模块是否启用
     *
     * @param string $moduleName
     * @return bool
     */
    public function isModuleEnable($moduleName)
    {
        $enableModules = $this->findAllEnabledModuleArray();
        $enableModulesNames = ArrayUtil::getColumn($enableModules, 'module');
        return in_array($moduleName, $enableModulesNames);
    }

    /**
     * 查找第一个安装的后台模块
     * @return array
     */
    public function getFirstInstallClientModule()
    {
        return Ibos::app()->db->createCommand()
            ->select('*')
            ->from(Module::model()->tableName(). ' module')
            ->Join(Menu::model()->tableName(). ' menu', 'module.module = menu.m')
            ->where('module.`iscore` = :iscore AND module.disabled != :isdisabled', array(':iscore' => 0, ':isdisabled' => 1))
            ->order('module.installdate ASC')
            ->queryRow();
    }
}
