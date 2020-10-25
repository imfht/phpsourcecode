<?php
namespace app\api\controller\config;
use app\api\controller\BaseController;

class ShopController extends BaseController
{
	//商城设置
	public function index(){
		$config = model('Config')->with('logo')->find();
		
		$delivery_time = x_model('AddonDeliveryConfig')->value('delivery_time');
        if($delivery_time){
            $config['delivery_time'] = explode(',',$delivery_time);
        }else{
            $config['delivery_time'] = '';
        }
        $config['old_id'] = model('WxConfig')->where('id',1)->value('old_id');
        $category_id = model('ArticleCategory')->where(array('name'=>'帮助','status'=>1))->value('id');
        $config['about_article'] = model('Article')->where('category_id',$category_id)->where('status',1)->select();

		$data['config'] = $config;
		return json(['data' => $data, 'msg' => '商城配置', 'code' => 1]);
	}
}