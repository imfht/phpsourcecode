<?php
namespace app\admin\controller;
use think\helper\Time;

class AnalysisController extends BaseController
{
	//用户分析
	public function user(){

		$totalUser = model('User')->count();
		$buyUser = model('User')->where('buy_num>0')->count();
		$buyRate = round((($buyUser / $totalUser) * 100),2);
		// $buyRate = sprintf("%1\$.2f",($buyUser / $totalUser) * 100);
		$this->assign("buyRate", $buyRate);

		$yesterday = model("Analysis")->whereTime('created_at', 'yesterday')->find();
		$this->assign("yesterdayNewUser", $yesterday["registers"]);
		$today = model("Analysis")->whereTime('created_at', 'today')->find();
		$this->assign("todayNewUser", $today["registers"]);

		$line_data = $this->getDateAnalysis();

		$newUserLine = array();
        foreach ($line_data as $key => $value) {
            $newUserLine[$key] = $value["registers"];
        }
        $this->assign("newUserLine", json_encode($newUserLine));

        $userBuyLine = array();
        foreach ($line_data as $key => $value) {
            $userBuyLine[$key] = $value["users"];
        }
        $this->assign("userBuyLine", json_encode($userBuyLine));

        $newUser = $today["registers"];
        $newUserBuy = model("User")->whereTime('created_at', 'today')->where('buy_num>0')->count();
        $newUserBuyRate = $newUser ? round((($newUserBuy / $newUser) * 100),2) : 0;
        $this->assign("newUserBuyRate", $newUserBuyRate);

		return view();
	}

	//订单分析
	public function order(){
		$yesterday = model("Analysis")->whereTime('created_at', 'yesterday')->find();
        $this->assign("yesterdayNewOrder", $yesterday["orders"]);
        $this->assign("yesterdayNewTrade", $yesterday["trades"]);
        $today = model("Analysis")->whereTime('created_at', 'today')->find();
        $this->assign("todayNewOrder", $today["orders"]);
        $this->assign("todayNewTrade", $today["trades"]);

        $line_data = $this->getDateAnalysis();

        $newOrderLine = array();
        foreach ($line_data as $key => $value) {
            $newOrderLine[$key] = $value["orders"];
        }
        $this->assign("newOrderLine", json_encode($newOrderLine));

        $userTradeLine = array();
        foreach ($line_data as $key => $value) {
            $userTradeLine[$key] = $value["trades"];
        }
        $this->assign("userTradeLine", json_encode($userTradeLine));
		return view();
	}

	//商品分析
	public function product(){
		$productList = model('Product')->with('skus')->order('id', 'desc')->paginate();

		$this->assign("productList", $productList);
		return view();
	}

	public function getDateAnalysis(){
        $analysis = model("Analysis")
                  ->whereTime('created_at', 'between', Time::dayToNow(18, true))
                  ->order('id desc')
                  ->select()
                  ->toArray();

        $date = array();
        for ($i = 18; $i >= 0; $i--) {
            array_push($date, date("Y-m-d", strtotime("-$i day")));
        }
        $this->assign("date", json_encode($date));

        $line_data = array();
        foreach ($date as $key => $value) {
            $line_data[$key] = array(
                "id" => "0",
                "orders" => "0",
                "trades" => "0",
                "registers" => "0",
                "users" => "0",
                "date" => "0",
            );
            foreach ($analysis as $k => $v) {
                if ($v["date"] == $value) {
                    $line_data[$key] = $v;
                }
            }
        }
        return $line_data;
	}




}