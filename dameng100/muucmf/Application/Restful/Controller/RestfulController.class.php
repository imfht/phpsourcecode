<?php
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


class RestfulController extends AdminController
{
    protected $RestfulApiModel;

    function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 接口配置文件
     * @return [type] [description]
     */
    public function config()
    {
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();

        $builder
            ->title('接口基本设置')
            //配置
            ->keyText('RESTFUL_CONFIG_SECRET','SECRET','API请求合法性的密钥')
            ->keyRadio('SIGNATURE','验证请求合法性开关','',array(1=>'开启',0=>'关闭'))
            
            ->buttonSubmit('', '保存')
            ->data($data);
        $builder->display();
    }
}
