<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/13 10:07
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use app\admin\model\GoodcateModel;
use app\admin\model\GoodimgsModel;
use app\admin\model\GoodModel;
use app\admin\validate\GoodValidate;
use think\Db;

class Good extends Base
{

    protected $title="商城管理";
    public function index(){

       $name = "商品管理";
       if(request()->isAjax()) {


           $param = input('param.');
           $limit = $param['pageSize'];
           $offset = ($param['pageNumber'] - 1) * $limit;
           $where = '';
           if (!empty($param['searchText'])) {
               $where=' good_name like "%'. $param['searchText'].'%"';


           }

           $good = new GoodModel();
           $res = $good->getByWhere($where, $offset, $limit);
           foreach ($res as $key => $vo) {
               $res[$key]['creattime'] = date('Y-m-d H:i:s', $vo['creattime']);
           }
           $return['total'] = $good->getAll($where);  //总数据
           $return['rows'] = $res;
           $return['sql'] = $good->getLastSql();
           return json($return);
       }else{
        $GoodCate = new GoodcateModel();
        $list = $GoodCate->listAll();

       $this->assign([
           'name'=>$name,
           'title'=>$this->title,
           'goodcate'=>$list
       ]);
       return $this->fetch();
     }
    }



    public function add(){
        $name = "添加商品";
        if(request()->isPost()){
           // $data = input('post.');
            /**
             * 分别存储
             */
            $data['good_name']=input('good_name');
            $data['good_s_name']=input('good_s_name');
            $data['keys']=input('keys');
            $data['cate_id'] = input('cate_id');
            $data['good_img']=input('good_img');
            $data['price']=input('price');
            $data['mall_price']=input('mall_price');
            $data['is_home']=input('is_home');
            $data['is_new']=input('is_new');
            $data['good_code']=rand_string(4,1);
            $data['wx_title']=input('wx_title');
            $data['wx_cont']=input('wx_cont');
            $data['context']=input('context');
            $imgs  = input('allimg/a');
            $good = new GoodModel();
            $result = $good->add($data,$imgs);


            if($result['code']==1){
                $this->ky_success($result['msg'],$result['data']);
            }else{
                $this->ky_error($result['msg']);
            }




        }else{
        $this->assign([
            'name'=>$name,
            'title'=>$this->title
        ]);
        return $this->fetch();
        }
    }

    public function cate(){
        return $this->fetch();
    }


    public function batchRemove(){
        $data = input('ids/a');
        $Good= new GoodModel();
        $res = $Good->batchRemove($data);
        return json(['code'=>$res['code'],'data'=>$res['data'],'msg'=>$res['msg']]);
    }

}