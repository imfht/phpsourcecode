<?php
/* 2017年2月24日 星期五 家族字辈 */
namespace app\clan\controller;
use app\common\controller\BasePage;
class Zibei extends BasePage
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'title'=>'字辈 - 祖公源居 - Conero','bootstrap'=>true,'js'=>['zibei/index']
        ]);
        $gno = getUrlBind('index');
        
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere('code');
        $wh['gen_no'] = $gno;
        // println($wh);
        $gnode = model('Gzibei');
        $count = $gnode->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['zibei'=>'字辈','pzibei'=>'父字辈','pinyin'=>'字辈拼音','mtime'=>'维护时间']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'gen_zibei','cols'=>[
                function($record){return '<a href="'.urlBuild('!.zibei/edit/'.$record['gen_no'].'/pid/'.$record['zibei_no']).'">'.$record['zibei'].'</a>';},
                'pinyin','pzibei','mtime',
                function($record){return '<a href="'.urlBuild('!.zibei/save','?uid='.bsjson(['mode'=>'D','gen_no'=>$record['gen_no'],'zibei_no'=>$record['zibei_no']])).'" class="text-danger dellink">删除</a>';}
            ]],function($db)use($wh,$bstp){
            $page = $bstp->page_decode();
            return $db->where($wh)->page($page,30)->order('ser_no asc,mtime desc')->select();
        });
        $this->bootstrap($this->view)->pageBar($count);

        $pages = [
            'addUrl' => urlBuild('!.zibei/edit/'.$gno),
            'zbodUrl' => urlBuild('!.zibei/save','?uid='.bsjson(['svid'=>'zbod','gno'=>$gno]))
        ];
        $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 编辑
    public function edit()
    {
        $gno = getUrlBind('edit');
        $pid = getUrlBind('pid');
        $this->loadScript([
            'title'=>'字辈 - 祖公源居 - Conero','bootstrap'=>true,'js'=>['zibei/edit']
        ]);
        $pages = [
            'backAhref' => '<a href="'.urlBuild('!.zibei/index/'.$gno).'">字辈</a>'
        ];
        $this->assign('pages',$pages);
        $data = ['mode'=>'A'];
        if($pid){
            $gzb = model('Gzibei');
            $data = $gzb->get($pid);
            $data['mode'] = 'M';
            $data['pk'] = '<input type="hidden" name="zibei_no" value="'.$pid.'">';
        }
        $data['gen_no'] = '<input type="hidden" name="gen_no" value="'.$gno.'">';
        $this->assign('data',$data);
        return $this->fetch();
    }
    protected function _savedata(&$data)
    {
        $zbMd = model('Gzibei');
        if(isset($data['svid'])){
            switch($data['svid']){
                case 'zbod': // 字辈自动排序
                    $ret = $zbMd->zibeiOrder($data['gno']);
                    if(isset($ret['error']) && $ret['error'] === 0) $this->success($ret['msg']);
                    $ret = [
                        'msg' => isset($ret['msg'])? $ret['msg']:'操作失败'
                    ];
                    $this->error($ret['msg']);
                    break;
            }
            die;
        }
        $retData = [
            'table' => $zbMd,
            'pk'    => 'zibei_no'
        ];
        if(isset($data['parent_no']) && empty($data['parent_no'])) unset($data['parent_no']);
        if(isset($data['parent_no'])){
            $pZb = $zbMd->get($data['parent_no']);
            $data['ser_no'] = empty($pZb->ser_no)? 1 : ($pZb->ser_no + 1);
        }
        $mode = isset($data['mode'])? $data['mode']:'';
        if('A' == $mode){
            $uInfo = uInfo();
            $data['user_code'] = $uInfo['code'];
            $data['user_name'] = $uInfo['nick'];            
        }
        $retData['url'] = urlBuild('!.zibei/index/'.$data['gen_no']);
        return $retData;
    }
}