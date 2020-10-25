<?php
namespace addons\common\coupon\controller;

class Admin extends Base
{
	//优惠券列表
    public function index()
    {
        //过期分类
        x_model('AddonsCommonCouponMenu')->where('last_time','<=',date("Y-m-d H:i:s"))->update(['status' => -1]);
		$couponList = x_model('AddonsCommonCouponMenu')->order('id desc')->paginate();

		cookie("prevUrl", request()->url());

    	$this->assign('couponList', $couponList);
    	return view('admin_index');
    }

    //新增优惠券
    public function add()
    {
    	if (request()->isPost()){
    		$data = input('post.');

    		if(input('post.id')){
				$result = x_model('AddonsCommonCouponMenu')->update($data);
			}else{
				$result = x_model('AddonsCommonCouponMenu')->create($data);
			}

			$number = $data['num'] = intval($data['num']);
			$data2['coupon_menu_id'] = $result->id;
			$data2['price'] = $data['price'];
			$data2['last_time'] = $data['last_time'];
			for ($i = 0; $i < $number; $i++) {
				$data2['code'] = rand_code(6);
				$code = x_model('AddonsCommonCoupon')->where('code',$data2['code'])->find();
				if (!isset($code)) {
	                $coupon = x_model('AddonsCommonCoupon')->create($data2);
	            } else {
	                $number = $number + 1;
	            }
			}

			if($coupon){
				$this->success("新增成功", cookie("prevUrl"));
			}else{
				$this->error('新增失败', cookie("prevUrl"));
			}
    	}else{
 
	    	return view('admin_add');
    	}
    }
    //详情列表
    public function detail()
    {
    	$id = input('param.id');
    	$couponList = x_model('AddonsCommonCoupon')->with('user,menu')->where('coupon_menu_id',$id)->paginate();

    	cookie("prevUrl", request()->url());
    	
    	$this->assign('couponList', $couponList);
    	return view('admin_detail');
    }
    //发送优惠券
    public function send()
    {
        $user_id = input('param.user_id');
        $coupon_id = input('param.id');

        $coupon = x_model('AddonsCommonCoupon')->with('menu')->find($coupon_id)->toArray();

        x_model('AddonsCommonCoupon')->where('id',$coupon['id'])->update(['user_id' => $user_id]);
        $result = x_model('AddonsCommonCouponChange')->create([
            'user_id'  =>  $user_id,
            'coupon_id' =>  $coupon['id'],
            'score'  =>  0,
            'type'  =>  2
        ]);
        if($result){
            $this->success("发送成功", cookie("prevUrl"));
        }else{
            $this->error('发送失败', cookie("prevUrl"));
        }
    }
    
    //兑换历史记录
    public function history()
    {
        $changeList = x_model('AddonsCommonCouponChange')->with('coupon.menu,user')->order('id desc')->paginate();

        $this->assign('changeList', $changeList);
        return view('admin_history');
    }
    //更新状态
    public function update_menu()
    {
    	$data = input('param.');

		$result = x_model('AddonsCommonCouponMenu')->where('id','in',$data['id'])->update(['status' => $data['status']]);
		if($result){
			$this->success("修改成功", cookie("prevUrl"));
		}else{
			$this->error('修改失败', cookie("prevUrl"));
		}
    }

    //导出优惠券
    public function export(){
    	$id = input('param.id');
    	$couponmenu = x_model('AddonsCommonCouponMenu')->find($id);

        $couponlist = x_model('AddonsCommonCoupon')->where('coupon_menu_id',$id)->select();
        $data = array(
            '0' => array(
                '1' => '编号',
                '2' => '优惠码',
                '3' => '状态',
                '4' => '金额',
                '5' => '截止时间',
            ),
        );
        foreach ($couponlist as &$v) {
            array_push($data, array(
                '1' => $v['id'],
                '2' => $v['code'],
                '3' => $v['status'],
                '4' => $v['price'],
                '5' => $v['last_time'],
            ));
        }
        export_to($data,$couponmenu['name']);//导出excle
    }


}
