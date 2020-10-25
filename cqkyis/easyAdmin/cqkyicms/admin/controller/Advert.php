<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 11:42
 */

namespace app\admin\controller;


use app\admin\model\AdvertModel;

class Advert extends Base
{

    protected $title="商品广告管理";

    public function index(){
        $name="商品广告";
        if(request()->isAjax()){
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = '';
            if (!empty($param['searchText'])) {
                $where=' advert_name like "%'. $param['searchText'].'%"';
            }
            $Advert = new AdvertModel();
            $res = $Advert->getByWhere($where, $offset, $limit);
            foreach ($res as $key => $vo) {
                $res[$key]['creattime'] = date('Y-m-d H:i:s', $vo['creattime']);
            }
            $return['total'] = $Advert->getAll($where);  //总数据
            $return['rows'] = $res;
            $return['sql'] = $Advert->getLastSql();
            return json($return);

        }else{
        $this->assign([
            'name'=>$name,
            'title'=>$this->title,

        ]);
        return $this->fetch();
        }
    }



    public function add(){
        $name="添加商品广告";
        if(request()->isPost()){
          $data = input('post.');
          $Advert = new AdvertModel();
          $result = $Advert->add($data);
          if($result['code']==1){
              $this->ky_success($result['msg'],$result['data']);
          }else{
              $this->ky_error($result['msg']);
          }

        }else{


        $this->assign([
            'name'=>$name,
            'title'=>$this->title,

        ]);
        return $this->fetch();
        }
    }

}