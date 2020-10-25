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
 * 文章-模型
 * 
 * @author 牧羊人
 * @date 2019-02-14
 */
namespace app\admin\model;
use app\common\model\BaseModel;
use think\Config;
class ArticleModel extends BaseModel
{
    // 设置数据表
    protected $name = 'article';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
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
            $table = $this->getArticleTable($id,false);
            $articleMod = db($table);
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
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::edit()
     */
    function edit($data, $id=0) 
    {
        $column = $this->getQuery()->getTableFields();
        $item = [];
        foreach ($data as $key=>$val){
            if (!in_array($key, $column)) {
                $item[$key] = $val;
                unset($data[$key]);
            }
        }
        
        //开启事务
        $this->getQuery()->startTrans();
        $rowId = parent::edit($data,$id);
        if(!$rowId) {
            //事务回滚
            $this->getQuery()->rollback();
            return false;
        }
        $result = $this->saveDetail($rowId, $item);
        if(!$result) {
            //事务回滚
            $this->getQuery()->rollback();
            return false;
        }
        //提交事务
        $this->getQuery()->commit();
        return $rowId;
    }
    
    /**
     * 保存信息到附表
     * 
     * @author 牧羊人
     * @date 2019-02-14
     */
    function saveDetail($id, $item)
    {
        $table = $this->getArticleTable($id);
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
        $table = $this->getArticleTable($id,false);
        $articleMod = db($table);
        if($info['id']) {
            $result = $articleMod->where("id={$id}")->update($data);
        }else{
            $result = $articleMod->insert($data);
        }
        if($result!==false) {
            return true;
        }
        return false;
    }
    
    /**
     * 获取附表名称
     * 
     * @author 牧羊人
     * @date 2019-02-14
     */
    function getArticleTable($id, $isPre=true) 
    {
        $table = substr($id, -1 , 1);
        $table = "article_{$table}";
        if($isPre) {
            $table = Config::get('config.db_prefix') . $table;
        }
        return $table;
    }
    
}