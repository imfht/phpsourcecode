<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: UCT <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\shop\controller;

use app\admin\controller\Admin;
use \think\Loader;
use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminTreeListBuilder;
//use Think\Db\Driver\Pdo;


class Shop extends Admin
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
        $this->product_cats_model = model('ShopProductCats');
	    $this->product_model = model('ShopProduct');
	    $this->order_model = model('ShopOrder');
	    $this->delivery_model = model('ShopDelivery');
	    $this->message_model = model('ShopMessages');
	    $this->coupon_model = model('ShopCoupon');
	    $this->user_coupon_model = model('ShopUserCoupon');
	    $this->order_logic = model('ShopOrder','Logic');
	    $this->coupon_logic = model('ShopCoupon','Logic');
	    $this->address_model = model('ShopUserAddress');
	    $this->product_comment_model = model('ShopProductComment');
        parent::_initialize();
    }


	public function index()
	{
		if(!modC('MP_ID', '', 'Shop'))
		{
			//未配置公众号
			redirect(url('shop/shop'));
		}
		else
		{
			redirect(url('shop/product'));
		}
	}

	public function shop()
	{

		$member_public = db('member_public')->column('id,public_name');
		array_unshift($member_public,'选择公众号');
		return $this->fetch();

	}

	/*
	 * 幻灯片
	 */
	public function slides($action='')
	{
		$shop_slides_model = db('shop_slides');
		switch ($action)
		{
			case 'add':
				if(request()->isPost())
				{

					$slides = $shop_slides_model->create();
					$slide['sort'] = (empty($slide['sort'])?0:$slide['sort']);
					if(utf8_strlen($slides['title'])>255)
					{
						$this->error('说明不要长于255个字符');
					}

					if(empty($slides['image']))
					{
						$this->error('请设置一张图片');
					}
					if(!empty($slides['id']))
					{
						unset($slides['create_time']);
						$ret = $shop_slides_model->where('id = '.$slides['id'])->save();
					}
					else
					{
						$ret = $shop_slides_model->add();
					}
					if($ret)
					{
						$this->success('添加成功');
					}
					else
					{
						$this->error('添加失败');
					}
				}
				else
				{
					$id = input('id');
					$slides = $shop_slides_model->where('id ='.$id)->find();
					return $this->fetch();
				}
				break;
			case 'delete':
				$ids = input('ids');
				is_array($ids) || $ids = array($ids);
				$ret = $shop_slides_model->where('id in ('.implode(',',$ids).')')->delete();
				if($ret)
				{
					$this->success('删除成功');
				}
				else
				{
					$this->error('删除失败');
				}
				break;
			default:
				$page = input('apge');
				$r = input('r');
				$slides = $shop_slides_model->order('sort desc,create_time desc')->paginate($page,$r);
//				var_dump(__file__.' line:'.__line__,$slides);exit;
				$totalCount = $shop_slides_model->count();
				$builder = new AdminListBuilder();
				return $this->fetch();
				break;
		}
	}
	/*
	 * 商品分类
	 */
	public function product_cats($action='',$page=1,$r=10)
	{

		switch($action)
		{
			case 'add':
			    //echo  ROOT_PATH;
				if(request()->isPost())
				{
//					var_dump(__file__.' line:'.__line__,$_REQUEST);exit;
					$product_cats = input('');//$this->product_cats_model->create();
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
					if ($ret)
					{

						$this->success('操作成功。', url('shop/product_cats',array('parent_id'=>input('parent_id',0))));
					}
					else
					{
						$this->error('操作失败。');
					}
				}
				else
				{
					$id = input('id');
					if(!empty($id))
					{
						$product_cats = $this->product_cats_model->get_product_cat_by_id($id);
					}
					else
					{
						$product_cats = array();
					}

					$select = $this->product_cats_model->get_produnct_cat_config_select();
//					var_dump(__file__.' line:'.__line__,$select);exit;
					return $this->fetch('add');
				}
				break;
			case 'delete':
				$ids = input('ids');
				$ret = $this->product_cats_model->delete_product_cats($ids);
				if ($ret)
				{

					$this->success('操作成功。', url('shop/product_cats'));
				}
				else
				{
					$this->error('操作失败。');
				}
				break;
			default:

				$option['parent_id'] = input('parent_id',0,'intval');
				if(!empty($option['parent_id']))
				{
					$parent_cat  = $this->product_cats_model->get_product_cat_by_id($option['parent_id']);
				}
				if(input('all')) $option = array();
				$option['page'] = $page;
				$option['r']  =  $r;
				$cats = $this->product_cats_model->get_product_cats($option);
				$totalCount = $cats['count'];
//				var_dump(__file__.' line:'.__line__,$parent_cat);exit;
				$select = $this->product_cats_model->get_produnct_cat_list_select();
				$this->assign('list',$cats['list']);//输出数据列表
                $this->assign('page',$cats['page']);
				return $this->fetch();
		}
	}

	/*
	 * 商品相关
	 */
	public function product($action = '')
	{
		switch($action)
		{
			case 'add':
				if(request()->isPost())
				{

					$product = $this->product_model->create();
					if (!$product){

						$this->error($this->product_model->getError());
					}
					$ret = $this->product_model->add_or_edit_product($product);
					if ($ret)
					{
						$this->success('操作成功。', url('shop/product'));
					}
					else
					{
						$this->error('操作失败。');
					}
				}
				else
				{
					$id = input('id');
					if(!empty($id))
					{
						$product = $this->product_model->get_product_by_id($id);
					}
					else
					{
						$product = array();
					}

					$select = $this->product_cats_model->get_produnct_cat_config_select('选择分类');
					if(count($select)==1)
					{
						$this->error('先添加一个商品分类吧',url('shop/product_cats',array('action'=>'add')),2);
					}
					$delivery_select = $this->delivery_model->column('id,title');
					empty($delivery_select) && $delivery_select=array();
					array_unshift($delivery_select,'不需要运费');
					$info_array = array(
//								'不货到付款','不包邮','不开发票','不保修','不退换货','不是新品',
					                    '6'=>'热销','7'=>'推荐');
					//注释的暂不支持
					return $this->fetch('');
				}
				break;
			case 'delete':
				$ids = input('ids');
				$ret = $this->product_model->delete_product($ids);
				if ($ret)
				{

					$this->success('操作成功。', url('shop/product'));
				}
				else
				{
					$this->error('操作失败。');
				}
				break;
			case 'cell_record':
				$option['product_id'] = input('product_id',0);
				$option['user_id'] = input('user_id',0);
//				$option['min_time'] = input('min_time',0);
				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
				$product_sell_model = model('shop/ShopProductSell');
				$product_sell_record = $product_sell_model->get_sell_record($option);
				$totalCount = $product_sell_record['count'];
				return $this->fetch();
				break;
			case 'delete_sku_table':
				if(request()->isPost())
				{
					$product['id'] = input('id','','intval');
					empty($product['id']) && $this->error('缺少商品id');
					$product['sku_table'] = '';
					$ret = $this->product_model->add_or_edit_product($product);
					if ($ret)
					{
						$this->success('操作成功。',url('shop/product',array('action'=>'sku_table','id'=>$product['id'])),1);
					}
					else
					{
						$this->error('操作失败。');
					}
				}
				break;
			case 'sku_table':
				if(request()->isPost())
				{
					$product['id'] = input('id','','intval');
					empty($product['id']) && $this->error('缺少商品id');
					$table = input('table');
					$info = input('info');
					$product['sku_table'] = array('table'=>$table,'info'=>$info);
					$product['sku_table'] = json_encode($product['sku_table']);
					$ret = $this->product_model->add_or_edit_product($product);
					if ($ret)
					{
						$this->success('操作成功。');
					}
					else
					{
						$this->error('操作失败。');
					}
				}
				else
				{
					$id = input('id');
					if(empty($id)
					||!($product = $this->product_model->get_product_by_id($id)))
					{
						$this->error('请选择一个商品','',2);
					}
					$this->assign('product', $product);
	                $this->display('Shop@Shop/sku_table');
				}
				
				break;
			case 'exi':
				if(request()->isPost())
				{
					//没写完
					var_dump(__file__.' line:'.__line__,$_REQUEST);exit;
					$product = array();
					$ret = $this->product_model->add_or_edit_product($product);
					if($ret)
					{
						$this->success('操作成功',url('shop/product'));
					}
					else
					{
						$this->error('操作失败');

					}
					//					var_dump(__file__.' line:'.__line__, $_REQUEST);exit;

				}
				else
				{
					$porduct_extra_info_model = model('Shop/ShopProductExtraInfo');

					$id = input('id');
					if(empty($id)
						||!($product = $this->product_model->get_product_by_id($id)))
					{
						$this->error('请选择一个商品','',2);
					}
					$exi = $porduct_extra_info_model->get_product_extra_info($id);
					$this->assign('exi', $exi);
					$this->display('Shop@shop/exi');
				}
				break;
			default:

				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
				$option['cat_id'] = input('cat_id');
				$count = input('count');
				if(empty($option['cat_id'])) unset($option['cat_id']);
				$product = $this->product_model->get_product_list($option);
				$totalCount = $product['count'];
				$select = $this->product_cats_model->get_produnct_cat_list_select('全部分类');
				$select2 = $this->product_cats_model->get_produnct_cat_config_select('全部分类');
//				$builder = new AdminListBuilder();
//				$builder
//					->title('商品管理')
//					->setSelectPostUrl(url('shop/product'))
//					->select('分类查看', 'cat_id', 'select', '', '', '', $select)
//					->select('显示模式', 'count', 'select', '', '', '', array(array('id'=>0,'value'=>'正常'),array('id'=>1,'value'=>'统计信息')))
//					->buttonnew(url('shop/product',array('action'=>'add')),'新增商品')
//					->ajaxButton(url('shop/product',array('action'=>'delete')),'','删除')
//					->keyText('id','商品id')
//					->keyText('title','商品名');
//				if(!$count)
//				{
//					$builder->keyMap('cat_id','所属分类',$select2)
//						->keyText('price','价格/（分）')
//						->keyText('quantity','库存')
//						->keyImage('main_img','图片')
//						//					->keyTime('create_time','创建时间')
//						//					->keyTime('modify_time','编辑时间')
//						->keyText('sort','排序')
//						->keyMap('status','状态',array('0'=>'正常','1'=>'下架'));
//				}
//				else
//				{
//					$builder
////						->keyText('like_cnt','点赞数')
////						->keyText('fav_cnt','收藏数')
//						->keyText('comment_cnt','评论数')
////						->keyText('click_cnt','点击数')
//						->keyText('sell_cnt','总销量')
//						->keyText('score_cnt','评分次数')
//						->keyText('score_total','总评分');
//				}
//
//				$builder->keyDoAction('admin/shop/product/action/add/id/###','基本信息')
//					->keyDoAction('admin/shop/product/action/sku_table/id/###','特殊规格')
////					->keyDoAction('admin/shop/product/action/exi/id/###','商品参数')
//					->data($product['list'])
//					->pagination($totalCount, $option['r'])
//					->display();

                return $this->fetch();
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
				$ret = $this->order_logic->delete_order($ids);
				if($ret)
				{
					$this->success('删除成功');
				}
				else
				{
					$this->error('删除失败，'.$this->order_logic->error_str,'',3);
				}
			break;
			case 'order_delivery':
				if(request()->isPost())
				{
					$id = input('id');
					empty($id) && $this->error('信息错误',1);
					$courier_no = input('courier_no');
					$courier_name = input('courier_name');
					$courier_phone = input('courier_phone','','intval');
					$delivery_info = array(
						'courier_no'=>$courier_no,
						'courier_name'=>$courier_name,
						'courier_phone'=>$courier_phone,
					);
					$order['delivery_info'] = json_encode($delivery_info);
					$order['id'] = $id;
					$ret = $this->order_model->add_or_edit_order($order);
					if($ret)
					{
						$this->success('操作成功');
					}
					else{
						$this->error('操作失败','',3);
					}
				}
				else{
					$id = input('id');
					$order = $this->order_model->get_order_by_id($id);
					$delivery_info = json_decode($order['delivery_info'],true);
					//				var_dump(__file__.' line:'.__line__,$order);exit;
					$delivery_info['id'] = $order['id'];
					$order['send_time'] = (empty($order['send_time'])?'未发货':date('Y-m-d H:i:s',$order['send_time']));
					$order['recv_time'] = (empty($order['recv_time'])?'未收货':date('Y-m-d H:i:s',$order['recv_time']));

					$delivery_info['send_time'] = $order['send_time'];
					$delivery_info['recv_time'] = $order['recv_time'];
					$builder       = new AdminConfigBuilder();
					$builder
						->title('发货信息')
						->suggest('发货信息')
						->keyReadOnly('id','订单id')
						->keyText('courier_no','快递单号')
						->keyText('courier_name','快递员姓名')
						->keyText('courier_phone','快递员电话')
						->keyText('send_time','发货时间')
						->keyText('recv_time','收货时间')
						->buttonSubmit(url('Shop/order',array('action'=>'order_delivery')),'修改')
						->buttonBack()
						->data($delivery_info)
						->display();
				}
				break;
			case 'order_address':
				$id = input('id');
				$order = $this->order_model->get_order_by_id($id);
				$address = is_array($order['address'])?$order['address']:json_decode($order['address'],true);
				$info  = is_array($order['info'])?$order['info']:json_decode($order['info'],true);

				foreach($info as $ik=>$iv)
				{
					$infos['info_'.$ik] = $iv;
				}

				$builder       = new AdminConfigBuilder();
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
			case 'order_detail':
				$id = input('id');
				$order = $this->order_model->get_order_by_id($id);
				$order['create_time'] =(empty($order['create_time'])?'':date('Y-m-d H:i:s',$order['create_time']));
				$order['paid_time'] =(empty($order['paid_time'])?'未支付':date('Y-m-d H:i:s',$order['paid_time']));
				$order['send_time'] = (empty($order['send_time'])?'未发货':date('Y-m-d H:i:s',$order['send_time']));
				$order['recv_time'] = (empty($order['recv_time'])?'未收货':date('Y-m-d H:i:s',$order['recv_time']));
				$builder       = new AdminConfigBuilder();
//				var_dump(__file__.' line:'.__line__,$order );exit;
				$builder
					->title('订单详情')
					->keyReadOnly('id','订单id')
//					->keytext('')
//					->keyText('use_point','使用积分')
//					->keyText('back_point','返回积分')
					->keytext('create_time','创建时间')
					;
//				$product_input_list = array(
//					'title'=>array('name'=>'商品名','type'=>'keytext'),
//					'quantity'=>array('name'=>'数量','type'=>'keytext'),
//					'paid_price'=>array('name'=>'价格','type'=>'keytext'),
//					'sku_id'=>array('name'=>'其他信息','type'=>'keytext'),
//					'main_img'=>array('name'=>'商品主图','type'=>'keySingleImage'));
				$product_input_list = array(
					'title'=>array('name'=>'商品名','type'=>'text'),
					'quantity'=>array('name'=>'数量','type'=>'text'),
					'paid_price'=>array('name'=>'价格/分','type'=>'text'),
					'sku_id'=>array('name'=>'其他信息','type'=>'text'),
//					'main_img'=>array('name'=>'商品主图','type'=>'SingleImage')
				);
				if(!empty($order['products']))
				{
					foreach($order['products'] as $pk=> $product)
					{
						$MultiInput_name='|';
						foreach($product_input_list as $k=>$kv)
						{
							$name = 'porduct'.$pk.$k;
							if($k == 'sku_id')
							{
								if($product['sku_id'] = explode(';',$product['sku_id']))
								{
									unset($product['sku_id'][0]);
									$order[$name] =(empty($product['sku_id'])?'无':implode(',',$product['sku_id'])) ;
								}
							}
							else
							{
								$order[$name] = $product[$k];
							}
							$order[$name.'title'] = $kv['name'];
//							$builder->$kv['type']($name,$kv['name']);
							$MultiInput_name .= $name.'title'.'|'.$name.'|';
							$MultiInput_array[] =array('type'=>$kv['type'],'style'=>'width:95px;margin-right:5px') ;
							$MultiInput_array[] =array('type'=>$kv['type'],'style'=>'width:295px;margin-right:5px') ;
						}
						$builder->keyMultiInput(trim($MultiInput_name,'|'),'商品['.($pk+1).']信息','',$MultiInput_array);

					}
				}
//				var_dump(__file__.' line:'.__line__,$order);exit;
				$builder
					->keytext('paid_time','支付时间')
					->keyMultiInput('paid_fee|discount_fee|delivery_fee','支付信息(单位：分)','支付金额|优惠金额|运费',array(
						array('type'=>'text','style'=>'width:95px;margin-right:5px'),
						array('type'=>'text','style'=>'width:95px;margin-right:5px'),
						array('type'=>'text','style'=>'width:95px;margin-right:5px'),
					))
					->keyText('send_time','发货时间')
					->keyText('recv_time','收货时间')
					->buttonBack()
					->data($order)
					->display();
			break;
			case 'edit_order_modal':
				if(request()->isPost())
				{
					$order_id = input('order_id','','intval');
					$status = input('status','','intval');
					$order = $this->order_model->get_order_by_id($order_id);
					if(empty($order_id) || empty($status) || !($order))
					{
						$this->error('参数错误');
					}
					else
					{
						switch ($status)
						{
							case '1':
								//取消订单
								$ret = $this->order_logic->cancal_order($order);
								if($ret)
								{
									$this->success('操作成功');
								}
								else
								{
									$this->error('操作失败,'.$this->order_logic->error_str);
								}
								break;
							case '2':
								//发货
								$courier_no = input('courier_no');
								$courier_name = input('courier_name');
								$courier_phone = input('courier_phone','','intval');
								$delivery_info = array(
									'courier_no'=>$courier_no,
									'courier_name'=>$courier_name,
									'courier_phone'=>$courier_phone,
								);
								$ret = $this->order_logic->send_good($order,$delivery_info);
								if($ret)
								{
									$this->success('操作成功');
								}
								else
								{
									$this->error('操作失败,'.$this->order_logic->error_str);
								}
								break;
							case '3':
								//确认收货
								$ret = $this->order_logic->recv_goods($order);
								if($ret)
								{
									$this->success('操作成功');
								}
								else
								{
									$this->error('操作失败,'.$this->order_logic->error_str);
								}
								break;
							case '8':
								//拒绝退款
								$refund_reason = input('refund_reason','');
								$this->error('暂不支持该操作,'.$this->order_logic->error_str);
								break;
							case '10':
								//删除订单
								$ret = $this->order_logic->delete_order($order['id']);
								if($ret)
								{
									$this->success('操作成功');
								}
								else
								{
									$this->error('操作失败,'.$this->order_logic->error_str);
								}
								break;
						}

					}
				}
				else{
					$id = input('id');                        //获取点击的ids
					$order = $this->order_model->get_order_by_id($id);
					$this->assign('order', $order);
					$this->display('Shop@Shop/edit_order_modal');
				}


				break;
			default:
				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
				$option['user_id'] = input('user_id');
				$option['status'] = input('status');
				$option['key'] = input('key');
				$option['ids'] = input('id');
				empty($option['ids']) || $option['ids'] = array($option['ids']);
				$option['show_type'] = input('show_type','','intval');
				$order = $this->order_model->get_order_list($option);
//				var_dump(__file__.' line:'.__line__,$order);exit;
				$status_select = $this->order_model->get_order_status_config_select();
				$status_select2 = $this->order_model->get_order_status_list_select();
				$show_type_array = array(array('id'=>0,'value'=>'订单信息'),array('id'=>1,'value'=>'订单状态'));
				$totalCount = $order['count'];
//				$builder = new AdminListBuilder();
//				$builder
//					->title('订单管理')
//					->setSearchPostUrl(url('shop/order'))
//					->search('', 'id', 'text', '订单id', '', '', '')
//					->search('', 'key', 'text', '商品名', '', '', '')
//					->select('订单状态：', 'status', 'select', '', '', '', $status_select2)
//					->select('显示模式:', 'show_type', 'select', '', '', '', $show_type_array)
//					->buttonNew(url('shop/order'), '全部订单')
//					->keyText('id','订单id')
//					->keyJoin('user_id','用户','uid','nickname','member','/admin/user/index');
////					->ajaxButton(url('shop/order',array('action'=>'delete')),'','删除')
//				$option['show_type'] && $builder
//					->keyTime('create_time','下单时间')
//					->keyTime('paid_time','支付时间')
//					->keyTime('send_time','发货时间')
//					->keyTime('recv_time','收货时间')
//					;

//				$option['show_type'] || $builder
//					->keyMap('status','订单状态',$status_select)
//					->keyText('paid_fee','总价/分')
//					->keyText('discount_fee','已优惠的价格')
//					->keyText('delivery_fee','邮费')
//					->keyText('product_cnt','商品种数')
//					->keyText('product_quantity','商品总数');
//
//				$builder->keyDoAction('admin/shop/order/action/order_detail/id/###','订单详情')
//					->keyDoAction('admin/shop/order/action/order_address/id/###','地址等信息')
//					->keyDoAction('admin/shop/order/action/order_delivery/id/###','发货信息')
//					->keyDoActionModalPopup('admin/shop/order/action/edit_order_modal/id/###','订单操作');
//				$builder
//					->data($order['list'])
//					->pagination($totalCount, $option['r'])
//					->display();
                return $this->fetch();
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
					$delivery = $this->delivery_model->create();
					if (!$delivery){

						$this->error($this->delivery_model->getError());
					}
//					isset($_REQUEST['express_enable']) && empty($_REQUEST['express_enable']) || $rule['express'] = input('express',0);
//					isset($_REQUEST['mail_enable']) && empty($_REQUEST['mail_enable']) ||$rule['mail'] = input('mail',0);
//					isset($_REQUEST['ems_enable']) && empty($_REQUEST['ems_enable']) ||$rule['ems'] =input('ems',0);
					isset($rule) && $delivery['rule'] =json_encode($rule);
					$ret = $this->delivery_model->add_or_edit_delivery($delivery);
					if ($ret)
					{
						$this->success('操作成功。', url('shop/delivery'),1);
					}
					else
					{
						$this->error('操作失败。');
					}
				}
				else
				{

					$id = input('id');
					if(!empty($id))
					{
						$delivery = $this->delivery_model->get_delivery_by_id($id);
					}
					else
					{
						$delivery = array();
					}
					$this->assign('delivery',$delivery);
					$this->display('Shop@Shop/adddelivery');exit;
					if(!empty($delivery))
					{
						$delivery['express_enable'] = (isset($delivery['rule']['express'])?1:0);
						$delivery['express'] = (empty($delivery['express_enable'])?'':$delivery['rule']['express']);
						$delivery['mail_enable'] = (isset($delivery['rule']['mail'])?1:0);
						$delivery['mail'] = (empty($delivery['mail_enable'])?'':$delivery['rule']['mail']);
						$delivery['ems_enable'] = (isset($delivery['rule']['ems'])?1:0);
						$delivery['ems'] = (empty($delivery['ems_enable'])?'':$delivery['rule']['ems']);
					}

//					$builder->title('新增/修改运费模板')
//						->keyId()
//						->keyText('title', '模板名称')
////						->keyRadio('valuation','计费方式','',array(' 固定邮费','计件'))
//						->keyMultiInput('express_enable|express','平邮','单位:分',array(array('type'=>'select','opt'=>array('不支持','支持'),'style'=>'width:95px;margin-right:5px'),array('type'=>'text','style'=>'width:95px;margin-right:5px')))
//						->keyMultiInput('mail_enable|mail','普通快递','单位：分',array(array('type'=>'select','opt'=>array('不支持','支持'),'style'=>'width:95px;margin-right:5px'),array('type'=>'text','style'=>'width:95px;margin-right:5px')))
//						->keyMultiInput('ems_enable|ems','EMS','单位：分',array(array('type'=>'select','opt'=>array('不支持','支持'),'style'=>'width:95px;margin-right:5px'),array('type'=>'text','style'=>'width:95px;margin-right:5px')))
//						->keyEditor('brief', '模板说明')
//						->keyCreateTime()
//						->data($delivery)
//						->buttonSubmit(url('shop/delivery',array('action'=>'add')))
//						->buttonBack()
//						->display();
				}
				break;
			case 'delete':
				$ids = input('ids');
				$ret = $this->delivery_model->delete_delivery($ids);
				if ($ret)
				{

					$this->success('操作成功。', url('shop/delivery'));
				}
				else
				{
					$this->error('操作失败。');
				}
				break;
			default:
				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
				$delivery = $this->delivery_model->get_delivery_list($option);
				$totalCount = $delivery['count'];

				return $this->fetch();
				break;
		}
	}

	/*
	 * 商城评论反馈
	 */
	public function message($action ='')
	{
		switch($action)
		{
			case 'review_message':
				$ids  = input('ids');
				is_array($ids) || $ids =array($ids);
				$status = input('status','0','intval');
				$ret = $this->message_model->where('id in('.implode(',',$ids).')')->save(array('status'=>$status));
				if ($ret)
				{
					$this->success('操作成功。', url('shop/message'));
				}
				else
				{
					$this->error('操作失败。');
				}
				break;
			case 'message_detail':
				$builder       = new AdminConfigBuilder();
				$id = input('id');
				if(!empty($id))
				{
					$message = $this->message_model->get_shop_message_by_id($id);
				}
				else
				{
					$message= array();
				}
				$builder->title('留言详情和回复')
					->keyId()
					->keyText('user_id','用户id')
					->keyTextArea('brief', '用户留言')
//					->keytext('rebrief','')
					->data($message)
//					->buttonSubmit(url('shop/message',array('action'=>'add')))
//					->buttonBack()
					->display();
				break;
			case 'delete':
				$ids = input('ids');
				$ret = $this->message_model->delete_shop_message($ids);
				if ($ret)
				{
					$this->success('操作成功。', url('shop/message'));
				}
				else
				{
					$this->error('操作失败。');
				}
				break;
			default :
				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
				$message = $this->message_model->get_shop_message_list($option);
				$totalCount = $message['count'];

				return $this->fetch();
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
				if(request()->isPost())
				{
					$coupon = $this->coupon_model->create();
					if(!$coupon)
					{
						$this->error($this->coupon_model->getError());
					}
					empty($_REQUEST['max_cnt_enable']) || $rule['max_cnt'] =input('max_cnt',0,'intval');
					empty($_REQUEST['max_cnt_day_enable']) || $rule['max_cnt_day'] =input('max_cnt_day',0,'intval');
					empty($_REQUEST['min_price_enable']) || $rule['min_price'] =input('min_price',0,'intval');
					if(empty($_REQUEST['discount']))
					{
						$this->error('请设置优惠金额');
					}
					else
					{
						$rule['discount'] =input('discount',0,'intval');
					}
					empty($rule) || $coupon['rule'] = json_encode($rule);

					$ret = $this->coupon_model->add_or_edit_coupon($coupon);
					if ($ret)
					{
						$this->success('操作成功。', url('shop/coupon'));
					}
					else
					{
						$this->error('操作失败。');
					}
				}
				else
				{
					$id = input('id');
					if(!empty($id))
					{
						$coupon = $this->coupon_model->get_coupon_by_id($id);
						if(!empty($coupon['rule']))
						{
							$coupon['rule']['max_cnt_enable'] = (empty($coupon['rule']['max_cnt'])?0:1);
							$coupon['rule']['max_cnt_day_enable'] = (empty($coupon['rule']['max_cnt_day'])?0:1);
							$coupon['rule']['min_price_enable'] = (empty($coupon['rule']['min_price'])?0:1);
							$coupon = array_merge($coupon,$coupon['rule']);
						}
					}
					else
					{
						$coupon =array();
					}
					return $this->fetch();
				}
				break;
			case 'delete':
				$ids= input('ids');
				$ret = $this->coupon_model->delete_coupon($ids);
				if ($ret)
				{
					$this->success('操作成功。', url('shop/coupon'));
				}
				else
				{
					$this->error('操作失败。');
				}
				break;
			case 'couponlink':
				$id = input('id');
				$id = $this->coupon_model->encrypt_id($id);
				redirect(url('Udriver/index/get_coupon',array('id'=>$id)));//优惠券id 加密 跳转 具体链接 依业务需求修改
				break;
			default:
				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
				$option['id'] = input('id');
				$coupon = $this->coupon_model->get_coupon_lsit($option);
//				empty($coupon['list'])
//					||
//				array_walk($coupon['list'],
//					function(&$a){
////						$a['link'] = think_encrypt($a['id'],'Coupon',0);
//						$a['link'] = \Think\Crypt\Driver\Des::encrypt($a['id'],md5('Coupon'),0);
//
//						$a['link'] = urlencode(base64_encode($a['link']));
////						var_dump(__file__.' line:'.__line__,$a['link']);exit;
//					});
				$totalCount = $coupon['count'];
				$builder = new AdminListBuilder();
                return $this->fetch();
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
				if(request()->isPost())
				{
					$coupon_id       = input('coupon_id', '', 'intval');
					$uid     = input('uid', '', 'trim');
					if(empty($coupon_id) || !($coupon = $this->coupon_model->get_coupon_by_id($coupon_id)))
						$this->error('请选择一个优惠券');
					if(empty($uid)) $this->error('请选择一个用户');
					$ret =$this->coupon_logic->add_a_coupon_to_user($coupon_id,$uid);
					if($ret)
					{
						$this->success('操作成功。', url('shop/user_coupon'));
					}
					else
					{
						$this->error('操作失败。'.$this->coupon_logic->error_str);
					}
				}
				else
				{
					$all_coupon_select = $this->coupon_model->column('id,title');
					if(empty($all_coupon_select))
					{
						redirect(url('shop/coupon',array('action'=>'add')));
					}
                    return $this->fetch('coupon_add');
				}


				break;
			case 'delete':
				$ids= input('ids');
				$ret = $this->user_coupon_model->delete_user_coupon($ids);
				if ($ret)
				{
					$this->success('操作成功。', url('shop/user_coupon'));
				}
				else
				{
					$this->error('操作失败。');
				}
				break;
			default:
				$option['id'] = input('id');
				$option['page'] = input('page',1);
				$option['r'] = input('r',10);
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
//				var_dump(__file__.' line:'.__line__,$user_coupon['list']);exit;

				$builder = new AdminListBuilder();
                return $this->fetch();
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
					if(empty($ids) || empty($status))
					{
						$this->error('参数错误');
					}
					$ret = $this->product_comment_model->edit_status_product_comment($ids,$status);
					if($ret)
					{
						$this->success('操作成功');
					}
					else
					{
						$this->error('操作失败');
					}
				}
				break;
			case 'show_pic':
				$id = input('id','','intval');
				$ret = $this->product_comment_model->find($id);
				$this->assign('product_comment',$ret);
//				var_dump(__file__.' line:'.__line__,$ret);exit;
				$this->display('Shop@Shop/show_pic');
				break;
			default:
				$option['page'] = input('page','1','intval');
				$option['r'] = input('r','10','intval');
				$product_comment  = $this->product_comment_model->get_product_comment_list($option);
				$builder = new AdminListBuilder();
                return $this->fetch();
				break;
		}

	}

//	上传文件
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $path = ROOT_PATH . 'uploads/picture/';
        $info = $file->move($path);
        if($info){
            // 成功上传后 获取上传信息
            // 输出 jpg
            echo $info->getExtension();
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getSaveName();
            // 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo $info->getFilename();
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }
}
