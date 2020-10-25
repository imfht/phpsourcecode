<?php
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;

use Think\Model;

class ScoreshopController extends AdminController
{

    protected $scoreshopModel;
    protected $scoreshop_configModel;
    protected $scoreshop_categoryModel;

    function _initialize()
    {
        $this->scoreshopModel = D('Scoreshop/Scoreshop');
        $this->configModel = D('Scoreshop/ScoreshopConfig');
        $this->categoryModel = D('Scoreshop/ScoreshopCategory');
        parent::_initialize();
    }

    /**
     * 积分商城配置
     */
    public function Config()
    {
        $builder = new AdminConfigBuilder;
        $data = $builder->handleConfig();

        //初始化数据
        !isset($data['SCORESHOP_SCORE_TYPE'])&&$data['SCORESHOP_SCORE_TYPE']='1';
        !isset($data['SCORESHOP_HOT_SELL_NUM'])&&$data['SCORESHOP_HOT_SELL_NUM']='10';

        //读取数据
        $map = array('status' => array('GT', -1));
        $model = D('Ucenter/Score');
        $score_types = $model->getTypeList($map);
        $score_type_options=array();
        foreach($score_types as $val){
            $score_type_options[$val['id']]=$val['title'];
        }

        $builder->title(L('_SHOP_CONF_'))
            ->keySelect('SCORESHOP_SCORE_TYPE', L('_SHOP_EXCHANGE_POINT_'), '',$score_type_options)
            ->keyInteger('SCORESHOP_HOT_SELL_NUM',L('_SHOP_HOT_SELL_LEVEL_'),L('_SHOP_HOT_SELL_LEVEL_VICE_'))->keyDefault('SCORESHOP_HOT_SELL_NUM',10)

            ->keyText('SCORESHOP_SHOW_TITLE', L('_TITLE_NAME_'), '')->keyDefault('SCORESHOP_SHOW_TITLE','热门商品')
            ->keyText('SCORESHOP_SHOW_COUNT', '显示积分商品的个数', '只有在网站首页模块中启用了积分商城模块之后才会显示')->keyDefault('SCORESHOP_SHOW_COUNT',4)
            ->keyRadio('SCORESHOP_SHOW_TYPE', '推荐的范围', '', array('1' => '新品', '0' => L('_EVERYTHING_')))->keyDefault('SCORESHOP_SHOW_TYPE',0)
            ->keyRadio('SCORESHOP_SHOW_ORDER_FIELD', L('_SORT_VALUE_'), L('_TIP_SORT_VALUE_'), array('sell_num' => '售出数量', 'createtime' => L('_DELIVER_TIME_'), 'changetime' => L('_UPDATE_TIME_'),))->keyDefault('SCORESHOP_SHOW_ORDER_FIELD','sell_num')
            ->keyRadio('SCORESHOP_SHOW_ORDER_TYPE', L('_SORT_TYPE_'), L('_TIP_SORT_TYPE_'), array('desc' => L('_COUNTER_'), 'asc' => L('_DIRECT_')))->keyDefault('SCORESHOP_SHOW_ORDER_TYPE','desc')
            ->keyText('SCORESHOP_SHOW_CACHE_TIME', L('_CACHE_TIME_'),L('_TIP_CACHE_TIME_'))->keyDefault('SCORESHOP_SHOW_CACHE_TIME','600')

            ->group(L('_BASIC_CONF_'),'SCORESHOP_SCORE_TYPE,SCORESHOP_HOT_SELL_NUM')
            ->group(L('_HOME_SHOW_CONF_'), 'SCORESHOP_SHOW_TITLE,SCORESHOP_SHOW_TYPE,SCORESHOP_SHOW_COUNT,SCORESHOP_SHOW_TITLE,SCORESHOP_SHOW_ORDER_TYPE,SCORESHOP_SHOW_ORDER_FIELD,SCORESHOP_SHOW_CACHE_TIME')
            ->groupLocalComment(L('_LOCAL_COMMENT_CONF_'),'goodsDetail')
            ->data($data)
            ->buttonSubmit()
            ->buttonBack()
            ->display();
    }
    /**
     * 接口配置
     * @return [type] [description]
     */
    public function api_config(){
        if ($_SERVER['HTTPS'] != "on") {
            $is_https = 'http://';
        }else{
            $is_https = 'https://';
        }
        
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();
        $builder->title('Api设置')
        //物流API配置
        ->keyText('SCORESHOP_DELIVERY_EBUSINESS','Ebusiness','请到快递鸟官网申请http://kdniao.com/reg')
        ->keyText('SCORESHOP_DELIVERY_APPKEY','AppKey','电商加密私钥，快递鸟提供，注意保管，不要泄漏')
        ->group('物流查询配置','SCORESHOP_DELIVERY_EBUSINESS,SCORESHOP_DELIVERY_APPKEY')
        
        ->data($data)
        ->buttonSubmit('', '保存')
        ->display();
    }


    /**
     * 商品分类
     * @author 大蒙<59262424@qq.com>
     */
    public function Category()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $tree = $this->categoryModel->getTree(0, 'id,title,sort,pid,status');
        $builder->title(L('_SHOP_CATEGORY_MANAGE_'))
            ->buttonNew(U('Scoreshop/add'))
            ->data($tree)
            ->display();
    }

    /**
     * 分类添加
     * @param int $id
     * @param int $pid
     * @author 大蒙<59262424@qq.com>
     */
    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            $title=$id?L('_EDIT_'):L('_ADD_');
            if ($this->categoryModel->editData()) {
                $this->success($title.L('_SUCCESS_').L('_PERIOD_'), U('Scoreshop/Category'));
            } else {
                $this->error($title.L('_FAIL_').L('_EXCLAMATION_').$this->categoryModel->getError());
            }
        } else {
            $builder = new AdminConfigBuilder();
            $categorys = $this->categoryModel->select();
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }
            if ($id != 0) {
                $category = $this->categoryModel->find($id);
            } else {
                $category = array('pid' => $pid, 'status' => 1);
                $father_category_pid=$this->categoryModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error(L('_ERROR_CATEGORY_HIERARCHY_').L('_EXCLAMATION_'));
                }
            }
            $builder->title(L('添加分类'))
                ->keyId()
                ->keyText('title', L('_TITLE_'))
                ->keySelect('pid', L('分类'), L('选择分类'), array('0' => L('顶级分类')) + $opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($category)
                ->buttonSubmit(U('Scoreshop/add'))
                ->buttonBack()
                ->display();
        }

    }

    /**
     * 分类回收站
     * @param int $page
     * @param int $r
     * @author 大蒙<59262424@qq.com>
     */
    public function categoryTrash($page = 1, $r = 20,$model='')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $list = $this->categoryModel->where($map)->page($page, $r)->select();
        $totalCount = $this->categoryModel->where($map)->count();

        //显示页面

        $builder->title(L('_SHOP_CATEGORY_TRASH_'))
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear('ScoreshopCategory')
            ->keyId()->keyText('title', L('_TITLE_'))->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置商品分类状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 大蒙<59262424@qq.com>
     */
    public function setStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('scoreshopCategory', $ids, $status);
    }

    /**
     * 设置商品状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 大蒙<59262424@qq.com>
     */
    public function setGoodsStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('scoreshop', $ids, $status);
    }

    /**
     * 商品列表
     * @param int $page
     * @param int $r
     * @author 大蒙<59262424@qq.com>
     */
    public function goodsList($page = 1, $r = 20)
    {
        $map['status'] = array('egt', 0);
        $goodsList = $this->scoreshopModel->where($map)->order('createtime desc')->page($page, $r)->select();
        $totalCount = $this->scoreshopModel->where($map)->count();
        $builder = new AdminListBuilder();
        $builder->title(L('_GOODS_LIST_'));
        $builder->meta_title = L('_GOODS_LIST_');
        foreach ($goodsList as &$val) {
            $category = $this->categoryModel->where('id=' . $val['category_id'])->getField('title');
            $val['category'] = $category;
            unset($category);
            $val['is_new'] = ($val['is_new'] == 0) ? L('_YES_') : L('_NOT_');
        }
        unset($val);

        $builder
            ->buttonNew(U('Scoreshop/goodsEdit'))
            ->buttonDelete(U('setGoodsStatus'))
            ->setStatusUrl(U('setGoodsStatus'));

        $builder
            ->keyId()
            ->keyText('goods_name', L('_GOODS_NAME_'))
            ->keyText('category', L('_GOODS_CATEGORY_'))
            ->keyText('goods_introduct', L('_GOODS_SLOGAN_'))
            ->keyText('price', L('_GOODS_PRICE_'))
            ->keyText('quantity', L('_GOODS_MARGIN_'))
            ->keyText('sell_num', L('_GOODS_SOLD_'))
            ->keyLink('is_new', L('_GOODS_NEW_'), 'Scoreshop/setNew?id=###')
            ->keyStatus('status', L('_GOODS_STATUS_'))
            ->keyUpdateTime('changetime')
            ->keyCreateTime('createtime')
            ->keyDoActionEdit('Scoreshop/goodsEdit?id=###')
            ->keyDoAction('Scoreshop/sku_table?id=###','规格');

        $builder->data($goodsList);
        $builder->pagination($totalCount, $r);
        $builder->display();
    }

    public function sku_table(){
        if(IS_POST){
            $product['id'] = I('id',0,'intval');
            empty($product['id']) && $this->error('缺少商品id');
            $table = I('table','','text');
            $info = I('info','','text');

            $product['sku_table'] = array('table'=>$table,'info'=>$info);
            $product['sku_table'] = json_encode($product['sku_table']);

            $res = $this->scoreshopModel->editData($product);
            if ($res){
                $this->success(L('_SUCCESS_SETTING_').L('_EXCLAMATION_'));
            }else{
                $this->error(L('_ERROR_SETTING_').L('_EXCLAMATION_'));
            }
        }else{

            $id = I('id',0,'intval');
            if(empty($id)){
                $this->error('请选择一个商品','',2);
            }

            $res = $this->scoreshopModel->getData($id);
            $builder=new AdminConfigBuilder();
            $builder->title('SKU');
            
            $this->assign('product', $res);
            $this->display('Scoreshop@Admin/sku_table');
        }
    }

    public function del_sku_table(){

        if(IS_POST){
            $product['id'] = I('id',0,'intval');
            empty($product['id']) && $this->error('缺少商品id');

            $product['sku_table'] = '';

            $res = $this->scoreshopModel->editData($product);
            if ($res){
                $this->success(L('_SUCCESS_SETTING_').L('_EXCLAMATION_'));
            }else{
                $this->error(L('_ERROR_SETTING_').L('_EXCLAMATION_'));
            }
        }
    }

    /**
     * 设置是否为新品
     * @param int $id
     * @author 大蒙<59262424@qq.com>
     */
    public function setNew($id = 0)
    {
        if ($id == 0) {
            $this->error(L('_GOODS_SELECT_'));
        }
        $is_new = intval(!$this->scoreshopModel->where(array('id' => $id))->getField('is_new'));
        $rs = $this->scoreshopModel->where(array('id' => $id))->setField(array('is_new' => $is_new, 'changetime' => time()));
        if ($rs) {
            $this->success(L('_SUCCESS_SETTING_').L('_EXCLAMATION_'));
        } else {
            $this->error(L('_ERROR_SETTING_').L('_EXCLAMATION_'));
        }
    }

    /**
     * 商品回收站
     * @param int $page
     * @param int $r
     * @author 大蒙<59262424@qq.com>
     */
    public function goodsTrash($page = 1, $r = 10,$model='')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $goodsList = $this->scoreshopModel->where($map)->order('changetime desc')->page($page, $r)->select();
        $totalCount = $this->scoreshopModel->where($map)->count();

        //显示页面

        $builder->title(L('_GOODS_TRASH_'))
            ->setStatusUrl(U('setGoodsStatus'))->buttonRestore()->buttonClear('Scoreshop/Scoreshop')
            ->keyId()->keyLink('goods_name',L('_TITLE_'), 'Scoreshop/goodsEdit?id=###')->keyCreateTime()->keyStatus()
            ->data($goodsList)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 商品编辑
     * @param int $id
     * @param $goods_name
     * @param $goods_img
     * @param $goods_introduct
     * @param $goods_detail
     * @param $price
     * @param $quantity
     * @param $status
     * @param $category_id
     * @param $is_new
     * @param $sell_num
     * @author 大蒙<59262424@qq.com>
     */
    public function goodsEdit()
    {
        $id = I('id',0,'intval');
        //判断是添加||编辑
        $isEdit = $id ? 1 : 0;
        if (IS_POST) {
            //获取传递过来的数据
            $goods_name = I('goods_name','','text');
            $goods_img = I('goods_img','','text');
            $goods_introduct = I('goods_introduct','','text');
            $goods_detail = I('goods_detail','','html');
            $price = I('price',0,'intval');
            $quantity = I('quantity',0,'intval');
            $status = I('status',0,'intval');
            $category_id = I('category_id',0,'intval');
            $is_new = I('is_new',0,'intval');
            $sell_num = I('sell_num',0,'intval');

            if ($goods_name == '' || $goods_name == null) {
                $this->error(L('_GOODS_INPUT_NAME_'));
            }
            if (!is_numeric($goods_img)) {
                $this->error(L('_GOODS_UPLOAD_BRAND_'));
            }
            if ($goods_introduct == '' || $goods_introduct == null) {
                if ($goods_detail == '' || $goods_detail == null) {
                    $this->error(L('_GOODS_INPUT_SLOGAN_'));
                } else {
                    $goods_introduct = substr($goods_detail, 0, 25);
                }
            }
            if (!(is_numeric($price) && $price >= 0)) {
                $this->error(L('_GOODS_INPUT_PRICE_'));
            }
            if (!(is_numeric($quantity) && $quantity >= 0)) {
                $this->error(L('_GOODS_INPUT_COUNT_REMIND_'));
            }
            if (!(is_numeric($sell_num) && $sell_num >= 0)) {
                $this->error(L('_GOODS_INPUT_COUNT_'));
            }
            $goods['goods_name'] = $goods_name;
            $goods['goods_img'] = $goods_img;
            $goods['goods_introduct'] = $goods_introduct;
            $goods['goods_detail'] = $goods_detail;
            $goods['price'] = $price;
            $goods['quantity'] = $quantity;
            $goods['status'] = $status;
            $goods['category_id'] = $category_id;
            $goods['is_new'] = $is_new;
            $goods['sell_num'] = $sell_num;
            $goods['changetime'] = time();
            if ($isEdit) {
                $rs = $this->scoreshopModel->where('id=' . $id)->save($goods);
            } else {
                //商品名存在验证
                $map['status'] = array('egt', 0);
                $map['goods_name'] = $goods_name;
                if ($this->scoreshopModel->where($map)->count()) {
                    $this->error(L('_ERROR_GOODS_SAME_NAME_'));
                }

                $goods['createtime'] = time();
                $rs = $this->scoreshopModel->add($goods);
            }
            if ($rs) {
                $this->success($isEdit ? L('_SUCCESS_ADD_') : L('_SUCCESS_EDIT_'), U('Scoreshop/goodsList'));
            } else {
                $this->error($isEdit ? L('_FAIL_ADD_') : L('fail_Edit'));
            }
        } else {
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? L('_GOODS_EDIT_') : L('_GOODS_ADD_'));
            $builder->meta_title = $isEdit ? L('_GOODS_EDIT_') : L('_GOODS_ADD_');

            //获取分类列表
            $category_map['status'] = array('egt', 0);
            $goods_category_list = $this->categoryModel->where($category_id)->order('pid desc')->select();
            $options = array_combine(array_column($goods_category_list, 'id'), array_column($goods_category_list, 'title'));
            //编辑器配置
            $edit_config = "
                'source',
                '|',
                'bold',
                'italic',
                'underline',
                'fontsize',
                'forecolor',
                '|',
                'blockquote',
                'justifyleft',
                'justifycenter',
                'justifyright',
                'fontfamily',
                '|',
                'map',
                'emotion',
                'insertvideo',
                'imagecenter',
                'simpleupload',
                'insertimage',
                'fullscreen'
            ";
            $builder->keyId()
                    ->keyText('goods_name', L('_GOODS_NAME_'))
                    ->keySingleImage('goods_img', L('_GOODS_BRAND_'))
                    ->keySelect('category_id',L('_GOODS_CATEGORY_'), '', $options)
                    ->keyText('goods_introduct', L('_GOODS_SLOGAN_'))
                    ->keyEditor('goods_detail', L('_GOODS_DETAIL_'),'',$edit_config,array('width' => '800px', 'height' => '400px'))
                    ->keyText('price', L('_GOODS_PRICE_'))
                    ->keyText('quantity', L('_GOODS_MARGIN_'))
                    ->keyText('sell_num', L('_GOODS_SOLD_'))
                    ->keyBool('is_new', L('_GOODS_NEW_'))
                    ->keyStatus('status', L('_GOODS_STATUS_'));

            if ($isEdit) {
                $goods = $this->scoreshopModel->where('id=' . $id)->find();
                $builder->data($goods);
                $builder->buttonSubmit(U('Scoreshop/goodsEdit'));
                $builder->buttonBack();
                $builder->display();
            } else {
                $goods['status'] = 1;
                $builder->buttonSubmit(U('Scoreshop/goodsEdit'));
                $builder->buttonBack();
                $builder->data($goods);
                $builder->display();
            }
        }
    }
    /**
     * 交易订单详情
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function buy_info($id){

        $builder = new AdminConfigBuilder();
        $builder->title('订单详情');
        
        $data = M('scoreshop_buy')->where('id='.$id)->find();
        //获取商品信息
        $goods_info = M('scoreshop')->where('id='.$data['goods_id'])->find();
        $data['goods_name'] = $goods_info['goods_name'];
        $data['goods_img'] = '<img src='.get_cover($goods_info['goods_img'])['path'].' />';
        
        //获取地址信息
        $address_info = M('scoreshop_address')->where('id='.$data['address_id'])->find();
        $data['address_name'] = $address_info['name'];
        $data['address_address'] = $address_info['address'];
        $data['address_phone'] = $address_info['phone'];
        //获取物流信息
        $data['logistic'] = json_decode($data['logistic'],true);
        $data['ShipperValue'] = $data['logistic']['ShipperName'].','.$data['logistic']['ShipperCode'];
        $data['LogisticCode'] = $data['logistic']['LogisticCode'];
        $path = APP_PATH  . 'Scoreshop/Conf/logistic.php';
        $logisticCom = load_config($path);
        $logistic = array();
        foreach($logisticCom as $v){
            $logistic[$v['title'].','.$v['code']]=$v['title'];
        }

        $builder->keyId('id','订单ID')
                ->keyReadOnlyHtml('goods_name', L('_GOODS_NAME_'))
                ->keyReadOnlyHtml('goods_img',L('_GOODS_BRAND_'))
                
                ->keyReadOnlyHtml('address_name',L('_NAME_'))
                ->keyReadOnlyHtml('address_address',L('_RECEIVING_ADDRESS_'))
                ->keyReadOnlyHtml('address_phone',L('_PHONE_NUMBER_'))

                ->keySelect('ShipperValue','物流公司','',$logistic)
                //->keyReadOnlyHtml('ShipperName','物流公司')
                ->keyText('LogisticCode','物流单号')
        ;
        $builder->group('商品信息','id,goods_name,goods_img')
                ->group('收货信息','address_name,address_address,address_phone')
                ->group('物流信息','ShipperValue,LogisticCode')
        ;

        $builder->buttonSubmit(U('Scoreshop/editLogistic?id=###'));
        $builder->buttonBack();
        $builder->data($data);
        $builder->display();
    }
    
    /**
     * 已完成交易列表
     * @param int $page
     * @param int $r
     * @author 大蒙<59262424@qq.com>
     */
    public function goodsBuySuccess($page = 1, $r = 20)
    {
        //读取列表
        $map = array('status' => 2);
        $model = M('scoreshop_buy');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$val) {
            $val['goods_name'] = $this->scoreshopModel->where('id=' . $val['goods_id'])->getField('goods_name');
            $address = D('scoreshop_address')->find($val['address_id']);
            $val['name'] = $address['name'];
            $val['address'] = $address['address'];
            $val['zipcode'] = $address['zipcode'];
            $val['phone'] = $address['phone'];
        }
        unset($val);
        //显示页面
        $builder = new AdminListBuilder();

        $builder->title(L('_TRADE_ACCOMPLISHED_'));
        $builder->meta_title = L('_TRADE_ACCOMPLISHED_');

        $builder
            ->buttonDisable(U('setGoodsBuyStatus'), L('_DELIVER_CANCEL_'))
            ->keyId()
            ->keyText('goods_name', L('_GOODS_NAME_'))
            ->keyUid()
            ->keyText('name', L('_RECEIVER_NAME_'))
            ->keyText('address', L('_RECEIVER_ADDRESS_'))
            ->keyText('zipcode', L('_POST_CODE_'))
            ->keyText('phone', L('_PHONE_NUMBER_'))
            ->keyCreateTime('createtime', L('_BUY_TIME_'))
            ->keyTime('gettime', L('_TRADE_ACCOMPLISH_TIME_'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 待发货交易列表
     * 
     * @author 大蒙<59262424@qq.com>
     */
    public function verify($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 0);
        $model = M('scoreshop_buy');
        $list = $model->where($map)->page($page, $r)->order('createtime desc')->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$val) {
            $val['goods_name'] = $this->scoreshopModel->where('id=' . $val['goods_id'])->getField('goods_name');
            $address = D('scoreshop_address')->find($val['address_id']);
            $val['name'] = $address['name'];
            $val['address'] = $address['address'];
            $val['phone'] = $address['phone'];
        }
        unset($val);
        //显示页面
        $builder = new AdminListBuilder();

        $builder->title(L('_GOODS_WAIT_DELIVER_'));
        $builder->meta_title = L('_GOODS_WAIT_DELIVER_');

        $builder
            
            ->keyId()
            ->keyText('goods_name', L('_GOODS_NAME_'))
            ->keyUid()
            ->keyText('name', L('_RECEIVER_NAME_'))
            ->keyText('address', L('_RECEIVER_ADDRESS_'))
            ->keyText('phone', L('_PHONE_NUMBER_'))
            ->keyCreateTime('createtime', L('_BUY_TIME_'))
            
            ->keyDoAction('Scoreshop/buy_info?id=###','订单详情')
            ->keyDoActionModalPopup('Scoreshop/editLogistic?id=###','发货')
            ->keyDoActionModalPopup('Scoreshop/logistic?id=###','物流查询','',array('data-title'=>'物流查询'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 已发货交易列表
     * 
     * @author 大蒙<59262424@qq.com>
     */
    public function delivered($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 1);
        $model = M('scoreshop_buy');
        $list = $model->where($map)->page($page, $r)->order('createtime desc')->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$val) {
            $val['goods_name'] = $this->scoreshopModel->where('id=' . $val['goods_id'])->getField('goods_name');
            $address = D('scoreshop_address')->find($val['address_id']);
            $val['name'] = $address['name'];
            $val['address'] = $address['address'];
            $val['phone'] = $address['phone'];
        }
        unset($val);
        //显示页面
        $builder = new AdminListBuilder();

        $builder->title('已发货商品');


        $builder
            ->buttonSetStatus(U('setGoodsBuyStatus'), 2,L('_ORDER_ACCOMPLISH_'),array('class'=>'btn ajax-post btn-success'))
            ->keyId()
            ->keyText('goods_name', L('_GOODS_NAME_'))
            ->keyUid()
            ->keyText('name', L('_RECEIVER_NAME_'))
            ->keyText('address', L('_RECEIVER_ADDRESS_'))
            ->keyText('phone', L('_PHONE_NUMBER_'))
            ->keyCreateTime('createtime', L('_BUY_TIME_'))
            
            ->keyDoAction('Scoreshop/buy_info?id=###','订单详情')
            ->keyDoActionModalPopup('Scoreshop/editLogistic?id=###','发货')
            ->keyDoActionModalPopup('Scoreshop/logistic?id=###','物流查询','',array('data-title'=>'物流查询'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 物流发货
     * @return [type] [description]
     */
    public function editLogistic(){
        
        if(IS_POST){
            //发货
            $id = I('id');
            $ShipperValue = I('ShipperValue');//快递公司名称及编号，以,分隔
            $LogisticCode = I('LogisticCode');//物流单号

            $ShipperValue = explode(',',$ShipperValue);
            
            $delivery_info = array(
                'ShipperName' =>$ShipperValue[0],
                'ShipperCode'=>$ShipperValue[1],
                'LogisticCode'=>$LogisticCode,
            );
            $delivery_info=json_encode($delivery_info);//转成json
            //处理数据，更新为已发货
            $data['id'] = $id;
            $data['logistic'] = $delivery_info;
            $ret = M('scoreshop_buy')->save($data);
            if($ret){
                $this->setGoodsBuyStatus([$data['id']], 1);
                $this->success('操作成功',U('Scoreshop/verify'));
            }else{
                $this->error('操作失败,');
            }
        }else{
            $id = I('get.id',0,'intval');
            //获取快递配置
            $path = APP_PATH  . 'Scoreshop/Conf/logistic.php';
            $logisticCom = load_config($path);
            $this->assign('delivery',$logisticCom);
            //获取订单数据
            $order = M('scoreshop_buy')->where('id='.$id)->find();

            $logistic_info = json_decode($order['logistic'],true);
            $order['logistic'] = $logistic_info;
            $this->assign('order',$order);
            $this->display('Scoreshop@Admin/editLogistic');
        }
    }

    /**
     * 物流查询
     * @return [type] [description]
     */
    public function logistic(){

        $id = I('id',0,'intval');
        empty($id) && $this->error('订单参数错误',1);
        $order = M('scoreshop_buy')->where('id='.$id)->find();
        $delivery_info = json_decode($order['logistic'],true);
        $delivery_info['id'] = $order['id'];
        $delivery_info['order_no'] = $order['order_no'];
        //组装获取物流信息的json数据
        $requesData=array(
            'OrderCode'=>$order['order_no'],
            'ShipperCode'=>$delivery_info['ShipperCode'],
            'LogisticCode'=>$delivery_info['LogisticCode']
        );
        $requesData=json_encode($requesData);//转成json
        //获取物流信息
        $result = D('Scoreshop/ScoreshopLogistic')->getOrderTracesByJson($requesData);
        $result = json_decode($result,true);
        $result['Traces'] = array_reverse($result['Traces']);//反转数组


        $this->assign('delivery_info',$delivery_info);
        $this->assign('result',$result);
        $this->display('Scoreshop@Public/logistic');
    }

    public function setGoodsBuyStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        if ($status == 1) {
            $gettime = time();
            foreach ($ids as $id) {
                D('scoreshop_buy')->where('id=' . $id)->setField('gettime', $gettime);
                $content = D('scoreshop_buy')->find($id);
                $message = L('_MESSAGE_TRADE_ACCOMPLISH_');
                D('Message')->sendMessageWithoutCheckSelf(
                    $content['uid'], 
                    L('_MESSAGE_DELIVER_'),
                    $message,  
                    'Scoreshop/Index/myGoods', 
                    array('status' => '1'), 
                    1,
                    'Scoreshop',
                    'Common_system'
                );

                //商城记录
                $goods_name = D('scoreshop')->field('goods_name')->find($content['goods_id']);
                $shop_log['message'] = L('_MESSAGE_AT_') . time_format($gettime) . '[' . is_login() . ']' . get_nickname( is_login()) . L('_MESSAGE_DELIVER_USER_').'[' . $content['uid'] . ']' . get_nickname( $content['uid']) . L('_MESSAGE_GOODS_BOUGHT_').L('_COLON_').'<a href="index.php?s=/Scoreshop/Index/goodsDetail/id/' . $content['goods_id'] . '.html" target="_black">' . $goods_name['goods_name'] . '</a>';
                $shop_log['uid'] = is_login();
                $shop_log['create_time'] = $gettime;
                D('scoreshop_log')->add($shop_log);
            }
        }
        $builder->doSetStatus('scoreshop_buy', $ids, $status);
    }

    /**
     * 商城日志
     * @param int $page
     * @param int $r
     * @author 大蒙<59262424@qq.com>
     */
    public function Log($page = 1, $r = 20)
    {
        //读取列表
        $model = M('scoreshop_log');
        $list = $model->page($page, $r)->order('create_time desc')->select();
        $totalCount = $model->count();
        //显示页面
        $builder = new AdminListBuilder();

        $builder->title(L('_SHOP_MESSAGE_RECORD_'));
        $builder->meta_title = L('_SHOP_MESSAGE_RECORD_');

        $builder->keyId()->keyText('message', L('_MESSAGE_'))->keyUid()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

}
