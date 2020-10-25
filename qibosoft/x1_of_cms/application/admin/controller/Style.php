<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
//use app\common\model\Plugin as PluginModel;
use app\common\traits\Market;
use app\common\model\Market AS MarketModel;

class Style extends AdminBase
{
    use AddEditList,Market;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
				'page_title'=>'风格管理',
				];
	
	/**
	 * 应用市场
	 */
	public function market($id=0,$page=0,$fid=8){
	    //执行安装云端模块
	    if($id){
	        return $this->get_style($id,'style');
	    }
	    $this->assign('fid',$fid?:8);	
	    return $this->fetch();
	}
	
	protected function get_style($id=0,$type='style'){
	    $keywords = input('keywords');
	    $appkey= input('appkey');
	    $domain= input('domain');
	    
	    $basepath = TEMPLATE_PATH;
	    
	    if(!is_writable($basepath)){
	        return $this->err_js($basepath.'目录不可写,请先修改目录属性可写');
	    }elseif ( is_dir($basepath.'index_style/'.$keywords) ){
	        //return $this->err_js($basepath.'index_style/'.$keywords.'目录已经存在了,无法安装此风格');
	    }
	    $url = "https://x1.php168.com/appstore/getapp/down.html?id=$id&domain=$domain&appkey=$appkey";
	    $result = $this->downModel($url,$keywords,$type);
	    if($result!==true){
	        return $this->err_js($result);
	    }
	    
	    $url = "https://x1.php168.com/appstore/getapp/info.html?id=$id&domain=$domain&appkey=$appkey";
	    if(($str=file_get_contents($url))==false){
	        $str = http_curl($url);
	    }
	    $info = json_decode($str,true);
	    
	    $data = [
	            'type'=>$info['type']?:'',
	            'keywords'=>$keywords,
	            'version_id'=>$id,
	            'name'=>$info['title']?:'',
	            'author'=>$info['author']?:'',
	            'author_url'=>$info['author_url']?:'',
	    ];
	    MarketModel::create($data);
	    
// 	    $result = $this->install($keywords,$type);
// 	    if($result!==true){
// 	        return $this->err_js($result);
// 	    }
	    
	    return $this->ok_js(['url'=>url('setting/index')],'风格安装成功,请在系统设置那里选择启用此风格');

	}
	
}
