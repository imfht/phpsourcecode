<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 16:43
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use think\Db;
use app\common\model\content\AdvsCategory;
use app\common\model\content\Advs as AdvsModel;

/**
 * @title 广告管理
 * Class Ads
 * @package app\admin\controller\content
 */
class Advs extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        $db = AdvsModel::where('is_deleted',0);

        $search = $this->request->get();
        // 精准查询
        foreach (['state','cid'] as $field){
            if (isset($search[$field]) && $search[$field] !== ''){
                $db->where("{$field}",'=', $search[$field]);
            }
        }
        // 模糊查询
        foreach (['title'] as $field){
            if (isset($search[$field]) && $search[$field] !== ''){
                $db->whereLike("{$field}", "%{$search[$field]}%");
            }
        }

        return $this->_list($db, true, $search);
    }

    /**
     * 列表前置 分类搜索下拉框赋值
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _index_list_before()
    {
        $cates = AdvsCategory::where('is_deleted',0)
            ->field('id,title')
            ->select();
        $this->assign('cates', $cates);
    }

    /**
     * @title 添加操作
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new AdvsModel(), 'form');
    }

    /**
     * @title 编辑操作
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new AdvsModel(), 'form');
    }

    /**
     * 表单前置
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _form_before(&$data)
    {
        if ($this->request->isGet()){
            $cates = AdvsCategory::where('is_deleted',0)
                ->where('state',1)
                ->field('id,title')
                ->select();
            $this->assign('cates', $cates);
        }else{
            if (!isset($data['id'])){
                $data['create_time'] = $this->request->time();
            }
        }
    }

    /**
     * @title 删除操作
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $this->_del(new AdvsModel(), $ids);
    }

    /**
     * @title 启用/禁用
     */
    public function change()
    {
        $id = $this->request->post('id');
        $state = $this->request->post('state');
        $this->_change(new AdvsModel(), $id, ['state' => $state]);
    }
}