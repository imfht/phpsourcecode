<?php
/* 2017年2月24日 星期五
 * 家谱页面公共例子 trait
*/
namespace app\clan\controller;
trait ClanFnTra{
    public function _initialize()
    {
        if($this->uLoginCkeck() == false){
            $this->error('您还没有登录系统！','index/index');
        }
    }
    /*
    protected function indextra()
    {
        $this->assign([
            'clan' => [
                'name'  => request()->controller(),
                'viewTemplate'  => $this->fetch(),
                'count' => $this->aboutVisit()
            ]
        ]);
        return $this->fetch('app/clan/view/navbar.html');
    }
    */
    // 访问合法性检测
    private function genCheckVisit($genNo=null)
    {
        if($genNo){
            $ctt = model('Gcenter')->where(['user_code'=>uInfo('code'),'gen_no'=>$genNo])->count();
            $ctt = $ctt? $ctt: 0;
            if($ctt > 0) return false;
        }
        return true;
    }
    // 获取 家谱中心的值
    protected function getCenterVar($gon,$key=null){
        $gcter = model('Gcenter');
        $data = $gcter->get($gon);
        $data = $data->toArray();
        if($key){
            if(is_array($key)){
                $key = array_flip($key);
                return array_intersect_key($data,$key);
            }
            elseif(is_array($data) && is_string($key) && array_key_exists($key,$data)) return $data[$key];
            return "";
        }
        return $data;
    }
}