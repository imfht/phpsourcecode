<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;

/**
 * 配置控制器
 */
class Config extends AdminBase
{

    // 配置逻辑
    private static $commonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class, 'config');


    }

    /**
     * 系统设置
     */
    public function setting($config = [])
    {

        IS_POST && $this->jump($this->settingSave($config));

        $where = empty($this->param['group']) ? ['group' => 1] : ['group' => $this->param['group']];

        $this->getConfigCommonData();

        $this->assign('list', self::$commonLogic->getDataList($where, true, 'sort', false));

        $this->assign('group', $where['group']);

        return $this->fetch('config_setting');
    }

    /**
     * 系统设置
     */
    public function settingSave($data = [])
    {

        foreach ($data as $name => $value) {

            $where = ['name' => $name];

            if ($name == 'WEB_SITE_FOOTER') {


                $value = htmlspecialchars_decode($value);


            }
            self::$commonLogic->setDataValue($where, 'value', $value);
        }
        array_map('xrmdir', glob(CACHE_PATH));

        array_map('xrmdir', glob(TEMP_PATH));

        array_map('xrmdir', glob(LOG_PATH));

        return [RESULT_SUCCESS, '设置保存成功', 'd', $data];
    }

    /**
     * 配置列表
     */
    public function configList()
    {

        $where = empty($this->param['group']) ? [] : ['group' => $this->param['group']];

        $where['status|>='] = 0;

        $this->getConfigCommonData();

        $this->assign('list', self::$commonLogic->getDataList($where));

        $this->assign('group', !empty($this->param['group']) ? $this->param['group'] : 0);

        return $this->fetch('config_list');
    }

    /**
     * 获取通用数据
     */
    public function getConfigCommonData()
    {

        $config_group_list = parse_config_array('config_group_list');

        $config_type_list = parse_config_array('config_type_list');

        $this->assign('config_group_list', $config_group_list);

        $this->assign('config_type_list', $config_type_list);
    }

    /**
     * 配置添加
     */
    public function configAdd()
    {

        IS_POST && $this->jump(self::$commonLogic->dataAdd($this->param));

        $this->getConfigCommonData();

        !empty($this->param['group']) && $this->assign('info', ['group' => $this->param['group']]);

        return $this->fetch('config_add');
    }

    /**
     * 配置编辑
     */
    public function configEdit()
    {

        IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param, ['id' => $this->param['id']]));

        $info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        $this->getConfigCommonData();

        return $this->fetch('config_edit');
    }

    /**
     * 配置删除
     */
    public function configDel($id = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $id]));
    }

    /**
     * 配置批量删除
     */
    public function configAlldel($ids = 0)
    {

        $this->jump(self::$commonLogic->dataDel(['id' => $ids]));
    }

}
