<?php
/**
 * 订单数据返回
 * Created by PhpStorm.
 * User: root
 * Date: 7/15/16
 * Time: 11:12 AM
 */
class Home extends Admin{

    public function index(){
        //$users=$this->model->where(['seller_id'=>0])->select();

       /* foreach($users as $user){
            echo '--------------------------'.$user['user_name'];
            $nav=$user['nav_list'];
            //var_dump($nav);
            $nav=str_replace('订单列表|order.php?act=list','订单列表|new.php?c=order',$nav);
            $nav=str_replace('订单查询|order.php?act=order_query,','',$nav);

            $arr = explode(',', $nav);

            foreach ($arr AS $val)
            {
                $tmp = explode('|', $val);
                $lst[$tmp[1]] = $tmp[0];
            }
           // var_dump($lst);
            echo $nav;
            $data['nav_list']=$nav;
            echo $this->model->where(['user_id'=>$user['user_id']])->save($data);
            echo '--------------------------over--------------------<br><br>';
        }*/
        $this->display('home/admin_home.html');
    }
    public function get_list(){
        return $this->model->find();
    }
}