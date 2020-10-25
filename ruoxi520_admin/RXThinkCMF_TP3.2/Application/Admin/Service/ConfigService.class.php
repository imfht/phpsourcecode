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
 * 配置-服务类
 * 
 * @author 牧羊人
 * @date 2018-09-22
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\ConfigModel;
class ConfigService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new ConfigModel();
    }
    
    /**
     * 获取数据列表
     *
     * @author 牧羊人
     * @date 2018-11-22
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
    
        $map = [];
    
        //查询条件
        $name = trim($param['name']);
        if($name) {
            $map['name'] = array('like',"%{$name}%");
        }
    
        return parent::getList($map);
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-09-25
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        //数据类型：1单行文本 2多行文本 3富文本 4图片 5图片集
        $type = (int)$data['type'];
        switch ($type) {
            case 1: {
                //单行文本
                $content = $data['content1'];
                
                break;
            }
            case 2: {
                //多行文本
                $content = $data['content2'];
                
                break;
            }
            case 3: {
                //富文本
                $content = $data['content3'];
                
                break;
            }
            case 4: {
                //单张图片
                $image = $data['image'];
                if(!$data['id'] && !$image) {
                    return message('请上传图片',false);
                }
                //封面处理
                if(strpos($image, "temp")) {
                    $content = \Zeus::saveImage($image, 'config');
                }
                
                break;
            }
            case 5: {
                //多张图集
                
                $imgsList = trim($data['imgs']);
                if($imgsList) {
                    $imgArr = explode(',', $imgsList);
                    foreach ($imgArr as $key => $val) {
                        if(strpos($val, "temp")) {
                            //新上传图片
                            $imgStr[] = \Zeus::saveImage($val, 'config');
                        }else{
                            //过滤已上传图片
                            $imgStr[] = str_replace(IMG_URL, "", $val);
                        }
                    }
                }
                $content = serialize($imgStr);

                break;
            }
        }
        
        //置空临时参数
        unset($data['content1']);
        unset($data['content2']);
        unset($data['content3']);
        unset($data['image']);
        unset($data['imgs']);
        unset($data['file']);
        
        $data['content'] = $content;
        return parent::edit($data);
        
    }
    
}