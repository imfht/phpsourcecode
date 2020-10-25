<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:52
 */

namespace Home\Service;


class CategoryService extends CommonService
{

    public function getParents($id = null)
    {
        $Category = $this->getM();
        $whereSql = "pid is null or pid = 0 ";
        if ($id != null) {
            $whereSql .= " and id <> " . $id;
        }
        $parents = $Category->where($whereSql)->order("sort")->select();
        return $parents;
    }


    public function getCategorysByRelatinModel($relation_model)
    {
        $Category = $this->getD()->relation(true);
        $whereSql = "relation_model='" . $relation_model . "' and (pid is null or pid = 0 )";
        $categorys = $Category->where($whereSql)->order("sort")->select();
        return $categorys;
    }


    /**
     * 添加栏目
     * @param  array $admin 管理员信息
     * @return array
     */
    public function add($category)
    {
        if (!$this->sameRelationModel($category)) {
            return $this->errorResultReturn('栏目父类型和关联模型不一致！');
        }
        $Category = $this->getD();
        $Category->startTrans();
        if (false === ($category = $Category->create($category))) {
            return $this->errorResultReturn($Category->getError());
        }
        //设置对应页面模版的默认值
        $category= $this->setDefaultTpl($category);
        $as = $Category->add($category);
        if (false === $as) {
            $Category->rollback();
            return $this->errorResultReturn('系统出错了！');
        }
        //更新父节点
//        if(!empty($category['pid'])){
//            $map['page_type'] = null;
//            $Category->where('id='.$category['pid'])->save($map);
//        }

        $Category->commit();
        return $this->resultReturn(true);
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update($category)
    {
        if (!$this->sameRelationModel($category)) {
            return $this->errorResultReturn('栏目父类型和关联模型不一致！');
        }
        $Category = $this->getD();
        if (false === ($category = $Category->create($category))) {
            return $this->errorResultReturn($Category->getError());
        }
        //设置对应页面模版的默认值
        $category= $this->setDefaultTpl($category);
        if (false === $Category->save($category)) {
            return $this->errorResultReturn('系统错误！');
        }

        return $this->resultReturn(true);
    }

    /**
     * 删除栏目并且删除子栏目
     * @param  int $id 需要删除模型的id
     * @return boolean
     */
    public function delete($id)
    {
        $Category = $this->getD();
        $Category->startTrans();
        // 删除栏目
        $delStatus = $Category->delete($id);
        // 删除子栏目
        $Category->where("pid=" . $id)->delete();
        // 删除文章
        if (false === $delStatus) {
            $Category->rollback();
            return $this->resultReturn(false);
        }

        $Category->commit();
        return $this->resultReturn(true);
    }

    public function existSubCategory($id)
    {
        $Category = $this->getM();
        $where = array('pid' => $id);
        $subCategorys = $Category->where($where)->count();
        if (null != $subCategorys && $subCategorys > 0) return true;
        return false;
    }

    public function getSubCategorys($pid)
    {
        $Category = $this->getM();
        $where = array('pid' => $pid);
        $subCategorys = $Category->where($where)->order("sort")->select();
        return $subCategorys;
    }

    public function isSinglePage($cate_id)
    {
        $Category = $this->getM();
        $count = $Category->where("pid is not null and pid<>0 and page_type=1 and id = %d", array($cate_id))->count();
        //echo $Article->getLastSql();
        if (null != $count && $count > 0) return true;
        return false;
    }

    private function sameRelationModel($category)
    {
        if (!empty($category['pid'])) {
            $parent = $this->getById($category['pid']);
            if ($parent['relation_model'] == $category['relation_model']) return true;
        }else{
            return true;
        }
        return false;
    }

    private function setDefaultTpl($category){
        if (empty($category['target_tpl'])) {
            if ($category['page_type'] == 1) $category['target_tpl'] = "single";
            if ($category['page_type'] == 2) {
                $category['target_tpl'] = "loop";
                if(empty($category['target_detail_tpl'])){
                    $category['target_detail_tpl'] = "detail";
                }
            }
        }
        return $category;
    }

    protected function getModelName()
    {
        return 'Category';
    }


} 