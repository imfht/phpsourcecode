<?php


namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;
use Admin\Builder\AdminSortBuilder;
use Common\Model\ContentHandlerModel;


class MuushopController extends AdminController
{
    protected $product_cats_model;
    protected $product_model;
    protected $order_model;
    protected $delivery_model;
    protected $message_model;
    protected $coupon_model;
    protected $user_coupon_model;
    protected $address_model;
    protected $product_comment_model;
	protected $order_logic;
	protected $coupon_logic;

    function _initialize()
    {
        $this->product_cats_model = D('Muushop/MuushopProductCats');
	    $this->product_model = D('Muushop/MuushopProduct');
	    $this->order_model = D('Muushop/MuushopOrder');
	    $this->delivery_model = D('Muushop/MuushopDelivery');
	    $this->message_model = D('Muushop/MuushopMessage');
	    $this->coupon_model = D('Muushop/MuushopCoupon');
	    $this->user_coupon_model = D('Muushop/MuushopUserCoupon');
	    $this->order_logic = D('Muushop/MuushopOrder','Logic');
	    $this->coupon_logic = D('Muushop/MuushopCoupon','Logic');
	    $this->address_model = D('Muushop/MuushopUserAddress');
	    $this->product_comment_model = D('Muushop/MuushopProductComment');
	    $this->custom_nav_model = D('Muushop/MuushopNav');
        parent::_initialize();
    }


	public function index()
	{
		if(!modC('MUUSHOP_SHOW_TITLE', '', 'Muushop'))
		{
			//未配置商城跳转
			redirect(U('Muushop/config'));
		}
		else
		{
			redirect(U('Muushop/product'));
		}
	}
	/**
	 * 系统基础配置
	 */
	public function config()
	{
        //获取所有支付方式
        $able_payment = D('Muushop/MuushopPay')->getPayment();

		$builder = new AdminConfigBuilder();
		$data = $builder->handleConfig();
		$builder->title('基本设置')
			->data($data)
			//基础配置
			->keyText('MUUSHOP_SHOW_TITLE', '商城名称', '在首页的商场名称')->keyDefault('MUUSHOP_SHOW_TITLE','MuuCmf轻量级商场解决方案')
			->keySingleImage('MUUSHOP_SHOW_LOGO','商场logo')
			->keyBool('MUUSHOP_SHOW_STATUS', '商城状态','默认正常')
			->keyEditor('MUUSHOP_SHOW_DESC', '商城简介','','all',array('width' => '800px', 'height' => '200px'))

            //支付设置
            ->keyCheckBox('MUUSHOP_PAYMENT','允许的支付方式','',$able_payment)
            ->keyText('MUUSHOP_PAY_CALLBACK','支付成功后的回调地址')

			//售后保障
			->keyEditor('MUUSHOP_SHOW_SERVICE', '售后保障','','all',array('width' => '800px', 'height' => '500px'))

			->group('商城基本配置', 'MUUSHOP_SHOW_TITLE,MUUSHOP_SHOW_LOGO,MUUSHOP_SHOP_STATUS,MUUSHOP_SHOW_PAYTYPE,MUUSHOP_SHOW_SCORE,MUUSHOP_SHOW_DESC,')
            ->group('支付设置','MUUSHOP_PAYMENT,MUUSHOP_PAY_CALLBACK')
			->group('售后保障','MUUSHOP_SHOW_SERVICE')
			
			->buttonSubmit('', '保存')
			->display();
	}

	public function api_config(){
		if ($_SERVER['HTTPS'] != "on") {
        	$is_https = 'http://';
        }else{
        	$is_https = 'https://';
        }
        $data['MUUSHOP_PINGPAY_WEBHOOKS'] =$_SERVER['SERVER_NAME'].'/muushop/pay/webhooks';
		$builder = new AdminConfigBuilder();
		$data = $builder->handleConfig();
		$builder->title('Api设置')
		//ping++配置
        ->keyText('MUUSHOP_PINGPAY_APIKEY','api_key','登录(https://dashboard.pingxx.com)->点击管理平台右上角公司名称->开发信息-> Secret Key')
        ->keyText('MUUSHOP_PINGPAY_APPID','app_id','登录(https://dashboard.pingxx.com)->点击你创建的应用->应用首页->应用 ID(App ID)')
        ->keyTextArea('MUUSHOP_PINGPAY_PUBLICKEY','ping++公钥','')
        ->keyText('MUUSHOP_PINGPAY_PUBLISHABLEKEY','Publishable Key','Ping++ 应用内快捷支付 Key')
        ->keyTextArea('MUUSHOP_PINGPAY_PRIVATEKEY','RSA 商户私钥','如：your_rsa_private_key.pem')
        ->keyReadOnlyText('MUUSHOP_PINGPAY_WEBHOOKS','webhooks回调地址')

        //物流API配置
        ->keyText('MUUSHOP_DELIVERY_EBUSINESS','Ebusiness','请到快递鸟官网申请http://kdniao.com/reg')
        ->keyText('MUUSHOP_DELIVERY_APPKEY','AppKey','电商加密私钥，快递鸟提供，注意保管，不要泄漏')

        ->group('ping++ 接口设置','MUUSHOP_PINGPAY_APIKEY,MUUSHOP_PINGPAY_APPID,MUUSHOP_PINGPAY_PUBLICKEY,MUUSHOP_PINGPAY_PUBLISHABLEKEY,MUUSHOP_PINGPAY_PRIVATEKEY,MUUSHOP_PINGPAY_WEBHOOKS')
        ->group('物流查询配置','MUUSHOP_DELIVERY_EBUSINESS,MUUSHOP_DELIVERY_APPKEY')
        
		->data($data)
        ->buttonSubmit('', '保存')
		->display();
	}
	/**
	 * @param  自定义导航
	 * @return [type]
	 */
	public function custom_nav($action='') {

        if (IS_POST) {
            $one = $_POST['nav'][1];
            if (count($one) > 0) {
                M()->execute('TRUNCATE TABLE ' . C('DB_PREFIX') . 'muushop_nav');
                for ($i = 0; $i < count(reset($one)); $i++) {
                    $data[$i] = array(
                        'pid' => 0,
                        'title' => op_t($one['title'][$i]),
                        'url' => op_t($one['url'][$i]),
                        'sort' => intval($one['sort'][$i]),
                        'target' => intval($one['target'][$i]),
                        'color' => op_t($one['color'][$i]),
                        'band_text' => op_t($one['band_text'][$i]),
                        'band_color' => op_t($one['band_color'][$i]),
                        'status' => 1

                    );
                    $pid[$i] = $this->custom_nav_model->add($data[$i]);
                }
                $two = $_POST['nav'][2];

                for ($j = 0; $j < count(reset($two)); $j++) {
                    $data_two[$j] = array(
                        'pid' => $pid[$two['pid'][$j]],
                        'title' => op_t($two['title'][$j]),
                        'url' => op_t($two['url'][$j]),
                        'sort' => intval($two['sort'][$j]),
                        'target' => intval($two['target'][$j]),
                        'color' => op_t($two['color'][$j]),
                        'band_text' => op_t($two['band_text'][$j]),
                        'band_color' => op_t($two['band_color'][$j]),
                        'status' => 1
                    );
                    $res[$j] = $this->custom_nav_model->add($data_two[$j]);
                }
                S('custom_nav',NULL);
                $this->success(L('_CHANGE_'));
            }
            $this->error(L('_NAVIGATION_AT_LEAST_ONE_'));


        } else {
            /* 获取自定义导航列表 */
            $map = array('status' => array('gt', -1), 'pid' => 0);
            $list = $this->custom_nav_model->where($map)->order('sort asc,id asc')->select();
            foreach ($list as $k => &$v) {
                $cats = D('MuushopProductCats')->where(array('id' => $v['url']))->find();
                $v['cats_title'] = $cats['title'];
                
                $child = $this->custom_nav_model->where(array('pid' => $v['id']))->order('sort asc,id asc')->select();
                foreach ($child as $key => &$val) {
                    $child_cats = D('MuushopProductCats')->where(array('id' => $val['url']))->find();
                    $val['cats_title'] = $child_cats['title'];
                }
                unset($key, $val);
                $child && $v['child'] = $child;
            }
            unset($k, $v);
            $this->assign('cats', $this->getProductCats());
            $this->assign('list', $list);

            
            $this->display('Muushop@Admin/custom_nav');
        }
	}
	private function getProductCats(){
		$option['parent_id'] = 0;
		$option['status'] = 1;
		$cats = $this->product_cats_model->where($option)->select();

		return $cats;
	}

	/*
	 * 商品分类
	 */
	public function product_cats($action='',$page=1,$r=20)
	{
		switch($action){
			case 'add':
				if(IS_POST){
					$product_cats = $this->product_cats_model->create();
					if (!$product_cats){

						$this->error($this->product_cats_model->getError());
					}
					if(!empty($product_cats['parent_id'] )
						&& (
							($product_cats['parent_id'] ==$product_cats['id']) ||
							(($sun_id = $this->product_cats_model->get_all_cat_id_by_pid($product_cats['id']))
							&& (in_array($product_cats['parent_id'],$sun_id))))
					)
					{
						$this->error('不要选择自己分类或自己的子分类');
					}
					$ret = $this->product_cats_model->add_or_edit_product_cats($product_cats);
					if ($ret){

						$this->success('操作成功。', U('muushop/product_cats',array('parent_id'=>I('parent_id',0))));
					}else{
						$this->error('操作失败。');
					}
				}else{
					$id = I('get.id',0,'intval');
					$parent_id = I('get.parent_id',0,'intval');

					if ($id != 0) {
		                $data = $this->product_cats_model->find($id);
		            }
					$parent_cats = $this->product_cats_model->get_produnct_cat_config_select();

		            $builder = new AdminConfigBuilder();
					$builder
						->title('新增/修改商品分类')
						->data($data)
						->keyId()
						->keyText('title', '分类名称')
						->keyText('title_en', '分类名称英文')
						->keySingleImage('image','图标','分类图标，建议80x80px png\gif\jpg图片')
						->keySelect('parent_id','上级分类','',$parent_cats)->keyDefault('parent_id',$parent_id)
						->keyText('sort', '排序')
						->keyStatus()->keyDefault('status',1)
						->keyCreateTime()
						
						->buttonSubmit(U('muushop/product_cats',array('action'=>'add')))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':
				$ids = I('ids');
				$ret = $this->product_cats_model->delete_product_cats($ids);
				if ($ret){

					$this->success('操作成功。', U('Muushop/product_cats'));
				}else{
					$this->error('操作失败。');
				}
				break;

			case 'sort':

		        if (IS_POST) {
		        	$ids = I('post.ids');
		            $builder = new AdminSortBuilder;
		            $builder->doSort('muushop_product_cats', $ids);
		        } else {

		        	$map['parent_id'] = I('get.parent_id',0,'intval');
		            $map['status'] = 1;
		            $list = $this->product_cats_model->getList($map);

		            foreach ($list as $key => $val) {
		                $list[$key]['title'] = $val['title'];
		            }
		            $builder = new AdminSortBuilder;
		            $builder->meta_title = '分类排序';
		            $builder->data($list);
		            $builder->buttonSubmit()->buttonBack();
		            $builder->display();
		        }
		        break;

			default:

				$option['parent_id'] = I('parent_id',0,'intval');
				if(!empty($option['parent_id'])){
					$parent_cat  = $this->product_cats_model->get_product_cat_by_id($option['parent_id']);
				}
				if(I('all')) $option = array();
				$option['page'] = $page;
				$option['r']  =  $r;
				$cats = $this->product_cats_model->get_product_cats($option);
				$totalCount = $cats['count'];
				
				$select = $this->product_cats_model->get_produnct_cat_list_select();
				$builder = new AdminListBuilder();
				$builder
					->title((empty($parent_cat)?'顶级的':$parent_cat['title'].' 的子').'商品分类')
					->setSelectPostUrl(U('muushop/product_cats'))
					->select('分类查看：', 'parent_id', 'select', '', '', '', $select);

				//顶级分类列表不显示上级分类按钮
				if($option['parent_id']>0){
					$builder->buttonNew(U('muushop/product_cats',array('parent_id'=>(empty($parent_cat['parent_id'])?0:$parent_cat['parent_id']))),'返回上级分类');
				}
				$builder->buttonNew(U('muushop/product_cats',array('action'=>'add','parent_id'=>$option['parent_id'])),'新增分类');
				$builder
				->buttonSort(U('muushop/product_cats',array('action'=>'sort','parent_id'=>$option['parent_id'])))
				->ajaxButton(U('muushop/product_cats',array('action'=>'delete')),'','删除')
				->keyText('id','id')
				->keyText('title','标题')
				->keyText('title_en','英文标题')
				->keyImage('image','ICON')
				->keyText('sort','排序')
				->keyTime('create_time','创建时间')
				->keyStatus('status','状态')
				->keyDoAction('admin/muushop/product_cats/action/add/id/###/parent_id/'.$option['parent_id'],'编辑')
				->keyDoAction('admin/muushop/product_cats/parent_id/###','查看下属分类')
				->data($cats['list'])
				->pagination($totalCount, $r)
				->display();
		}
	}
	/**
	 * 商品配置管理
	 * @return [type] [description]
	 */
	public function product_config()
	{
		$builder = new AdminConfigBuilder();
		$data = $builder->handleConfig();

		$position_options=$this->_getPositions();
		$builder->title('商品设置')
			->data($data)
			//展示位
			->keyTextArea('MUUSHOP_POS_POSITION','展示位配置','每行一条，格式：key:value')
			->keyDefault('MUUSHOP_POS_POSITION',$default_position)
			->keyText('MUUSHOP_LIST_NUM','列表页每页显示商品数');
		//已设置展示位配置
		foreach($position_options as $k=>$v){
		$builder->keyText('MUUSHOP_POS_'.$k.'_TITLE', '标题名称', '展示块的标题')->keyDefault('MUUSHOP_POS_'.$k.'_TITLE',$v.'商品')
            ->keyText('MUUSHOP_POS_'.$k.'DESCRIPTION', '简短描述', '精简的描述模块内容')->keyDefault('MUUSHOP_POS_'.$k.'DESCRIPTION',$v.'展示块简单描述')
            ->keyText('MUUSHOP_POS_'.$k.'COUNT', '显示商品的个数', '只有在启用了展示块之后才会显示')->keyDefault('MUUSHOP_POS_'.$k.'COUNT',4)

            ->keyRadio('MUUSHOP_POS_'.$k.'ORDER_FIELD', '排序值', '展示块的数据排序字段', array('click_cnt' => '点击数','sell_cnt' => '总销量','create_time' => '上架时间', 'modify_time' => '更新时间'))->keyDefault('MUUSHOP_POS_'.$k.'ORDER_FIELD','click_cnt')

            ->keyRadio('MUUSHOP_POS_'.$k.'ORDER_TYPE', '排序方式', '展示块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'))->keyDefault('MUUSHOP_POS_'.$k.'ORDER_TYPE','desc')
            ->keyText('MUUSHOP_POS_'.$k.'CACHE_TIME', '缓存时间', '默认600秒，以秒为单位')->keyDefault('MUUSHOP_POS_'.$k.'CACHE_TIME','600');
        }
        
		$builder->group('商品配置', 'MUUSHOP_POS_POSITION,MUUSHOP_LIST_NUM');
			
		foreach($position_options as $k=>$v){
			$builder->group($v.'配置','MUUSHOP_POS_'.$k.'_TITLE,MUUSHOP_POS_'.$k.'DESCRIPTION,MUUSHOP_POS_'.$k.'COUNT,MUUSHOP_POS_'.$k.'TYPE,MUUSHOP_POS_'.$k.'ORDER_FIELD,MUUSHOP_POS_'.$k.'ORDER_TYPE,MUUSHOP_POS_'.$k.'CACHE_TIME');
		}
			
		$builder->buttonSubmit('', '保存')
		->display();
	}

	private function _getPositions($type=0)
    {
        $default_position=<<<str
1:热销
2:推荐
4:新品
str;
        $positons=modC('MUUSHOP_POS_POSITION',$default_position,'Muushop');
        $positons = str_replace("\r", '', $positons);
        $positons = explode("\n", $positons);
        $result=array();
        if($type){
            foreach ($positons as $v) {
                $temp = explode(':', $v);
                $result[] = array('id'=>$temp[0],'value'=>$temp[1]);
            }
        }else{
            foreach ($positons as $v) {
                $temp = explode(':', $v);
                $result[$temp[0]] = $temp[1];
            }
        }
        return $result;
    }
	/*
	 * 商品相关
	 */
	public function product($action = '')
	{
		switch($action)
		{
			case 'add':
				if(IS_POST){

					$product = $this->product_model->create();
					if (!$product){
						$this->error($this->product_model->getError());
					}
					$product['price'] = sprintf("%.2f",$product['price']*100);
					$product['ori_price'] = sprintf("%.2f",$product['ori_price']*100);

					$ret = $this->product_model->edit_product($product);
					if ($ret){
						$this->success('操作成功。', U('muushop/product'));
					}else{
						$this->error('操作失败。');
					}
				}else{
					$builder = new AdminConfigBuilder();
					$id = I('id');
					if(!empty($id)){
						$product = $this->product_model->get_product_by_id($id);
						$product['price'] = sprintf("%.2f",$product['price']/100);
						$product['ori_price'] = sprintf("%.2f",$product['ori_price']/100);
					}else{
						$product = array();
					}

					$select = $this->product_cats_model->get_produnct_cat_config_select('选择分类');
					if(count($select)==1){
						$this->error('先添加一个商品分类吧',U('muushop/product_cats',array('action'=>'add')),2);
					}
					$delivery_select = $this->delivery_model->getfield('id,title');
					//商品展示位
					$position_options=$this->_getPositions();
					//其它配置
					//$info_array = array(
					//    '6'=>'热销','7'=>'推荐'
					//);
					//注释的暂不支持
					$builder->title('新增/修改商品')
						->keyId()
						->keyText('title', '商品名称')
						->keyTextArea('description','简单描述')
						->keySingleImage('main_img','商品主图')
						->keyMultiImage('images','商品图片,分号分开多张图片')
						->keySelect('cat_id','商品分类','',$select)
						->keyInteger('price', '价格/元','交易价格')
						->keyInteger('ori_price', '原价/元','显示被划掉价格')
						->keyInteger('quantity', '库存')
						->keyText('product_code', '商家编码,可用于搜索')
						->keyCheckBox('position','商品定位','',$position_options)
						->keyInteger('back_point', '购买返还积分')
//						->keyInteger('point_price', '积分换购所需分数')
//						->keyInteger('buy_limit', '限购数,0不限购')
//						->keytext('location','货物所在地址')
						->keySelect('delivery_id','运费模板, 可先保存后再修改运费模板,避免丢失已编辑信息','<a target="_blank" href="index.php?s=/admin/muushop/delivery">点击添加运费模板</a>',$delivery_select)
						->keyText('sort', '排序')
						->keyRadio('status','状态','',array('1'=>'正常','0'=>'下架'))
						->keyEditor('content', '商品详情','','all',array('width' => '800px', 'height' => '500px'))
						->group('基本信息','id,title,description,main_img,images,cat_id,price,ori_price,quantity,product_code,position,back_point,delivery_id,sort,status')
						->group('商品详情','content')
						->data($product)
						->buttonSubmit(U('muushop/product',array('action'=>'add')))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':

				if(IS_POST){
					$ids = I('ids');
					$ret = $this->product_model->delete_product($ids);
					if ($ret){
						$this->success('操作成功。', U('muushop/product'));
					}else{
						$this->error('操作失败。');
					}
				}else{
		        	$ids=I('ids');
		            $ids=implode(',',$ids);
		            $this->assign('ids',$ids);
		            $this->display(T('Muushop@Admin/del'));
			    }

				break;
			case 'cell_record':
				$option['product_id'] = I('product_id',0);
				$option['user_id'] = I('user_id',0);
				$option['page'] = I('page',1);
				$option['r'] = I('r',10);
				$product_sell_model = D('muushop/ShopProductSell');
				$product_sell_record = $product_sell_model->get_sell_record($option);
				$totalCount = $product_sell_record['count'];
				$builder = new AdminListBuilder();
				$builder
					->title('商品成交记录')
					->keyText('product_id','商品id')
					->keyText('order_id','订单id')
					->keyText('user_id','用户id')
					->keyText('paid_price','下单价格/（分）')
					->keyText('quantity','下单数目')
					->keyTime('create_time','创建时间')
					->data($product_sell_record['list'])
					->pagination($totalCount, $option['r'])
					->display();
				break;
			case 'delete_sku_table':

				if(IS_POST){
					$product['id'] = I('id','','intval');
					empty($product['id']) && $this->error('缺少商品id');
					$product['sku_table'] = '';
					$ret = $this->product_model->edit_product($product);
					if ($ret){
						$this->success('操作成功。',U('muushop/product',array('action'=>'sku_table','id'=>$product['id'])),1);
					}else{
						$this->error('操作失败。');
					}
				}
				break;
			case 'sku_table':

				if(IS_POST){
					$product['id'] = I('id',0,'intval');
					empty($product['id']) && $this->error('缺少商品id');
					$table = I('table','','text');
					$info = I('info','','text');

					$product['sku_table'] = array('table'=>$table,'info'=>$info);
					$product['sku_table'] = json_encode($product['sku_table']);
					$ret = $this->product_model->edit_product($product);
					if ($ret){
						$this->success('操作成功。');
					}else{
						$this->error('操作失败。');
					}
				}
				else
				{
					$id = I('id',0,'intval');
					if(empty($id) || !($product = $this->product_model->get_product_by_id($id)))
					{
						$this->error('请选择一个商品','',2);
					}
					$this->assign('product', $product);
	                $this->display('Muushop@Admin/sku_table');
				}
				
				break;
			case 'exi':
				if(IS_POST)
				{
					//没写完
					var_dump(__file__.' line:'.__line__,$_REQUEST);exit;
					$product = array();
					$ret = $this->product_model->edit_product($product);
					if($ret){
						$this->success('操作成功',U('muushop/product'));
					}else{
						$this->error('操作失败');
					}

				}else{
					$porduct_extra_info_model = D('Muushop/MuushopProductExtraInfo');

					$id = I('id');
					if(empty($id) || !($product = $this->product_model->get_product_by_id($id))){
						$this->error('请选择一个商品','',2);
					}
					$exi = $porduct_extra_info_model->get_product_extra_info($id);
					$this->assign('exi', $exi);
					$this->display('Muushop@Admin/exi');
				}
				break;
			//默认展示商品列表
			default:
				$page = I('page',1);
				if(I('cat_id')){
					$option['cat_id'] = I('cat_id');
				}
				
				$count = I('count');
				if(empty($option['cat_id'])) unset($option['cat_id']);
				list($product,$totalCount) = $this->product_model->getListByPage($option,$page,'create_time desc');
				foreach($product as &$val){
					$val['price']='￥'.sprintf("%.2f",$val['price']/100);
				}
				unset($val);
				
				$select = $this->product_cats_model->get_produnct_cat_list_select('全部分类');
				$select2 = $this->product_cats_model->get_produnct_cat_config_select('全部分类');
				$builder = new AdminListBuilder();
				$builder
					->title('商品管理')
					->setSelectPostUrl(U('muushop/product'))
					->select('分类查看：', 'cat_id', 'select', '', '', '', $select)
					->select('显示模式：', 'count', 'select', '', '', '', array(array('id'=>0,'value'=>'正常'),array('id'=>1,'value'=>'统计信息')))
					->buttonnew(U('muushop/product',array('action'=>'add')),'新增商品')
					->buttonModalPopup(U('muushop/product',array('action'=>'delete')),'','彻底删除',array('data-title'=>'是否彻底删除','target-form'=>'ids'))
					->keyText('id','商品id')
					->keyImage('main_img','图片')
					->keyText('title','商品名');
				if(!$count){
					$builder
						->keyMap('cat_id','所属分类',$select2)
						->keyText('price','价格/（元）')
						->keyText('quantity','库存')
						->keyTime('create_time','创建时间')
						->keyText('sort','排序')
						->keyMap('status','状态',array('1'=>'正常','0'=>'下架'));
				}else{
					$builder
						->keyText('like_cnt','点赞数')
						->keyText('fav_cnt','收藏数')
						->keyText('comment_cnt','评论数')
						->keyText('click_cnt','点击数')
						->keyText('sell_cnt','总销量')
						->keyText('score_cnt','评分次数')
						->keyText('score_total','总评分');
				}

				$builder->keyDoAction('admin/muushop/product/action/add/id/###','编辑')
						->keyDoAction('admin/muushop/product/action/sku_table/id/###','规格')
						->data($product)
						->pagination($totalCount, 20)
						->display();
			break;
		}
	}

	/*
	 *  订单相关
	 */
	public function order($action= '')
	{
		switch($action)
		{
			case 'delete':
				$ids = I('ids');
				$ret = $this->order_logic->delete_order($ids);
				if($ret){
					$this->success('删除成功');
				}else{
					$this->error('删除失败，'.$this->order_logic->error_str,'',3);
				}
			break;
			case 'order_delivery'://发货信息
					
					$id = I('id');
					empty($id) && $this->error('订单参数错误',1);
					$order = $this->order_model->get_order_by_id($id);
					$delivery_info = json_decode($order['delivery_info'],true);
					$delivery_info['id'] = $order['id'];
					$order['send_time'] = (empty($order['send_time'])?'未发货':date('Y-m-d H:i:s',$order['send_time']));
					$order['recv_time'] = (empty($order['recv_time'])?'未收货':date('Y-m-d H:i:s',$order['recv_time']));
					$delivery_info['send_time'] = $order['send_time'];
					$delivery_info['recv_time'] = $order['recv_time'];
					$delivery_info['order_no'] = $order['order_no'];
					//组装获取物流信息的json数据
					$requesData=array(
						'OrderCode'=>$order['order_no'],
						'ShipperCode'=>$delivery_info['ShipperCode'],
						'LogisticCode'=>$delivery_info['LogisticCode']
					);
					$requesData=json_encode($requesData);//转成json
					//获取物流信息
					$result = D('Muushop/MuushopDeliveryInfo')->getOrderTracesByJson($requesData);
					$result = json_decode($result,true);
					$result['Traces'] = array_reverse($result['Traces']);//反转数组


					$this->assign('delivery_info',$delivery_info);
					$this->assign('result',$result);
					$this->display('Muushop@Public/order_delivery');
					
				break;
			case 'order_address':
				$id = I('id');
				$order = $this->order_model->get_order_by_id($id);
				$address = is_array($order['address'])?$order['address']:json_decode($order['address'],true);
				$info  = is_array($order['info'])?$order['info']:json_decode($order['info'],true);

				foreach($info as $ik=>$iv)
				{
					$infos['info_'.$ik] = $iv;
				}

				$builder = new AdminConfigBuilder();
				$builder
					->title('地址等信息')
					->keyReadOnly('id','订单id')
					->keyJoin('user_id','用户','uid','nickname','member','/admin/user/index')
					->keyText('name','姓名')
					->keyText('phone','手机')
					->keyMultiInput('province|city|town','地址','省|市|区',array(
						array('type'=>'text','style'=>'width:95px;margin-right:5px'),
						array('type'=>'text','style'=>'width:95px;margin-right:5px'),
						array('type'=>'text','style'=>'width:95px;margin-right:5px'),
					))
					->keyText('address','详细地址')
					->keyText('info_remark','备注')
					->keyText('info_fapiao','发票抬头');
				//其他信息 滚出
				foreach($infos as $ik=>$iv)
				{
					if(in_array($ik,array('info_remark','info_fapiao')))
						continue;
					$builder->keyText($ik,$ik);
				}
				$address = is_array($address)?$address:array();
				$builder
					->buttonBack()
					->data(array_merge($address,$infos))
					->display();
				break;
			case 'order_detail'://订单详情
				$id = I('id');
				$order = $this->order_model->get_order_by_id($id);
				$order['create_time'] =(empty($order['create_time'])?'':date('Y-m-d H:i:s',$order['create_time']));
				$order['paid_time'] =(empty($order['paid_time'])?'未支付':date('Y-m-d H:i:s',$order['paid_time']));
				$order['send_time'] = (empty($order['send_time'])?'未发货':date('Y-m-d H:i:s',$order['send_time']));
				$order['recv_time'] = (empty($order['recv_time'])?'未收货':date('Y-m-d H:i:s',$order['recv_time']));

				$order['user_info'] = query_user('nickname',$order['user_id']);

				$order['address']["province"] = D('district')->where(array('id' => $order['address']["province"]))->getField('name');
			    $order['address']["city"] = D('district')->where(array('id' => $order['address']["city"]))->getField('name');
			    $order['address']["district"] = D('district')->where(array('id' => $order['address']["district"]))->getField('name');

			    //设置支付类型
			    switch ($order['pay_type']){
			    	case 'balance':
			    		$order['pay_type_cn']="余额支付";
			    	break;
			    	case 'delivery':
			    		$order['pay_type_cn']="货到付款";
			    	break;
			    	case 'onlinepay':
			    		$order['pay_type_cn']="在线支付";
			    	break;
			    	default:
			    		$order['pay_type_cn']="未设置";
			    }
			    
				$order['paid_fee']='¥ '.sprintf("%01.2f", $order['paid_fee']/100);
				$order['delivery_fee']='¥ '.sprintf("%01.2f", $order['delivery_fee']/100);
				$order['discount_fee']='- ¥ '.sprintf("%01.2f", $order['discount_fee']/100);

				if(!empty($order['products'])){
					foreach($order['products'] as &$val){
						//商品列表价格单位转为元
						$val['paid_price']='¥ '.sprintf("%01.2f", $val['paid_price']/100);
						//sku_id转为数组
						$val['sku'] = explode(';',$val['sku_id']);
						unset($val['sku'][0]);
					}
				}
				unset($val);
				//dump($order);exit;
				$this->setTitle('订单详情');
				$this->assign('order',$order);
				$this->display('Muushop@Admin/order_detail');
			break;
			case 'edit_order_modal':
				if(IS_POST)
				{
					$order_id = I('order_id','','intval');
					$status = I('status','','intval');
					$order = $this->order_model->get_order_by_id($order_id);
					if(empty($order_id) || empty($status) || !($order)){
						$this->error('参数错误');
					}else{
						switch ($status){
							case '1':
								//取消订单
								$ret = $this->order_logic->cancal_order($order);
								if($ret){
									$this->success('操作成功',U('Muushop/order'));
								}else{
									$this->error('操作失败,'.$this->order_logic->error_str);
								}
								break;
							case '2':
								if(IS_POST){
									//发货
									$ShipperValue = I('ShipperValue');//快递公司名称及编号，以,分隔
									$LogisticCode = I('LogisticCode');//物流单号

									$ShipperValue = explode(',',$ShipperValue);
									
									$delivery_info = array(
										'ShipperName' =>$ShipperValue[0],
										'ShipperCode'=>$ShipperValue[1],
										'LogisticCode'=>$LogisticCode,
									);
									$ret = $this->order_logic->send_good($order,$delivery_info);
									if($ret){
										$this->success('操作成功',U('Muushop/order'));
									}else{
										$this->error('操作失败,'.$this->order_logic->error_str);
									}
								}
								
								break;
							case '3':
								//确认收货
								$ret = $this->order_logic->recv_goods($order);
								if($ret){
									$this->success('操作成功',U('Muushop/order'));
								}else{
									$this->error('操作失败,'.$this->order_logic->error_str);
								}
								break;
							case '10':
								//删除订单
								$ret = $this->order_logic->delete_order($order['id']);
								if($ret){
									$this->success('操作成功',U('Muushop/order'));
								}else{
									$this->error('操作失败,'.$this->order_logic->error_str);
								}
								break;
						}
					}
				}else{
					$id = I('id');//获取点击的ids
					$order = $this->order_model->get_order_by_id($id);
					$this->assign('order', $order);
					$path = APP_PATH  . 'Muushop/Conf/delivery.php';
        			$delivery = load_config($path);
        			$this->assign('delivery',$delivery);
					$this->display('Muushop@Admin/edit_order_modal');
				}
				break;

			default:
				$option['page'] = I('page',1);
				$option['r'] = I('r',20);
				$option['user_id'] = I('user_id');
				$option['status'] = I('status');
				$option['key'] = I('key');
				$option['ids'] = I('id');
				empty($option['ids']) || $option['ids'] = array($option['ids']);
				$option['show_type'] = I('show_type','','intval');
				$order = $this->order_model->get_order_list($option);

				foreach($order['list'] as &$val){
					$val['paid_fee']='¥ '.sprintf("%01.2f", $val['paid_fee']/100);
					$val['delivery_fee']='¥ '.sprintf("%01.2f", $val['delivery_fee']/100);
					$val['discount_fee']='- ¥ '.sprintf("%01.2f", $val['discount_fee']/100);
				}
				unset($val);
				//支付方式
				$payment = D('Muushop/MuushopPay')->getPayment();

				$status_select = $this->order_model->get_order_status_config_select();
				$status_select2 = $this->order_model->get_order_status_list_select();
				$show_type_array = array(array('id'=>0,'value'=>'订单信息'),array('id'=>1,'value'=>'订单状态'));
				$totalCount = $order['totalCount'];
				$builder = new AdminListBuilder();
				$builder
					->title('订单管理')
					->setSearchPostUrl(U('muushop/order'))
					->search('', 'id', 'text', '订单id', '', '', '')
					->search('', 'key', 'text', '商品名', '', '', '')
					->select('订单状态: ', 'status', 'select', '', '', '', $status_select2)
					->select('显示模式: ', 'show_type', 'select', '', '', '', $show_type_array)

					->keyText('id','订单id')
					->keyText('order_no','订单号')
					//->keyJoin('user_id','用户','uid','nickname','member','/admin/user/index')
					->keyUid('user_id','用户');

				$option['show_type'] && $builder
					->keyTime('create_time','下单时间')
					->keyTime('paid_time','支付时间')
					->keyTime('send_time','发货时间')
					->keyTime('recv_time','收货时间');

				$option['show_type'] || $builder
					->keyMap('pay_type','支付方式',$payment)
					->keyText('paid_fee','总价/元')
					->keyText('discount_fee','已优惠的价格')
					->keyText('delivery_fee','邮费')
					->keyMap('status','订单状态',$status_select);

				$builder
					->keyDoAction('admin/muushop/order/action/order_detail/id/###','详情')
					->keyDoActionModalPopup('admin/muushop/order/action/order_delivery/id/###','物流','',array('data-title'=>'物流查询'))
					->keyDoActionModalPopup('admin/muushop/order/action/edit_order_modal/id/###','操作');
				$builder
					->data($order['list'])
					->pagination($totalCount, $option['r'])
					->display();
			break;
		}
	}

	/*
	 * 运费模板
	 */
	public function delivery($action = '')
	{
		switch($action)
		{
			case 'add':
				if(IS_POST)
				{	
					$delivery = $this->delivery_model->create();
					$delivery['rule'] = json_decode($delivery['rule'],true);
					if($delivery['valuation']==0){
						foreach($delivery['rule'] as &$val){
							$val['cost']=$val['cost']*100;
						}
						unset($val);
					}else{
						foreach($delivery['rule'] as &$val){
								$val['normal']['start_fee']=$val['normal']['start_fee']*100;
								$val['normal']['add_fee']=$val['normal']['add_fee']*100;
							foreach($val["custom"] as &$c){
								$c['cost']['start_fee']=$c['cost']['start_fee']*100;
								$c['cost']['add_fee']=$c['cost']['add_fee']*100;
							}
							unset($c);
						}
						unset($val);
					}
					$delivery['rule'] = json_encode($delivery['rule']);
					if (!$delivery){
						$this->error($this->delivery_model->getError());
					}
					$ret = $this->delivery_model->add_or_edit_delivery($delivery);
					if ($ret){
						$this->success('操作成功。', U('muushop/delivery'),1);
					}else{
						$this->error('操作失败。');
					}
				}else{
					$id = I('get.id',0,'intval');
					if(!empty($id)){
						$delivery = $this->delivery_model->get_delivery_by_id($id);
						if($delivery['valuation']==0){
							foreach($delivery['rule'] as &$val){
								$val['cost']=$val['cost']/100;
							}
							unset($val);
						}else{
							foreach($delivery['rule'] as &$val){
									$val['normal']['start_fee']=sprintf("%01.2f",$val['normal']['start_fee']/100);
									$val['normal']['add_fee']=sprintf("%01.2f",$val['normal']['add_fee']/100);
								foreach($val["custom"] as &$c){
									$c['cost']['start_fee']=sprintf("%01.2f",$c['cost']['start_fee']/100);
									$c['cost']['add_fee']=sprintf("%01.2f",$c['cost']['add_fee']/100);
								}
								unset($c);
							}
							unset($val);
						}
					}else{
						$delivery = array();
					}
					//获取中国省份列表
					$district = $this->District(1);

					$this->meta_title = '运费模板编辑';
					$this->assign('district',$district);
					$this->assign('delivery',$delivery);
					$this->display('Muushop@Admin/adddelivery');exit;
				}
				break;
			case 'delete':
				$ids = I('ids');
				$ret = $this->delivery_model->delete_delivery($ids);
				if ($ret){

					$this->success('操作成功。', U('muushop/delivery'));
				}else{
					$this->error('操作失败。');
				}
				break;
			default:
				$option['page'] = I('page',1);
				$option['r'] = I('r',10);
				$delivery = $this->delivery_model->get_delivery_list($option);
				$totalCount = $delivery['count'];

				$builder = new AdminListBuilder();
				$builder
					->title('运费模板管理')
					->buttonnew(U('Muushop/Delivery',array('action'=>'add')),'新增运费模板')
					->ajaxButton(U('Muushop/Delivery',array('action'=>'delete')),'','删除')
					->keyText('id','id')
					->keyText('title','标题')
					->keyText('brief','模板说明')
					->keyTime('create_time','创建时间')
					->keyDoAction('admin/muushop/delivery/action/add/id/###','编辑')
					->data($delivery['list'])
					->pagination($totalCount, $option['r'])
					->display();
				break;
		}
	}
	/*
	 * 优惠券
	 */
	public function coupon($action = '')
	{
		switch($action)
		{
			case 'add':
				if(IS_POST){
					$coupon = $this->coupon_model->create();
					if(!$coupon){
						$this->error($this->coupon_model->getError());
					}
					empty($_REQUEST['max_cnt_enable']) || $rule['max_cnt'] =I('max_cnt',0,'intval');
					empty($_REQUEST['max_cnt_day_enable']) || $rule['max_cnt_day'] =I('max_cnt_day',0,'intval');
					empty($_REQUEST['min_price_enable']) || $rule['min_price'] =I('min_price',0,'intval');
					if(empty($_REQUEST['discount'])){
						$this->error('请设置优惠金额');
					}else{
						$rule['discount'] =I('discount',0,'intval');
					}
					empty($rule) || $coupon['rule'] = json_encode($rule);

					$ret = $this->coupon_model->add_or_edit_coupon($coupon);
					if ($ret){
						$this->success('操作成功。', U('muushop/coupon'));
					}else{
						$this->error('操作失败。');
					}
				}else{
					$id = I('id');
					if(!empty($id)){
						$coupon = $this->coupon_model->get_coupon_by_id($id);
						if(!empty($coupon['rule'])){
							$coupon['rule']['max_cnt_enable'] = (empty($coupon['rule']['max_cnt'])?0:1);
							$coupon['rule']['max_cnt_day_enable'] = (empty($coupon['rule']['max_cnt_day'])?0:1);
							$coupon['rule']['min_price_enable'] = (empty($coupon['rule']['min_price'])?0:1);
							$coupon = array_merge($coupon,$coupon['rule']);
						}
					}else{
						$coupon =array();
					}
					
					$builder = new AdminConfigBuilder();
					$builder
						->title('优惠券详情')
						->keyId()
						->keytext('title','优惠券名称')
						->keySingleImage('img','优惠券图片')
						->keyInteger('publish_cnt','总发放数量')
						->keyInteger('discount','优惠金额','单位：分')
						->keySelect('duration','有效期','',array('0'=>'永久有效','86400'=>'一天内有效','604800'=>'一周内有效','2592000'=>'一月内有效'))
						->keyMultiInput('max_cnt_enable|max_cnt','领取限制','每个用户最多允许领取多少张',array(array('type'=>'select','opt'=>array('不限制','限制'),'style'=>'width:95px;margin-right:5px'),array('type'=>'text','style'=>'width:95px;margin-right:5px')))
						->keyMultiInput('max_cnt_day_enable|max_cnt_day','领取限制','每个用户每天最多允许领取多少张',array(array('type'=>'select','opt'=>array('不限制','限制'),'style'=>'width:95px;margin-right:5px'),array('type'=>'text','style'=>'width:95px;margin-right:5px')))
						->keyMultiInput('min_price_enable|min_price','使用限制','最低可以使用的价格（单位：分），即满多少可用',array(array('type'=>'select','opt'=>array('不限制','限制'),'style'=>'width:95px;margin-right:5px'),array('type'=>'text','style'=>'width:95px;margin-right:5px')))
						->keySelect('valuation','类型','',array('现金券','折扣券'))
						->keyEditor('brief','优惠券说明')
						->keyCreateTime()
						->data($coupon)
						->buttonSubmit(U('muushop/coupon',array('action'=>'add')))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':
				$ids= I('ids');
				$ret = $this->coupon_model->delete_coupon($ids);
				if ($ret){
					$this->success('操作成功。', U('muushop/coupon'));
				}else{
					$this->error('操作失败。');
				}
				break;
			case 'couponlink':
				$id = I('id');
				$id = $this->coupon_model->encrypt_id($id);
				redirect(U('Udriver/index/get_coupon',array('id'=>$id)));//优惠券id 加密 跳转 具体链接 依业务需求修改
				break;
			default:
				$option['page'] = I('page',1);
				$option['r'] = I('r',10);
				$option['id'] = I('id');
				$coupon = $this->coupon_model->get_coupon_lsit($option);
				$totalCount = $coupon['count'];
				$builder = new AdminListBuilder();
				$builder
					->title('优惠券')
					->buttonnew(U('muushop/coupon',array('action'=>'add')),'新增优惠券')
					->ajaxButton(U('muushop/coupon',array('action'=>'delete')),'','删除')
					->keyText('id','优惠券id')
					->keyText('title','优惠券名称')
					->keyImage('img','优惠券图片')
					->keyMap('valuation','类型',array('现金券','折扣券'))
					->keyTruncText('brief','优惠券说明','25')
					->keyText('used_cnt','已发放数量')
					->keyText('publish_cnt','总发放数量')
					->keyTime('create_time','创建时间')
					->keyLinkByFlag('','领取链接','/muushop/coupon/get_coupon/coupon_id/###','id')
					->keyMap('duration','有效期',array('0'=>'永久有效','86400'=>'一天内有效','604800'=>'一周内有效','2592000'=>'一月内有效'))
					->keyDoAction('admin/muushop/coupon/action/add/id/###','查看和编辑')
					->data($coupon['list'])
					->pagination($totalCount, $option['r'])
					->display();
				break;
		}
	}

	/*
	 * 优惠券领取情况
	 */
	public function user_coupon($action = '')
	{
		switch($action)
		{

			case 'add':
				//派优惠券
				if(IS_POST){
					$coupon_id       = I('coupon_id', '', 'intval');
					$uid     = I('uid', '', 'trim');
					if(empty($coupon_id) || !($coupon = $this->coupon_model->get_coupon_by_id($coupon_id)))
						$this->error('请选择一个优惠券');
					if(empty($uid)) $this->error('请选择一个用户');
					$ret =$this->coupon_logic->add_a_coupon_to_user($coupon_id,$uid);
					if($ret){
						$this->success('操作成功。', U('muushop/user_coupon'));
					}else{
						$this->error('操作失败。'.$this->coupon_logic->error_str);
					}
				}else{
					$all_coupon_select = $this->coupon_model->getfield('id,title');
					if(empty($all_coupon_select)){
						redirect(U('muushop/coupon',array('action'=>'add')));
					}
					$builder = new AdminConfigBuilder();
					$builder
						->title('手动发放优惠券')
						->keySelect('coupon_id','优惠券','要发放的优惠券',$all_coupon_select)
						->keyInteger('uid','用户id','')

						->buttonSubmit(U('muushop/user_coupon',array('action'=>'add')))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':
				$ids= I('ids');
				$ret = $this->user_coupon_model->delete_user_coupon($ids);
				if ($ret){
					$this->success('操作成功。', U('muushop/user_coupon'));
				}else{
					$this->error('操作失败。');
				}
				break;
			default:
				$option['id'] = I('id');
				$option['page'] = I('page',1);
				$option['r'] = I('r',10);
				$user_coupon = $this->user_coupon_model->get_user_coupon_list($option);

				empty($user_coupon['list']) ||
				array_walk($user_coupon['list'],
					function(&$a){
						$a['coupon_title'] = (empty($a['info']['title'])?'':$a['info']['title']);
						$a['coupon_img'] = (empty($a['info']['img'])?'':$a['info']['img']);
						$a['coupon_valuation'] = (empty($a['info']['valuation'])?'':$a['info']['valuation']);
						$a['coupon_discount'] = (empty($a['info']['rule']['discount'])?'':$a['info']['rule']['discount']);
						$a['coupon_min_price'] = (empty($a['info']['rule']['min_price'])?'':$a['info']['rule']['min_price']);
					});
				$totalCount = $user_coupon['count'];

				$builder = new AdminListBuilder();
				$builder
					->title('已领取优惠券')
					->buttonnew(U('muushop/user_coupon',array('action'=>'add')),'派发优惠券')
					->ajaxButton(U('muushop/user_coupon',array('action'=>'delete')),'','删除')
					->keyId()
					->keyUid('user_id')
					->keyLinkByFlag('coupon_title','优惠券','admin/muushop/coupon/id/###','coupon_id')
					->keyImage('coupon_img','优惠券图片')
					->keytext('coupon_discount','折扣,单位:分')
					->keytext('coupon_min_price','满多少可用,单位:分')
					->keyTime('create_time','发放时间')
					->keyTime('expire_time','到期时间')
					->keyLinkByFlag('order_id','订单号（无）','admin/shop/order/key/###','order_id')
					->keyMap('status','状态',array('0'=>'未使用','1'=>'已使用','2'=>'已过期'))
					->data($user_coupon['list'])
					->pagination($totalCount, $option['r'])
					->display();
				break;
		}
	}


	/*
	 *商品评论
	 */
	public function product_comment($action ='')
	{
		switch($action)
		{
			case 'edit_status':
				if(IS_POST)
				{
					$ids  =  I('ids');
					$status  =  I('get.status','','/[012]/');
					if(empty($ids) || empty($status)){
						$this->error('参数错误');
					}
					$ret = $this->product_comment_model->edit_status_product_comment($ids,$status);
					if($ret){
						$this->success('操作成功');
					}else{
						$this->error('操作失败');
					}
				}
				break;
			case 'show_pic':
				$id = I('id','','intval');
				$ret = $this->product_comment_model->find($id);
				$this->assign('product_comment',$ret);
				$this->display('Muushop@Admin/show_pic');
				break;
			default:
				$option['page'] = I('page','1','intval');
				$option['r'] = I('r','10','intval');
				$product_comment  = $this->product_comment_model->get_product_comment_list($option);
				$builder = new AdminListBuilder();
				$builder
					->title('商品评论管理')
					->ajaxButton(U('shop/product_comment',array('action'=>'edit_status','status'=>1)),'','审核通过')
					->ajaxButton(U('shop/product_comment',array('action'=>'edit_status','status'=>2)),'','审核不通过')
					->keyId()
					->keyJoin('product_id','商品','id','title','muushop_product','/admin/muushop/product')
					->keyJoin('order_id','订单','id','id','muushop_order','/admin/muushop/order')
					->keyJoin('user_id','用户','uid','nickname','member','/admin/user/index')
					->keyText('score','星数')
					->keyText('brief','评论内容')
					->keyTime('create_time','评论时间')
					->keyMap('status','状态',array('0'=>'未审核','1'=>'已通过','2'=>'未通过'))
					->data($product_comment['list'])
					->pagination($product_comment['totalCount'], $option['r'])
					->display();
				break;
		}

	}
	/*
	获取中国省份、城市
	 */
	private function District($level=1){
			$map['level'] = $level;
			$map['upid'] = 0;
			$list = D('Addons://ChinaCity/District')->_list($map);
			return $list;
	}

}
