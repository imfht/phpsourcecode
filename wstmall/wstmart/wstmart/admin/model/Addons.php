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
 * 插件业务处理
 */
class Addons extends Base{
	
	/**
	 * 获取插件列表
	 * @param string $addon_dir
	 */
	public function pageQuery($addon_dir = ''){
		if(!$addon_dir)$addon_dir = WST_ADDON_PATH;
		$dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
		if($dirs === FALSE || !file_exists($addon_dir)){
			$this->error = '插件目录不可读或者不存在';
			return FALSE;
		}
		$addons = array();
		$where[] = ['dataFlag','=', 1]; 
		$this->where($where)->where('name','not in',$dirs)->delete();

		$names = $this->column("name");
		$names = array_map('strtolower', $names);
		$list = array();
		foreach ($dirs as $value) {
			if(!in_array($value,$names)){
				$class = get_addon_class($value);
				if(!class_exists($class)){ // 实例化插件失败忽略执行
					\Log::record('插件'.$value.'的入口文件不存在！');
					continue;
				}
				$obj = new $class;
				$data	= $obj->info;
				$config = $obj->getConfig();
				$data["isConfig"] = count($config)?1:0;
				$data["createTime"] = date("Y-m-d H:i:s");
				$data["updateTime"] = date("Y-m-d H:i:s");
				$data["dataFlag"] = 1; 
				$list[] = $data;
			}
		}
		if(count($list)>0)$this->saveAll($list);
		$status = (int)input("status",-1);
		$where[] = ["dataFlag",'=',1];
		$where[] = ["status","=",$status];
		$order = 'updateTime desc';
		$page = $this->where($where)->order($order)->paginate(input('post.limit/d'))->toArray();
		//echo $this->getLastSql();
		if(count($page['data'])>0){
			foreach ($page['data'] as $key => $v){
				$page['data'][$key]['statusName'] = WSTLangAddonStatus($v['status']);
				$page['data'][$key]['hasConf'] = ($v['isConfig']!='')?1:0;
			}
		}
		cache('WST_ADDONS_MAPS',null);
		return $page;

	}
	
	/**
	 * 保存插件设置
	 */
	public function saveConfig(){
		$id = input("id/d",0);
		$config =   isset($_POST['config'])?$_POST['config']:array();
		$data["config"] = json_encode($config);
		$data["updateTime"] = date('Y-m-d H:i:s');
		$flag = $this->save($data,['addonId'=>$id]);
		hook('adminAfterConfigAddon',['addonId'=>$id]);
		if($flag !== false){
			return WSTReturn("保存成功", 1);
		}else{
			return WSTReturn('保存失败',-1);
		}
	}
	
	/**
	 * 获取指定记录
	 */
	public function getById(){
		$id = input("id/d",0);
		return $this->get(['addonId'=>$id,'dataFlag'=>1])->toArray();
	}
	
	/**
	 * 新增
	 */
	public function add(){
		
        return WSTReturn("新增成功", 1);
	}
	 
    /**
	 * 编辑
	 */
	public function edit(){
		return WSTReturn("编辑成功", 1);
	}
	
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id/d');
	    $result = $this->where(["addonId"=>$id,'dataFlag'=>1])->delete();
		if($result!==false){
			cache('WST_ADDONS_MAPS',null);
			return WSTReturn("卸载成功", 1);
		}
		return WSTReturn('卸载失败',-1);
	}
	
	/**
     * 修改插件状态
     */
    public function editStatus($status){
    	$id = (int)input('post.id/d');
    	$data = array();
    	$data["status"] = $status;
    	$data["updateTime"] = date('Y-m-d H:i:s');
    	$result = $this->save($data,['addonId'=>$id]);
    	if($result!==false){
    		return WSTReturn("操作成功", 1);
    	}
    	return WSTReturn('操作失败',-1);
    }
    
	
}
