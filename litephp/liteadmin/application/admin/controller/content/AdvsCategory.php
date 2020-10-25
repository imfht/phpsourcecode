<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 16:44
 */

namespace app\admin\controller\content;

use app\common\controller\BaseAdmin;
use app\common\model\content\AdvsCategory as AdvsCategoryModel;
use app\common\model\content\Advs;

/**
 * @title 广告分类
 * Class AdvCategory
 * @package app\admin\controller\content
 */
class AdvsCategory extends BaseAdmin
{
    /**
     * @title 列表页
     * @return mixed
     */
    public function index()
    {
        $db = AdvsCategoryModel::where('is_deleted','=',0);

        $search = $this->request->get();
        // 精准查询
        foreach (['state'] as $field){
            if (isset($search[$field]) && $search[$field] !== ''){
                $db->where($field,'=', $search[$field]);
            }
        }
        // 模糊查询
        foreach (['title'] as $field){
            if (isset($search[$field]) && $search[$field] !== ''){
                $db->whereLike($field, "%{$search[$field]}%");
            }
        }

        return $this->_list($db, true, $search);
    }

    /**
     * @title 添加操作
     * @return array|mixed
     */
    public function add()
    {
        return $this->_form(new AdvsCategoryModel(), 'form');
    }

    /**
     * @title 编辑操作
     * @return array|mixed
     */
    public function edit()
    {
        return $this->_form(new AdvsCategoryModel(), 'form');
    }

    /**
     * @title 禁用/启用
     */
    public function change()
    {
        $id = $this->request->post('id');
        $state = $this->request->post('state');
        $this->_change(new AdvsCategoryModel(), $id, ['state' => $state]);
    }

    /**
     * @title 删除操作
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        $ids = $this->request->post('ids');
        $hasChild = Advs::whereIn('cid',$ids)->where('is_deleted',0)->count();
        if ($hasChild){
            $this->error("该分类下有广告链接，不能删除");
        }
        $this->_del(new AdvsCategoryModel(), $ids);
    }
}