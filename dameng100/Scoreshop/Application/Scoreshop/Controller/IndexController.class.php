<?php
namespace Scoreshop\Controller;
use Common\Controller\CommonController;

class IndexController extends CommonController
{
    public function _initialize()
    {
        //基础公共控制器
        parent::_initialize();
        
        $tree = D('scoreshopCategory')->getTree();
        $this->assign('tree', $tree);
        $score_type = modC('SCORESHOP_SCORE_TYPE', '1', 'Scoreshop');
        $money_type = D('Ucenter/Score')->getType(array('id' => $score_type));

        if (is_login()) {
            $moneyData = D('Member')->where(array('uid' => get_uid()))->field('score' . $score_type)->find();
            $money = $moneyData['score' . $score_type];
            $this->assign('my_money', $money);
            $menu_list = array(
                'left' =>
                    array(
                        array('tab' => 'all', 'title' => L('_ALL_'), 'href' => U('scoreshop/index/goods')),
                    ),
                'right' =>
                    array(
                        array('tab' => 'orders', 'title' => L('_ORDER_MY_'), 'href' => U('scoreshop/index/mygoods'), 'icon' => 'list-alt'),
                        array('tab' => 'money', 'title' => L('_CURRENT_') . $money_type['title'] . '：' . $moneyData['score' . $score_type] . ' ' . $money_type['unit'], 'icon' => 'stats')
                    )
            );
        } else {
            $menu_list = array(
                'left' =>
                    array(
                        array('tab' => 'all', 'title' => L('_ALL_'), 'href' => U('Scoreshop/index/goods')),
                    ),
                'right' =>
                    array(
                        array('tab' => 'orders', 'title' => L('_ORDER_MY_'), 'href' => U('Scoreshop/index/mygoods'), 'icon' => 'list-alt')
                    )
            );
        }
        $this->assign('money_type', $money_type);

        $hot_num = modC('SCORESHOP_HOT_SELL_NUM', 10, 'Scoreshop');
        $this->assign('hot_num', $hot_num);

        foreach ($tree as $category) {
            $menu = array('tab' => 'category_' . $category['id'], 'title' => $category['title'], 'href' => U('Scoreshop/index/goods', array('category_id' => $category['id'])));
            if ($category['_']) {
                $menu['children'][] = array('title' => L('_ALL_'), 'href' => U('Scoreshop/index/goods', array('category_id' => $category['id'])));
                foreach ($category['_'] as $child)
                    $menu['children'][] = array('title' => $child['title'], 'href' => U('Scoreshop/index/goods', array('category_id' => $child['id'])));
            }
            $menu_list['left'][] = $menu;
        }
        //dump($menu_list);exit;

        $this->assign('sub_menu', $menu_list);
        $this->assign('current', 'home');
        $this->setTitle(L('_MALL_'));
    }

    /**
     * 商品页初始化
     * @author 大蒙<59262424@qq.com>
     */
    public function _goods_initialize()
    {
        $shop_address = D('scoreshop_address')->where('uid=' . is_login())->find();
        $this->assign('scoreshop_address', $scoreshop_address);
    }

    /**
     * 积分商城首页
     * @author 大蒙<59262424@qq.com>
     */
    public function index()
    {
        $this->_goods_initialize();
        //新品上架
        $map_new['is_new'] = 1;
        $map_new['status'] = 1;
        $goods_list_new =  D('scoreshop')->getList($map_hot,'changetime desc',8);
        $this->assign('contents_new', $goods_list_new);

        //热销商品
        $hot_num = modC('SCORESHOP_HOT_SELL_NUM', 10, 'Scoreshop');
        $map_hot['sell_num'] = array('egt', $hot_num);
        $map_hot['status'] = 1;
        $goods_list_hot = D('scoreshop')->getList($map_hot,'sell_num desc',8);
        $this->assign('contents_hot', $goods_list_hot);
        $this->display();
    }

    /**
     * 商品列表页
     * @param int $page
     * @author 大蒙<59262424@qq.com>
     */
    public function goods($page = 1,$r=20)
    {
        $this->_goods_initialize();
        $category_id = I('category_id',0,'intval');
        $goods_category = D('scoreshopCategory')->find($category_id);
        if ($category_id) {
            $goods_categorys = D('scoreshop_category')->where("id=%d OR pid=%d", array($category_id, $category_id))->limit(999)->select();
            $ids = array();
            foreach ($goods_categorys as $v) {
                $ids[] = $v['id'];
            }
            $map['category_id'] = array('in', implode(',', $ids));
        }
        $map['status'] = 1;

        list($goods_list,$totalCount) = D('scoreshop')->getListByPage($map,$page,'sell_num desc','*',20);
        
        foreach ($goods_list as &$v) {
            $v['category'] = D('scoreshopCategory')->field('id,title')->find($v['category_id']);
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
            $cate_name = D('scoreshopCategory')->where(array('id' => $top_category_id))->getField('title');
            $this->assign('category_name', $cate_name);
            $this->assign('child_category_name', $goods_category['title']);
        }
        
        $this->setTitle('{$category_name|text}' . ' ' . L('_MALL_'));
        $this->setKeywords('{$category_name|text}' . ', ' . L('_MALL_'));
        $this->display();
    }

    /**
     * 商品详情页
     * @author 大蒙<59262424@qq.com>
     */
    public function goodsDetail()
    {
        $id=I('id',0,'intval');
        $this->_goods_initialize();
        $goods = D('scoreshop')->getData($id);
        if (!$goods) {
            $this->error('404 not found');
        }
        //商品SKU
        if($goods['sku_table']){
            $goods['sku_table'] = json_decode($goods['sku_table'],true);
            $minPrice= intval($goods['price']);
            $maxPrice= intval($goods['price']);

            foreach($goods['sku_table']['info'] as $val){
                if($val['price']==''){
                    $val['price']= intval($goods['price']);
                }
                if($val['price']<=$minPrice){
                    $minPrice = $val['price'];
                }
                if($val['price']>=$maxPrice){
                    $maxPrice = $val['price'];
                }
            }
            unset($val);
            if ($minPrice==$maxPrice){
                $goods['price']=$minPrice;
                //$goods['price'] = $goods['price'];
            }else{
                $goods['price']=$minPrice.'-'.$maxPrice;
            }
        }
        $goods['sku_table_json'] = json_encode($goods['sku_table']);
        //分类信息
        $category = D('scoreshopCategory')->find($goods['category_id']);

        $top_category_id = $category['pid'] == 0 ? $category['id'] : $category['pid'];
        $this->assign('top_category', $top_category_id);
        $this->assign('category_id', $category['id']);
        if ($top_category_id == $category['id']) {
            $this->assign('category_name', $category['title']);
        } else {
            $this->assign('category_name', D('scoreshopCategory')->where(array('id' => $top_category_id))->getField('title'));
            $this->assign('child_category_name', $category['title']);
        }
        $this->assign('content', $goods);
        //dump($goods);

        //同类对比
        $goods_categorys_ids = D('scoreshop_category')->where("id=%d OR pid=%d", array($category['id'], $category['id']))->limit(999)->field('id')->select();
        foreach ($goods_categorys_ids as &$v) {
            $v = $v['id'];
        }
        $map['category_id'] = array('in', $goods_categorys_ids);
        $map['status'] = 1;
        $map['id'] = array('neq', $id);
        $same_category_goods = D('scoreshop')->where($map)->limit(3)->order('sell_num desc')->select();
        $this->assign('contents_same_category', $same_category_goods);

        //添加最近浏览
        $map_see['uid'] = is_login();
        $map_see['goods_id'] = $id;
        $rs = D('ScoreshopSee')->where($map_see)->find();
        if ($rs) {
            $data['update_time'] = time();
            D('ScoreshopSee')->where($map_see)->save($data);
        } else {
            $map_see['create_time'] = $map_see['update_time'] = time();
            D('ScoreshopSee')->add($map_see);
        }

        $this->display();
    }

    /**
     * 兑换商品
     * @author 大蒙<59262424@qq.com>
     */
    public function goodsBuy()
    {
        $this->_needLogin();
        if(IS_POST){
            $id=I('post.id',0,'intval');
            $name=I('post.name','','text');
            $address=I('post.address','','text');
            $phone=I('phone','','text');
            $address_id =I('address_id',0,'intval');
            $num = 1;
            
            $this->checkAuth('Scoreshop/Index/goodsBuy', -1, L('_INFO_AUTHORITY_BUY_EXCHANGE_LACK_') . L('_EXCLAMATION_'));
            $this->checkActionLimit('Scoreshop_goods_buy', 'scoreshop', $id, is_login());

            $goods = D('scoreshop')->where('id=' . $id)->find();
            if ($goods) {
                if ($num <= 0) {
                    $this->error(L('_ERROR_NEGATIVE_') . L('_PERIOD_'));
                }
                //验证开始
                //判断地址ID
                if (!$address_id){
                    $this->error('地址未选择');
                }
                //判断商品余量
                if ($num > $goods['quantity']) {
                    $this->error(L('_ERROR_MARGIN_'));
                }

                //扣积分
                $ScoreModel = D('Ucenter/Score');
                $score_type = modC('SCORESHOP_SCORE_TYPE', '1', 'Scoreshop');
                $money_type = $ScoreModel->getType(array('id' => $score_type));
                $price = $num * $goods['price'];
                $my_money = D('Member')->where(array('uid' => get_uid()))->field('score' . $score_type)->find();

                if ($price > $my_money['score' . $score_type]) {
                    $this->error(L('_TOAST_TIP_LACK_') . $money_type['title'] . L('_TOAST_TIP_LACK2_'));
                }
                //验证结束

                $data['goods_id'] = $id;
                $data['quantity'] = $num;
                $data['status'] = 0;
                $data['uid'] = is_login();
                $data['createtime'] = time();
                $data['address_id'] = $address_id;

                $ScoreModel->setUserScore(array(is_login()), $price, $score_type, 'dec', 'Scoreshop', $id, get_nickname(is_login()) . L('_PRODUCT_BUY_YET_'));

                $res = D('ScoreshopBuy')->add($data);
                if ($res) {
                    //商品数量减少,已售量增加
                    D('Scoreshop')->where('id=' . $id)->setDec('quantity', $num);
                    D('Scoreshop')->where('id=' . $id)->setInc('sell_num', $num);
                    //发送系统消息
                    $message = $goods['goods_name'] . L('_MESSAGE_BUY_') . L('_PERIOD_');
                    send_message_without_check_self(
                        is_login(), 
                        L('_MESSAGE_TOAST_BUY_'), 
                        $message, 
                        'Scoreshop/Index/myGoods', 
                        array('status' => '0'),
                        1,
                        'Scoreshop',
                        'Common_system'
                    );

                    //商城记录
                    $shop_log['message'] = L('_LOG_1_') . '[' . is_login() . ']' . get_nickname(is_login()) . L('_LOG_2_') . time_format($data['createtime']) . L('_LOG_3_') . '<a href="index.php?s=/Scoreshop/Index/goodsDetail/id/' . $goods['id'] . '.html" target="_black">' . $goods['goods_name'] . '</a>';
                    $shop_log['uid'] = is_login();
                    $shop_log['create_time'] = $data['createtime'];
                    D('Scoreshop_log')->add($shop_log);

                    action_log('Scoreshop_goods_buy', 'Scoreshop', $id, is_login());

                    $this->success(L('_SUCCESS_BUY_') . $price . $money_type['title'], $_SERVER['HTTP_REFERER']);
                } else {
                    $this->error(L('_ERROR_BUY_') . L('_EXCLAMATION_'));
                }
            } else {
                $this->error(L('_ERROR_SELECT_'));
            }
        }else{
            $id=I('id',0,'intval');
            $uid = is_login();
            //获取商品信息
            $goods = D('scoreshop')->where('id=' . $id)->find();
            $ScoreModel = D('Ucenter/Score');
            $score_type = modC('SCORESHOP_SCORE_TYPE', '1', 'Scoreshop');
            $user_score = $ScoreModel->getUserScore($uid, $score_type);
            $this->assign('score_num',$user_score);
            $this->assign('goods',$goods);
            //获取地址信息
            $address = D('scoreshop_address')->where('uid='.$uid)->order('change_time desc')->select();
            $address_count = D('scoreshop_address')->where('uid='.$uid)->count();
            $this->assign('address_count',$address_count);
            $this->assign('address',$address);

            $this->display();
        }
    }

    /**
     * 个人商品页
     * @param int $page
     * @author 大蒙<59262424@qq.com>
     */
    public function myGoods($page = 1,$r=20)
    {
        $this->_needLogin();
        $status=I('status',999,'intval');
        if($status!=999){
            $map['status'] = $status;
        }
        
        $map['uid'] = is_login();
        list($goods_buy_list,$totalCount) = D('ScoreshopBuy')->getListByPage($map,$page,'createtime desc','*',$r);
        
        foreach ($goods_buy_list as &$v) {
            $v['goods'] = D('scoreshop')->where('id=' . $v['goods_id'])->field($this->goods_info)->find();
            $v['category'] = D('scoreshopCategory')->field('id,title')->find($v['goods']['category_id']);
            if($v['status']==-1){
                $v['status_info'] = '已取消';
            }
            if($v['status']==0){
                $v['status_info'] = '待发货';
            }
            if($v['status']==1){
                $v['status_info'] = '已发货';
            }
            if($v['status']==2){
                $v['status_info'] = '已完成';
            }
        }
        unset($v);
        //dump($goods_buy_list);exit;
        $this->assign('contents', $goods_buy_list);
        $this->assign('totalPageCount', $totalCount);
        $this->assign('status', $status);
        $this->assign('current', 'orders');
        $this->display();
    }

    public function myAddress(){

        $this->_needLogin();
        $map['uid'] = is_login();
        $list = D('ScoreshopAddress')->where($map)->select();

        $this->ajaxReturn($list);
    }

    public function editAddress(){
        $this->_needLogin();
        if(IS_POST){
            //POST
            $data['id'] = I('post.address_id','','intval');
            $data['uid'] = is_login();
            $data['phone'] = I('post.phone',0,'intval');
            $data['name'] = I('post.name','','text');
            $data['address'] = I('post.address','','text');
            $data['change_time'] = time();

            //判断数据
            //用户地址处理
                if ($data['name'] == '') {
                    $this->error(L('_ERROR_ADDRESS_NAME_'));
                }
                if ($data['address'] == '') {
                    $this->error(L('_ERROR_ADDRESS_'));
                }
                if ($data['phone']  == '' || !preg_match("/^1[3458][0-9]{9}$/", $data['phone'] )) {
                    $this->error(L('_ERROR_PHONE_'));
                }

            if($data['id']){
                $result = M('scoreshop_address')->save($data);
            }else{
                //判断地址数量,用户只允许添加5条
                $add_num = M('scoreshop_address')->where('uid='.is_login())->count();
                if($add_num>=5){
                    $this->error(L('_ERROR_NUM_ADDRESS_'));
                }
                $data['create_time'] = time();
                $result = M('scoreshop_address')->add($data);
            }

            if($result){
                $this->success(L('_SUCCESS_ADD_ADDRESS_'));
            }else{
                $this->error(L('_ERROR_ADD_ADDRESS_'));
            }    
        }else{
            $aId=I('get.id',0,'intval');
            //商品ID
            $goods_id=I('goods_id',0,'intval');
            $this->assign('goods_id',$goods_id);
            if($aId){
                $data=D('scoreshop_address')->where('id='. $aId)->find();
                $this->assign('data',$data);
            }
            $title=$aId?"编辑":"新增";
            $this->assign('title',$title);
            $this->display();

        }
    }

    public function delAddress(){
        $this->_needLogin();
        $aId = I('id',0,'intval');

        if(IS_POST){
            if($aId) {
                $map['uid'] = is_login();
                $map['id'] = $aId;
                $result = M('ScoreshopAddress')->where($map)->delete();

                if($result){
                    $this->success(L('_SUCCESS_DEL_ADDRESS_'));
                }else{
                    $this->error(L('_ERROR_DEL_ADDRESS_'));
                }
            }
        }else{
            //商品ID
            $goods_id=I('goods_id',0,'intval');
            $this->assign('goods_id',$goods_id);
            $this->assign('id',$aId);
            $this->display();

        }
        
    }
    /**
     * 物流查询
     * @return [type] [description]
     */
    public function logistic(){
        $id = I('get.id',0,'intval');
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


    public function history_view(){
        //最近浏览
        if (is_login()) {
            //关联查询最近浏览
            $sql = "SELECT a." . $this->goods_info . " FROM `" . C('DB_PREFIX') . "scoreshop` AS a , `" . C('DB_PREFIX') . "scoreshop_see` AS b WHERE ( b.`uid` =" . is_login() . " ) AND ( b.`goods_id` <> '" . $id . "' ) AND ( a.`status` = 1 )AND(a.`id` =b.`goods_id`) ORDER BY b.update_time desc LIMIT 3";
            $Model = new \Think\Model();
            $goods_see_list = $Model->query($sql);
            $this->assign('goods_see_list', $goods_see_list);
            
            
        }
    }
    private function _needLogin()
    {   
        //调用通用用户授权方法
        if(!_need_login()){
            $this->error(L('_ERROR_PLEASE_LOGIN_'));
        }
    }

}