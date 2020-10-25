<?php
namespace wstmart\admin\model;
use think\Db;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 钩子业务处理
 */
class Hooks extends Base{
	
	/**
	 * 获取插件列表
	 * @param string $addon_dir
	 */
	public function pageQuery(){
		
		$keyWords = input("keyWords");
		$parentId = input('parentId/d',0);
		$where[] = ["name","like","%$keyWords%"];
		$page = $this->where($where)->order('name asc')->paginate(input('post.limit/d'))->toArray();
		
		return $page;

	}
	
	/**
	 * 保存插件设置
	 */
	public function saveConfig(){
		$id = input("id/d",0);
		$config =   $_POST['config'];
		$flag = $this->where(["addonId"=>$id])->setField('config',json_encode($config));
		if($flag !== false){
			return WSTReturn("保存成功", 1);
		}else{
			return WSTReturn('保存失败',-1);
		}
	}
	
	/**
	 * 保存插件顺序
	 */
	public function changgeHookOrder(){
		$hook = input('hook');
		$hook2 = explode(',',$hook);
		$id = (int)input('id');
		$data = $this->where('hookId',$id)->find();
		$addons = explode(',',$data->addons);
		$isError = false;
		foreach ($hook2 as $key => $v) {
			if(!in_array($v,$addons)){
				$isError = true;
				break;
			}
		}
        if($isError || count($hook2)!=count($addons))return WSTReturn('无效的监听插件列表');
        $data->addons = $hook;
        $data->save();
        return WSTReturn('操作成功',1);
	}
    
	
}
