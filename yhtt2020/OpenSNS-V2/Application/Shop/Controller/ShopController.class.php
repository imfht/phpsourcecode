<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-6-18
 * Time: 上午10:07
 * @author 郑钟良<zzl@ourstu.com>
 */
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;

use Think\Model;

/**
 * Class ShopController
 * @package Admin\controller
 * @郑钟良
 */
class ShopController extends AdminController
{

    protected $shopModel;
    protected $shop_configModel;
    protected $shop_categoryModel;

    function _initialize()
    {
        $this->shopModel = D('Shop/Shop');
        $this->shop_configModel = D('Shop/ShopConfig');
        $this->shop_categoryModel = D('Shop/ShopCategory');
        parent::_initialize();
    }

    /**商品分类
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function shopCategory()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $tree = $this->shop_categoryModel->getTree(0, 'id,title,sort,pid,status');

        $builder->title(L('_SHOP_CATEGORY_MANAGE_'))
            ->buttonNew(U('Shop/add'))
            ->data($tree)
            ->display();
    }

    /**分类添加
     * @param int $id
     * @param int $pid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            $title=$id?L('_EDIT_'):L('_ADD_');
            if ($this->shop_categoryModel->editData()) {
                $this->success($title.L('_SUCCESS_').L('_PERIOD_'), U('Shop/shopCategory'));
            } else {
                $this->error($title.L('_FAIL_').L('_EXCLAMATION_').$this->shop_categoryModel->getError());
            }
        } else {
            $builder = new AdminConfigBuilder();
            $categorys = $this->shop_categoryModel->select();
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }
            if ($id != 0) {
                $category = $this->shop_categoryModel->find($id);
            } else {
                $category = array('pid' => $pid, 'status' => 1);
                $father_category_pid=$this->shop_categoryModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error(L('_ERROR_CATEGORY_HIERARCHY_').L('_EXCLAMATION_'));
                }
            }
            $builder->title(L('_CATEGORY_ADD_'))->keyId()->keyText('title', L('_TITLE_'))->keySelect('pid', L('_CATEGORY_FATHER_'), L('_CATEGORY_FATHER_SELECT_'), array('0' => L('_CATEGORY_TOP_')) + $opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($category)
                ->buttonSubmit(U('Shop/add'))->buttonBack()->display();
        }

    }

    /**分类回收站
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function categoryTrash($page = 1, $r = 20,$model='')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $list = $this->shop_categoryModel->where($map)->page($page, $r)->select();
        $totalCount = $this->shop_categoryModel->where($map)->count();

        //显示页面

        $builder->title(L('_SHOP_CATEGORY_TRASH_'))
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear('ShopCategory')
            ->keyId()->keyText('title', L('_TITLE_'))->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 设置商品分类状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('shopCategory', $ids, $status);
    }

    /**
     * 设置商品状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setGoodsStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('shop', $ids, $status);
    }

    /**商品列表
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsList($page = 1, $r = 20)
    {
        $map['status'] = array('egt', 0);
        $goodsList = $this->shopModel->where($map)->order('createtime desc')->page($page, $r)->select();
        $totalCount = $this->shopModel->where($map)->count();
        $builder = new AdminListBuilder();
        $builder->title(L('_GOODS_LIST_'));
        $builder->meta_title = L('_GOODS_LIST_');
        foreach ($goodsList as &$val) {
            $category = $this->shop_categoryModel->where('id=' . $val['category_id'])->getField('title');
            $val['category'] = $category;
            unset($category);
            $val['is_new'] = ($val['is_new'] == 0) ? L('_YES_') : L('_NOT_');
        }
        unset($val);
        $builder->buttonNew(U('Shop/goodsEdit'))->buttonDelete(U('setGoodsStatus'))->setStatusUrl(U('setGoodsStatus'));
        $builder->keyId()->keyText('goods_name', L('_GOODS_NAME_'))->keyText('category', L('_GOODS_CATEGORY_'))->keyText('goods_introduct', L('_GOODS_SLOGAN_'))
            ->keyText('money_need', L('_GOODS_PRICE_'))->keyText('goods_num', L('_GOODS_MARGIN_'))->keyText('sell_num', L('_GOODS_SOLD_'))->keyLink('is_new', L('_GOODS_NEW_'), 'Shop/setNew?id=###')->keyStatus('status', L('_GOODS_STATUS_'))->keyUpdateTime('changetime')->keyCreateTime('createtime')->keyDoActionEdit('Shop/goodsEdit?id=###');
        $builder->data($goodsList);
        $builder->pagination($totalCount, $r);
        $builder->display();
    }

    /**设置是否为新品
     * @param int $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setNew($id = 0)
    {
        if ($id == 0) {
            $this->error(L('_GOODS_SELECT_'));
        }
        $is_new = intval(!$this->shopModel->where(array('id' => $id))->getField('is_new'));
        $rs = $this->shopModel->where(array('id' => $id))->setField(array('is_new' => $is_new, 'changetime' => time()));
        if ($rs) {
            $this->success(L('_SUCCESS_SETTING_').L('_EXCLAMATION_'));
        } else {
            $this->error(L('_ERROR_SETTING_').L('_EXCLAMATION_'));
        }
    }

    /**商品回收站
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsTrash($page = 1, $r = 10,$model='')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $goodsList = $this->shopModel->where($map)->order('changetime desc')->page($page, $r)->select();
        $totalCount = $this->shopModel->where($map)->count();

        //显示页面

        $builder->title(L('_GOODS_TRASH_'))
            ->setStatusUrl(U('setGoodsStatus'))->buttonRestore()->buttonClear('Shop/Shop')
            ->keyId()->keyLink('goods_name',L('_TITLE_'), 'Shop/goodsEdit?id=###')->keyCreateTime()->keyStatus()
            ->data($goodsList)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * @param int $id
     * @param $goods_name
     * @param $goods_ico
     * @param $goods_introduct
     * @param $goods_detail
     * @param $money_need
     * @param $goods_num
     * @param $status
     * @param $category_id
     * @param $is_new
     * @param $sell_num
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsEdit($id = 0, $goods_name = '', $goods_ico = '', $goods_introduct = '', $goods_detail = '', $money_need = '', $goods_num = '', $status = '', $category_id = 0, $is_new = 0, $sell_num = 0)
    {
        $isEdit = $id ? 1 : 0;
        if (IS_POST) {
            if ($goods_name == '' || $goods_name == null) {
                $this->error(L('_GOODS_INPUT_NAME_'));
            }
            if (!is_numeric($goods_ico)) {
                $this->error(L('_GOODS_UPLOAD_BRAND_'));
            }
            if ($goods_introduct == '' || $goods_introduct == null) {
                if ($goods_detail == '' || $goods_detail == null) {
                    $this->error(L('_GOODS_INPUT_SLOGAN_'));
                } else {
                    $goods_introduct = substr($goods_detail, 0, 25);
                }
            }
            if (!(is_numeric($money_need) && $money_need >= 0)) {
                $this->error(L('_GOODS_INPUT_PRICE_'));
            }
            if (!(is_numeric($goods_num) && $goods_num >= 0)) {
                $this->error(L('_GOODS_INPUT_COUNT_REMIND_'));
            }
            if (!(is_numeric($sell_num) && $sell_num >= 0)) {
                $this->error(L('_GOODS_INPUT_COUNT_'));
            }
            $goods['goods_name'] = $goods_name;
            $goods['goods_ico'] = $goods_ico;
            $goods['goods_introduct'] = $goods_introduct;
            $goods['goods_detail'] = $goods_detail;
            $goods['money_need'] = $money_need;
            $goods['goods_num'] = $goods_num;
            $goods['status'] = $status;
            $goods['category_id'] = $category_id;
            $goods['is_new'] = $is_new;
            $goods['sell_num'] = $sell_num;
            $goods['changetime'] = time();
            if ($isEdit) {
                $rs = $this->shopModel->where('id=' . $id)->save($goods);
            } else {
                //商品名存在验证
                $map['status'] = array('egt', 0);
                $map['goods_name'] = $goods_name;
                if ($this->shopModel->where($map)->count()) {
                    $this->error(L('_ERROR_GOODS_SAME_NAME_'));
                }

                $goods['createtime'] = time();
                $rs = $this->shopModel->add($goods);
            }
            if ($rs) {
                $this->success($isEdit ? L('_SUCCESS_ADD_') : L('_SUCCESS_EDIT_'), U('Shop/goodsList'));
            } else {
                $this->error($isEdit ? L('_FAIL_ADD_') : L('fail_Edit'));
            }
        } else {
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? L('_GOODS_EDIT_') : L('_GOODS_ADD_'));
            $builder->meta_title = $isEdit ? L('_GOODS_EDIT_') : L('_GOODS_ADD_');

            //获取分类列表
            $config = get_editor_config('SHOP_ADMIN_ADD', '', 1) ;
            $category_map['status'] = array('egt', 0);
            $goods_category_list = $this->shop_categoryModel->where($category_id)->order('pid desc')->select();
            $options = array_combine(array_column($goods_category_list, 'id'), array_column($goods_category_list, 'title'));
            $builder->keyId()->keyText('goods_name', L('_GOODS_NAME_'))
                ->keySingleImage('goods_ico', L('_GOODS_BRAND_'))
                ->keySelect('category_id',L('_GOODS_CATEGORY_'), '', $options)
                ->keyText('goods_introduct', L('_GOODS_SLOGAN_'))
                ->keyEditor('goods_detail', L('_GOODS_DETAIL_'),'',$config)
                ->keyText('money_need', L('_GOODS_PRICE_'))->keyText('goods_num', L('_GOODS_MARGIN_'))->keyText('sell_num', L('_GOODS_SOLD_'))->keyBool('is_new', L('_GOODS_NEW_'))->keyStatus('status', L('_GOODS_STATUS_'));
            if ($isEdit) {
                $goods = $this->shopModel->where('id=' . $id)->find();
                $builder->data($goods);
                $builder->buttonSubmit(U('Shop/goodsEdit'));
                $builder->buttonBack();
                $builder->display();
            } else {
                $goods['status'] = 1;
                $builder->buttonSubmit(U('Shop/goodsEdit'));
                $builder->buttonBack();
                $builder->data($goods);
                $builder->display();
            }
        }
    }

    public function shopConfig()
    {
        $builder = new AdminConfigBuilder;
        $data = $builder->handleConfig();

        //初始化数据
        !isset($data['SHOP_SCORE_TYPE'])&&$data['SHOP_SCORE_TYPE']='1';
        !isset($data['SHOP_HOT_SELL_NUM'])&&$data['SHOP_HOT_SELL_NUM']='10';

        //读取数据
        $map = array('status' => array('GT', -1));
        $model = D('Ucenter/Score');
        $score_types = $model->getTypeList($map);
        $score_type_options=array();
        foreach($score_types as $val){
            $score_type_options[$val['id']]=$val['title'];
        }

        $builder->title(L('_SHOP_CONF_'))
            ->keySelect('SHOP_SCORE_TYPE', L('_SHOP_EXCHANGE_POINT_'), '',$score_type_options)
            ->keyInteger('SHOP_HOT_SELL_NUM',L('_SHOP_HOT_SELL_LEVEL_'),L('_SHOP_HOT_SELL_LEVEL_VICE_'))->keyDefault('SHOP_HOT_SELL_NUM',10)

            ->keyText('SHOP_SHOW_TITLE', L('_TITLE_NAME_'), L('_HOME_BLOCK_TITLE_'))->keyDefault('SHOP_SHOW_TITLE','热门商品')
            ->keyText('SHOP_SHOW_COUNT', '显示积分商品的个数', '只有在网站首页模块中启用了积分商城模块之后才会显示')->keyDefault('SHOP_SHOW_COUNT',4)
            ->keyRadio('SHOP_SHOW_TYPE', '推荐的范围', '', array('1' => '新品', '0' => L('_EVERYTHING_')))->keyDefault('SHOP_SHOW_TYPE',0)
            ->keyRadio('SHOP_SHOW_ORDER_FIELD', L('_SORT_VALUE_'), L('_TIP_SORT_VALUE_'), array('sell_num' => '售出数量', 'createtime' => L('_DELIVER_TIME_'), 'changetime' => L('_UPDATE_TIME_'),))->keyDefault('SHOP_SHOW_ORDER_FIELD','sell_num')
            ->keyRadio('SHOP_SHOW_ORDER_TYPE', L('_SORT_TYPE_'), L('_TIP_SORT_TYPE_'), array('desc' => L('_COUNTER_'), 'asc' => L('_DIRECT_')))->keyDefault('SHOP_SHOW_ORDER_TYPE','desc')
            ->keyText('SHOP_SHOW_CACHE_TIME', L('_CACHE_TIME_'),L('_TIP_CACHE_TIME_'))->keyDefault('SHOP_SHOW_CACHE_TIME','600')

            ->group(L('_BASIC_CONF_'),'SHOP_SCORE_TYPE,SHOP_HOT_SELL_NUM')
            ->group(L('_HOME_SHOW_CONF_'), 'SHOP_SHOW_TITLE,SHOP_SHOW_TYPE,SHOP_SHOW_COUNT,SHOP_SHOW_TITLE,SHOP_SHOW_ORDER_TYPE,SHOP_SHOW_ORDER_FIELD,SHOP_SHOW_CACHE_TIME')
            ->groupLocalComment(L('_LOCAL_COMMENT_CONF_'),'goodsDetail')
            ->data($data)
            ->buttonSubmit()
            ->buttonBack()
            ->display();
    }

    /**已完成交易列表
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsBuySuccess($page = 1, $r = 20)
    {
        //读取列表
        $map = array('status' => 1);
        $model = M('shop_buy');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        foreach ($list as &$val) {
            $val['goods_name'] = $this->shopModel->where('id=' . $val['goods_id'])->getField('goods_name');
            $address = D('shop_address')->find($val['address_id']);
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

        $builder->buttonDisable(U('setGoodsBuyStatus'), L('_DELIVER_CANCEL_'))
            ->keyId()->keyText('goods_name', L('_GOODS_NAME_'))->keyUid()->keyText('name', L('_RECEIVER_NAME_'))->keyText('address', L('_RECEIVER_ADDRESS_'))->keyText('zipcode', L('_POST_CODE_'))->keyText('phone', L('_PHONE_NUMBER_'))->keyCreateTime('createtime', L('_BUY_TIME_'))->keyTime('gettime', L('_TRADE_ACCOMPLISH_TIME_'))->key('status',L('_STATUS_'), 'status',array(0=>L('_DELIVER_NOT_'),1=>L('_DELIVER_ALREADY_')))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**待发货交易列表
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function verify($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 0);
        $model = M('shop_buy');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();
        foreach ($list as &$val) {
            $val['goods_name'] = op_t($this->shopModel->where('id=' . $val['goods_id'])->getField('goods_name'));
            $address = D('shop_address')->find($val['address_id']);
            $val['name'] = op_t($address['name']);
            $val['address'] = op_t($address['address']);
            $val['zipcode'] = op_t($address['zipcode']);
            $val['phone'] = op_t($address['phone']);
        }
        unset($val);
        //显示页面
        $builder = new AdminListBuilder();

        $builder->title(L('_GOODS_WAIT_DELIVER_'));
        $builder->meta_title = L('_GOODS_WAIT_DELIVER_');

        $builder->setStatusUrl(U('setGoodsBuyStatus'))->buttonEnable('', L('_DELIVER_'))
            ->keyId()->keyText('goods_name', L('_GOODS_NAME_'))->keyUid()->keyText('name', L('_RECEIVER_NAME_'))->keyText('address', L('_RECEIVER_ADDRESS_'))->keyText('zipcode', L('_POST_CODE_'))->keyText('phone', L('_PHONE_NUMBER_'))->keyCreateTime('createtime', L('_BUY_TIME_'))->keyTime('gettime', L('_TRADE_ACCOMPLISH_TIME_'))->key('status',L('_STATUS_'), 'status',array(0=>L('_DELIVER_NOT_'),1=>L('_DELIVER_ALREADY_')))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }


    public function setGoodsBuyStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        if ($status == 1) {
            $gettime = time();
            foreach ($ids as $id) {
                D('shop_buy')->where('id=' . $id)->setField('gettime', $gettime);
                $content = D('shop_buy')->find($id);
                $message = L('_MESSAGE_TRADE_ACCOMPLISH_');
                D('Message')->sendMessageWithoutCheckSelf($content['uid'], L('_MESSAGE_DELIVER_'),$message,  'Shop/Index/myGoods', array('status' => '1'), is_login(), 1);

                //商城记录
                $goods_name = D('shop')->field('goods_name')->find($content['goods_id']);
                $shop_log['message'] = L('_MESSAGE_AT_') . time_format($gettime) . '[' . is_login() . ']' . get_nickname( is_login()) . L('_MESSAGE_DELIVER_USER_').'[' . $content['uid'] . ']' . get_nickname( $content['uid']) . L('_MESSAGE_GOODS_BOUGHT_').L('_COLON_').'<a href="index.php?s=/Shop/Index/goodsDetail/id/' . $content['goods_id'] . '.html" target="_black">' . $goods_name['goods_name'] . '</a>';
                $shop_log['uid'] = is_login();
                $shop_log['create_time'] = $gettime;
                D('shop_log')->add($shop_log);
            }
        }
        $builder->doSetStatus('shop_buy', $ids, $status);
    }

    /**商城日志
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function shopLog($page = 1, $r = 20)
    {
        //读取列表
        $model = M('shop_log');
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
