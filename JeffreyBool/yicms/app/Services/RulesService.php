<?php
/**
 * YICMS
 * ============================================================================
 * 版权所有 2014-2017 YICMS，并保留所有权利。
 * 网站地址: http://www.yicms.vip
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Created by PhpStorm.
 * Author: kenuo
 * Date: 2017/11/13
 * Time: 下午12:32
 */

namespace App\Services;

use App\Handlers\Tree;
use App\Repositories\RulesRepository;

class RulesService
{
    protected $tree;

    protected $rulesRepository;

    /**
     * RulesService constructor.
     * @param RulesRepository $rulesRepository
     * @param Tree $tree
     */
    public function __construct(RulesRepository $rulesRepository,Tree $tree)
    {
        $this->tree = $tree;

        $this->rulesRepository = $rulesRepository;
    }

    /**
     * 创建权限数据
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
        return $this->rulesRepository->create($params);
    }

    /**
     * 根据id获取权限的详细信息
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return $this->rulesRepository->ById($id);
    }

    /**
     * 获取树形结构权限列表
     * @return array
     */
    public function getRulesTree()
    {
        $rules = $this->rulesRepository->getRules()->toArray();
        return Tree::tree($rules,'name','id','parent_id');
    }
}