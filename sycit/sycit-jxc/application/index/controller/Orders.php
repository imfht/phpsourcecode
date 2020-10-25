<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/8/29
// +----------------------------------------------------------------------
// | Title:  Orders.php
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\index\model\Purchase;
use app\index\model\Customers;
use app\index\model\Finance AS FinanceModel;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;
use think\Url;

class Orders extends Common_base
{
    public function index() {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $query = $Request->param(); // 分页查询传参数
        $m = $Request->param('m');
        $k = $Request->param('k');
        $model = new Purchase();
        if ($m == 'pnumber' && $k !=='') {
            $list = $model->scope('pnumber', $k)->paginate('', false, ['query' => $query ]);

        } elseif ($m == 'status' && $k !=='') {
            $list = $model->scope('status', $k)->paginate('', false, ['query' => $query ]);

        } elseif ($m == 'affirm' && $k !=='') {
            $list = $model->scope('affirm', $k)->paginate('', false, ['query' => $query ]);

        } else {
            $list = $model->where('status','>=',0)->paginate();
        }

        //遍历获取订金金额
        foreach ($list AS $key=>$val) {
            //订单订金
            $amo_dj = FinanceModel::where('fpnumber',$val['pnumber'])->where('sort',1)->sum('amount');
            $list[$key]['amo_dj'] = '￥'.number_format($amo_dj,2);
            //订单余款
            $amo_yk = FinanceModel::where('fpnumber',$val['pnumber'])->where('sort',2)->sum('amount');
            $list[$key]['amo_yk'] = '￥'.number_format($amo_yk,2);
        }

        // 获取分页显示
        $page = $list->render();
        $assign = [
            'title' => '销售订单',
            'list'  => $list,
            'page'  => $page,
            'empty' => '<tr><td colspan="12" align="center">当前条件没有查到数据</td></tr>',
        ];
        $this->assign($assign);
        return $this->fetch();
        //p($list);
    }

    //
    public function add() {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        // 锁具查询
        $Fittings = Db::name('fittings_lock')->where('status',1)->field('lname,lprice')->select();
        // 产品系列查询
        $Number = Db::name('product_number')->where('status',1)->field('pn_id,pn_name,pn_price')->select();
        // 包边线查询
        $Baobian = Db::name('material_att')->select();
        // 颜色查询
        $Color = Db::name('product_color')->where('status',1)->field('pc_id,pc_name')->select();
        // 厚度单价
        $Thick = [
            '0'=>['name'=>'150','jg'=>'178'],
            '1'=>['name'=>'180','jg'=>'200'],
            '2'=>['name'=>'280','jg'=>'266'],
            '3'=>['name'=>'281','jg'=>'322'],
        ];
        foreach ($Thick as $aa=>$bb) {
            $Thick_aa[] = $bb['name'].':'.$bb['jg'];
            $Thick_bb = join(',',$Thick_aa);
        }

        //销售单号
        $StrOrderOne = StrOrderOne2();

        $assign = [
            'title' => '添加销售订单',
            'StrOrderOne' => $StrOrderOne,
            'shijian' => date('Y-m-d'),
            'Fittings' => $Fittings,
            'Number' => $Number,
            'Baobian' => $Baobian,
            'Color' => $Color,
            'Thick' => $Thick_bb,
        ];

        $this->assign($assign);
        return $this->fetch();
        //p($bb);
    }

    // 保存数据
    public function add_do() {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        if ($Request->isPost()) {
            //验证数据
            $validate = Loader::validate('Orders');
            if (!$validate->scene("add")->check($_POST)) {
                // dump($validate->getError()); // 输出 验证的 错误信息
                //$this->error($validate->getError());
            }

            //严谨判断优惠
            $youhui = isset($_POST['Preferential']) ? $_POST['Preferential'] : ''; //优惠
            if ($youhui =='' && $youhui <= '0' || $youhui >= '100') {
               $Preferential = '100';
            } else {
                $Preferential = $youhui;
            }

            $AmountSmall   = self::hasOrdersArray(filter_mount($_POST['AmountSmall'])); //总金额
            $OrderQuantity = self::hasOrdersArray($_POST['OrderQuantity']); //总数量
            $qiyename      = $_POST['qiyename']; //企业名称
            $pcus_id       = $_POST['pcus_id']; //企业id
            $StrOrderOne   = $_POST['StrOrderOne']; //销售订单号
            $kehumingcheng = $_POST['kehumingcheng']; //收货人
            //$shouhuodizhi  = $_POST['shouhuodizhi']; //收货地址
            //$fahuowuliu    = $_POST['fahuowuliu']; //发货物流
            $xiaoshouriqi  = $_POST['xiaoshouriqi']; //销售日期
            $fahuoriqi     = $_POST['fahuoriqi']; //发货日期
            //$lianxidianhua = $_POST['lianxidianhua']; //联系电话

            if (!Customers::get($pcus_id)) {
                $this->error('提交了错误信息');
            }

            $byDanhao = Purchase::get(['pnumber' => $StrOrderOne]);
            if ($byDanhao) {
                //$this->error('订单号重复，请刷新');
            }

            //过滤提交数据
            $Yanse    = self::hasOrdersArray($_POST['Yanse']); //产品颜色
            $Products = self::hasOrdersArray($_POST['Products']);  //产品编号系列
            $Chanph   = self::hasOrdersArray($_POST['Chanph']);  //产品编号数字
            $Breadth  = self::hasOrdersArray($_POST['Breadth']);  //规格-宽
            $Heiget   = self::hasOrdersArray($_POST['Heiget']);  //规格-高
            $Mianji   = self::hasOrdersArray($_POST['Mianji']);  //计算面积 m²
            $Thick    = self::hasOrdersArray($_POST['Thick']);  //规格-厚
            //$Thick    = $_POST['Thick'];  //规格-厚
            $Diaojiao = self::hasOrdersArray($_POST['Diaojiao']);  //吊脚高度
            $Attribute= self::hasOrdersArray($_POST['Attribute']);  //包边线属性
            $Baobian  = self::hasOrdersArray($_POST['Baobian']);  //包边线设置
            $Suoxiang = self::hasOrdersArray($_POST['Suoxiang']);  //锁向
            $Fittings = self::hasOrdersArray($_POST['Fittings']);  //锁具
            $Quantity = self::hasOrdersArray($_POST['Quantity']);  //数量
            $UnitPrice= self::hasOrdersArray($_POST['UnitPrice']);  //单价
            $Amount   = self::hasOrdersArray($_POST['Amount']);  //金额
            $Remark   = self::hasOrdersArray($_POST['Remark']);  //备注

            if (!empty($_POST)) {
                //unset($_POST); //转存变量后删除原数据
            }

            //判断提交的单行金额是否为空
            if (empty(array_filter($Amount))) {
                $this->error('提交的数据不完整1');
            }

            //以单行金额为准，删除多余空数据
            foreach ($Amount as $k=>$v) {
                if ($v=='' || $v<='0') {
                    //删除空值
                    unset($Amount[$k]);//金额
                    unset($Yanse[$k]);  //颜色
                    unset($Products[$k]);//编号系列
                    unset($Chanph[$k]);//编号数字
                    unset($Breadth[$k]);//规格-宽
                    unset($Heiget[$k]);//规格-高
                    unset($Mianji[$k]);//计算面积 m²
                    unset($Thick[$k]);//规格-厚
                    unset($Diaojiao[$k]);//吊脚高度
                    unset($Attribute[$k]);//包边线属性
                    unset($Baobian[$k]);//包边线设置
                    unset($Suoxiang[$k]);//锁向
                    unset($Fittings[$k]);//锁具
                    unset($Quantity[$k]);//数量
                    unset($UnitPrice[$k]);//单价
                    unset($Remark[$k]);//删除备注空值
                }
            }

            //重组后的数组数据判断是否还有空
            self::hasOrdersNumeral($Amount);//金额
            self::hasOrdersNumeral($Yanse);  //颜色
            self::hasOrdersNumeral($Products);//编号系列
            self::hasOrdersNumeral($Chanph);//编号数字
            self::hasOrdersNumeral($Breadth);//规格-宽
            self::hasOrdersNumeral($Heiget);//规格-高
            self::hasOrdersNumeral($Mianji);//计算面积 m²
            //self::hasOrdersNumeral($Thick);//规格-厚
            //self::hasOrdersNumeral($Diaojiao);//吊脚高度
            //self::hasOrdersNumeral($Attribute);//包边线属性
            //self::hasOrdersNumeral($Baobian);//包边线设置
            //self::hasOrdersNumeral($Suoxiang);//锁向
            //self::hasOrdersNumeral($Fittings);//锁具
            self::hasOrdersNumeral($Quantity);//数量
            self::hasOrdersNumeral($UnitPrice);//单价

            //p($Diaojiao);
            //exit();
            $purchase = new Purchase();
            $purchase->data([
                'pnumber' => $StrOrderOne,
                'pcus_id' => $pcus_id,
                'pbname' => $qiyename,
                'pcsname' => $kehumingcheng,
                //'pcsphone' => $lianxidianhua,
                'pyouhui' => $Preferential,
                'pamount' => $AmountSmall,
                'pcount' => $OrderQuantity,
                //'pfhwl' => $fahuowuliu,
                //'pshdz' => $shouhuodizhi,
                'pstart_date' => $xiaoshouriqi,
                'pend_date' => $fahuoriqi,
            ]);
            $result = $purchase->save();
            if ($result == true) {
                $data=array();
                $kai='1';
                foreach ($Amount as $key=>$val) {
                    $data[] = [
                        'xuhao'=> $kai++,
                        'ord_pnumber'=> $StrOrderOne,
                        'yanse'=> $Yanse[$key],
                        'products'=> $Products[$key],
                        'chanph'=> $Chanph[$key],
                        'breadth'=> $Breadth[$key],
                        'heiget'=> $Heiget[$key],
                        'mianji'=> $Mianji[$key],
                        'thick'=> $Thick[$key],
                        'diaojiao'=> $Diaojiao[$key],
                        'attribute'=> $Attribute[$key],
                        'baobian'=> $Baobian[$key],
                        'suoxiang'=> $Suoxiang[$key],
                        'fittings'=> $Fittings[$key],
                        'quantity'=> $Quantity[$key],
                        'unitPrice'=> $UnitPrice[$key],
                        'amount'=> $Amount[$key],
                        'remark'=> $Remark[$key],
                    ];
                }
                $db = Db::name('purchase_orders')->insertAll($data);
                //p($db);
                if ($db <= '0' || empty($db)) {
                    Db::name('purchase_orders')->where('ord_pnumber',$StrOrderOne)->delete();
                    Purchase::destroy(['pnumber'=>$StrOrderOne]);
                    $this->error('保存订单附表失败，请联系管理员');
                    //p($db);
                    //p('保存订单附表失败，请联系管理员');
                } else {
                    //p($db);
                    $this->success('保存订单成功，请尽快联系客户确认订单', Url::build('orders/index'));
                }
            } else {
                $this->error('保存数据失败，请联系管理员');
            }
        } else {
            return json('非法提交数据');
        }
    }

    //修改订单
    public function edit() {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $handle = $Request->param('handle');

        //查看或提交操作
        if ($handle == 'edit' && $Request->isPost()) {
            //修改提交操作
            //验证数据
            $validate = Loader::validate('Orders');
            if (!$validate->scene("edit")->check($_POST)) {
                // dump($validate->getError()); // 输出 验证的 错误信息
                $this->error($validate->getError());
            }
            $pid = isInteger($_POST['pid']) ? $_POST['pid'] : '';
            if (empty($pid)) {
                $this->error('参数错误1');
            }
            $model = new Purchase();
            $data = $model->where('pid', $pid)->find();
            if (empty($data)) {
                $this->error('参数错误2');
            }
            if ($data['affirm'] > '0' || $data['status'] >= '1' || $data['pshoudj'] > '0') {
                $this->error('此订单不能操作');
            }

            //严谨判断优惠
            $youhui = isset($_POST['Preferential']) ? $_POST['Preferential'] : ''; //优惠
            if ($youhui =='' && $youhui <= '0' || $youhui >= '100') {
                $Preferential = '100';
            } else {
                $Preferential = $youhui;
            }
            $AmountSmall   = self::hasOrdersArray(filter_mount($_POST['AmountSmall'])); //总金额
            $OrderQuantity = self::hasOrdersArray($_POST['OrderQuantity']); //总数量
            $StrOrderOne   = $_POST['StrOrderOne']; //销售订单号
            $fahuoriqi     = $_POST['fahuoriqi']; //发货日期
            //判断提交的订单号是否与保持的一致
            if ($data['pnumber'] !== $StrOrderOne ) {
                $this->error('你在非法提交数据');
            }

            //unset($data); //此时可删除判断的数据

            //过滤提交数据
            $Yanse    = self::hasOrdersArray($_POST['Yanse']); //产品颜色
            $Products = self::hasOrdersArray($_POST['Products']);  //产品编号系列
            $Chanph   = self::hasOrdersArray($_POST['Chanph']);  //产品编号数字
            $Breadth  = self::hasOrdersArray($_POST['Breadth']);  //规格-宽
            $Heiget   = self::hasOrdersArray($_POST['Heiget']);  //规格-高
            $Mianji   = self::hasOrdersArray($_POST['Mianji']);  //计算面积 m²
            $Thick    = self::hasOrdersArray($_POST['Thick']);  //规格-厚
            //$Thick    = $_POST['Thick'];  //规格-厚
            $Diaojiao = self::hasOrdersArray($_POST['Diaojiao']);  //吊脚高度
            $Attribute= self::hasOrdersArray($_POST['Attribute']);  //包边线属性
            $Baobian  = self::hasOrdersArray($_POST['Baobian']);  //包边线设置
            $Suoxiang = self::hasOrdersArray($_POST['Suoxiang']);  //锁向
            $Fittings = self::hasOrdersArray($_POST['Fittings']);  //锁具
            $Quantity = self::hasOrdersArray($_POST['Quantity']);  //数量
            $UnitPrice= self::hasOrdersArray($_POST['UnitPrice']);  //单价
            $Amount   = self::hasOrdersArray($_POST['Amount']);  //金额
            $Remark   = self::hasOrdersArray($_POST['Remark']);  //备注

            if (!empty($_POST)) {
                //unset($_POST); //转存变量后删除原数据
            }

            //判断提交的单行金额是否为空
            if (empty(array_filter($Amount))) {
                $this->error('提交的数据不完整1');
            }

            //以单行金额为准，删除多余空数据
            foreach ($Amount as $k=>$v) {
                if ($v=='' || $v<='0') {
                    //删除空值
                    unset($Amount[$k]);//金额
                    unset($Yanse[$k]);  //颜色
                    unset($Products[$k]);//编号系列
                    unset($Chanph[$k]);//编号数字
                    unset($Breadth[$k]);//规格-宽
                    unset($Heiget[$k]);//规格-高
                    unset($Mianji[$k]);//计算面积 m²
                    unset($Thick[$k]);//规格-厚
                    unset($Diaojiao[$k]);//吊脚高度
                    unset($Attribute[$k]);//包边线属性
                    unset($Baobian[$k]);//包边线设置
                    unset($Suoxiang[$k]);//锁向
                    unset($Fittings[$k]);//锁具
                    unset($Quantity[$k]);//数量
                    unset($UnitPrice[$k]);//单价
                    unset($Remark[$k]);//删除备注空值
                }
            }

            //重组后的数组数据判断是否还有空
            self::hasOrdersNumeral($Amount);//金额
            self::hasOrdersNumeral($Yanse);  //颜色
            self::hasOrdersNumeral($Products);//编号系列
            self::hasOrdersNumeral($Chanph);//编号数字
            self::hasOrdersNumeral($Breadth);//规格-宽
            self::hasOrdersNumeral($Heiget);//规格-高
            self::hasOrdersNumeral($Mianji);//计算面积 m²
            //self::hasOrdersNumeral($Thick);//规格-厚
            //self::hasOrdersNumeral($Diaojiao);//吊脚高度
            //self::hasOrdersNumeral($Attribute);//包边线属性
            //self::hasOrdersNumeral($Baobian);//包边线设置
            //self::hasOrdersNumeral($Suoxiang);//锁向
            //self::hasOrdersNumeral($Fittings);//锁具
            self::hasOrdersNumeral($Quantity);//数量
            self::hasOrdersNumeral($UnitPrice);//单价

            //保存主表数据
            $result = $model->allowField(['pyouhui','pamount','pcount','pend_date'])->save([
                'pyouhui' => $Preferential, //订单优惠
                'pamount' => $AmountSmall,  //订单金额
                'pcount' => $OrderQuantity, //订单数量
                'pend_date' => $fahuoriqi,  //发货日期
            ],['pnumber' => $StrOrderOne]);

            //保持附加表数据
            if ($result == true) {
                //查找附表旧数据
                $byjiu = Db::name('purchase_orders')->where('ord_pnumber',$StrOrderOne)->field('oid')->select();
                $item=array();
                $kai='1';

                foreach ($Amount as $key=>$val) {
                    $item[] = [
                        'xuhao'=> $kai++,
                        'ord_pnumber'=> $StrOrderOne,
                        'yanse'=> $Yanse[$key],
                        'products'=> $Products[$key],
                        'chanph'=> $Chanph[$key],
                        'breadth'=> $Breadth[$key],
                        'heiget'=> $Heiget[$key],
                        'mianji'=> $Mianji[$key],
                        'thick'=> $Thick[$key],
                        'diaojiao'=> $Diaojiao[$key],
                        'attribute'=> $Attribute[$key],
                        'baobian'=> $Baobian[$key],
                        'suoxiang'=> $Suoxiang[$key],
                        'fittings'=> $Fittings[$key],
                        'quantity'=> $Quantity[$key],
                        'unitPrice'=> $UnitPrice[$key],
                        'amount'=> $Amount[$key],
                        'remark'=> $Remark[$key],
                    ];
                }

                $orders = Db::name('purchase_orders')->insertAll($item);
                if ($orders <= '0' || empty($orders)) {
                    //保存附表失败后 返回旧主表数据
                    Db::table('purchase')->where('pid', $data['pid'])->update([
                        'pbname' => $data['pbname'],
                        'pcsname' => $data['pcsname'],
                        'pcsphone' => $data['pcsphone'],
                        'pyouhui' => $data['pyouhui'],
                        'pamount' => $data['pamount'],
                        'pcount' => $data['pcount'],
                        'pfhwl' => $data['pfhwl'],
                        'pshdz' => $data['pshdz'],
                        'pstart_date' => $data['pstart_date'],
                        'pend_date' => $data['pend_date'],
                        'create_time' => $data['create_time'],
                        'update_time' => $data['update_time'],
                    ]);

                    $this->error('保存订单失败，请联系管理员');
                } else {
                    //删除附表旧数据
                    if (!empty($byjiu)) {
                        foreach ($byjiu AS $kb=>$vb) {
                            Db::name('purchase_orders')->delete($vb);
                        }
                    }
                    $this->success('修改订单成功', Url::build('orders/index'));
                }

            } else {
                $this->error('保存数据失败，请联系管理员');
            }

        } else {
            //修改查看操作
            $id = $Request->param('pid');
            $pid = isInteger($id) ? $id : '';
            if (empty($pid)) {
                $this->error('参数错误1');
            }
            $model = new Purchase();
            $data = $model->where('pnumber', $pid)->find();
            if (empty($data)) {
                $this->error('参数错误2');
            }
            if ($data['affirm'] > '0' || $data['status'] >= '1' || $data['pshoudj'] > '0') {
                $this->error('此订单不能操作');
            }
            $list = Db::name('purchase_orders')->where('ord_pnumber', $data['pnumber'])->order('xuhao', 'asc')->select();
            // 锁具查询
            $Fittings = Db::name('fittings_lock')->where('status',1)->field('lname,lprice')->select();
            // 产品系列查询
            $Number = Db::name('product_number')->where('status',1)->field('pn_id,pn_name,pn_price,pn_baobian')->select();
            //编号
            $Bancai = Db::name('bancai_list')->field('blname')->select();
            // 包边线查询
            $Baobian = Db::name('material_att')->select();
            // 颜色查询
            $Color = Db::name('product_color')->where('status',1)->field('pc_id,pc_name')->select();

            // 厚度单价和包边分类
            foreach ($list AS $keyL=>$valL) {
                $num = Db::name('product_number')->where('pn_name', $valL['products'])->field('pn_price,pn_baobian')->find();

                $baob = Db::name('others_baobian')
                    ->where('bname', $num['pn_baobian'])
                    ->where('bval', $valL['baobian'])->find();

                $suoju = Db::name('fittings_lock')->where('lname', $valL['fittings'])->field('lprice')->find();

                $bsun = Db::name('others_baobian')
                    ->where('bname', $num['pn_baobian'])
                    ->field('bremark,bname', true)->select();

                //厚度金额计算
                $list[$keyL]['Thick_qhjc'] = $baob['qhjc'];
                $list[$keyL]['Thick_qhdz'] = $baob['qhdz'];
                $list[$keyL]['Thick_qhdzamo'] = $baob['qhdzamo'];
                //产品系列单价
                $list[$keyL]['Products_price'] = $num['pn_price'];
                //包边线单价
                $list[$keyL]['Baobian_price'] = $baob['bamo'];
                //锁具单价
                $list[$keyL]['Fittings_price'] = $suoju['lprice'];
                //包边线select
                $list[$keyL]['bsun'] = $bsun;
            }


            $assign = [
                'title' => '修改订单',
                'data' => $data,
                'list' => $list,
                'Fittings' => $Fittings,
                'Number' => $Number,
                'Color' => $Color,
                'Baobian' => $Baobian,
                'empty' => '<tr><td colspan="16" style="height: 30px;padding: 10px;"><span style="color: #9E9E9E;font-size: 18px;">还未添加产品颜色</span></td></tr>',
            ];
            $this->assign($assign);
            return $this->fetch();
            //p($list);
        }
    }

    //查看订单
    public function view() {
        // 是否有权限
        IS_ROOT([1,2,4])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $pid = $Request->param('pid');
        $pnumber = isInteger($pid) ? $pid : '';
        if (empty($pnumber)) {
            $this->error('参数错误');
        }
        $model = new Purchase();
        $data = $model->where('pnumber', $pnumber)->find();
        if (empty($data)) {
            $this->error('参数错误');
        }
        //$rows = '5'; //每页的数据
        //$query = $Request->param(); // 分页查询传参数
        $list = Db::name('purchase_orders')->where('ord_pnumber', $pnumber)->order('xuhao', 'asc')->select();

        //统计几个序号
        $count = Db::name('purchase_orders')->where('ord_pnumber',$data['pnumber'])->count();

        //备注合并为一
        $remark = array();
        foreach ($list as $key=>$val) {
            if ($val['remark'] != '') {
                $remark[] = $val['remark'];
            }
        }

        // 获取分页显示
        //$page = $list->render();
        $assign = [
            'title' => '查看订单',
            'data' => $data,
            'list' => $list,
            'count' => $count,
            'remark' => join(' - ', $remark),
            //'page' => $page,
            //'rows' => $rows,
            'empty' => '<tr><td colspan="15" style="height: 30px;padding: 10px;"><span style="color: #9E9E9E;font-size: 18px;">还未添加产品</span></td></tr>',
        ];

        $this->assign($assign);
        return $this->fetch();
    }

    // 查询数据
    public function getSelect() {
        $Request = Request::instance();
        if ($Request->isPost()) {
            // 锁具查询
            $Fittings = [
                '0'=> [
                    'name'=>'至尊锁',
                    'jg' =>'120',
                ],
                '1'=> [
                    'name'=>'白至尊',
                    'jg' =>'135',
                ],
                '2'=> [
                    'name'=>'豪华锁',
                    'jg' =>'150',
                ],
            ];
            // 产品系列查询
            $Products = [
                '0'=>[
                    'name'=>'P',
                    'jg'=>'655'
                ],
                '1'=>[
                    'name'=>'W',
                    'jg'=>'800'
                ],
                '2'=>[
                    'name'=>'D',
                    'jg'=>'700'
                ],
                '3'=>[
                    'name'=>'Q',
                    'jg'=>'616'
                ]
            ];
            // 包边线查询
            $Baobian = [
                '0'=>['name'=>'单包','jg'=>'0'],
                '1'=>['name'=>'双包','jg'=>'20'],
            ];
            // 颜色查询
            $Yanse = [
                '0'=>['name'=>'q','jg'=>'0'],
                '1'=>['name'=>'w','jg'=>'20'],
            ];

            switch ($Request->param('name')) {
                case 'Fittings':
                    $data = $Fittings;
                    $code = '1';
                    break;
                case 'Products':
                    $data = $Products;
                    $code = '1';
                    break;
                case 'Baobian':
                    $data = $Baobian;
                    $code = '1';
                    break;
                case 'Yanse':
                    $data = $Yanse;
                    $code = '1';
                    break;
                default:
                    $data = '';
                    $code = '0';
            }
            //$data = ['name'=>'thinkphp','url'=>'thinkphp.cn'];
            return ['data'=>$data,'code'=>$code,'q'=>$Request->param('name')];
            //$result =array('data'=>$data,'code'=>$code);
            //echo json_encode($result);//返回数据
        }
    }

    // 计算面积
    public function calculating() {
        //$breadth 宽  //$heiget 高  //$area 面积  //$area_amount 面积金额  //$thick 厚  //$thick_amount 厚单价
        //is_int 判断是否整数
        $Request = Request::instance();
        if ($Request->isPost()) {
            //
            $breadth = $Request->param('breadth');
            $heiget  = $Request->param('heiget');
            $thick   = $Request->param('thick');
            $post = [
                'breadth'  => $breadth,
                'heiget'  => $heiget,
                'thick'  => $thick,
                '__token__' => $Request->param('__token__'),
            ];
            $validate = Loader::validate('Orders');
            if (!$validate->scene('add')->check($post)) {
                // dump($validate->getError()); // 输出 验证的 错误信息
                $this->error($validate->getError());
            }
            p($Request->param());
        }
    }

    //查找企业名称
    public function select_cusname() {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $query = $Request->param(); // 分页查询传参数
        $q = $Request->param('q');
        $model = new Customers();
        //$join = [
        //    ['customers_premises p', 'c.cus_id=p.pre_cus_id'],
        //    ['logistics l', 'p.pre_log_id=l.log_id']
        //];
        //$field = [
        //    'c.cus_id','c.cus_name','p.pre_name','p.pre_phone','p.pre_prov','p.pre_city','p.pre_dist','l.log_name'
        //];
        if (!empty($q)) {
            //$list = Db::name('customers')->alias('c')
            //    ->join($join )
            //    ->where('c.cus_name|c.cus_duty', 'like', '%'.$q.'%')
            //    ->where('c.status', '=',1)
            //    ->field($field)
            //    ->order('c.cus_id','deas')
            //    ->paginate('10', false, ['query' => $query ]);
            $list = $model->where('cus_name|cus_duty', 'like', '%'.$q.'%')
                ->where('status','=',1)
                ->order('cus_id','deas')
                ->paginate('10', false, ['query' => $query ]);
        } else {
            //$list = Db::name('customers')->alias('c')
            //    ->join($join )
            //    ->where('c.status', '=',1)
            //    ->field($field)
            //    ->order('c.cus_id','deas')
            //    ->paginate(10);
            $list = $model->where('status','=',1)
                ->order('cus_id','deas')
                ->paginate('10', false, ['query' => $query ]);
        }
        // 获取分页显示
        $page = $list->render();
        $assign = [
            'list' => $list,
            'page' => $page,
            'empty' => '<tr><td colspan="6" align="center">当前条件没有查到数据</td></tr>',

        ];
        $this->assign($assign);
        return $this->fetch();
        //p($assign);
    }

    //查询包边线设置
    public function select_baobian() {
        $Request = Request::instance();
        $pid = $Request->param('pid');
        if ($Request->isPost()) {

            $number = Db::name('product_number')->where('pn_name', $pid)->field('pn_baobian')->find();

            $baobian = Db::name('others_baobian')->where('bname', $number['pn_baobian'])->field('bremark,bname', true)->select();

            if (!empty($number) || !empty($baobian)) {
                $data = $baobian;
                $code = '1';

            } else {
                $data = '';
                $code = '0';
            }
            return ['code'=>$code,'data'=>$data];
            //p($baobian);
        } else {
            return ['code'=>0,'data'=>''];
        }
    }

    //废除订单
    public function scrap() {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        if ($Request->isPost()) {
            $id = $Request->param('pid');
            if (empty($id)) {
                $this->error('错误参数');
            }
            $model = new Purchase();
            $by = $model->where('pid', $id)->find();
            if (empty($by)) {
                $this->error('不晓得建国后不许成精麽……');
            }
            if ($by['affirm'] > '0' || $by['status'] >= '1' || $by['pshoudj'] > '0') {
                $this->error('此订单不能操作');
            }
            $model->allowField(['status'])->save(['status'=>'-1'],['pid'=>$id]);
            $this->success('订单已废除',Url::build('orders/index'));
        } else {
            $this->error('非法提交数据');
        }
    }

    //恢复订单
    public function huifu() {
        // 是否有权限
        IS_ROOT([1])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        if ($Request->isPost()) {
            $id = $Request->param('pid');
            if (empty($id)) {
                $this->error('错误参数');
            }
            $model = new Purchase();
            $by = $model->where('pid', $id)->find();
            if (empty($by)) {
                $this->error('不晓得建国后不许成精麽……');
            }
            if ($by['status'] >= '0') {
                $this->error('此订单不能操作');
            }
            $model->allowField(['status'])->save(['status'=>'0'],['pid'=>$id]);
            $this->success('订单已恢复正常',Url::build('orders/index'));
        } else {
            $this->error('非法提交数据');
        }
    }

    //删除订单
    public function delete() {
        // 是否有权限
        IS_ROOT([1,2])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        if ($Request->isPost()) {
            $pid = $Request->param('pid');
            if (empty($pid)) {
                $this->error('错误参数');
            }
            //主表 purchase
            Db::name('purchase')->where('pnumber', $pid)->delete();
            //附表 purchase_affirm
            Db::name('purchase_affirm')->where('a_pnumber', $pid)->delete();
            //附表 purchase_orders
            Db::name('purchase_orders')->where('ord_pnumber', $pid)->delete();
            $this->success('操作成功',Url::build('orders/index'));
        } else {
            $this->error('非法提交数据');
        }
    }

    //提交的销售订单做数组判断 第一次过滤
    public function hasOrdersArray($data) {
        if (empty($data)) {
            return $this->error('提交的数据不完整2');
        }
        if (is_array($data)) {
            $item = array();
            foreach ($data as $k=>$v) {
                $item[] = filterOrders($v);
            }
            return $item;
        } else {
            //return $this->error('你在非法提交数据', Url::build('login/logout'));
            if ($data <= '0' || $data =='') {
                return $this->error('提交的数据不完整3');
            } else {
                return $data;
            }
        }
    }

    //判断提交的数据是否为空或为0 第二次判断
    public function hasOrdersNumeral($data) {
        if (is_array($data)) {
            foreach ($data as $k=>$v) {
                if ($v == '' || $v <= '0') {
                    $this->error('你提交了有个别空数据');
                }
            }
        }
    }
}