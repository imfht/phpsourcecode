<?php


namespace Shop\Controller;

use Think\Controller;

/**
 * Class IndexController
 * @package Shop\Controller
 * @郑钟良
 */
class IndexController extends Controller
{
    protected $goods_info = 'id,goods_name,goods_ico,goods_introduct,money_need,goods_num,changetime,status,createtime,category_id,is_new,sell_num';

    /**
     * 商城初始化
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _initialize()
    {
        if(D('Common/Module')->isInstalled('Mob')) {
            $sign = modC('JUMP_MOB', 0, 'mob');
            if(is_mobile() && ($sign == 0)) {
                redirect('Mob/Shop/index');
            }
        }

        $tree = D('shopCategory')->getTree();
        $this->assign('tree', $tree);
        $score_type = modC('SHOP_SCORE_TYPE', '1', 'Shop');
        $money_type = D('Ucenter/Score')->getType(array('id' => $score_type));
        if (is_login()) {
            $moneyData = D('Member')->where(array('uid' => get_uid()))->field('score' . $score_type)->find();
            $money = $moneyData['score' . $score_type];
            $this->assign('my_money', $money);
            $menu_list = array(
                'left' =>
                    array(
                        array('tab' => 'all', 'title' => L('_ALL_'), 'href' => U('shop/index/goods')),
                    ),
                'right' =>
                    array(
                        array('tab' => 'orders', 'title' => L('_ORDER_MY_'), 'href' => U('shop/index/mygoods'), 'icon' => 'list-alt'),
                        array('tab' => 'money', 'title' => L('_CURRENT_') . $money_type['title'] . '：' . $moneyData['score' . $score_type] . ' ' . $money_type['unit'], 'icon' => 'stats')
                    )
            );
        } else {
            $menu_list = array(
                'left' =>
                    array(
                        array('tab' => 'all', 'title' => L('_ALL_'), 'href' => U('shop/index/goods')),
                    ),
                'right' =>
                    array(
                        array('tab' => 'orders', 'title' => L('_ORDER_MY_'), 'href' => U('shop/index/mygoods'), 'icon' => 'list-alt')
                    )
            );
        }
        $this->assign('money_type', $money_type);

        $hot_num = modC('SHOP_HOT_SELL_NUM', 10, 'Shop');
        $this->assign('hot_num', $hot_num);

        foreach ($tree as $category) {
            $menu = array('tab' => 'category_' . $category['id'], 'title' => $category['title'], 'href' => U('shop/index/goods', array('category_id' => $category['id'])));
            if ($category['_']) {
                $menu['children'][] = array('title' => L('_ALL_'), 'href' => U('shop/index/goods', array('category_id' => $category['id'])));
                foreach ($category['_'] as $child)
                    $menu['children'][] = array('title' => $child['title'], 'href' => U('shop/index/goods', array('category_id' => $child['id'])));
            }
            $menu_list['left'][] = $menu;
        }


        $this->assign('sub_menu', $menu_list);
        $this->assign('current', 'home');
        $this->setTitle(L('_MALL_'));
    }

    /**
     * 商品页初始化
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _goods_initialize()
    {
        $shop_address = D('shop_address')->where('uid=' . is_login())->find();
        $this->assign('shop_address', $shop_address);
    }

    /**
     * 商城首页
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function index()
    {
        $this->_goods_initialize();
        //新品上架
        $map['is_new'] = 1;
        $map['status'] = 1;
        $goods_list_new = D('shop')->where($map)->order('changetime desc')->limit(8)->field($this->goods_info)->select();
        $this->assign('contents_new', $goods_list_new);

        //热销商品
        $hot_num = modC('SHOP_HOT_SELL_NUM', 10, 'Shop');
        $map_hot['sell_num'] = array('egt', $hot_num);
        $map_hot['status'] = 1;
        $goods_list_hot = D('shop')->where($map_hot)->order('sell_num desc')->limit(8)->field($this->goods_info)->select();
        $this->assign('contents_hot', $goods_list_hot);
        $this->display();
    }

    /**
     * 商品页
     * @param int $page
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goods($page = 1)
    {
        $this->_goods_initialize();
        $category_id = I('category_id',0,'intval');
        $goods_category = D('shopCategory')->find($category_id);
        if ($category_id != 0) {
            $goods_categorys = D('shop_category')->where("id=%d OR pid=%d", array($category_id, $category_id))->limit(999)->select();
            $ids = array();
            foreach ($goods_categorys as $v) {
                $ids[] = $v['id'];
            }
            $map['category_id'] = array('in', implode(',', $ids));
        }
        $map['status'] = 1;
        $goods_list = D('shop')->where($map)->order('createtime desc')->page($page, 16)->field($this->goods_info)->select();
        $totalCount = D('shop')->where($map)->count();
        foreach ($goods_list as &$v) {
            $v['category'] = D('shopCategory')->field('id,title')->find($v['category_id']);
        }
        unset($v);
        $this->assign('contents', $goods_list);
        $this->assign('totalPageCount', $totalCount);
        $top_category_id = $goods_category['pid'] == 0 ? $goods_category['id'] : $goods_category['pid'];
        $this->assign('top_category', $top_category_id);
        $this->assign('category_id', $category_id);
        if ($top_category_id == $category_id) {
            $cate_name = $goods_category['title'];
            $this->assign('category_name', $cate_name);
        } else {
            $cate_name = D('shopCategory')->where(array('id' => $top_category_id))->getField('title');
            $this->assign('category_name', $cate_name);
            $this->assign('child_category_name', $goods_category['title']);
        }
        $this->setTitle('{$category_name|op_t}' . ' ' . L('_MALL_'));
        $this->setKeywords('{$category_name|op_t}' . ', ' . L('_MALL_'));
        if ($category_id == 0) {
            $this->assign('current', 'all');
        } else {
            $this->assign('current', 'category_' . $top_category_id);
        }
        // dump( 'category_'.intval($category_id));exit;

        $this->display();
    }

    /**
     * 商品详情页
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsDetail()
    {
        $id=I('id',0,'intval');
        $this->_goods_initialize();
        $goods = D('shop')->find($id);
        if (!$goods) {
            $this->error('404 not found');
        }

        $category = D('shopCategory')->find($goods['category_id']);
        $top_category_id = $category['pid'] == 0 ? $category['id'] : $category['pid'];
        $this->assign('top_category', $top_category_id);
        $this->assign('category_id', $category['id']);
        if ($top_category_id == $category['id']) {
            $this->assign('category_name', $category['title']);
        } else {
            $this->assign('category_name', D('shopCategory')->where(array('id' => $top_category_id))->getField('title'));
            $this->assign('child_category_name', $category['title']);
        }
        $this->assign('content', $goods);
        //同类对比
        $goods_categorys_ids = D('shop_category')->where("id=%d OR pid=%d", array($category['id'], $category['id']))->limit(999)->field('id')->select();
        foreach ($goods_categorys_ids as &$v) {
            $v = $v['id'];
        }
        $map['category_id'] = array('in', $goods_categorys_ids);
        $map['status'] = 1;
        $map['id'] = array('neq', $id);
        $same_category_goods = D('shop')->where($map)->limit(3)->order('sell_num desc')->field($this->goods_info)->select();
        $this->assign('contents_same_category', $same_category_goods);
        //最近浏览
        if (is_login()) {
            //关联查询最近浏览
            $sql = "SELECT a." . $this->goods_info . " FROM `" . C('DB_PREFIX') . "shop` AS a , `" . C('DB_PREFIX') . "shop_see` AS b WHERE ( b.`uid` =" . is_login() . " ) AND ( b.`goods_id` <> '" . $id . "' ) AND ( a.`status` = 1 )AND(a.`id` =b.`goods_id`) ORDER BY b.update_time desc LIMIT 3";
            $Model = new \Think\Model();
            $goods_see_list = $Model->query($sql);
            $this->assign('goods_see_list', $goods_see_list);
            //添加最近浏览
            $map_see['uid'] = is_login();
            $map_see['goods_id'] = $id;
            $rs = D('ShopSee')->where($map_see)->find();
            if ($rs) {
                $data['update_time'] = time();
                D('ShopSee')->where($map_see)->save($data);
            } else {
                $map_see['create_time'] = $map_see['update_time'] = time();
                D('ShopSee')->add($map_see);
            }
        }
        $this->setTitle('{$content.goods_name|op_t}' . ' ' . L('_MALL_'));
        $this->setKeywords('{$content.goods_name|op_t}' . ', ' . L('_MALL_'));
        $this->display();
    }

    /**
     * 购买商品
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsBuy()
    {
        
        $id=I('id',0,'intval');
        $name=I('name','','text');
        $address=I('address','','text');
        $zipcode=I('zipcode','','text');
        $phone=I('phone','','text');
        $address_id =I('address_id',0,'intval');
        $num = 1;
        if (!is_login()) {
            $this->error(L('_ERROR_LOGIN_') . L('_EXCLAMATION_'));
        }
        $this->checkAuth('Shop/Index/goodsBuy', -1, L('_INFO_AUTHORITY_BUY_EXCHANGE_LACK_') . L('_EXCLAMATION_'));
        $this->checkActionLimit('shop_goods_buy', 'shop', $id, is_login());
        $goods = D('shop')->where('id=' . $id)->find();
        if ($goods) {
            if ($num <= 0) {
                $this->error(L('_ERROR_NEGATIVE_') . L('_PERIOD_'));
            }
            //验证开始
            //判断商品余量
            if ($num > $goods['goods_num']) {
                $this->error(L('_ERROR_MARGIN_'));
            }

            //扣money
            $ScoreModel = D('Ucenter/Score');
            $score_type = modC('SHOP_SCORE_TYPE', '1', 'Shop');
            $money_type = $ScoreModel->getType(array('id' => $score_type));

            $money_need = $num * $goods['money_need'];
          //  $my_money = query_user(array('score' . $score_type));
            $my_money = D('Member')->where(array('uid' => get_uid()))->field('score' . $score_type)->find();

            if ($money_need > $my_money['score' . $score_type]) {
                $this->error(L('_TOAST_TIP_LACK_') . $money_type['title'] . L('_TOAST_TIP_LACK2_'));
            }

            //用户地址处理
            if ($name == '' || !preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $name)) {
                $this->error(L('_ERROR_ADDRESS_'));
            }
            if ($address == '') {
                $this->error(L('error_username  '));
            }
            if ($zipcode == '' || strlen($zipcode) != 6 || !is_numeric($zipcode)) {
                $this->error(L('_ERROR_POST_'));
            }
            if ($phone == '' || !preg_match("/^1[3458][0-9]{9}$/", $phone)) {
                $this->error(L('_ERROR_PHONE_'));
            }
            $shop_address['phone'] = $phone;
            $shop_address['name'] = $name;
            $shop_address['address'] = $address;
            $shop_address['zipcode'] = $zipcode;
            if ($address_id) {
                $address_save = D('shop_address')->where(array('id' => $address_id))->save($shop_address);
                if ($address_save) {
                    D('shop_address')->where(array('id' => $address_id))->setField('change_time', time());
                }
                $data['address_id'] = $address_id;
            } else {
                $shop_address['uid'] = is_login();
                $shop_address['create_time'] = time();
                $data['address_id'] = D('shop_address')->add($shop_address);
            }
            //验证结束

            $data['goods_id'] = $id;
            $data['goods_num'] = $num;
            $data['status'] = 0;
            $data['uid'] = is_login();
            $data['createtime'] = time();

            $ScoreModel->setUserScore(array(is_login()), $money_need, $score_type, 'dec', 'shop', $id, get_nickname(is_login()) . L('_PRODUCT_BUY_YET_'));
            $res = D('shop_buy')->add($data);
            if ($res) {
                //商品数量减少,已售量增加
                D('shop')->where('id=' . $id)->setDec('goods_num', $num);
                D('shop')->where('id=' . $id)->setInc('sell_num', $num);
                //发送系统消息
                $message = $goods['goods_name'] . L('_MESSAGE_BUY_') . L('_PERIOD_');
                D('Message')->sendMessageWithoutCheckSelf(is_login(), L('_MESSAGE_TOAST_BUY_'), $message, 'Shop/Index/myGoods', array('status' => '0'));

                //商城记录
                $shop_log['message'] = L('_LOG_1_') . '[' . is_login() . ']' . get_nickname(is_login()) . L('_LOG_2_') . time_format($data['createtime']) . L('_LOG_3_') . '<a href="index.php?s=/Shop/Index/goodsDetail/id/' . $goods['id'] . '.html" target="_black">' . $goods['goods_name'] . '</a>';
                $shop_log['uid'] = is_login();
                $shop_log['create_time'] = $data['createtime'];
                D('shop_log')->add($shop_log);

                action_log('shop_goods_buy', 'shop', $id, is_login());

                $this->success(L('_SUCCESS_BUY_') . $money_need . $money_type['title'], $_SERVER['HTTP_REFERER']);
            } else {
                $this->error(L('_ERROR_BUY_') . L('_EXCLAMATION_'));
            }
        } else {
            $this->error(L('_ERROR_SELECT_'));
        }
    }

    /**
     * 个人商品页
     * @param int $page
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function myGoods($page = 1)
    {
        if (!is_login()) {
            $this->error(L('_ERROR_PLEASE_LOGIN_'));
        }
        $status=I('status',0,'intval');
        $map['status'] = $status;
        $map['uid'] = is_login();
        $goods_buy_list = D('shop_buy')->where($map)->page($page, 16)->order('createtime desc')->select();
        $totalCount = D('shop_buy')->where($map)->count();
        foreach ($goods_buy_list as &$v) {
            $v['goods'] = D('shop')->where('id=' . $v['goods_id'])->field($this->goods_info)->find();
            $v['category'] = D('shopCategory')->field('id,title')->find($v['goods']['category_id']);
        }
        unset($v);
        $this->assign('contents', $goods_buy_list);
        $this->assign('totalPageCount', $totalCount);
        $this->assign('status', $status);
        $this->assign('current', 'orders');
        $this->display();

    }
}