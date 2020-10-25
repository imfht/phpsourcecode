<?php
/* 2017年2月24日 星期五 家谱纪事 */
namespace app\clan\controller;
use app\common\controller\BasePage;
use hyang\Util;
class History extends BasePage
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'title' => '家族纪事 - 祖公源居 - Conero','bootstrap'=>true,'js'=>['history/index']
        ]);
        $genno = request()->param('genno');
        $pages = [];
        $pages['addUrl'] = url('history/edit',['genno'=>$genno]);
        $hmd = model('Ghistory');
        $bstp = $this->bootstrap($this->view);
        $wh = ['gen_no'=>$genno];
        $count = $hmd->where($wh)->count();
        $wh = $bstp->getSearchWhere($wh);
        $data = $hmd->where($wh)->select();
        $pages['empty_data'] = 'N';
        if($count == 0){
            $pages['empty_data'] = 'Y';
        }
        elseif($count > 0){
            $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['title'=>'标题','edittm'=>'编辑时间']]);
             $bstp->listGrid([
                'col' => function($record){
                    return '<a href="'.url('history/edit',['genno'=>$record['gen_no'],'lstn'=>$record['listno']]).'">'.$record['title'].'</a><span style="float:right;">'.($record['axis_mk'] == 'Y'? '<a href="'.url('history/axis',['plstn'=>$record['listno']]).'">时间轴</a> ':'').$record['edittm'].'</span>';
                },
                '__view__' => 'listdata'
            ],$data);
        }
        $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 时间轴
    public function axis()
    {
        $this->loadScript([
            'title' => '时间轴 - 家族纪事 - 祖公源居 - Conero','bootstrap'=>true,'css'=>['timeline'],'js'=>['history/axis']
        ]);
        $plstn = request()->param('plstn');
        $gaxis = model('Gaxis');$ghist = model('Ghistory');$gctmd = model('Gcenter');
        $gnode = model('Gnode');
        $query = $ghist->get($plstn);
        $genno = $query['gen_no'];
        $data = [];       
        $data = array_merge($data,[
            'gen_no' => '<input type="hidden" name="gen_no" value="'.$genno.'">',
            'homeUrl'=> url('history/index',['genno'=>$genno]),
            'homeAel'=> '<a href="'.urlBuild('!.center/index/'.$genno).'#history_cro">'.($gctmd->where('gen_no',$genno)->value('gen_title')).'</a>',
            'name'  => $query['title']
        ]);        
        $qnode = $gnode->selectNodeDiv();
        /*
            Db::table('gen_node')->alias('a')
            ->join('gen_node b','a.pers_id=`b`.father','LEFT')
            ->join('gen_node c','a.pers_id=`c`.mother','LEFT')
            ->where(['a.gen_no'=>$genno])->limit(10)->field('a.pers_id,a.name,b.name as father,c.name as mother')->select();
        */
        $xhtml = '点击设置关联人： ';
        foreach($qnode as $nd){
            $xhtml .= '<a href="javascript:void(0)" data-pid="'.$nd['pers_id'].'">'.$nd['name'].'</a> ';
        }
        $data['ndSelected'] = $xhtml;
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 编辑页面
    public function edit()
    {
        $this->loadScript([
            'title' => '家族纪事 - 祖公源居 - Conero','bootstrap'=>true,'require'=>['tinymce','datetimepicker'],'js'=>['history/edit']
        ]);
        $param = request()->param();
        $genno = $param['genno'];
        $data = [];
        $mode = 'A';
        if(isset($param['lstn'])){
            $hmd = model('Ghistory');
            $data = $hmd->get($param['lstn'])->toArray();
            $mode = 'M';
        }
        $gctmd = model('Gcenter');
        $data = array_merge($data,[
            'gen_no' => '<input type="hidden" name="gen_no" value="'.$genno.'">',
            'mode'   => $mode,
            'homeUrl'=> url('history/index',['genno'=>$genno]),
            'homeAel'=> '<a href="'.urlBuild('!.center/index/'.$genno).'#history_cro">'.($gctmd->where('gen_no',$genno)->value('gen_title')).'</a>'
        ]);        
        $this->assign('data',$data);
        return $this->fetch();
    }
    protected function _savedata(&$data)
    {
        $data = Util::dataClear($data,['start_dt','end_dt','pers_id']); // 数据清洗
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode == 'A'){
            $uInfo = uInfo();
            $data = array_merge($data,[
                'user_code' => $uInfo['code'],
                'user_name' => $uInfo['nick']
            ]);
        }
        // println($data);die;        
        $hmd = model('Ghistory');
        $retData = [
            'table' => $hmd,
            'pk'    => 'listno'
        ];
        return $retData;
    }
}