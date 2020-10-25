<?php
namespace app\center\Logic;
use app\center\Logic\Controller;
class Mates extends Controller{
    public function init(&$opts,$action){
        $js = $opts['js'];
        $js[] = 'index/mates';
        $opts['js'] = $js;
    }
    public function main()
    {
        //$this->assign('mates_grid',$this->GridList());
        $this->viewInit();
        $this->GridList();
        $this->app->_JsVar('uid',uInfo('uid'));
        return $this->fetch('mates');
    }
    private function GridList()
    {
        $app = $this->app;
        //$bstp = $app->bootstrap($this->view);
        $bstp = $app->bootstrap($this);
        $wh = $bstp->getSearchWhere('cid');
        $count = $app->croDb('mates')->where($wh)->count();
        $html = $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'姓名','mt_nick'=>'昵称','detal'=>'详情','family_mk'=>'家人','start_dt'=>'编辑时间'],'ipts'=>'<input type="hidden" name="mates">']);
        $html .= $bstp->tableGrid(['__viewTr__'=>'trs'],[
                'table'=>'mates','dataid'=>'mate_no',
                'cols'=>['name','mt_nick','detal','family_mk','start_dt',
                    function($v){// 账号信息
                        if(!empty($v['mate_code'])) $html = '已经注册';
                        else $html = '未注册';
                        return $html;
                    },
                    '<a href="javascript:void(0);" class="edit_link">修改</a><a href="javascript:void(0);" class="del_link">删除</a>'                    
                ]
            ],
            function($db){
                $bstp = $this->app->bootstrap();
                $page = $bstp->page_decode();
                $wh = $bstp->getSearchWhere('cid');
                return $db->where($wh)->order('start_dt desc')->page($page,30)->select();
        });
        //$html .= $bstp->pageBar($count);
        $this->assign('pageBar',$bstp->pageBar($count));
        return $html;
    }
    public function save()
    {
        $data = $_POST;$app = $this->app;$mateNo = '';
        if(count($data) == 0) $data = $_GET;
        //echo substr_count(base64_decode($data['mode']),'delete_').'<br>';echo base64_decode($data['mode']);
        //debugOut($data,true);die;
        $ret = '数据维护失败!!';
        if(isset($data['uid']) && $data['uid'] != uInfo('uid')) $ret = '非法地址请求!!!';
        elseif(isset($data['dataid']) && isset($data['mode'])){
            if(substr_count(base64_decode($data['mode']),'delete_') > 0){
                $app->pushRptBack('mates',['mate_no'=>$data['dataid']],true);
                if($app->croDb('mates')->where('mate_no',$data['dataid'])->delete()) $ret = '数据删除成功！！';
                else $ret = '数据删除失败，请稍后重试！！';
            }
            else $ret = '数据维护失败，请求参数无效！！';
            $this->success($ret);return;
        }
        else{
            $add = 0;$met = 0;$ctt = count($data);
            // 数据新增/修改操作
            foreach($data as $v){
                $source = json_decode($v,true);
                if(isset($source['mate_no'])){ // 修改数据
                    $mateNo = $source['mate_no'];unset($source['mate_no']);
                    if(isset($source['command'])) unset($source['command']);
                    if($app->croDb('mates')->where('mate_no',$mateNo)->update($source)) $met += 1;
                }
                else{   // 数据新增                
                    $source['center_id'] = uInfo('cid');
                    if(isset($source['mate_code']) && $source['mate_code']){
                        $pswd = $source['command'];unset($source['command']);
                    }
                    else{
                        if(isset($source['mate_code'])) unset($source['mate_code']);
                        if(isset($source['command'])) unset($source['command']);
                    }
                    if($app->croDb('mates')->insert($source)) $add += 1;                
                }            
            }
            if($add > 0 || $met > 0){
                $ret = '本次数据维护中';
                if($add > 0) $ret .= '成功新增数据【'.$add.'/'.$ctt.'】条';
                if($met > 0) $ret .= ($add > 0? '；':'').'成功修改数据【'.$met.'/'.$ctt.'】条';
            }
        }
        $this->success($ret);
    }
    public function ajax()
    {
        $data = $_POST;$item = isset($data['item'])? $data['item']:'';
        $ret = '非法请求地址';
        if($item) $app = $this->app;
        switch($item){
            case 'getData':                
                $ret = $app->croDb('mates')->where('mate_no',$data['dataid'])->field('mate_no,name,mt_nick,detal,family_mk,mate_code')->find();
                $ret = $app->unEmptyArray($ret);
                $ret = json_encode($ret);
                break;
        }
        echo $ret;
    }
}