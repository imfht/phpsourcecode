<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * CMS管理-模型
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class ArticleModel extends CBaseModel {
    function __construct() {
        parent::__construct('article');
    }
    
    //自动验证
    protected $_validate = array(
        array('title', 'require', '文章标题不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('title', '1,150', '文章标题长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('guide', 'require', '文章导读不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('content', 'require', '文章内容不能为空！', self::EXISTS_VALIDATE, '', 3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //文章详情
            $info['detail_url'] = "https://tech.sina.com.cn/digi/2019-01-10/doc-ihqfskcn5721729.shtml";
            
            //获取栏目
            if($info['cate_id']) {
                $itemCateMod = new ItemCateModel();
                $cateInfo = $itemCateMod->getInfo($info['cate_id']);
                $info['cate_name'] = $cateInfo['item_name'] . ">>" . $cateInfo['name'];
            }
            
            //封面
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }
            
            //获取分表信息
            $table = $this->getTable($id,false);
            $articleMod = M($table);
            $array = $articleMod->find($id);
            if($array['content']) {
                while(strstr($array['content'],"[IMG_URL]")){
                    $array['content'] = str_replace("[IMG_URL]", IMG_URL, $array['content']);
                }
            }
            $info = array_merge($info, $array);

            //图集
            if($info['imgs']) {
                $imgsList =  unserialize($info['imgs']);
                foreach ($imgsList as &$row) {
                    $row = IMG_URL . $row;
                }
                $info['imgsList'] = $imgsList;
            }

        }
        return $info;
    }
    
    /**
     * 添加或编辑
     *
     * @author 牧羊人
     * @date 2018-04-15
     */
    public function edit($data, $id=0) {
        $column = $this->getDbFields();
        $item = [];
        foreach ($data as $key=>$val){
            if (!in_array($key, $column)) {
                $item[$key] = $val;
                unset($data[$key]);
            }
        }
        
        //开启事务
        $this->startTrans();
        $rowId = parent::edit($data,$id);
        if(!$rowId) {
            //事务回滚
            $this->rollback();
            return false;
        }
        $result = $this->saveDetail($rowId, $item);
        if(!$result) {
            //事务回滚
            $this->rollback();
            return false;
        }
        //提交事务
        $this->commit();
        return $rowId;
    }
    
    /**
     * 保存文章附表信息
     *
     * @author 牧羊人
     * @date 2015-07-09
     */
    function saveDetail($id,$item) {
        $table = $this->getTable($id);
        $info = $this->where(['id'=>$id])->table($table)->find();
        
        $data = [];
        if(!$info) {
            $data['id'] = $id;
        }
        $data['content'] = $item['content'];
        if($item['guide']) {
            $data['guide'] = $item['guide'];
        }
        if($item['imgs']) {
            $data['imgs'] = $item['imgs'];
        }
        
        //获取分表模型
        $table = $this->getTable($id,false);
        $articleMod = M($table);
        if($articleMod->create()) {
            if($info['id']) {
                $result = $articleMod->where("id={$id}")->save($data);
            }else{
                $result = $articleMod->add($data);
            }
            if($result!==false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 获取分表名称
     *
     * @author 牧羊人
     * @date 2018-07-17
     */
    function getTable($id,$isPre=true){
        $table = substr($id, -1 , 1);
        $table = "article_{$table}";
        if($isPre) {
            $table = $this->tablePrefix . $table; 
        }
        return $table;
    }
    
}