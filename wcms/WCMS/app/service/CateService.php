<?php

class CateService
{
    public function ztree ()
    {
        $rs=CategoryModel::instance()->getAllCategory();
        

        foreach ($rs as $k => $v) {
            
            $fcate = CategoryModel::instance()->getCategoryByFid($v['id']);
            if (! empty($fcate)) {
                $rs[$k]['isParent'] = true;
            } else {
                $rs[$k]['isParent'] = false;
            }
            $rs[$k]['drag'] = true;
            $rs[$k]['open'] = $v['id'] == 1 ? true : false;
            $rs[$k]['name'] = $v['name'] . $v['id'];
        }
        return json_encode($rs);
    }

    public function saveCategoryNameById($name,$id){
        return CategoryModel::instance()->saveCategoryNameById($name,$id);
    }

    public function addCate($data){
        return CategoryModel::instance()->addCategory($data);
    }

    /**
     * 获取分类
     * 
     * @param int $cid            
     */
    public function getCateById ($id)
    {
        return CategoryModel::instance()->getCateogryById($id);
    }


    public function getCategory ()
    {
       return  CategoryModel::instance()->getAllCategory();
    }


    public function deleteCatetgoryById($id){
        CategoryModel::instance()->delCategoryByFid($id);
        CategoryModel::instance()->deleteCategoryById($id);
    }
}

class CategoryModel extends Db {

    /**
     * 新闻分类表
     *
     * @var string
     */
    protected $_news_category = 'w_news_category';

    /**
     * 获取子类
     * @param int $fid
     * @return Array
     */
    public function getCategoryByFid($fid = 0, $num = 100) {
        $sql = "SELECT * FROM $this->_news_category WHERE `fid`=$fid   ORDER BY sort ASC LIMIT $num";
        return $this->fetchAll ( $sql );
    }

    public function saveCategoryNameById($name,$id){
      return  $this->update($this->_news_category,array('name'=>$name),array('id'=>$id));
    }

    public function deleteCategoryById($id){
        return $this->delete($this->_news_category,array('id'=>$id));
    }

    public function delCategoryByFid($fid){
        return $this->delete($this->_news_category,array('fid'=>$fid));
    }

    /**
     * 获取分类
     * Enter description here ...
     * @param unknown_type $where
     */
    public function getAllCategory() {
        return $this->getAll ( $this->_news_category);
    }

    /**
     * 获取当前类
     * @param unknown_type $id
     */
    public function getCateogryById($id) {
        return $this->getOne ( $this->_news_category, array ('id' => $id ) );
    }

    /**
     * 添加分类
     *
     * @param array $arr
     */
    public function addCategory($arr) {
      return  $this->add ( $this->_news_category, $arr );
    }



    /**
     *
     *更新分类
     * @param unknown_type $v
     * @param unknown_type $where
     */
    public function save($v, $where) {
        return $this->update ( $this->_news_category, $v, $where );
    }


    /**
     * 返回CategoryModel
     *
     * @return CategoryModel
     */
    public static function instance() {
        return self::_instance ( __CLASS__ );
    }
}