<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/9/25
// +----------------------------------------------------------------------
// | Title:  Schedule.php
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\index\model\Bancai;
use app\index\model\BancaiList;
use app\index\model\ProductColor;
use app\index\model\ProductNumber;
use app\index\model\Purchase;
use app\index\model\PurchaseOrders;
use app\index\model\Stockpile;
use app\index\model\StockpileLock;
use think\Request;
use think\Db;

class Schedule extends Common_base
{
    //生产订单
    public function index() {
        IS_ROOT([1,5])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $query = $Request->param(); // 分页查询传参数
        $m = $Request->param('m');
        $k = $Request->param('k');
        $purchase = new Purchase();
        if ($m == 'pnumber' && $k !=='') {
            $list = $purchase->scope('pnumber', $k)->paginate('', false, ['query' => $query ]);

        } else {
            $list = $purchase->where('affirm','=',1)->where('pshoudj','=',1)->where('status','=',1)->whereOr('status','=',2)->paginate();
        }

        // 获取分页显示
        $page = $list->render();
        $assign = [
            'title' => '生产订单',
            'list' => $list,
            'page' => $page,
            'empty' => '<tr><td colspan="8" align="center">当前条件没有查到数据</td></tr>',
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //订单查看
    public function view() {
        IS_ROOT([1,5])  ? true : $this->error('没有权限');
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

        $assign = [
            'title' => '订单查看',
            'data' => $data,
            'list' => $list,
            'count' => $count,
            'remark' => join(' - ', $remark),
            'empty' => '<tr><td colspan="15" style="height: 30px;padding: 10px;"><span style="color: #9E9E9E;font-size: 18px;">还未添加产品</span></td></tr>',
        ];

        $this->assign($assign);
        return $this->fetch();
    }

    //开始生产 model
    public function in() {
        IS_ROOT([1,5])  ? true : $this->error('没有权限');
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
        if ($Request->isPost()) {
            //提交动作
            //验证数据
            $result = $this->validate(['__token__' => $_POST['__token__']],['__token__'=>'token']);
            if(true !== $result){
                //$this->error('数据提交错误，请返回刷新');
            }
            //self::deduction($pnumber);
            //exit();

            $result = $model->allowField('status')->save(['status'=>2],['pnumber'=>$pnumber]);
            if ($result) {
                //扣除库存
                self::deduction($pnumber);

                $this->success('操作成功');
            } else {
                $this->error('保存数据失败，请联系管理员');
            }
        } else {
            $assign = [
                'data'  => $data,
            ];
            $this->assign($assign);
            return $this->fetch();
        }
    }

    //完成订单 list
    public function shengchan() {
        IS_ROOT([1,5])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $query = $Request->param(); // 分页查询传参数
        $m = $Request->param('m');
        $k = $Request->param('k');
        $purchase = new Purchase();
        if ($m == 'pnumber' && $k !=='') {
            $list = $purchase->scope('pnumber', $k)->paginate('', false, ['query' => $query ]);

        } else {
            $list = $purchase->where('status','egt',2)->where('status','elt',4)->paginate();
        }

        // 获取分页显示
        $page = $list->render();
        $assign = [
            'title' => '完成订单',
            'list' => $list,
            'page' => $page,
            'empty' => '<tr><td colspan="8" align="center">当前条件没有查到数据</td></tr>',
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //生产完成 model
    public function shengcwc() {
        IS_ROOT([1,5])  ? true : $this->error('没有权限');
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
        if ($Request->isPost()) {
            //提交动作
            //验证数据
            $result = $this->validate(['__token__' => $Request->param('__token__')],['__token__'=>'token']);
            if(true !== $result || empty($Request->param('ok'))){
                $this->error('数据提交错误，请返回刷新');
            }
            if ($data['pshoudj'] >= 2) {
                $status = 4;
            } else {
                $status = 3;
            }
            $result = $model->allowField(['pshengcwc','status'])->save(['pshengcwc'=>'1','status'=>$status],['pnumber'=>$pnumber]);
            if ($result) {
                $this->success('操作成功');
            } else {
                $this->error('保存数据失败，请联系管理员');
            }
        } else {
            $assign = [
                'data'  => $data,
            ];
            $this->assign($assign);
            return $this->fetch();
        }
    }

    //订单出库 model
    public function chuku() {
        IS_ROOT([1,4])  ? true : $this->error('没有权限');
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
        if ($Request->isPost()) {
            //提交动作
            //验证数据
            $pend_date = $Request->param('pend_date');
            $result = $this->validate(['__token__' => $Request->param('__token__')],['__token__'=>'token']);
            if(true !== $result || empty($Request->param('ok'))){
                $this->error('数据提交错误，请返回刷新');
            }
            $result = $model->allowField(['pend_date','status'])->save(['pend_date'=>$pend_date,'status'=>'5'],['pnumber'=>$pnumber]);
            if ($result) {
                $this->success('操作成功');
            } else {
                $this->error('保存数据失败，请联系管理员');
            }
        } else {
            $assign = [
                'data'  => $data,
            ];
            $this->assign($assign);
            return $this->fetch();
        }
    }

    //发货订单查看和打印
    public function delivery() {
        IS_ROOT([1,4,5])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $pnumber = $Request->param('pid');
        //$pnumber = isInteger($pid) ? $pid : '';
        if (empty($pnumber)) {
            $this->error('参数错误');
        }
        $model = new Purchase();
        $data = $model->where('pnumber', $pnumber)->find();
        if (empty($data)) {
            $this->error('参数错误');
        }
        //详细清单
        $list = Db::name('purchase_orders')->where('ord_pnumber', $pnumber)->order('xuhao', 'asc')->select();
        //物流信息
        //$pfhwl = Db::name('logistics')->where('log_name',$data['pfhwl'])->find();
        //统计附表总金额
        $sum_amount = Db::name('purchase_orders')->where('ord_pnumber',$data['pnumber'])->sum('amount');
        //统计几个序号
        $count = Db::name('purchase_orders')->where('ord_pnumber',$data['pnumber'])->count();
        //计算优惠后的金额
        $youhuije = ($sum_amount * 1) - ($data['pamount'] * 1);
        //备注合并为一
        $remark = array();
        foreach ($list as $key=>$val) {
            if ($val['remark'] != '') {
                $remark[] = $val['remark'];
            }
        }

        $assign = [
            'title' => '出库订单',
            'data' => $data,
            'list' => $list,
            'count' => $count,
            'remark' => join(' - ', $remark),
            //'pfhwl' => $pfhwl,
            'youhuije' => $youhuije,
            'empty' => '<tr><td colspan="15" style="height: 30px;padding: 10px;"><span style="color: #9E9E9E;font-size: 18px;">还未添加产品</span></td></tr>',
        ];

        $this->assign($assign);
        return $this->fetch();
        //p();
    }

    //历史订单
    public function endck() {
        IS_ROOT([1,5])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $query = $Request->param(); // 分页查询传参数
        $m = $Request->param('m');
        $k = $Request->param('k');
        $model = new Purchase();
        if ($m == 'pnumber' && $k !=='') {
            $list = $model->scope('pnumber', $k)->where('status','=',5)->paginate('', false, ['query' => $query ]);
        } else {
            $list = $model->where('status','egt',5)->paginate('30');
        }

        // 获取分页显示
        $page = $list->render();
        $assign = [
            'title' => '交货订单',
            'list' => $list,
            'page' => $page,
            'empty' => '<tr><td colspan="8" align="center">当前条件没有查到数据</td></tr>',
        ];
        $this->assign($assign);
        return $this->fetch();
    }

    //打印小标签
    public function lodop() {
        IS_ROOT([1,5])  ? true : $this->error('没有权限');
        $Request = Request::instance();
        $pid = $Request->param('pid');
        $pnumber = isInteger($pid) ? $pid : '';
        if (empty($pnumber)) {
            $this->error('参数错误');
        }
        $model = new Purchase();
        $data = $model->where('pnumber', $pnumber)->where('status','>=',2)->find();
        if (empty($data)) {
            $this->error('订单未生产');
        }
        //详细清单
        $list = Db::name('purchase_orders')->where('ord_pnumber', $pnumber)->order('xuhao', 'asc')->field('yanse,products,chanph,breadth,heiget,thick,suoxiang,quantity')->select();

        //统计订单数量
        $count = Db::name('purchase_orders')->where('ord_pnumber', $pnumber)->sum('quantity');

        //遍历数组转为二维数组
        $array = array();
        foreach ($list as $k=>$v) {
            if ($v['quantity'] >= 1) {
                for ($i=0;$i<$v['quantity'];$i++) {
                    $array[] = [
                        'yanse' => $v['yanse'],
                        'products' => $v['products'],
                        'chanph' => $v['chanph'],
                        'breadth' => $v['breadth'],
                        'heiget' => $v['heiget'],
                        'thick' => $v['thick'],
                        'suoxiang' => $v['suoxiang'],
                        'quantity' => $v['quantity'],
                    ];
                }
                //unset($list[$k]); //删除指定数组下标
            }
        }

        //$lodop = array_merge($list, $array);
        //$lodop = $array;
        unset($list); //删除指定数组

        //收货地址
        //$shouhuo = Db::name('customers_premises')->where('pre_cus_id', $data['pcus_id'])->field('pre_prov,pre_city,pre_dist')->find();

        $assign = [
            'title' => '打印标签',
            'data' => $data,
            'list' => $array,
            'count' => $count,
            //'shouhuo' => $shouhuo,
            'empty' => '<tr><td colspan="15" style="height: 30px;padding: 10px;"><span style="color: #9E9E9E;font-size: 18px;">还未添加产品</span></td></tr>',
        ];

        $this->assign($assign);
        return $this->fetch();
        //p($count);
        //p($array);
    }

    //导出小标签
    public function lodop_excel2() {
        $Request = Request::instance();
        $pid = $Request->param('pid');
        $pnumber = isInteger($pid) ? $pid : '';
        if (empty($pnumber)) {
            $this->error('参数错误');
        }
        //访问模块 $excelData
        $Customers = new Customers();

        $model = new Purchase();
        $data = $model->where('pnumber', $pnumber)->where('status','>=',2)->find();
        if (empty($data)) {
            $this->error('订单未生产');
        }
        //详细清单
        $list = Db::name('purchase_orders')->where('ord_pnumber', $pnumber)->order('xuhao', 'asc')->field('yanse,products,chanph,breadth,heiget,thick,suoxiang,quantity')->select();
        //遍历数组转为二维数组
        $array = array();
        foreach ($list as $k=>$v) {
            for ($i=1;$i<=$v['quantity'];$i++) {
                $array[$i]['pnumber'] = $data["pnumber"]; //单号
                $array[$i]['pcsname'] = $data["pcsname"]; //客户名称
                $array[$i]['cus_prov'] = mb_substr($data["pcus_id"]["cus_prov"], 0, 2, 'utf-8'); //地址 省
                $array[$i]['cus_city'] = mb_substr($data["pcus_id"]["cus_city"], 0, 2, 'utf-8'); //地址 市
                $array[$i]['cus_dist'] = mb_substr($data["pcus_id"]["cus_dist"], 0, 2, 'utf-8'); //地址 县
                $array[$i]['quantity'] = $v['quantity']."-".$i; //包装编号
                $array[$i]['yanse'] = $v['yanse']; //颜色
                $array[$i]['products'] = $v['products']; //系列名称
                $array[$i]['chanph'] = $v['chanph']; //系列编号
                $array[$i]['breadth'] = $v['breadth']; //规格宽
                $array[$i]['heiget'] = $v['heiget']; //规格高
                $array[$i]['thick'] = $v['thick']; //规格厚
                $array[$i]['suoxiang'] = $v['suoxiang']; //锁向
            }
            unset($list[$k]); //删除指定数组下标
        }

        $lodop = array_merge($list, $array);
        unset($list); //删除指定数组

        if (is_array($lodop) === false) {
            $this->error('数据错误');
        }

        p($lodop);

        //开始导出
        $filename = $data["pnumber"]."-标签"; //文件名
        $this->exportExcel($lodop, $filename, '','Sheet1');

    }

    public function lodop_excel() {
        $Request = Request::instance();
        $pid = $Request->param('pid');
        $pnumber = isInteger($pid) ? $pid : '';
        if (empty($pnumber)) {
            $this->error('参数错误');
        }
        //访问模块 $excelData
        $Customers = new Customers();

        $model = new Purchase();
        $data = $model->where('pnumber', $pnumber)->where('status','>=',2)->find();
        if (empty($data)) {
            $this->error('订单未生产');
        }
        //详细清单
        $list = Db::name('purchase_orders')->where('ord_pnumber', $pnumber)->order('xuhao', 'asc')->field('yanse,products,chanph,breadth,heiget,thick,suoxiang,quantity')->select();
        //遍历数组转为二维数组
        $array = array();
        foreach ($list as $k=>$v) {
            if ($v['quantity'] > 1) {
                for ($i=0;$i<$v['quantity'];$i++) {
                    $array[$i]['yanse'] = $v['yanse'];
                    $array[$i]['products'] = $v['products'];
                    $array[$i]['chanph'] = $v['chanph'];
                    $array[$i]['breadth'] = $v['breadth'];
                    $array[$i]['heiget'] = $v['heiget'];
                    $array[$i]['thick'] = $v['thick'];
                    $array[$i]['suoxiang'] = $v['suoxiang'];
                }
                unset($list[$k]); //删除指定数组下标
            }
        }

        $lodop = array_merge($list, $array);
        unset($list); //删除指定数组
        //p($lodop);
        //exit();

        //开始导出
        $filename = $data["pnumber"]."-标签.xls"; //文件名

        $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";

        $str .= "<style>\r\n";
        $str .= "table tr td{width:51px;height:66px;text-align:center;vertical-align:middle;font-size:18px;}";
        $str .= "</style>\r\n";
        $str .= "\r\n</head>\r\n<body>";

        //
        $str .= "\n<table>\n<tbody>\n";
        $str .= "<tr>\n";
        $str .= "<td style=\"height: 0;border: 0;\">&nbsp;</td>";
        $str .= "<td style=\"height: 0;border: 0;\">&nbsp;</td>";
        $str .= "<td style=\"height: 0;border: 0;\">&nbsp;</td>";
        $str .= "<td style=\"height: 0;border: 0;\">&nbsp;</td>";
        $str .= "<td style=\"height: 0;border: 0;\">&nbsp;</td>";
        $str .= "<td style=\"height: 0;border: 0;\">&nbsp;</td>";
        $str .= "<td style=\"height: 0;border: 0;\">&nbsp;</td>";
        $str .= "\n</tr>\n";
        $str .= "</tbody>\n</table>\n";
            //开始循环
        $i = 1;

        foreach ($lodop as $key=>$val)
        {
            //
            $str .= "\n<table border='1'>\n<tbody>\n";
            $str .= "<tr>\n";
            $str .= "<td>客户<br>名称</td><td colspan=\"3\" style=\"font-weight:600;font-size:26px;\">".$data["pcsname"]."</td><td>包装<br>编号</td><td colspan=\"2\" style=\"vnd.ms-excel.numberformat:@;font-size:24px;font-weight:600;\">".$data["pcount"]."-".$i."</td>";
            $str .= "</tr>\n";
            //
            $str .= "<tr>\n";
            $str .= "<td>收货<br>地址</td>";
            $str .= "<td colspan=\"2\" style=\"font-weight:600;font-size:26px;\">".mb_substr($data["pcus_id"]["cus_prov"], 0, 2, 'utf-8')."</td>";
            $str .= "<td colspan=\"2\" style=\"font-weight:600;font-size:26px;\">".mb_substr($data["pcus_id"]["cus_city"], 0, 2, 'utf-8')."</td>";
            $str .= "<td colspan=\"2\" style=\"font-weight:600;font-size:26px;\">".mb_substr($data["pcus_id"]["cus_dist"], 0, 2, 'utf-8')."</td>";
            $str .= "</tr>\n";
            //
            $str .= "<tr>\n";
            $str .= "<td>产品<br>信息</td>";
            $str .= "<td colspan=\"2\" style=\"font-size: 14px;\">订单号：<br>".$data["pnumber"]."</td>";
            $str .= "<td colspan=\"4\" style=\"font-size: 14px;\">";
            $str .= $val["yanse"]."&nbsp;&nbsp;&nbsp;".$val["suoxiang"]."<BR/>"; //颜色
            $str .= $val["products"].$val["chanph"]."&nbsp;&nbsp;"; //型号
            $str .= $val["breadth"]."*".$val["heiget"]."*".$val["thick"]; //规格
            //$str .= "<BR/>".$val["suoxiang"]; //锁向
            $str .= "</td>";
            $str .= "</tr>\n";
            //结束
            $str .= "</tbody>\n</table>\n";
            //$str .= "<BR/>\n";
            $i++;
        }
        $str .= "</body>\r\n</html>";
        header( "Content-Type: application/vnd.ms-excel; name='excel'" );
        header( "Content-type: application/octet-stream" );
        header( "Content-Disposition: attachment; filename=".$filename );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Pragma: no-cache" );
        header( "Expires: 0" );
        exit( $str );
    }

    //扣除关联库存
    public function deduction($pnumber) {
        if (empty($pnumber)) {
            return $this->error('数据不完整，关联库存未扣除');
        }
        //扣除库存
        $Orders = Db::name('purchase_orders')->where('ord_pnumber', $pnumber)
            ->field('yanse,products,chanph,attribute,baobian,fittings,quantity')->select();

        foreach ($Orders as $key=>$val) {
            //查询颜色ID
            $pcid = Db::name('product_color')->where('pc_name', $val['yanse'])->field('pc_id')->find();
            //查询系列
            //$xulid = ProductNumber::get(['pn_name'=>$val['products']]);
            //查询板材
            $bclist = BancaiList::get(['blname'=>$val['chanph']]);
            //查询原料设定
            $blid = Db::name('material_set')->where('ms_pnid',$val['products'])
                ->where('ms_blname', $val['chanph'])
                ->where('ms_maname', $val['attribute'])
                ->where('ms_baobian', $val['baobian'])->find();
            //扣除板材库存 'bpcid'=>$pcid['pc_id']颜色ID，bplid'=>$bclist['blid']板材ID
            Bancai::where(['bpcid'=>$pcid['pc_id'], 'bplid'=>$bclist['blid']])->update(['bquantity'=>['exp', 'bquantity -'.$val['quantity']]]);
            //扣除关联料型
            if (!empty($blid['ms_gl'])) {
                //转换数组
                $blgl = explode(',', $blid['ms_gl']);
                foreach ($blgl as $kb=>$vb) {
                    $exp[] = explode(':', $vb);
                }
                foreach ($exp as $ke=>$ve) {
                    $num = $ve[1] * $val['quantity'];
                    Stockpile::where(['sp_pcid'=>$pcid['pc_id'], 'sp_lxid'=>$ve[0]])->update(['sp_quantity'=> ['exp', 'sp_quantity -'.$num]]);
                }
                unset($exp);
                unset($num);
            }

            //查询锁具ID
            $lid = Db::name('fittings_lock')->where('lname', $val['fittings'])->field('lid')->find();
            //扣除锁具
            StockpileLock::where('st_lid', $lid['lid'])->update(['st_quantity'=> ['exp', 'st_quantity -' . $val['quantity']]]);
        }
    }

    /**
     * 导出excel
     * @param array $data 导入数据
     * @param string $savefile 导出excel文件名
     * @param array $fileheader excel的表头
     * @param string $sheetname sheet的标题名
     */
    public function exportExcel($data, $savefile, $fileheader, $sheetname){
        //引入phpexcel核心文件，不是tp，你也可以用include（‘文件路径’）来引入
        import("Org.PHPExcel");
        import("Org.PHPExcel.Reader.Excel2007");
        //或者excel5，用户输出.xls，不过貌似有bug，生成的excel有点问题，底部是空白，不过不影响查看。
        //import("Org.Util.PHPExcel.Reader.Excel5");
        //new一个PHPExcel类，或者说创建一个excel，tp中“\”不能掉
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $objPHPExcel  = new \PHPExcel();

        if (is_null($savefile)) {
            $savefile = time();
        }else{
            //防止中文命名，下载时ie9及其他情况下的文件名称乱码
            iconv('UTF-8', 'GB2312', $savefile);
        }

        //设置单元格边框

        // 水平居中（位置很重要，建议在最初始位置）
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //垂直居中
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(6.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(6.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(6.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(6.5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(6.5);

        //设置默认行高
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(12);

        //边框样式
        $color='0xCC000000';
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,//细边框
                    'color' => array('argb' => $color),
                ),
            ),
        );
        //$objPHPExcel->getActiveSheet()->getStyle('A2:D2')->applyFromArray($styleArray);

        //Set properties 设置文件属性
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
        $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
        $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
        $objPHPExcel->getProperties()->setCategory("Test result file");

        //$objPHPExcel->getActiveSheet()->getStyle( 'A1')->applyFromArray($styleThinBlackBorderOutline);

        $count = count($data);
        $a = 0;//设置默认值
        foreach ($data as $k=>$v) {
            for ($i=1;$i<=$v['quantity'];$i++) {
                $objPHPExcel->setActiveSheetIndex()
                    ->setCellValue('A'.$i, '客户名称')
                    ->setCellValue('B'.$i, $data[$i-1]['pcsname']) //客户名称
                    ->setCellValue('E'.$i, '包装编号')
                    ->setCellValue('F'.$i, $data[$i-1]['quantity'])
                    ->setCellValue('A'.($i+1), '收货地址')
                    ->setCellValue('B'.($i+1), $data[$i-1]['cus_prov']) //省份
                    ->setCellValue('E'.($i+1), '包装编号')
                    ->setCellValue('F'.($i+1), $data[$i-1]['quantity']);

                //合并
                //$objPHPExcel->getActiveSheet()->mergeCells( 'B'.$i.':D'.$i); //1
                //$objPHPExcel->getActiveSheet()->mergeCells( 'F'.$i.':G'.$i); //1

                //$objPHPExcel->getActiveSheet()->mergeCells( 'A'.$i.':A'.($i+3)); //1
                //$objPHPExcel->getActiveSheet()->mergeCells( 'A'.($i+4).':A'.($i+7)); //1
                //$objPHPExcel->getActiveSheet()->mergeCells( 'A'.($i+8).':A'.($i+11)); //1

                //$objPHPExcel->getActiveSheet()->mergeCells( 'B'.($i+1).':C'.($i+1)); //2
                //$objPHPExcel->getActiveSheet()->mergeCells( 'D'.($i+1).':E'.($i+1)); //2
                //$objPHPExcel->getActiveSheet()->mergeCells( 'F'.($i+1).':G'.($i+1)); //2
                //设置自动换行
                $objPHPExcel->getActiveSheet()->getStyle("A$i")->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getStyle("E$i")->getAlignment()->setWrapText(true);

                //设置边框
                //$objPHPExcel->getActiveSheet()->getStyle("A$i:G$i")->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

                //后面添加一行
                //$objPHPExcel->setActiveSheetIndex()->setCellValue('A'.($i + 1), '');
            }
        }

        //保存文件名称
        $objPHPExcel->getActiveSheet()->setTitle(''.$savefile.'');
        $objPHPExcel->setActiveSheetIndex(0);

        //清除缓冲区,避免乱码
        ob_end_clean();

        // excel头参数
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$savefile.'.xls"'); //日期为文件名后缀
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //excel5为xls格式，excel2007为xlsx格式
        $objWriter->save('php://output');
    }
}