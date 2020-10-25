<?php
namespace app\finance\controller;
use think\Controller;
use app\Server\Finance;
use think\Db;
class Organ extends Controller
{
    // 主页面
    public function index()
    {
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财物计划','js'=>['Organ/index'],'css'=>['Organ/index'],'bootstrap'=>true
        ]);
        // 数据加载
        $btsp = $this->bootstrap($this->view);
        $wh = $btsp->getSearchWhere('cid');
        $count = $this->croDb('finc_organ')->where($wh)->count();
        $btsp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'名称','cur_figure'=>'余额','his_max'=>'历史最高','type'=>'类型','is_use'=>'正在使用','last_date'=>'编辑日期']]);
        $btsp->tableGrid(['__viewTr__'=>'trs'],[
            'table'  =>'finc_organ',
            'dataid' => 'id',
            'cols'   => [
                function($record){
                        $title = !empty($record['plus_name'])? ' title="'.$record['plus_name'].'"':'';
                        return '<a href="javascript:void(0);" class="ogran_link"'.$title.'>'.$record['name'].'</a>';
                    }
                ,'cur_figure','his_max','type','is_use','last_date']],
            function($db) use ($wh,$btsp){
                    $page = $btsp->page_decode();
                    return $db->page($page,30)->where($wh)->order('type asc,last_date desc')->select();
            });
        $btsp->pageBar($count);

        return $this->fetch();
    }
    // 编辑页面   
    public function edit()
    {
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财物计划','bootstrap'=>true,'js'=>['Organ/edit']
        ]);
        $pages = ['mode'=>'A'];
        $id = getUrlBind('edit');
        if($id){
            $data = $this->croDb('finc_organ')->where('id',$id)->find();
            $pages['mode'] = 'M';
            $pages['formStatic'] = $this->bootstrap()->staticFormGrids([
                ['label'=>'最近编辑日期','value'=>$data['last_date']]
            ]);
        }
        else $data = ['set_date'=>sysdate('date')];
        $this->assign([
            'pages' => $pages,
            'data'  => $data
        ]);

        return $this->fetch();
    }
    // 数据保存页面
    public function save()
    {
        $data = count($_POST) >0? $_POST:$_GET;
        if(isset($data['map'])) $data = bsjson($data['map']);
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);
        if($mode == 'A'){
            $data['center_id'] = uInfo('cid');
            if($this->croDb('finc_organ')->insert($data)) $this->success('【财务机构】新增成功!',urlBuild('!finance:organ'));
            else $this->success('【财务机构】新增失败!');
        }
        elseif($mode == 'M'){
            $id = $data['id'];
            unset($data['id']);
            if($this->croDb('finc_organ')->where('id',$id)->update($data)) $this->success('【财务机构】修改成功!',urlBuild('!finance:organ'));
            else $this->success('【财务机构】修改失败!');
        }
        elseif($mode == 'D'){
            $this->pushRptBack('finc_organ',['id'=>$data['id']],true);
            if($this->croDb('finc_organ')->where('id',$data['id'])->delete()) $this->success('【财务机构】删除一条记录',urlBuild('!finance:organ'));
            else $this->success('【财务机构】删除数据失败！');
        }
        println($data);
    }
}