<?php

/*
 *  @author myf
 *  @date 2014-11-13 13:53:11
 *  @Description
 */

use Minyifei\Model\User;
use Minyifei\Lib\MyfIp;
use Myf\Mvc\Page;

class IndexController extends Controller {

    /**
     * 首页
     */
    public function index() {
        $now = date("Y-m-d H:i:s");
        $this->assign("now", $now);
        $this->assign("name", "MyfMVC");
        $ipInfo = MyfIp::getIpInfo();
        if($ipInfo){
            //创建用户记录
            $user = new User();
            $data = array(
                "province" => $ipInfo["province"],
                "city" => $ipInfo["city"],
                "ip" => $ipInfo["ip"],
                "isp" => $ipInfo["isp"],
                "created" => getCurrentTime(),
            );
            $user->save($data);
        }

        $this->display("index.html");
    }

    /**
     * 显示用户数据
     */
    public function showUsers() {
        $p = getInteger("p");
        if(empty($p) || $p<1){
            $p=1;
        }
        $pageSize = 20;
        $start = ($p-1)*$pageSize;
        $muser = new User();
        $users = $muser->order("created desc")->limit($start.",".$pageSize)->find();
        $this->assign("users",$users);
        //分页
        $count = $muser->count();
        $page = new Page($pageSize, $count, $p);
        $show = $page->show();
        $this->assign("pager",$show);
        $this->display("users.html");
    }

}
