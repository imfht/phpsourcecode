<?php
namespace app\center\Logic;
use app\center\Logic\Controller;
use hyang\Util;
class Userlog extends Controller{
    public function init(&$opts,$action){
        // println($opts,$action);
        if($action == 'index'){
            $js = $opts['js'];
            $js[] = 'index/userlog';
            $opts['js'] = $js;
        }elseif($action == 'edit'){
            $js = $opts['js'];
            $js[] = 'index/userlog_edit';
            $opts['js'] = $js;
            $opts['require'] = ['tinymce','datetimepicker'];
        }
    }
    // 首页
    public function main()
    {
        $log = model('LifeLog');        
        $app = $this->app;
        $bstp = $app->bootstrap();
        $wh = $bstp->getSearchWhere('cid');
        $searchform = $bstp->GridSearchForm(['__cols__'=>['title'=>'标题','life_date'=>'日期'],'ipts'=>'<input type="hidden" name="userlog">']);
        $page = $bstp->page_decode();
        $trs = '';
        if(isset($_GET['all']) && 'y' == $_GET['all']){     
            $count = $log->where($wh)->count();   
            $data = $log->where($wh)->order('groupid,edit_dt desc')->page($page,30)->select();
            $ctt = 1;
            foreach($data as $v){
                $v = $v->toArray();
                $trs .= '<li class="list-group-item"><input type="checkbox" class="logno_ckbox hidden" value="'.$v['log_no'].'"> <a href="/conero/center/index/edit/userlog/'.$v['log_no'].'.html">'.$ctt.'. '.$v['title'].'</a><span style="float:right;">'.$v['life_date'].'</span></li>';
                $ctt++;
            }
            if($trs){
                $trs = '<ul class="list-group">'.$trs.'</ul>';
                $this->assign('pageBar',$bstp->pageBar($count));
            }        
        }
        else{
            // 按标题分组
            $sql = 'select (select count(*) from `life_log` where `center_id`=a.`center_id` and `groupid`=a.groupid and `title`=a.`title`) as `ctt`,a.* from(select * from life_log where `center_id`=? group by title) a';
            $map = [uInfo('cid')];
            $data = uLogic('Dbhelper')->sqlPage($sql,$map,$page);
            $sql = 'select count(*) as `ctt` from(select * from life_log where `center_id`=? group by title) a';            
            $pctt = $app->_query($sql,$map);
            $count = isset($pctt['ctt'])? $pctt['ctt'] : 0;
            $ctt = 1;
            foreach($data as $v){
                $urlParam = ($v['ctt'] >0 && !empty($v['groupid']))? '.html?gid='.$v['groupid'].'&title='.base64_encode($v['title']): '/'.$v['log_no'].'.html';
                $trs .= '<li class="list-group-item"><input type="checkbox" class="logno_ckbox hidden" value="'.$v['log_no'].'"> <a href="/conero/center/index/edit/userlog'.$urlParam.'">'.$ctt.'. '.$v['title'].'</a><span style="float:right;">'.$v['life_date'].'</span></li>';
                $ctt++;
            }
            if($trs){
                $trs = '<ul class="list-group">'.$trs.'</ul>';
                $this->assign('pageBar',$bstp->pageBar($count));
            }  
        }
        $assign = [
            'trs'           => $trs,
            'searchform'    => $searchform
        ];
        // logList 分组列表显示
        $data = $log->where('groupid is not null and center_id = \''.uInfo('cid').'\'')->group('groupid')->select();
        $logList = '';
        foreach($data as $v){
            // $logList .= '<div class="col-md-3"><a href="/conero/center/index/edit/userlog.html?gid='.$v['groupid'].'">'.$v['groupid'].'</a></div>';
            $logList .= '<div class="col-md-3"><a href="'.url('/center/index/edit/userlog').'?gid='.$v['groupid'].'">'.$v['groupid'].'</a></div>';
        }
        if($logList) $assign['logList'] = $logList;
        $this->assign($assign);
        return $this->fetch('userlog');
    }
    // 编辑页面
    public function edit($view){
        if(isset($_GET['gid'])){ // 分组数据维护
            return $this->editByGid($view,$_GET['gid']);
        }elseif(isset($_GET['search'])){ // 内容搜索
            return $this->editBySearch($view,$_GET);
        }
        // 单条数据维护
        $this->viewInit($view);
        $editParam = [
            'navbar'    => '<li><a href="/conero/center.html?userlog">日志系统</a></li>',
            'navActive' => '编辑'
        ];
        $no = getUrlBind('userlog');
        $data = ['life_date'=>sysdate('date')];
        if($no){
            $life = model('LifeLog');
            $data = $life->where('log_no',$no)->find();
            if($data){
                $data['log_no'] = '<input type="hidden" name="log_no" value="'.$data['log_no'].'"><input type="hidden" name="mode" value="M">';
                $data['delUrl'] = url('/center/index/save/userlog').'?uid='.bsjson(['mode'=>'D','log_no'=>$no]);    
                if(!empty($data['groupid'])) $data['gidUrl'] = urlBuild('!.index/edit/userlog','?gid='.$data['groupid'].'&title='.base64_encode($data['title']));   // 分组地址
            }                   
        }
        $data['type_sels'] = uLogic('Conero')->const_option('type',[
            'value' => isset($data['type'])? $data['type']:'40',
            'phtml' => '<select name="type" class="form-control" id="typeIpter" required>',
            'unempty'   => true
        ]);
        $this->assign('data',$data); 
        $this->editPageParam($editParam);
        $this->form($view);
    }
    // 按分组显示日志
    private function editByGid($view,$gid){
        // println($_GET);
        $this->viewInit($view);
        $editParam = [
            'navbar'    => '<li><a href="/conero/center.html?userlog">日志系统</a></li>',
            'navActive' => $gid
        ];
        $this->editPageParam($editParam);
        $log = model('LifeLog');
        $page = [];
        $map = ['center_id'=>uInfo('cid'),'groupid'=>$gid];
        $data = $log->where($map)->group('title')->select();
        $queryTitle = isset($_GET['title'])? base64_decode($_GET['title']):null;
        $xhtml = '';        
        foreach($data as $v){
            $title = $v['title'];
            $classAttr = ($queryTitle && $queryTitle == $title)? ' class="text-muted bg-success"':'';
            $count = $log->where(array_merge($map,['title'=>$title]))->count();
            $xhtml .= '<div class="col-md-3"><a href="?gid='.$gid.'&title='.base64_encode($title).'"'.$classAttr.'>'.$title.'/'.$count.'</a></div>';
        }
        if($xhtml && $queryTitle) $xhtml .= '<div class="col-md-3"><a href="?gid='.$gid.'">'.$gid.' 概述</a></div>';
        $page['titles'] = $xhtml;
        $recomdt = date('Y-m-d');
        // 按 标题分组
        if($queryTitle){
            $title = $queryTitle;
            $map = ['center_id'=>uInfo('cid'),'groupid'=>$gid,'title'=>$title];
            $data = $log->where($map)->order('life_date')->select();
            $count = $log->where($map)->count();
            $xhtml = '';$addr=null;
            foreach($data as $v){
                $lifedt = $v['life_date'];
                $xhtml .= '<div class="page-header"><a href="javascript:void(0);" class="edit_links text-danger" dataid="'.$v['log_no'].'" title="点击可进入编辑页，也可保存编辑内容">'.$lifedt.' '.(Util::getWeek($lifedt)).'</a><a href="'.urlBuild('!.index/edit/userlog/'.$v['log_no']).'" style="float:right;"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></div><div class="detal_dance">'.$v['detal'].'</div>';
                if(!empty($v['addr'])) $addr = $v['addr'];
            }
            $page['content'] = $xhtml;
            $page['title'] = $title;
            $page['count'] = $count;
            if($addr) $page['addr'] = $addr;
            // $page.recomdt
            $recomdtTmp = $log->where($map)->order('life_date desc')->value('life_date');
            if($recomdtTmp) $recomdt = dateadd($recomdtTmp,1);
        }
        else{
            // 没有具体的标题时，显示该数据项的概略
            // $map = 'center_id="'.uInfo('cid').'" and groupid="'.$gid.'" and groupid is not null';
            $count = $log->where($map)->count();
            $xhtml = '<p>查询到所有数据记录[ '.$count.' ]条.</p>';
            $counts = $log->query('select count(*) as ctt from (select * from life_log where center_id="'.uInfo('cid').'" and groupid="'.$gid.'" and groupid is not null group by title) a');
            $gctt = $counts? $counts[0]['ctt']:0;
            $xhtml .= '<p>分组数： '.$gctt.'</p>';
            $qData = $log->where($map)->group('addr')->field('addr')->count();
            $tXhtml = '';
            foreach($data as $v){
                $tXhtml .= '<li>'.$v['addr'].'</li>';
            }
            if($tXhtml) $xhtml .= '按地点分组>><ul>'.$tXhtml.'</ul>';
            // $xhtml .= '时间跨度>>'.($log->where($map)->limit(1)->order('life_date')->find('life_date')); // 数据不准确
            // println($log->where($map)->limit(1)->order('life_date')->find('life_date'),$map);
            $page['aboutGid'] = $xhtml;
        }
        $page['recomdt'] = $recomdt;
        $this->assign('page',$page);
        $this->form($view,'userlog_gid');
    }
    // 内容搜索
    private function editBySearch($view,$data){
        $this->viewInit($view);
        $editParam = [
            'navbar'    => '<li><a href="/conero/center.html?userlog">日志系统</a></li>',
            'navActive' => '内容搜索'
        ];
        $this->editPageParam($editParam);
        $page = [];
        $svalue = isset($data['search'])? $data['search']:'';
        $page['search_ipt'] = $svalue;
        $this->assign('page',$page);
        // 搜索处理
        if($svalue){
            $lifelog = model('LifeLog');
            $sdata = $lifelog
                    ->where("detal like '%$svalue%'")
                    ->where(['center_id'=>uInfo('cid')])
                    ->order('life_date desc')
                    ->select();
            $sdt = [];
            foreach ($sdata as $v){
                $sdt[] = [
                    'date' => $v['life_date'],
                    'title' => $v['title'],
                    'content' => str_replace($svalue,"<span style='color:red'>$svalue</span>",strip_tags($v['detal'])),
                    'no'    => base64_encode($v['log_no'])
                ];
            }
            $this->assign('sdata',$sdt);
        }
        $this->form($view,'userlog_sch');
    }
    public function save()
    {
        list($data,$mode,$map) = $this->app->_getSaveData('log_no');
        $lifelog = model('LifeLog');
        // println($data,$mode,$map);die;
        if(isset($data['addr'])) $data['addr'] = trim($data['addr']);
        // 快速写入保存数据预处理
        if(isset($data['formid']) && 'fast_save' == $data['formid']){
            unset($data['formid']);
            $map = ['center_id'=>uInfo('cid'),'groupid'=>$data['groupid'],'title'=>$data['title']];
            $qdata = $lifelog->where($map)->field('type')->find()->toArray();
            // println($qdata);
            $data = array_merge($data,$qdata,$map);
            $mode = 'A';
        }
        if($mode == 'D'){
            if(isset($data['list'])){
                $ctt = 0;
                foreach($data['list'] as $v){
                    $map = ['log_no'=>$v];
                    $this->app->pushRptBack('life_log',$map,true);
                    if($lifelog->where($map)->delete()) $ctt += 1;
                }
                $this->success('数据删除记录'.$ctt.'/'.count($data['list']));
            }
            else{
                $this->app->pushRptBack('life_log',$map,true);
                if($lifelog->where($map)->delete()) $this->success('数据删除成功！');
                else $this->error('十分遗憾，数据删除失败！');
            }
        }
        elseif($mode == 'M'){
            if($lifelog->where($map)->update($data)) $this->success('数据修改成功');
            else $this->error('十分遗憾，数据更新失败！');
        }
        elseif($mode == 'A'){
            $data['center_id'] = uInfo('cid');
            if($lifelog->insert($data)) $this->success('数据新增成功');
            else $this->error('十分遗憾，数据新增失败！');
        }        
        println($data);
    }
    public function ajax()
    {
        list($item,$data) = $this->app->_getAjaxData();
        $ret = '';
        switch($item){
            case 'groupid_modal':
                $data = model('LifeLog')->where(['center_id'=>uInfo('cid')])->group('groupid')->field('groupid')->order('edit_dt desc')->limit(30)->select();
                $ctt = 1;
                foreach($data as $v){
                    if(empty($v['groupid'])) continue;
                    $ret .= '<li class="list-group-item">'.$ctt.'. <a href="javascript:void(0);" dataid="groupid_opts">'.$v['groupid'].'</a></li>';
                    $ctt++;
                }
                $ret = '<ul class="list-group">'.$ret.'</ul>';
                // println($data);
                break;         
            case 'fast_edit_save':
                $map = ['log_no'=>$data['logno']];
                if(model('LifeLog')->where($map)->update([
                    'detal' => $data['content']
                ])) $ret = 1;
                else $ret = -1;
                break;
            case 'slist_get_content':
                $data = model('LifeLog')->get(base64_decode($data['no']))->toArray();
                $data['code'] = $data['log_no'];
                unset($data['log_no']);
                unset($data['center_id']);
                $ret = json_encode($data);
        }
        // print_r($data);
        echo $ret;
    }
}