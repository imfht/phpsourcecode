<?php
namespace app\muushop\controller;

use think\Db;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminTreeListBuilder;
use app\admin\builder\AdminSortBuilder;
use app\common\model\ContentHandlerModel;
use app\admin\controller\Admin as MuuAdmin;

class Admin extends MuuAdmin
{
    protected $product_cats_model;
    protected $product_model;
    protected $order_model;
    protected $delivery_model;
    protected $coupon_model;
    protected $user_coupon_model;
    protected $address_model;
	protected $order_logic;
	protected $coupon_logic;
	protected $service_model;
	protected $pay_model;

    function _initialize()
    {
        $this->product_cats_model     = model('muushop/MuushopProductCats');
	    $this->product_model          = model('muushop/MuushopProduct');
	    $this->order_model 			  = model('muushop/MuushopOrder');
	    $this->delivery_model         = model('muushop/MuushopDelivery');
	    $this->coupon_model           = model('muushop/MuushopCoupon');
	    $this->user_coupon_model      = model('muushop/MuushopUserCoupon');
	    $this->order_logic            = model('muushop/MuushopOrder','logic');
	    $this->coupon_logic           = model('muushop/MuushopCoupon','logic');
	    $this->address_model          = model('muushop/MuushopUserAddress');
	    $this->service_model          = model('muushop/MuushopService');
	    $this->pay_model              = model('muushop/MuushopPay');

        parent::_initialize();
    }


	public function index()
	{
		if(!modC('MUUSHOP_SHOW_TITLE', '', 'Muushop'))
		{
			//未配置商城跳转
			redirect(url('muushop/admin/config'));
		}
		else
		{
			redirect(url('muushop/admin/product'));
		}
	}
	/**
	 * 系统基础配置
	 */
	public function config()
	{
		$builder = new AdminConfigBuilder();
		$data = $builder->handleConfig();
		//$template = scandir(APP_PATH . 'muushop/view');
		$template = [];
		foreach(glob(APP_PATH . 'muushop/view/*') as $vv)
		{
			if(is_dir($vv) && basename($vv)!='admin' && basename($vv)!='widget'){
				$template[basename($vv)] = basename($vv);
			}
		}
		
		$builder
			->title('基本设置')
			->data($data)
			//基础配置
			->keyText('MUUSHOP_SHOW_TITLE', '商城名称', '在首页的商场名称')->keyDefault('MUUSHOP_SHOW_TITLE','MuuCmf轻量级商场解决方案')
			->keySingleImage('MUUSHOP_SHOW_LOGO','商场logo')
			->keyBool('MUUSHOP_SHOW_STATUS', '商城状态','默认正常')
			->keySelect('MUUSHOP_SHOW_PC_TEMPLATE','PC端模板','默认muushop',$template)
			->keySelect('MUUSHOP_SHOW_MOBILE_TEMPLATE','移动端模板','默认muushop',$template)
			->keyEditor('MUUSHOP_SHOW_DESC', '商城简介','','all',array('width' => '800px', 'height' => '200px'))
			->group('基本配置', 'MUUSHOP_SHOW_TITLE,MUUSHOP_SHOW_LOGO,MUUSHOP_SHOP_STATUS,MUUSHOP_SHOW_PC_TEMPLATE,MUUSHOP_SHOW_MOBILE_TEMPLATE,MUUSHOP_SHOW_DESC,');

            //支付设置
            //获取所有支付方式
        	$able_payment = model('muushop/MuushopPay')->getPaytype();
        $builder
            ->keyCheckBox('MUUSHOP_PAYMENT','允许的支付方式','',$able_payment)
            
            ->group('支付设置','MUUSHOP_PAYMENT');
            //积分设置
            //启用的积分类型
			$score_list = model('ucenter/Score')->getTypeList(['status' => 1]);
	        $score_type=[];
	        foreach($score_list as $val){
	            $score_type=array_merge($score_type,['score'.$val['id']=>$val['title']]);
	        }
	    $builder
            ->keyRadio('MUUSHOP_SCORE_TYPE','积分类型','抵用现金或返还的积分类型',$score_type)
            //积分兑换比例   
            ->keyText('MUUSHOP_SCORE_PROP','积分兑换比例','如输入100，即100所选积分数=1元RMB')
            ->group('积分设置','MUUSHOP_SCORE_TYPE,MUUSHOP_SCORE_PROP');
			
		//物流API配置
		//获取物流查询插件
		$expressAddon = \think\Hook::get('express');
		$opt = ['none' => lang('_NONE_')];
		foreach ($expressAddon as $name) {
            if (class_exists($name)) {
            	$class= new $name;
                $opt[$class->info['name']] = $class->info['title'];
            }
        }
        $builder
        	->keySelect('MUUSHOP_EXPRESS_ADDON','物流插件配置','',$opt)
        	->group('物流查询配置','MUUSHOP_EXPRESS_ADDON');
        //评价晒图配置
        $builder
        	->keySelect('MUUSHOP_COMMENT_ADDON_ABLE','是否启用','需安装评价晒图插件',[0=>'禁用',1=>'启用'])
        	->group('评价晒图配置','MUUSHOP_COMMENT_ADDON_ABLE');
       //售后保障
        $builder
			->keyEditor('MUUSHOP_SHOW_SERVICE', '售后保障','','all',array('width' => '800px', 'height' => '500px'))
			->group('售后保障','MUUSHOP_SHOW_SERVICE');
       //确认按钮
        $builder
			->buttonSubmit('', '保存配置')
			->display();
	}
	/**
	 * @param  自定义导航
	 * @return [type]
	 */
	public function custom_nav($action='') {

        if (request()->isPost()) {
            $one = $_POST['nav'][1];
            if (count($one) > 0) {
                Db::execute('TRUNCATE TABLE ' . config('database.prefix') . 'muushop_nav');
                for ($i = 0; $i < count(reset($one)); $i++) {
                    $data[$i] = [
                        'pid' => 0,
                        'title' => text($one['title'][$i]),
                        'url' => text($one['url'][$i]),
                        'sort' => intval($one['sort'][$i]),
                        'target' => intval($one['target'][$i]),
                        'color' => text($one['color'][$i]),
                        'band_text' => text($one['band_text'][$i]),
                        'band_color' => text($one['band_color'][$i]),
                        'status' => 1

                    ];
                    $pid[$i] = Db::name('muushop_nav')->insert($data[$i]);
                }
                if(isset($_POST['nav'][2])) {
                	$two = $_POST['nav'][2];
	                for ($j = 0; $j < count(reset($two)); $j++) {
	                    $data_two[$j] = [
	                        'pid' => $pid[$two['pid'][$j]],
	                        'title' => text($two['title'][$j]),
	                        'url' => text($two['url'][$j]),
	                        'sort' => intval($two['sort'][$j]),
	                        'target' => intval($two['target'][$j]),
	                        'color' => text($two['color'][$j]),
	                        'band_text' => text($two['band_text'][$j]),
	                        'band_color' => text($two['band_color'][$j]),
	                        'status' => 1
	                    ];
	                    $res[$j] = Db::name('muushop_nav')->insert($data_two[$j]);
	                }
                }
                cache('muushop_custom_nav',null);
               
                $this->success(lang('_CHANGE_'));
            }
            $this->error(lang('_NAVIGATION_AT_LEAST_ONE_'));

        } else {
            /* 获取自定义导航列表 */
            $map = ['status' => 1, 'pid' => 0];
            $list = Db::name('muushop_nav')->where($map)->order('sort asc,id asc')->select();
            foreach ($list as $k => &$v) {
                $cats = model('muushopProductCats')->where(['id' => $v['url']])->find();
                $v['cats_title'] = $cats['title'];
                
                $child = Db::name('muushop_nav')->where(['pid' => $v['id']])->order('sort asc,id asc')->select();
                foreach ($child as $key => &$val) {
                    $child_cats = model('muushopProductCats')->where(['id' => $val['url']])->find();
                    $val['cats_title'] = $child_cats['title'];
                }
                unset($key, $val);
                $child && $v['child'] = $child;
            }
            unset($k, $v);
            //获取分类列表
            $cats = $this->product_cats_model->getList(['parent_id'=>0,'status'=>1],'sort asc','*');
            $this->assign('cats', $cats);
            $this->assign('list', $list);

            return $this->fetch('admin/custom_nav');
        }
	}
	
	/*
	 * 商品分类
	 */
	public function product_cats($action='',$page=1,$r=20)
	{
		switch($action){
			case 'add':
				if(request()->isPost()){
					$input = input('post.');

					if($input['parent_id'] == $input['id']) {
						$this->error('不要选择自己分类或自己的子分类');
					}
					$res = $this->product_cats_model->editData($input);

					if ($res){
						$this->success('操作成功。', url('product_cats',['parent_id'=>input('parent_id',0)]));
					}else{
						$this->error('操作失败。');
					}
				}else{
					$id = input('id',0,'intval');
					$parent_id = input('parent_id',0,'intval');

					$data = '';
					if ($id != 0) {
		                $data = $this->product_cats_model->find($id);
		            }
					$parent_cats = $this->product_cats_model->getListForConfig();

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
						
						->buttonSubmit(url('product_cats',['action'=>'add']))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':
				$ids = input('ids');
				$ret = $this->product_cats_model->deleteData($ids);
				if ($ret){

					$this->success('操作成功。', url('Muushop/product_cats'));
				}else{
					$this->error('操作失败。');
				}
				break;

			case 'sort':

		        if (request()->isPost) {
		        	$ids = input('post.ids');
		            $builder = new AdminSortBuilder;
		            $builder->doSort('muushop_product_cats', $ids);
		        } else {

		        	$map['parent_id'] = input('get.parent_id',0,'intval');
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

				$map['parent_id'] = input('parent_id',0,'intval');
				if(!empty($map['parent_id'])){
					$parent_cat  = $this->product_cats_model->getDataById($map['parent_id']);
				}
				
				$list = $this->product_cats_model->getListByPage($map);
				$select = $this->product_cats_model->getListForSelect();
				
				$builder = new AdminListBuilder();
				$builder
					->title((empty($parent_cat)?'顶级的':$parent_cat['title'].' 的子').'商品分类')
					->setSelectPostUrl(url('product_cats'))
					->select('分类查看：', 'parent_id', 'select', '', '', '', $select);

				//顶级分类列表不显示上级分类按钮
				if($map['parent_id']){
					$builder
					->buttonNew(url('product_cats',['parent_id'=>$parent_cat['parent_id']]),'返回上级分类');
				}
				$builder
				->buttonNew(url('product_cats',['action'=>'add','parent_id'=>$map['parent_id']]),'新增分类')
				->buttonDelete(url('product_cats',['action'=>'delete']),'删除')
				->buttonSort(url('product_cats',['action'=>'sort','parent_id'=>$map['parent_id']]))
				->keyText('id','id')
				->keyText('title','标题')
				->keyText('title_en','英文标题')
				->keyImage('image','ICON')
				->keyText('sort','排序')
				->keyTime('create_time','创建时间')
				->keyStatus('status','状态')
				->keyDoActionEdit('product_cats?action=add&id=###&parent_id='.$map['parent_id'],'编辑')
				->keyDoAction('product_cats?parent_id=###','查看下级分类')
				->data($list)
				//->pagination($totalCount, $r)
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
		$default_position=<<<str
1:热销
2:推荐
4:新品
str;
		$position_options=$this->_getPositions();
		//dump($position_options);
		$builder
			->title('商品设置')
			->data($data)
			//商品配置
			->keyTextArea('MUUSHOP_POS_POSITION','展示位配置','每行一条，格式：key:value')
			->keyDefault('MUUSHOP_POS_POSITION',$default_position)
			->keyInteger('MUUSHOP_LIST_NUM','商品列表每页显示数')
			->keyInteger('MUUSHOP_COMMENT_LIST_NUM','商品评价每页显示数');
		//已设置展示位配置
		foreach($position_options as $k=>$v){
		$builder->keyText('MUUSHOP_POS_'.$k.'_TITLE', '标题名称', '展示块的标题')->keyDefault('MUUSHOP_POS_'.$k.'_TITLE',$v.'商品')
            ->keyText('MUUSHOP_POS_'.$k.'DESCRIPTION', '简短描述', '精简的描述模块内容')->keyDefault('MUUSHOP_POS_'.$k.'DESCRIPTION',$v.'展示块简单描述')
            ->keyText('MUUSHOP_POS_'.$k.'COUNT', '显示商品的个数', '只有在启用了展示块之后才会显示')->keyDefault('MUUSHOP_POS_'.$k.'COUNT',4)

            ->keyRadio('MUUSHOP_POS_'.$k.'ORDER_FIELD', '排序值', '展示块的数据排序字段', array('click_cnt' => '点击数','sell_cnt' => '总销量','create_time' => '上架时间', 'modify_time' => '更新时间'))->keyDefault('MUUSHOP_POS_'.$k.'ORDER_FIELD','click_cnt')

            ->keyRadio('MUUSHOP_POS_'.$k.'ORDER_TYPE', '排序方式', '展示块的数据排序方式', array('desc' => '倒序，从大到小', 'asc' => '正序，从小到大'))->keyDefault('MUUSHOP_POS_'.$k.'ORDER_TYPE','desc')
            ->keyText('MUUSHOP_POS_'.$k.'CACHE_TIME', '缓存时间', '默认600秒，以秒为单位')->keyDefault('MUUSHOP_POS_'.$k.'CACHE_TIME','600');
        }
        
		$builder->group('商品配置', 'MUUSHOP_POS_POSITION,MUUSHOP_LIST_NUM,MUUSHOP_COMMENT_LIST_NUM');
			
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
        $positons=modC('MUUSHOP_POS_POSITION',$default_position,'muushop');
        $positons = str_replace("\r", '', $positons);
        $positons = explode("\n", $positons);
        
        foreach($positons as $k=>$v){
        	if(empty($v)) {
        		unset($positons[$k]);
        	}
        }
        
        $result=[];
        if($type){
            foreach ($positons as $v) {
                $temp = explode(':', $v);
                $result[] = ['id'=>$temp[0],'value'=>$temp[1]];
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
				if(request()->isPost()){

					$product = input('post.');
					$product['price'] = sprintf("%.2f",$product['price']*100);
					$product['ori_price'] = sprintf("%.2f",$product['ori_price']*100);

					$ret = $this->product_model->editData($product);
					if ($ret){
						$this->success('操作成功。', url('product'));
					}else{
						$this->error('操作失败。');
					}
				}else{
					
					$id = input('id');
					if(!empty($id)){
						$product = $this->product_model->getDataById($id);
						$product['price'] = sprintf("%.2f",$product['price']/100);
						$product['ori_price'] = sprintf("%.2f",$product['ori_price']/100);
					}else{
						$product = [];
					}

					$select = $this->product_cats_model->getListForConfig('选择分类');
					if(count($select)==1){
						$this->error('先添加一个商品分类吧',url('muushop/product_cats',['action'=>'add']),2);
					}
					$delivery_select = $this->delivery_model->column('id,title');
					//商品展示位
					$position_options=$this->_getPositions();

					$builder = new AdminConfigBuilder();
					//注释的暂不支持
					$builder
						->title('新增/修改商品')
						->keyId()
						->keyText('title', '商品名称')
						->keyTextArea('description','简单描述')
						->keySingleImage('main_img','商品主图')
						->keyMultiImage('images','商品图片,多张图片')
						->keySelect('cat_id','商品分类','',$select)
						->keyInteger('price', '价格/元','交易价格')
						->keyInteger('ori_price', '原价/元','显示被划掉价格')
						->keyInteger('quantity', '库存')
						->keyText('product_code', '商家编码,可用于搜索')
						->keyCheckBox('position','商品定位','',$position_options)
						->keyInteger('back_point', '购买返还积分')
						//->keyInteger('point_price', '积分换购所需分数')
						//->keyInteger('buy_limit', '限购数,0不限购')
						->keySelect('delivery_id','运费模板, 可先保存后再修改运费模板,避免丢失已编辑信息','<a href="'.url("delivery").'">点击添加运费模板</a>',$delivery_select)
						->keyInteger('sort', '排序')
						->keySelect('status','状态','',[1=>'上架',0=>'下架',-1=>'删除'])->keyDefault('status',1)
						->keyEditor('content', '商品详情','','wangeditor')
						->group('基本信息','id,title,description,main_img,images,cat_id,price,ori_price,quantity,product_code,position,back_point,delivery_id,sort,status')
						->group('商品详情','content')
						->data($product)
						->buttonSubmit(url('product',array('action'=>'add')))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':
				if(request()->isPost()){
					$ids = input('ids');
					$ret = $this->product_model->deleteData($ids);
					if ($ret){
						$this->success('操作成功。', url('product'));
					}else{
						$this->error('操作失败。');
					}
				}else{
		        	$ids=input('ids');
		            $ids=implode(',',$ids);
		            $this->assign('ids',$ids);
		            return $this->fetch('admin/del');
			    }

				break;
			case 'cell_record':
				$option['product_id'] = input('product_id',0);
				$option['uid'] = input('uid',0);
				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
				$product_sell_model = model('muushop/ShopProductSell');
				$product_sell_record = $product_sell_model->getData($option);
				$totalCount = $product_sell_record['count'];
				$builder = new AdminListBuilder();
				$builder
					->title('商品成交记录')
					->keyText('product_id','商品id')
					->keyText('order_id','订单id')
					->keyText('uid','用户id')
					->keyText('paid_price','下单价格/（分）')
					->keyText('quantity','下单数目')
					->keyTime('create_time','创建时间')
					->data($product_sell_record['list'])
					->display();
				break;
			case 'delete_sku_table':

				if(request()->isPost()){
					$product['id'] = input('id','','intval');
					empty($product['id']) && $this->error('缺少商品id');
					$product['sku_table'] = '';
					$ret = $this->product_model->editData($product);
					if ($ret){
						$this->success('操作成功。',url('product',['action'=>'sku_table','id'=>$product['id']]));
					}else{
						$this->error('操作失败。');
					}
				}
				break;
			case 'sku_table':

				if(request()->isPost()){
					$product['id'] = input('id',0,'intval');
					empty($product['id']) && $this->error('缺少商品id');
					$table = input('table/a');
					$info = input('info/a');

					$product['sku_table'] = array('table'=>$table,'info'=>$info);
					$product['sku_table'] = json_encode($product['sku_table']);
					$ret = $this->product_model->editData($product);
					if ($ret){
						$this->success('操作成功。');
					}else{
						$this->error('操作失败。');
					}
				}else{
					$id = input('id',0,'intval');
					$product = $this->product_model->getDataById($id);
					$product['sku_table'] = json_encode($product['sku_table']);
					if(!$product){
						$this->error('请选择一个商品','',2);
					}
					$this->assign('product', $product);
	                return $this->fetch('admin/sku_table');
				}
				
				break;
			//默认展示商品列表
			default:
				
				$map = [];
				if(input('cat_id')){
					$map['cat_id'] = input('cat_id');
				}
				
				$list = $this->product_model->getListByPage($map);
				foreach($list as &$val){
					$val['price']='￥'.sprintf("%.2f",$val['price']/100);
					$val['main_img']=getThumbImageById($val['main_img'], 80, 80);
				}
				unset($val);
				
				$select = $this->product_cats_model->getListForSelect('全部分类');
				$select2 = $this->product_cats_model->getListForConfig('全部分类');
				$builder = new AdminListBuilder();
				$builder
					->title('商品管理')
					->setSelectPostUrl(url('product'))
					->select('分类查看：', 'cat_id', 'select', '', '', '', $select)
					->select('显示模式：', 'count', 'select', '', '', '', [['id'=>0,'value'=>'正常'],['id'=>1,'value'=>'统计信息']])
					->buttonnew(url('product',['action'=>'add']),'新增商品')
					->buttonModalPopup(url('product',['action'=>'delete']),'','彻底删除',['data-title'=>'是否彻底删除','target-form'=>'ids'])
					->keyText('id','商品id')
					->keyImage('main_img','图片')
					->keyText('title','商品名');
				if($list){
					$builder
						->keyMap('cat_id','所属分类',$select2)
						->keyText('price','价格/（元）')
						->keyText('quantity','库存')
						->keyTime('create_time','创建时间')
						->keyText('sort','排序')
						->keyMap('status','状态',array('1'=>'上架','0'=>'下架'));
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

				$builder->keyDoActionEdit('product?action=add&id=###','编辑')
						->keyDoAction('product?action=sku_table&id=###','规格')
						->data($list)
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
				$ids = input('ids');
				$ret = $this->order_logic->deleteData($ids);
				if($ret){
					$this->success('删除成功');
				}else{
					$this->error('删除失败，'.$this->order_logic->error_str,'',3);
				}
			break;

			case 'order_delivery'://发货信息

				$delivery = '';
				if(modC('MUUSHOP_EXPRESS_ADDON','muushop') == 'kdniao') {
					$delivery = model('addons\kdniao\model\Kdniao')->shipper;
				}
				$this->assign('delivery',$delivery);

				$id = input('id');
				empty($id) && $this->error('订单参数错误',1);
				$order = $this->order_model->getDataById($id);
				
				$this->assign('order',$order);
				return $this->fetch('public/_delivery');	
			break;

			case 'order_address':
				$id = input('id');
				$order = $this->order_model->getDataById($id);
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
					->keyJoin('uid','用户','uid','nickname','member','/admin/user/index')
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
				$id = input('id');
				$order = $this->order_model->getDataById($id);
				$order = $order->toArray();
				$order['user_info'] = query_user('nickname',$order['uid']);

				$order['address']["province"] = Db::name('district')->where(['id' => $order['address']["province"]])->value('name');
			    $order['address']["city"] = Db::name('district')->where(['id' => $order['address']["city"]])->value('name');
			    $order['address']["district"] = Db::name('district')->where(['id' => $order['address']["district"]])->value('name');
			    
			    //设置支付类型
			    $order['pay_type_cn'] = $this->pay_model->payTypeStr($order['pay_type']);
			    $order['channel'] = $this->pay_model->_channel($order['channel']);
			    //价格转为元
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

				$this->setTitle('订单详情');
				$this->assign('order',$order);
				$this->assign('delivery',delivery_addons());
				return $this->fetch('admin/order_detail');
			break;

			case 'edit_order':

				if(request()->isPost()){

					$id = input('id','','intval');
					$status = input('status','','intval');
					$type = input('type','','text'); //操作方法
					$order = $this->order_model->getDataById($id);
					$order = $order->toArray();
					if(empty($id) || empty($status) || empty($type) || !($order) ){
						$this->error('参数错误');
					}else{
						switch ($type){
						case 'change_price':
							$paid_fee = input('paid_fee');
							$delivery_fee = input('delivery_fee');
							$paid_price = input('paid_price/a');
							
							$price_info = [
								'paid_fee' => $paid_fee,//总价
								'delivery_fee' => $delivery_fee,//邮费
								'paid_price' => $paid_price,//单个商品价格数组
				            ];

							$res = $this->order_logic->changePrice($order,$price_info);
							if($res){
								$this->success('操作成功',url('order'));
							}else{
								$this->error('操作失败,'.$this->order_logic->error_str);
							}
						break;
						case 'cannel_order':
							//取消订单
							$res = $this->order_logic->cancalOrder($order);
							if($res){
								$this->success('操作成功',url('order'));
							}else{
								$this->error('操作失败,'.$this->order_logic->error_str);
							}
						break;
						case 'send_good':
							//发货
							$ShipperValue = input('ShipperValue');//快递公司名称及编号，以,分隔
							$LogisticCode = input('LogisticCode');//物流单号
							$ShipperValue = explode(',',$ShipperValue);
							
							$delivery_info = [
								'ShipperName' =>$ShipperValue[0],
								'ShipperCode'=>$ShipperValue[1],
								'LogisticCode'=>$LogisticCode,
							];
							$res = $this->order_logic->sendGood($order,$delivery_info);
							if($res){
								$this->success('操作成功',url('order'));
							}else{
								$this->error('操作失败,'.$this->order_logic->error_str);
							}
					
						break;
						case 'recv_good':
							//确认收货
							$ret = $this->order_logic->recvGoods($order);
							if($ret){
								$this->success('操作成功',url('order'));
							}else{
								$this->error('操作失败,'.$this->order_logic->error_str);
							}
						break;
						
						case 'del_order':
							//删除订单
							$ret = $this->order_logic->deleteOrder($order['id']);
							if($ret){
								$this->success('操作成功',url('order'));
							}else{
								$this->error('操作失败,'.$this->order_logic->error_str);
							}
						break;
						}
					}
				}else{
					$type = input('type','','text');//操作类型 RevisePrice:修改价格 Edit:操作
					$this->assign('type',$type);

					$id = input('id');//获取点击的id
					$order = $this->order_model->getDataById($id);
					$order = $order->toArray();
					if(!empty($order['products'])){
						foreach($order['products'] as &$val){
							//商品列表价格单位转为元
							$val['paid_price'] = sprintf("%01.2f", $val['paid_price']/100);
							//sku_id转为数组
							$val['sku'] = explode(';',$val['sku_id']);
							unset($val['sku'][0]);
						}
					}
					unset($val);
					$order['delivery_fee'] = sprintf("%01.2f", $order['delivery_fee']/100);
					$order['paid_fee'] = sprintf("%01.2f", $order['paid_fee']/100);

					$this->assign('order', $order);

					//获取物流查询插件
					$expressAddon = modC('MUUSHOP_EXPRESS_ADDON','muushop');
					//初始化物流公司和编码数组
					$delivery = [];
					if($expressAddon == 'kdniao') {
						$delivery = model('addons\kdniao\model\Kdniao')->shipper;
					}else{
						return "未配置物流插件或不支持的插件";
					}
        			$this->assign('delivery',$delivery);
					return $this->fetch('admin/edit_order');
				}
			break;

			default:
				
				$map = [];
				$order_no = input('order_no');
				if($order_no){
					$map['order_no'] = $order_no;
				}
				$key = input('key');
				if($key) {
					$map['products'] = ['like','%'.unicode_encode($key).'%'];
				}
				$status = input('status');
				if($status){
					$map['status'] = $status;
				}
				
				$order = $this->order_model->getListByPage($map,'create_time desc','*',20);
				$page = $order->render();
				$order = $order->toArray()['data'];
				
				foreach($order as &$val){
					$val['paid_fee']='¥ '.sprintf("%01.2f", $val['paid_fee']/100);
					$val['delivery_fee']='¥ '.sprintf("%01.2f", $val['delivery_fee']/100);
					$val['discount_fee']='- ¥ '.sprintf("%01.2f", $val['discount_fee']/100);
					$val['pay_type_str'] = $this->pay_model->payTypeStr($val['pay_type']);
					$val['status_str'] = $this->order_model->statusStr($val['status']);
				}
				unset($val);

				$status_select = $this->order_model->order_status_list_select();
				
				$builder = new AdminListBuilder();
				$builder
					->title('订单管理')
					->setSearchPostUrl(url('order'))
					->search('订单号', 'order_no', 'text', '按订单号搜索')
					->search('关键字', 'key', 'text', '按商品名称关键字搜索')
					->select('订单状态：', 'status', 'select', '', '', '', $status_select)
					->keyText('id','订单id')
					->keyText('order_no','订单号')
					->keyUid('uid','用户')
					->keyTime('create_time','下单时间')
					->keyText('pay_type_str','支付方式')
					->keyText('paid_fee','总价/元')
					->keyText('discount_fee','优惠/元')
					->keyText('delivery_fee','邮费')
					->keyText('status_str','订单状态');

				$builder
					//->keyDoAction('order?action=order_detail&id=###','详情','操作','btn-info')
					->keyDoActionModalPopup('order?action=order_detail&id=###','详情','操作',['data-title'=>'订单详情'],'btn-info')
					->keyDoActionModalPopup(
						'order?action=edit_order&type=change_price&id=###',
						'改价','操作',['data-title'=>'修改订单价格'],'btn-warning',
						['status','>',1]//根据条件隐藏本操作
					)
					->keyDoActionModalPopup('order?action=edit_order&type=cannel_order&id=###','取消订单','操作',['data-title'=>'操作'],'btn-danger',['status','!=',1])

					->keyDoActionModalPopup('order?action=edit_order&type=send_good&id=###','发货','操作',['data-title'=>'操作'],'btn-success',['status','>2','==1','||'])

					->keyDoActionModalPopup('order?action=edit_order&type=send_good&id=###','改物流','操作',['data-title'=>'修改物流'],'btn-warning',['status','<3','>=4','||'])

					->keyDoActionModalPopup('order?action=order_delivery&id=###','查物流','操作',['data-title'=>'物流查询'],'',['status','<3','==10','||']);

				$builder
					->data($order)
					->page($page)
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
				if(request()->isPost())
				{	
					$input = input('post.');
					$input['rule'] = json_decode($input['rule'],true);
					if($input['valuation']==0){
						foreach($input['rule'] as &$val){
							$val['cost']=$val['cost']*100;
						}
						unset($val);
					}else{
						foreach($input['rule'] as &$val){
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
					$input['rule'] = json_encode($input['rule']);
					if (!$input){
						$this->error($this->delivery_model->getError());
					}
					$res = $this->delivery_model->editData($input);
					if ($res){
						$this->success('操作成功。', url('delivery'),1);
					}else{
						$this->error('操作失败。');
					}
				}else{
					$id = input('id',0,'intval');
					if($id){
						$delivery = $this->delivery_model->getDataById($id)->toArray();
						if(!isset($delivery['rule']['express']['cost'])){
							$delivery['rule']['express']['cost'] = '';
						}
						
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
						$delivery = [];
					}
					//获取中国省份列表
					$district = $this->District(1);

					$this->meta_title = '运费模板编辑';
					$this->assign('district',$district);
					$this->assign('delivery',$delivery);
					error_reporting(E_ERROR | E_WARNING | E_PARSE);
					return $this->fetch('admin/adddelivery');
				}
				break;
			case 'delete':
				$ids = input('ids/a');
				$res = $this->delivery_model->deleteData($ids);
				if ($res){
					$this->success('操作成功。', url('delivery'));
				}else{
					$this->error('操作失败。');
				}
				break;
			default:
				$map = [];
				$delivery = $this->delivery_model->getListByPage($map,'id asc','*',20);
				
				$builder = new AdminListBuilder();
				$builder
					->title('运费模板管理')
					->buttonNew(url('Delivery',['action'=>'add']),'新增运费模板')
					->buttonDelete(url('Delivery',['action'=>'delete']),'删除')
					->keyText('id','id')
					->keyText('title','标题')
					->keyText('brief','模板说明')
					->keyTime('create_time','创建时间')
					->keyDoActionEdit('delivery?action=add&id=###','编辑')
					->data($delivery)
					
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
				if(request()->isPost()){
					$inputData = input('post.');
					
					empty($inputData['max_cnt_enable']) || $rule['max_cnt'] = $inputData['max_cnt']; //每用户领取张数限制
					empty($inputData['max_cnt_day_enable']) || $rule['max_cnt_day'] = $inputData['max_cnt_day']; //每用户每天领取张数限制
					empty($inputData['min_price_enable']) || $rule['min_price'] = $inputData['min_price']*100; //满多少可用

					$coupon['id'] = $inputData['id'];
					$coupon['title'] = $inputData['title'];
					$coupon['img'] = $inputData['img'];
					$coupon['publish_cnt'] = $inputData['publish_cnt'];
					$coupon['expire_time'] = $inputData['expire_time'];
					$coupon['valuation'] = $inputData['valuation'];
					$coupon['brief'] = $inputData['brief'];


					if(empty($inputData['discount'])){
						$this->error('请设置优惠金额');
					}else{
						$rule['discount'] = $inputData['discount']*100;
					}
					$coupon['rule'] = json_encode($rule);

					$res = $this->coupon_model->editData($coupon);
					if ($res){
						$this->success('操作成功。', url('coupon'));
					}else{
						$this->error('操作失败。');
					}
				}else{
					$id = input('id');
					if(!empty($id)){
						$coupon = $this->coupon_model->getDataById($id)->toArray();
						
						if(!empty($coupon['rule'])){
							
							$coupon['rule']['max_cnt_enable'] = (empty($coupon['rule']['max_cnt'])?0:1);
							$coupon['rule']['max_cnt_day_enable'] = (empty($coupon['rule']['max_cnt_day'])?0:1);
							$coupon['rule']['min_price_enable'] = (empty($coupon['rule']['min_price'])?0:1);
							if($coupon['rule']['min_price_enable']) {
								$coupon['rule']['min_price'] = sprintf("%.2f",$coupon['rule']['min_price']/100);
							}
							$coupon['rule']['discount'] = sprintf("%.2f",$coupon['rule']['discount']/100);

							$coupon = array_merge($coupon,$coupon['rule']);

						}
					}else{
						$coupon =[];
					}
					
					$builder = new AdminConfigBuilder();
					$builder
						->title('优惠券详情')
						->keyId()
						->keytext('title','优惠券名称')
						->keySingleImage('img','优惠券图片')
						->keyInteger('publish_cnt','总发放数量')
						->keyInteger('discount','优惠金额','单位：元')
						->keyTime('expire_time','过期时间')
						//->keySelect('duration','有效期','',['0'=>'永久有效','86400'=>'一天内有效','604800'=>'一周内有效','2592000'=>'一月内有效'])
						->keyMultiInput('max_cnt_enable|max_cnt','领取限制','每个用户最多允许领取多少张',[
							['type'=>'select','opt'=>['不限制','限制'],'style'=>'width:95px;margin-right:5px'],
							['type'=>'text','style'=>'width:95px;margin-right:5px']
						])
						->keyMultiInput('max_cnt_day_enable|max_cnt_day','领取限制','每个用户每天最多允许领取多少张',[
							['type'=>'select','opt'=>['不限制','限制'],'style'=>'width:95px;margin-right:5px'],
							['type'=>'text','style'=>'width:95px;margin-right:5px']
						])
						->keyMultiInput('min_price_enable|min_price','使用限制','最低可以使用的价格（单位：元），即满多少可用',[
							['type'=>'select','opt'=>['不限制','限制'],'style'=>'width:95px;margin-right:5px'],
							['type'=>'text','style'=>'width:95px;margin-right:5px']
						])
						->keySelect('valuation','类型','',array('现金券','折扣券'))
						->keyTextArea('brief','优惠券说明')

						->data($coupon)
						->buttonSubmit(url('coupon',['action'=>'add']))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':
				$ids= input('ids');
				$ret = $this->coupon_model->deleteData($ids);
				if ($ret){
					$this->success('操作成功。', url('coupon'));
				}else{
					$this->error('操作失败。');
				}
				break;
			case 'couponlink':
				$id = input('id');
				$id = $this->coupon_model->encrypt_id($id);
				redirect(url('get_coupon',['id'=>$id]));//优惠券id 加密 跳转 具体链接 依业务需求修改
				break;
			default:
				
				$map = [];
				$list = $this->coupon_model->getListByPage($map);
				$list_arr = $list->toArray()['data'];
				foreach($list_arr as &$val){
					$val['discount'] = sprintf("%.2f",$val['rule']['discount']/100);
					if(isset($val['rule']['min_price'])) {
						$val['rule']['min_price'] = sprintf("%.2f",$val['rule']['min_price']/100);
					}
				}
				unset($val);

				$builder = new AdminListBuilder();
				$builder
					->title('优惠券')
					->buttonNew(url('coupon',['action'=>'add']),'新增优惠券')
					->buttonAjax(url('coupon',['action'=>'delete']),'','删除')
					->keyText('id','优惠券id')
					->keyText('title','优惠券名称')
					->keyImage('img','优惠券图片')
					->keyMap('valuation','类型',array('现金券','折扣券'))
					->keyTruncText('brief','优惠券说明','25')
					->keyText('used_cnt','已发放数量')
					->keyText('publish_cnt','总发放数量')
					->keyText('discount','金额')
					->keyTime('create_time','创建时间')
					->keyTime('expire_time','过期时间')
					->keyLink('','领取链接','/muushop/coupon/get_coupon/id/###')
					->keyDoActionEdit('coupon?action=add&id=###','查看和编辑')
					->data($list_arr)
					->page($list->render())
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
				if(request()->isPost()){
					$coupon_id       = input('coupon_id', '', 'intval');
					$uid     = input('uid', '', 'trim');
					if(empty($coupon_id) || !($coupon = $this->coupon_model->getDataById($coupon_id)))
						$this->error('请选择一个优惠券');
					if(empty($uid)) $this->error('请选择一个用户');
					$ret =$this->coupon_logic->add_a_coupon_to_user($coupon_id,$uid);
					if($ret){
						$this->success('操作成功。', url('user_coupon'));
					}else{
						$this->error('操作失败。'.$this->coupon_logic->error_str);
					}
				}else{
					$all_coupon_select = $this->coupon_model->column('id,title');
					if(empty($all_coupon_select)){
						redirect(url('coupon',['action'=>'add']));
					}
					$builder = new AdminConfigBuilder();
					$builder
						->title('手动发放优惠券')
						->keySelect('coupon_id','优惠券','要发放的优惠券',$all_coupon_select)
						->keyInteger('uid','用户id','')

						->buttonSubmit(url('user_coupon',array('action'=>'add')))
						->buttonBack()
						->display();
				}
				break;
			case 'delete':
				$ids= input('ids');
				$ret = $this->user_coupon_model->deleteData($ids);
				if ($ret){
					$this->success('操作成功。', url('user_coupon'));
				}else{
					$this->error('操作失败。');
				}
				break;

			default:
				$map = [];
				$list = $this->user_coupon_model->getListByPage($map,'id desc','*',20);
				$list_arr = $list->toArray()['data'];
				//dump($list_arr);exit;
				foreach($list_arr as &$val){
					$val['coupon_title'] = $val['info']['title'];
					if($val['info']['img']){
						$val['coupon_img'] = $val['info']['img'];
					}else{
						$val['coupon_img'] = 0;
					}
					
					$val['coupon_discount'] = sprintf("%.2f",$val['discount']/100);
					if(isset($val['coupon_info']['rule']['min_price'])) {
						$val['coupon_min_price'] = sprintf("%.2f",$val['coupon_info']['rule']['min_price']/100);
					}else{
						$val['coupon_min_price'] = '不限';
					}
				}
				unset($val);

				$builder = new AdminListBuilder();
				$builder
					->title('已领取优惠券')
					->buttonNew(url('user_coupon',['action'=>'add']),'派发优惠券')
					->buttonAjax(url('user_coupon',['action'=>'delete']),'','删除')
					->keyId()
					->keyUid('uid')
					->keyLink('coupon_title','优惠券','coupon?action=add&id=###','coupon_id')
					->keyImage('coupon_img','优惠券图片')
					->keytext('coupon_discount','折扣金额')
					->keytext('coupon_min_price','满多少可用')
					->keyTime('create_time','发放时间')
					->keyTime('expire_time','到期时间')
					//->keyLink('order_id','订单ID（无）','order?action=order_detail&id=###','order_id')
					->keyMap('status','状态',['0'=>'未使用','1'=>'已使用','2'=>'已过期'])
					->keyDoActionModalPopup('order?action=order_detail&id=###','详情','操作',['data-title'=>'订单详情'],'btn-info','','order_id')
					->data($list_arr)
					->page($list->render())
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
				if(request()->isPost())
				{
					$ids  =  input('ids');
					$status  =  input('get.status','','/[012]/');
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

			default:

				//判断是否安装评价晒图插件
				if (!class_exists('\addons\evaluate\Evaluate')) {
		        	$this->error('评价晒图插件未安装');
		        }
		        $map['app'] = 'muushop';
            	$map['model'] = 'product';
            	
		        //调用插件接口获取列表
	            $list = model('addons\evaluate\model\Evaluate')->getListByPage($map,'create_time desc','*',20);
	            $page = $list->render();
	            
				$builder = new AdminListBuilder();
				$builder
					->title('商品评论管理')
					->buttonAjax(url('product_comment',array('action'=>'edit_status','status'=>1)),'','审核通过')
					->buttonAjax(url('product_comment',array('action'=>'edit_status','status'=>2)),'','审核不通过')
					->keyId()
					//->keyJoin('row_id','商品','id','title','muushop_product','product')
					//->keyJoin('order_id','订单','id','id','muushop_order','order')
					->keyUid('uid')
					->keyText('value','星数')
					->keyText('content','评论内容')
					->keyTime('create_time','评论时间')
					->keyMap('status','状态',[0=>'未审核',1=>'已通过',2=>'未通过'])
					->data($list)
					->page($page)
					->display();
				break;
		}

	}
	/**
	 * 售后管理
	 * @param  string $action [description]
	 * @return [type]         [description]
	 */
	public function service($action ='')
	{
		switch($action)
		{
			case 'handle': //处理售后申请

			if(request()->isPost()){

				$data['id'] = input('post.id');
				$res = $this->service_model->getDataById($data['id']);

				$info = $res['info'];
				$data['status'] = input('post.status');
				$info['reason'] = input('post.reason');
				
				$ShipperValue = input('post.ShipperValue');
				if(!empty($ShipperValue)){
					$ShipperValue = explode(',',$ShipperValue);
					$info['replace_express']['ShipperName'] = $ShipperValue[0];
					$info['replace_express']['ShipperCode'] = $ShipperValue[1];
					$info['replace_express']['LogisticCode'] = input('post.LogisticCode');
				}
				$data['info'] = json_encode($info);

				$res = $this->service_model->editData($data);
				if ($res){
					$this->success('操作成功。','',$res);
				}else{
					$this->error('操作失败。');
				}

			}else{
				$id = input('id');
				$data = $this->service_model->getDataById($id);
				$data = $data->toArray();
				$data['images_small'] = [];
				$data['images_big'] = [];
				foreach($data['images'] as &$val){
					array_push($data['images_small'],getThumbImageById($val,100,100));
					array_push($data['images_big'],pic($val));
				};
				unset($val);
				$this->assign('data',$data);
				$this->assign('delivery',delivery_addons());
				return $this->fetch('service_handle');
			}	
			break;

			case 'return_express_info': //退货物流信息

				$id = input('id');
				$data = $this->service_model->getDataById($id);
				
				$this->assign('data',$data);
				return $this->fetch('user/service/return_express_info');
			break;

			case 'replace_express_info': //商家发货物流信息

				$id = input('id');
				$data = $this->service_model->getDataById($id);
				
				$this->assign('data',$data);
				return $this->fetch('user/service/replace_express_info');
			break;

			default:

				$map = [];
		        //获取列表
	            $list = $this->service_model->getListByPage($map,'create_time desc','*',20);
	            $page = $list->render();
	            $list = $list->toArray()['data'];

				$builder = new AdminListBuilder();
				$builder
					->title('售后管理')
					->keyId()
					->keyJoin('product_id','商品','id','title','muushop_product','muushop/Index/product','_blank')
					->keyJoin('order_id','订单','id','order_no','muushop_order','order?action=order_detail')
					->keyUid('uid')
					->keyText('type_str','类型')
					->keyText('description','申请描述')
					->keyTime('create_time','申请时间')
					->keyText('status_str','状态')

					->keyDoActionModalPopup(
						'service?action=return_express_info&id=###',
						'退货物流','操作',['data-title'=>'退货物流'],'',
						['status','<2','>=3','||']//根据条件隐藏本操作
					)

					->keyDoActionModalPopup(
						'service?action=replace_express_info&id=###',
						'发货物流','操作',['data-title'=>'发货物流'],'',
						['status','<','4']//根据条件隐藏本操作
					)

					->keyDoActionModalPopup(
						'service?action=handle&id=###',
						'操作','操作',['data-title'=>'操作'],'btn-primary'
					)

					->data($list)
					->page($page)
					->display();
		}

	}
	/**
	 * 获取中国省份、城市
	 */
	private function District($level=1){
			$map['level'] = $level;
			$map['upid'] = 0;
			$list = model('addons\chinacity\model\District')->_list($map);
			return $list;
	}

}
