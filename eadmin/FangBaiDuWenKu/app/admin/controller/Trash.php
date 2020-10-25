<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\logic\Trash as LogicTrash;

/**
 * 回收站控制器
 */
class Trash extends AdminBase
{
    
    // 回收站逻辑
    private static $trashLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        
        self::$trashLogic = get_sington_object('trashLogic', LogicTrash::class);
    }
    
    /**
     * 回收站列表
     */
    public function trashList()
    {
        
        $this->assign('list', self::$trashLogic->getTrashList());
        
        return $this->fetch('trash_list');
    }
    
    /**
     * 数据列表
     */
    public function trashDataList()
    {
        
        $data = self::$trashLogic->getTrashDataList($this->param['name']);
        
        $this->assign('model_name', $data['model_name']);
        $this->assign('list', $data['list']);
        $this->assign('dynamic_field', $data['dynamic_field']);
        
        return $this->fetch('trash_data_list');
    }
    
    /**
     * 数据删除
     */
    public function trashDataDel($model_name = '', $id = 0)
    {
        
        $this->jump(self::$trashLogic->trashDataDel($model_name, $id));
    }
    
    /**
     * 数据恢复
     */
    public function restoreData($model_name = '', $id = 0)
    {
        
        $this->jump(self::$trashLogic->restoreData($model_name, $id));
    }
}
