<?php
namespace app\order\service;
/**
 * 购物车接口
 */
class CartService extends \app\base\service\BaseService {

    private $model = 'order/OrderCart';

    /**
     * 产品编号验证规则
     * @var string
     */
    public $product_id_rules = '\.a-z0-9_-';

    /**
     * 产品名称验证
     * @var string
     */
    public $product_name_rules = '\w \-\.\:';

    /**
     * 只允许安全产品名
     * @var bool
     */
    public $product_name_safe = false;

    /**
     * 购物车数据
     * @var array
     */
    public $product_data = [];


    public $type = [];

    public $storage = null;

    public function __construct() {
        $config = \dux\Config::get('dux.use');
        $this->storage = \dux\Dux::storage($config['data_cache'], 10);
    }

    /**
     * 购物车信息
     * @param $userId
     * @return bool
     */
    public function getCart($userId) {
        $data = json_decode($this->storage->get('cart_' . $userId), true);
        $data = $data ? $data : [];
        $this->product_data = $data;
        return $this->success([
            'items' => $data['items'],
            'total' => price_format($data['total']),
            'checked_items' => $data['checked_items'],
            'checked_total' => $data['checked_total'],
            'list' => $data['data']
        ]);
    }


    /**
     * 商品列表
     * @param $userId
     * @return bool
     */
    public function getList($userId) {
        if(!empty($this->product_data)) {
            $this->success($this->product_data);
        }
        $data = json_decode($this->storage->get('cart_' . $userId), true);
        $data = $data ? $data : [];
        $this->product_data = $data;
        return $this->success($data['data'] ? $data['data'] : []);
    }

    /**
     * 商品信息
     * @param $userId
     * @param $id
     * @return bool
     */
    public function getInfo($userId, $id) {
        $data = $this->getList($userId);
        return $this->success($data[$id] ? $data[$id] : []);
    }

    /**
     * 添加商品
     * @param $userId
     * @param array $data
     * @param $rowId
     * @return bool
     */
    public function add($userId, $data = [], $rowId = 0) {
        $cart = $this->getList($userId);
        if (!is_array($data) OR count($data) === 0) {
            return $this->error('没有发现商品数据');
        }
        $cartData = [];
        if (isset($data['id'])) {
            $cartData[] = $data;
        } else {
            $cartData = $data;
        }
        $keys = [];
        foreach ($cartData as $items) {
            if (!isset($items['id'], $items['qty'], $items['price'], $items['name'], $items['app'], $items['item_no'])) {
                return $this->error('必须包含编号(id)、数量(qty)、价格(price)、商品名称(name)、应用名(app)、商品货号(item_no)');
            }
            $items['qty'] = (float)$items['qty'];
            if ($items['qty'] == 0) {
                return $this->error('插入商品不能为空!');
            }
            if (!preg_match('/^[' . $this->product_id_rules . ']+$/i', $items['id'])) {
                return $this->error('商品编号不符合规则!');
            }
            if ($this->product_name_safe && !preg_match('/^[' . $this->product_name_rules . ']+$/i' . (UTF8_ENABLED ? 'u' : ''), $items['name'])) {
                return $this->error('商品名称"' . $items['name'] . '"验证失败！');
            }
            $items['price'] = price_format($items['price']);

            if (is_array($items['options']) && count($items['options']) > 0) {
                $rowid = md5($items['app'] . $items['id'] . serialize($items['options']));
            } else {
                $rowid = md5($items['app'] . $items['id']);
            }
            $keys[] = $rowid;
            $qty = isset($cart[$rowid]['qty']) ? (int)$cart[$rowid]['qty'] : 0;
            $items['rowid'] = $rowid;
            $items['qty'] += $qty;
            $items['checked'] = 1;
            $cart = array_merge($cart, [$rowid => $items]);
        }
        $this->saveCart($userId, $cart);
        if($rowId) {
            $this->del($userId, $rowId);
        }
        return $this->success($keys);
    }

    /**
     * 更新商品
     * @param $userId
     * @param $data
     * @return bool
     */
    public function update($userId, $data) {
        $cart = $this->getList($userId);
        if (!is_array($data) OR count($data) === 0) {
            return  $this->error('没有发现商品数据');
        }
        $cartData = [];
        if (isset($data['rowid'])) {
            $cartData[] = $data;
        } else {
            $cartData = $data;
        }
        $keys = [];
        foreach ($cartData as $items) {
            $keys[] = $items['rowid'];
            if (!isset($items['rowid'], $cart[$items['rowid']])) {
                return $this->error('购物车商品不存在!');
            }
            if (isset($items['qty'])) {
                $items['qty'] = (float)$items['qty'];
                if ($items['qty'] == 0) {
                    $items['qty'] = 1;
                }
            }
            if (isset($items['checked'])) {
                $items['checked'] = $items['checked'] ? 1 : 0;
            }
            $keys = array_intersect(array_keys($cart[$items['rowid']]), array_keys($items));
            if (isset($items['price'])) {
                $items['price'] = price_format($items['price']);
            }
            foreach (array_diff($keys, array('id', 'name')) as $key) {
                $cart[$items['rowid']][$key] = $items[$key];
            }
        }
        $this->saveCart($userId, $cart);
        return $this->success($keys);
    }

    /**
     * 移除商品
     * @param $userId
     * @param $rowids
     * @return bool
     */
    public function del($userId, $rowids) {
        $cart = $this->getList($userId);
        if(is_array($rowids)) {
            foreach ($rowids as $rowid) {
                unset($cart[$rowid]);
            }
        }else {
            unset($cart[$rowids]);
        }
        $this->saveCart($userId, $cart);
        return $this->success($rowids);
    }

    /**
     * 清空购物车
     * @param $userId
     * @return bool
     */
    public function clear($userId) {
        $this->saveCart($userId, []);
        return $this->success();
    }

    /**
     * 保存购物车
     * @param $userId
     * @param array $data
     * @return bool
     */
    private function saveCart($userId, $data = []) {
        $items = $total = 0 ;
        $checkedItems = $checkedTotal = 0;
        foreach ($data as $key => $val) {
            $priceTotal = price_format($val['price'] * $val['qty']);
            $data[$key]['total'] = $priceTotal;
            $items += $val['qty'];
            $total += $priceTotal;
            if(!$val['checked']) {
                continue;
            }
            $checkedItems += $val['qty'];
            $checkedTotal += $priceTotal;
        }
        $dataTotal = [
            'items' => $items,
            'total' => price_format($total),
            'checked_items' => $checkedItems,
            'checked_total' => price_format($checkedTotal),
            'data' => $data
        ];

        $this->storage->set('cart_' . $userId, json_encode($dataTotal));
        return $this->success($data);
    }


}
