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
 * 文章-服务类
 * 
 * @author 牧羊人
 * @date 2019-02-14
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\ArticleModel;
class ArticleService extends AdminServiceModel
{
    /**
     * 初始化模型
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::initialize()
     */
    function initialize()
    {
        parent::initialize();
        $this->model = new ArticleModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::getList()
     */
    function getList()
    {
        $param = input("request.");
        
        $map = [];
        
        //查询条件
        $title = trim($param['title']);
        if($title) {
            $map['title'] = array('like',"%{$title}%");
        }
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::edit()
     */
    function edit()
    {
        $data = input('post.', '', 'trim');
        $cover = trim($data['cover']);
        
        //图集处理
        $imgsList = trim($data['imgs']);
        if($imgsList) {
            $imgArr = explode(',', $imgsList);
            foreach ($imgArr as $key => $val) {
                if(strpos($val, "temp")) {
                    //新上传图片
                    $imgStr[] = \Common::saveImage($val, 'article');
                }else{
                    //过滤已上传图片
                    $imgStr[] = str_replace(IMG_URL, "", $val);
                }
            }
        }
        $data['imgs'] = serialize($imgStr);
        
        //封面处理
        if(strpos($cover, "temp")) {
            $data['cover'] = \Common::saveImage($cover, 'article');
        }
        
        //内容处理
        \Common::saveImageByContent($data['content'],$data['title'],"article");
        
        return parent::edit($data);
    }
    
    /**
     * 设置文章是否显示
     * 
     * @author 牧羊人
     * @date 2019-02-14
     */
    function setIsShow()
    {
        $data = input('post.', '', 'trim');
        if(!$data['id']) {
            return message('文章ID不能为空',false);
        }
        if(!$data['is_show']) {
            return message('文章状态不能为空',false);
        }
        
        //数据表验证
        if(!$this->model->create($data)) {
            $error = $this->getError();
            return message($error,false);
        }
        $result = $this->model->save($data);
        if($result!==false) {
            //手动设置缓存
            $this->model->_cacheReset($data['id'],$data,true);
            return message();
        }
        return message('操作失败',false);
    }
    
}