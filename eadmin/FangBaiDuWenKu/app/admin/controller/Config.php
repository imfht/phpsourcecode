<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Config as LogicConfig;

/**
 * 配置控制器
 */
class Config extends AdminBase
{
    
    // 配置逻辑
    private static $configLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        
        self::$configLogic = get_sington_object('configLogic', LogicConfig::class);
    }
    
    /**
     * 系统设置
     */
    public function setting($config = [])
    {
      
        IS_POST && $this->jump(self::$configLogic->settingSave($config));
        
        $where = empty($this->param['group']) ? ['group' => 1] : ['group' => $this->param['group']];
        
        $this->getConfigCommonData();
        
        $this->assign('list', self::$configLogic->getConfigList($where, true, 'sort', false));
        
        $this->assign('group', $where['group']);
        
        return  $this->fetch('config_setting');
    }

    /**
     * 配置列表
     */
    public function configList()
    {
        
        $where = empty($this->param['group']) ? [] : ['group' => $this->param['group']];
        
        $this->getConfigCommonData();
        
        $this->assign('list', self::$configLogic->getConfigList($where));
        
        $this->assign('group', $where ? $this->param['group'] : 0);
        
        return $this->fetch('config_list');
    }
    
    /**
     * 获取通用数据
     */
    public function getConfigCommonData()
    {
        
        $config_group_list = parse_config_array('config_group_list');
        
        $config_type_list  = parse_config_array('config_type_list');
        
        $this->assign('config_group_list', $config_group_list);
        
        $this->assign('config_type_list' , $config_type_list);
    }
    
    /**
     * 配置添加
     */
    public function configAdd()
    {
        
        IS_POST && $this->jump(self::$configLogic->configAdd($this->param));
        
        $this->getConfigCommonData();
        
        !empty($this->param['group']) && $this->assign('info', ['group'=> $this->param['group']]);
        
        return  $this->fetch('config_add');
    }
    
    /**
     * 配置编辑
     */
    public function configEdit()
    {
        
        IS_POST && $this->jump(self::$configLogic->configEdit($this->param));
        
        $info = self::$configLogic->getConfigInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        $this->getConfigCommonData();
        
        return $this->fetch('config_edit');
    }
    
    /**
     * 配置删除
     */
    public function configDel($id = 0)
    {
        
        $this->jump(self::$configLogic->configDel(['id' => $id]));
    }
    
    /**
     * 配置批量删除
     */
    public function configAlldel($ids = 0)
    {
    
    	$this->jump(self::$configLogic->configAlldel($ids));
    }
    
}
