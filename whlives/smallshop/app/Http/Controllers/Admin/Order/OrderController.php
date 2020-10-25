<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Admin\BaseController;
use App\Libs\Kdniao;
use App\Models\Areas;
use App\Models\ExpressCompany;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\OrderDeliveryTemplate;
use App\Models\OrderGoods;
use App\Models\OrderInvoice;
use App\Models\OrderLog;
use App\Models\Payment;
use App\Models\Seller;
use App\Models\SellerAddress;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Validator;

class OrderController extends BaseController
{

    /**
     * 列表获取
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function index(Request $request)
    {
        list($page, $limit, $offset) = get_page_params();
        $id = (int)$request->input('id');
        $order_no = $request->input('order_no');
        $full_name = $request->input('full_name');
        $tel = $request->input('tel');
        $seller_id = $request->input('seller_id');
        $username = $request->input('username');
        $status = $request->input('status');

        //搜索
        $where = array();
        if ($id) $where[] = array('id', $id);
        if ($order_no) $where[] = array('order_no', $order_no);
        if ($full_name) $where[] = array('full_name', $full_name);
        if ($tel) $where[] = array('tel', $tel);
        if ($seller_id) $where[] = array('seller_id', $seller_id);
        if (is_numeric($status)) $where[] = array('status', $status);
        if ($username) {
            $member_id = Member::where('username', $username)->value('id');
            if ($member_id) {
                $where[] = array('m_id', $member_id);
            } else {
                api_error(__('admin.content_is_empty'));
            }
        }

        $res_list = Order::select('id', 'm_id', 'order_no', 'flag', 'payment_id', 'subtotal', 'full_name', 'tel', 'status', 'pay_at', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        //查询用户
        $m_ids = array();
        foreach ($res_list as $value) {
            $m_ids[] = $value['m_id'];
        }
        if ($m_ids) {
            $member_data = Member::whereIn('id', array_unique($m_ids))->pluck('username', 'id');
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['subtotal'] = '￥' . $value['subtotal'];
            $_item['status_text'] = Order::STATUS_DESC[$value['status']];
            $_item['username'] = isset($member_data[$value['m_id']]) ? $member_data[$value['m_id']] : '';
            $_item['payment'] = $value['payment_id'] ? Payment::PAYMENT_DESC[$value['payment_id']] : '';
            $data_list[] = $_item;
        }
        $total = Order::where($where)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 获取订单状态组
     */
    public function getStatus()
    {
        return $this->success(Order::STATUS_DESC);
    }

    /**
     * 根据id获取信息
     * @param Request $request
     * @return array
     */
    public function detail(Request $request)
    {
        $id = (int)$request->input('id');
        $data = array();
        if ($id) {
            $order = Order::find($id);
            if (!$order) {
                api_error(__('admin.order_error'));
            }
            $order['is_delivery'] = OrderService::isDelivery($order);
            $order['is_pay'] = OrderService::isPay($order);
            $order['status_label'] = Order::STATUS_DESC[$order['status']];
            $order['delivery_type'] = Order::DELIVERY_DESC[$order['delivery_type']];
            $order['payment_name'] = $order['payment_id'] ? Payment::find($order['payment_id'])->value('title') : '';
            $order['username'] = $order['m_id'] ? Member::find($order['m_id'])->value('username') : '';

            $seller = Seller::select('id', 'title')->find($order['seller_id']);//店铺信息
            $invoice = OrderInvoice::select('type', 'title', 'tax_no')->where('order_id', $id)->first();
            if ($invoice) $invoice['type_text'] = OrderInvoice::TYPE_DESC[$invoice['type']];

            //获取订单商品
            $goods = array();
            $goods_res = $order->goods()
                ->select('id', 'goods_title', 'image', 'sku_code', 'sell_price', 'market_price', 'buy_qty', 'spec_value', 'delivery', 'refund')
                ->where('order_id', $id)
                ->orderBy('id', 'desc')
                ->get();
            if ($goods_res->isEmpty()) {
                api_error(__('admin.order_goods_error'));
            }
            foreach ($goods_res as $value) {
                $_item = $value;
                $_item['sell_price'] = '￥' . $value['sell_price'];
                $_item['delivery_text'] = OrderGoods::DELIVERY_DESC[$value['delivery']];
                $_item['refund'] = OrderGoods::REFUND_DESC[$value['refund']];
                $goods[] = $_item;
            }

            //物流公司
            $express_company = ExpressCompany::where('status', ExpressCompany::STATUS_ON)->orderBy('id', 'desc')->pluck('title', 'id');

            $data['order'] = $order;
            $data['seller'] = $seller;
            $data['goods'] = $goods;
            $data['express_company'] = $express_company;
            $data['invoice'] = $invoice;
        }

        return $this->success($data);
    }

    /**
     * 获取发货信息
     * @param Request $request
     * @return array
     */
    public function getDelivery(Request $request)
    {
        $id = (int)$request->input('id');
        $delivery = array();
        if ($id) {
            $delivery = OrderDelivery::select('company_name', 'company_code', 'code', 'note', 'created_at')
                ->where('order_id', $id)
                ->orderBy('id', 'desc')
                ->get();
        }
        return $this->success($delivery);
    }

    /**
     * 获取日志信息
     * @param Request $request
     * @return array
     */
    public function getLog(Request $request)
    {
        $id = (int)$request->input('id');
        $log = array();
        if ($id) {
            $log = OrderLog::select('username', 'action', 'note', 'created_at')
                ->where('order_id', $id)
                ->orderBy('id', 'desc')
                ->get();
        }
        return $this->success($log);
    }

    /**
     * 根据id获取价格信息
     * @param Request $request
     * @return array
     */
    public function getPrice(Request $request)
    {
        $id = (int)$request->input('id');
        if ($id) {
            $data = Order::select('id', 'sell_price_total', 'promotion_price', 'discount_price', 'delivery_price_real', 'subtotal')->find($id);
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        return $this->success($data);
    }

    /**
     * 修改价格
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function updatePrice(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'discount_price' => 'required|price',
            'delivery_price_real' => 'required|price',
        ], [
            'id.required' => 'id不能为空',
            'id.numeric' => 'id只能是数字',
            'discount_price.required' => '改价金额错误',
            'discount_price.price' => '改价金额格式错误',
            'delivery_price_real.required' => '运费金额错误',
            'delivery_price_real.price' => '运费金额格式错误',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $id = (int)$request->input('id');
        $discount_price = $request->input('discount_price');
        $delivery_price_real = $request->input('delivery_price_real');
        $admin_user = $this->getUserInfo();

        $order = Order::find($id);
        if (!$order) {
            api_error(__('admin.order_error'));
        }
        $res = OrderService::updatePrice($order, $discount_price, $delivery_price_real, $admin_user, OrderLog::USER_TYPE_ADMIN);
        if ($res === true) {
            return $this->success(true);
        } else {
            api_error($res);
        }
    }

    /**
     * 根据id获取地址信息
     * @param Request $request
     * @return array
     */
    public function getAddress(Request $request)
    {
        $id = (int)$request->input('id');
        if ($id) {
            $data = Order::select('id', 'full_name', 'tel', 'prov', 'city', 'area', 'address')->find($id);
        }
        if (!$data) {
            api_error(__('admin.content_is_empty'));
        }
        $data['prov_id'] = Areas::getAreaId($data['prov'], 0);
        if ($data['prov_id']) {
            $data['city_id'] = Areas::getAreaId($data['city'], $data['prov_id']);
        }
        if ($data['city_id']) {
            $data['area_id'] = Areas::getAreaId($data['area'], $data['city_id']);
        }
        return $this->success($data);
    }

    /**
     * 修改地址
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function updateAddress(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'full_name' => 'required',
            'tel' => 'required',
            'prov_id' => 'required|numeric',
            'city_id' => 'required|numeric',
            'area_id' => 'required|numeric',
            'address' => 'required',
        ], [
            'id.required' => 'id不能为空',
            'id.numeric' => 'id只能是数字',
            'full_name.required' => '姓名不能为空',
            'tel.required' => '电话不能为空',
            'prov_id.required' => '省份不能为空',
            'prov_id.numeric' => '省份只能是数字',
            'city_id.required' => '城市不能为空',
            'city_id.numeric' => '城市只能是数字',
            'area_id.required' => '地区不能为空',
            'area_id.numeric' => '地区只能是数字',
            'address.required' => '地址不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $id = (int)$request->input('id');
        $prov_id = (int)$request->input('prov_id');
        $city_id = (int)$request->input('city_id');
        $area_id = (int)$request->input('area_id');

        foreach ($request->only(['full_name', 'tel', 'address']) as $key => $value) {
            $save_data[$key] = ($value || $value == 0) ? $value : null;
        }
        $save_data['prov'] = Areas::getAreaName($prov_id);
        $save_data['city'] = Areas::getAreaName($city_id);
        $save_data['area'] = Areas::getAreaName($area_id);

        $order = Order::find($id);
        if (!$order) {
            api_error(__('admin.order_error'));
        }
        if (OrderService::isUpdateAddress($order)) {
            $res = Order::where('id', $id)->update($save_data);
            if ($res) {
                return $this->success();
            } else {
                api_error(__('admin.save_error'));
            }
        } else {
            api_error(__('admin.save_error'));
        }
    }

    /**
     * 支付订单
     * @param Request $request
     */
    public function pay(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'note' => 'required'
        ], [
            'id.required' => '订单ID不能为空',
            'id.numeric' => '订单ID只能是数字',
            'note.required' => '备注不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $id = (int)$request->input('id');
        //开始支付
        $admin_user = $this->getUserInfo();
        $note = $request->input('note');

        $order = Order::find($id);
        if (!$order) {
            api_error(__('admin.order_error'));
        }
        $res = OrderService::payOrder($order, $admin_user, OrderLog::USER_TYPE_ADMIN, $note);
        if ($res === true) {
            return $this->success(true);
        } else {
            api_error($res);
        }
    }

    /**
     * 订单取消
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cancel(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'note' => 'required'
        ], [
            'id.required' => '订单ID不能为空',
            'id.numeric' => '订单ID只能是数字',
            'note.required' => '备注不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }

        $id = (int)$request->input('id');
        //开始取消
        $admin_user = $this->getUserInfo();
        $note = $request->input('note');

        $order = Order::find($id);
        if (!$order) {
            api_error(__('admin.order_error'));
        }
        $res = OrderService::cancel($order, $admin_user, OrderLog::USER_TYPE_ADMIN, $note);
        if ($res === true) {
            return $this->success(true);
        } else {
            api_error($res);
        }
    }

    /**
     * 订单发货
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delivery(Request $request)
    {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'order_goods_id' => 'required|array',
            'order_goods_id[]' => 'numeric',
            'company_id' => 'required|numeric',
            'code' => 'required',
        ], [
            'id.required' => '订单ID不能为空',
            'id.numeric' => '订单ID只能是数字',
            'order_goods_id.required' => '发货商品不能为空',
            'order_goods_id.array' => '发货商品不能为空',
            'order_goods_id[].numeric' => '发货商品不能为空',
            'company_id.required' => '物流ID不能为空',
            'company_id.numeric' => '物流ID格式错误',
            'code.required' => '物流单号不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            api_error(current($error));
        }
        $id = (int)$request->input('id');

        //开始发货
        $admin_user = $this->getUserInfo();
        $order_goods_id = $request->input('order_goods_id');
        $company_id = (int)$request->input('company_id');
        $code = $request->input('code');
        $note = $request->input('note');

        $order = Order::find($id);
        if (!$order) {
            api_error(__('admin.order_error'));
        }
        $res = OrderService::delivery($order, $order_goods_id, $company_id, $code, $admin_user, OrderLog::USER_TYPE_ADMIN, $note);
        if ($res === true) {
            return $this->success(true);
        } else {
            api_error($res);
        }
    }

    /**
     * 批量发货
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function batchDeliveryList(Request $request)
    {
        $id = $this->checkBatchId();
        if (!$id) {
            api_error(__('admin.invalid_params'));
        }

        $where = array();
        $where[] = ['status', Order::STATUS_PAID];
        $res_list = Order::select('id', 'm_id', 'order_no', 'flag', 'full_name', 'tel', 'prov', 'city', 'area', 'address', 'status')
            ->where($where)
            ->whereIn('id', $id)
            ->orderBy('id', 'desc')
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['status_text'] = Order::STATUS_DESC[$value['status']];
            $data_list[] = $_item;
        }
        $total = Order::where($where)->whereIn('id', $id)->count();
        return $this->success($data_list, $total);
    }

    /**
     * 批量发货提交
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function batchDeliverySubmit(Request $request)
    {
        $company_id = (int)$request->input('company_id');
        $address_id = (int)$request->input('address_id');
        $id = $this->checkBatchId();
        if (!$id || !$company_id || !$address_id) {
            api_error(__('admin.invalid_params'));
        }

        $express_company = ExpressCompany::select('title', 'code', 'customer_name', 'customer_pwd', 'month_code', 'send_site', 'pay_type')->where('id', $company_id)->first();
        if (!$express_company) {
            api_error(__('admin.express_company_error'));
        }

        $address = SellerAddress::where(['id' => $address_id, 'seller_id' => 1])->first();
        if (!$address) {
            api_error(__('admin.delivery_address_error'));
        }

        $where = array();
        $where[] = ['status', Order::STATUS_PAID];
        $res_list = Order::select('id', 'seller_id', 'order_no', 'status', 'full_name', 'tel', 'prov', 'city', 'area', 'address')->where($where)
            ->whereIn('id', $id)
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $admin_user = $this->getUserInfo();

        $error_order_no = array();
        $kdniao = new Kdniao();
        //开始发货操作
        foreach ($res_list->toArray() as $value) {
            $kdniao_delivery = $kdniao->order($value, $express_company, $address);
            if ($kdniao_delivery['status']) {
                //电子面单获取成功
                OrderService::apiDelivery($value, $express_company, $kdniao_delivery, $admin_user, OrderLog::USER_TYPE_ADMIN);
            } else {
                $error_order_no[] = $value['order_no'] . '【' . $kdniao_delivery['msg'] . '】';
            }
        }

        if (!$error_order_no) {
            return $this->success(true);
        } else {
            $order_no_str = join(',', $error_order_no);
            api_error('1|订单' . $order_no_str . '发货失败');
        }
    }

    /**
     * 批量打印发货单
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function printGoods(Request $request)
    {
        $id = $this->checkBatchId();
        if (!$id) {
            api_error(__('admin.invalid_params'));
        }

        $where = array();
        $res_list = Order::select('id', 'order_no', 'full_name', 'tel', 'prov', 'city', 'area', 'address', 'note', 'created_at as create_at', 'sell_price_total', 'delivery_price_real', 'promotion_price', 'discount_price', 'subtotal')
            ->where($where)
            ->whereIn('id', $id)
            ->orderBy('id', 'desc')
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $order_ids = array();
        foreach ($res_list as $value) {
            $order_ids[] = $value['id'];
        }
        $order_goods_res = OrderGoods::select('order_id', 'goods_title', 'sku_code', 'sell_price', 'buy_qty', 'spec_value', 'refund')->whereIn('order_id', array_unique($order_ids))->get();
        if ($order_goods_res->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        $order_goods = array();
        foreach ($order_goods_res as $value) {
            $_item = $value;
            $_item['refund'] = OrderGoods::REFUND_DESC[$value['refund']];
            $order_goods[$value['order_id']][] = $_item;
        }

        $data_list = array();
        foreach ($res_list as $key => $value) {
            $_item = $value;
            $_item['order_goods'] = isset($order_goods[$value['id']]) ? $order_goods[$value['id']] : [];
            $data_list[] = $_item;
        }
        return $this->success($data_list);
    }

    /**
     * 批量打印快递单
     * @param Request $request
     * @return array
     * @throws \App\Exceptions\ApiException
     */
    public function printDelivery(Request $request)
    {
        $id = $this->checkBatchId();
        if (!$id) {
            api_error(__('admin.invalid_params'));
        }

        $where = array();
        $res_list = OrderDeliveryTemplate::select('content')
            ->where($where)
            ->whereIn('order_id', $id)
            ->orderBy('id', 'desc')
            ->get();
        if ($res_list->isEmpty()) {
            api_error(__('admin.content_is_empty'));
        }
        return $this->success($res_list);
    }
}
