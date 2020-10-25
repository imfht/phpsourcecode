<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        if(UID){
            $this->meta_title = '管理首页';
            $this->display();
        } else {
            $this->redirect('Public/login');
        }
    }

    public function bmob() {
        try {
            /*
             *  BmobObject 的例子
            */
//            $bmobObj = new \BmobObject("GameScore");
//            $res = $bmobObj->apps("85898d6e01ff1bc35a496cb05917ed04");
//            $bmobObj = new \BmobObject("GameScore");
//            $res=$bmobObj->create(array("score"=>80,"playerName"=>"game")); //添加对象
//            $res=$bmobObj->get("119231eb0b"); // 获取id为bd89c6bce9的对象
//            $res=$bmobObj->get(); //获取所有对象
//            dump($res);
//            //更新对象bd89c6bce9, 任何您未指定的key都不会更改,所以您可以只更新对象数据的一个子集
//            $res=$bmobObj->update("119231eb0b", array("score"=>60,"playerName"=>"game"));
//            $res=$bmobObj->delete("f5791f84f7"); //删除对象bd89c6bce9
//            //对象的查询,这里是表示查找playerName为"game"的对象，只返回２个结果
//            $res=$bmobObj->get("",array('where={"playerName":"game"}','limit=2'));
//            //id为bd89c6bce9的field score数值减2
//            $res=$bmobObj->increment("119231eb0b","score",array(-2));
//            //id为bd89c6bce9的field score数值加2
//            $res=$bmobObj->increment("119231eb0b","score",array(2));

            /*
             *  BmobUser 的例子
             */
//            $bmobUser = new \BmobUser();
//            //用户注册, 其中username和password为必填字段
//            //$res = $bmobUser->register(array("username"=>"cooldude118", "password"=>"p_n7!-e8", "phone"=>"415-392-0202", "email"=>"bmobtest112@126.com"));
//            //用户登录, 第一个参数为用户名,第二个参数为密码
//            $res = $bmobUser->login("cooldude117","p_n7!-e8");
//            // 获取id为415b8fe99a用户的信息
//            $res = $bmobUser->get("7a6e42c4e0");
//            $res = $bmobUser->get(); // 获取所有用户的信息
//            $res = $bmobUser->update("7a6e42c4e0", "c54c4c784055318780030b0163a43d74", array("phone"=>"02011111")); // 更新用户的信息
//            // 请求重设密码,前提是用户将email与他们的账户关联起来
//            $res = $bmobUser->requestPasswordReset("bmobtest111@126.com");
//            // 删除id为415b8fe99a的用户, 第一参数是用户id, 第二个参数为sessiontoken,在用户登录或注册后获取, 必填
//            $res = $bmobUser->delete("dfbeb177f0", "050391db407114d9801c8f2788c6b25a");

            /*
             *  BmobCloudCode 的例子
             */
            //调用名字为getMsgCode的云端代码
            $cloudCode = new \BmobCloudCode('tableDetails');
            //传入参数name，其值为bmob
            $res = $cloudCode->get(array("name"=>"bmob"));


            $this->ajaxReturn($res);

        } catch (Exception $e) {
            echo $e;
        }
    }

    public function bmobset() {
        session('bmob_appid','85898d6e01ff1bc35a496cb05917ed04');
        session('bmob_restkey','a76469712789001f540b642f1c9cf8a4');
    }

    public function ac() {
        import('Common.Common.Apicloud.ApiCloudModel');
        $ApiCloud = new \ApiCloudModel();;
        $map['class'] = 'jrtools';
        //add
        //$ret = $ApiCloud->where($map)->add(array('name'=>'swing'));
        //edit
        //$map['id'] = '55a35d6f1bd1c923759dd39b';
        //$ApiCloud->where($map)->save(array('name'=>'ssss'));
        //delete
        //$ApiCloud->where($map)->delete('55a35d6f1bd1c923759dd39b');

        $ret = $ApiCloud->where($map)->find();
        dump($ret);
        //$ret = $ApiCloud->getPage($map,1,10);
        //dump($ret);
    }
    public function acset() {
        session('API_ID','A6987745997816');
        session('API_KEY','385F1D0E-93B5-EBC5-ED89-8E92D8E14E74');
    }
}
