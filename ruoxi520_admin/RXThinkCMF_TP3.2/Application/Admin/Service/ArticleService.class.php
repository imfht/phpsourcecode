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
 * 文章管理-服务类
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\ArticleModel;
class ArticleService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new ArticleModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        //查询条件
        $keywords = trim($param['keywords']);
        if($keywords) {
            $map['title'] = array('like',"%{$keywords}%");
        }
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-17
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $cover = trim($data['cover']);
        
        //图集处理
        $imgsList = trim($data['imgs']);
        if($imgsList) {
            $imgArr = explode(',', $imgsList);
            foreach ($imgArr as $key => $val) {
                if(strpos($val, "temp")) {
                    //新上传图片
                    $imgStr[] = \Zeus::saveImage($val, 'article');
                }else{
                    //过滤已上传图片
                    $imgStr[] = str_replace(IMG_URL, "", $val);
                }
            }
        }
        $data['imgs'] = serialize($imgStr);
        
        //封面处理
        if(strpos($cover, "temp")) {
            $data['cover'] = \Zeus::saveImage($cover, 'article');
        }
        
        //内容处理
        \Zeus::saveImageByContent($data['content'],$data['title'],"article");

        return parent::edit($data);
        
    }
    
    /**
     * 设置是否显示
     * 
     * @author 牧羊人
     * @date 2019-01-11
     */
    function setIsShow() {
        $data = I('post.', '', 'trim');
        if(!$data['id']) {
            return message('文章ID不能为空',false);
        }
        if(!$data['is_show']) {
            return message('文章状态不能为空',false);
        }
        
        //数据表验证
        if(!$this->mod->create($data)) {
            $error = $this->getError();
            return message($error,false);
        }
        $result = M("article")->save($data);
        if($result!==false) {
            //手动设置缓存
            $this->mod->_cacheReset($data['id'],$data,true);
            return message();
        }
        return message('显示状态设置失败',false);
        
    }
    
}