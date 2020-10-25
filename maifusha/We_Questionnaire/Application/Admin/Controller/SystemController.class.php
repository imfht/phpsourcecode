<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * ：处理系统配置相关请求
 */
class SystemController extends CommonController
{
	protected function _initialize()
	{
		parent::_initialize();
	}

	public function config()
	{
        $this->bcItemPush('系统配置');

        if( IS_GET ){ //访问页面
        	$configs = M('Settings')->getField('name,value');
        	$this->assign($configs);

			$this->display();
        }else{ //提交配置
        	$fieldList = array('weixin_AppID', 'weixin_AppSecret', 'weixin_Token', 'weixin_cryptType', 'weixin_EncodingAESKey');
        	
        	if( $configList = checkFilled($fieldList, I('post.')) ){ //case: 配置表单完整填写
        		$settings = D('Settings');
                $configList['weixin_domain'] = I('server.SERVER_NAME');

        		$settings->saveConfig($configList) OR $this->error( $settings->getError() );
                        F('settings', null); //清除系统配置缓存
        		$this->success('系统设置成功， 前往配置问卷。', U('Admin/Questionnaire/index'));
        	}else{ //case: 配置表单填写不完整
        		$this->error("请将配置填写完整！");
        	}
        }
	}

	public function account()
	{
        $this->bcItemPush('账号管理');

        if( IS_GET ){ //访问页面
			$this->display();
        }else{ //提交配置
        	//
        }
	}
}
?>