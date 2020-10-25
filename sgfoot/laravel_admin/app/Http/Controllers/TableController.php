<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function data()
    {
        return [
            [
                'id'         => 1,
                'name'       => '小明',
                'sex'        => 1,
                'company'    => '谷歌公司',
                'school'     => '耶鲁',
                'updated_at' => time(),
            ],
            [
                'id'         => 2,
                'name'       => '小芳',
                'sex'        => 0,
                'company'    => '丰田公司',
                'school'     => '早稻田大学',
                'updated_at' => time(),
            ],
            [
                'id'         => 3,
                'name'       => '小李',
                'sex'        => 1,
                'company'    => '索尼公司',
                'school'     => '东京大学',
                'updated_at' => time(),
            ],
            [
                'id'         => 4,
                'name'       => '小王',
                'sex'        => 1,
                'company'    => '松下公司',
                'school'     => '关西学院大学',
                'updated_at' => time(),
            ],
            [
                'id'         => 5,
                'name'       => '小余',
                'sex'        => 0,
                'company'    => '第一生命公司',
                'school'     => '神户国际大学',
                'updated_at' => time(),
            ],
        ];
    }

    public function form()
    {
        $title = '动态y表单';
        return view('demo.form', compact('title'));
    }
    public function xform()
    {
        $title = '动态x表单';
        return view('demo.xform', compact('title'));
    }
    public function lists()
    {
        if ($this->isPost()) {
            $data = $this->data();
            $i    = 1;
            foreach ($data as $key => $item) {
                $data[$key]['sid']           = $i;
                $data[$key]['update_format'] = Carbon::createFromTimestamp($item['updated_at'])->diffForHumans();
//                $item->update_format = $item->updated_at->diffForHumans();
                $i++;
            }
            $content = [
                'code'  => 0,
                'msg'   => '',
                'count' => count($data),
                'data'  => $data,
            ];
            return $content;
        }
        $title = '订单列表';
        return view('demo.table', compact('title'));
    }

    public function del()
    {
        $id = $this->request->input('id');
        return $this->setJson(0, '删除成功: ' . $id);
    }

    public function simple(){
        /**
         * ajax 请求的列表
         */
        if ($this->isPost()) {
            $order_sn     = $this->request->input('order_sn', '');
            $mobile       = $this->request->input('mobile', '');
            $order_status = $this->request->input('order_status', 0);
            $pay_status   = $this->request->input('pay_status', 0);
            $where        = '1';
            if (!empty($order_sn)) {
                $where .= ' and instr(order_sn, "' . $order_sn . '")';
            }
            if (!empty($mobile)) {
                $where .= ' and instr(mobile, "' . $mobile . '")';
            }
            if (intval($order_status) >= 0) {
                $where .= ' and order_status=' . $order_status;
            }
            if (intval($pay_status) >= 0) {
                $where .= ' and pay_status=' . $pay_status;
            }
            $data = Orders::orderBy('id', 'desc')
                ->whereRaw($where)
                ->paginate($this->request->input('limit'));
            $i    = 1;
            foreach ($data as $item) {
                $item->sid                 = $i;
                $item->update_format       = $item->updated_at->format('Y-m-d');
                $item->order_format        = $item->created_at->diffForHumans();
                $i++;
            }
            $list    = $data->toArray();
            $content = [
                'code'  => 0,
                'msg'   => '',
                'count' => $list['total'],
                'data'  => $list['data'],
            ];
            return $content;
        }
    }
}
