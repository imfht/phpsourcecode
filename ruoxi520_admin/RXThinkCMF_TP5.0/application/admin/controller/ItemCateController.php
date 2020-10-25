<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 栏目-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\controller;
use app\admin\model\ItemCateModel;
use app\admin\service\ItemCateService;
class ItemCateController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new ItemCateModel();
        $this->service = new ItemCateService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::edit()
     */
    function edit()
    {
        $pid = input("get.pid",0);
        if($pid) {
            $pInfo = $this->model->getInfo($pid);
        }
        
        //获取站点信息
        $itemList = db('item')->where(['status'=>1,'mark'=>1])->select();
        $this->assign('itemList',$itemList);
        
        return parent::edit([
            'parent_id'=>$pid,
            'parent_name'=>$pInfo['name'],
        ]);
    }
    
    /**
     * 获取子级栏目【挂件专用】
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function getChilds()
    {
        if(IS_POST) {
            $message = $this->service->getChilds();
            return $message;
        }
    }
    
}