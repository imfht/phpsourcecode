<?php
/*
    2016年11月29日 星期二
    财务纪事首页
*/
namespace app\finance\controller;
use think\Controller;
use app\Server\Finance;
class Fevthome extends Controller
{
    // 初始化
    public function _initialize(){
        if($this->_initTplCheck(['save','ajax'])) return;
        if(!isset($_GET['listno'])){
            $this->error('非法请求地址或页面参数无效！');
            die;
        }
        $name = $this->croDb('fevent')->column('name');$name = isset($name[0])? $name[0]:$name;
        $action = request()->action();
        $this->loadScript([
            'auth'=>'','title'=>$name.'-Conero-财务纪事','js'=>['Fevthome/'.$action],'css'=>['Fevthome/'.$action]
        ]);
        $this->useFrontFk('bootstrap');
    }
    public function index()
    {
        // 首页全局信息
        $listno = $_GET['listno'];
        //$this->assign('page',$this->croMd('fevent',function($db){return $db->where('list_no',$_GET['listno'])->find();})->row());
        $page = $this->croDb('fevent')->where('list_no',$listno)->find();
        if($page['content']) $page['content'] = nl2br($page['content']);
        $page['abstract'] = nl2br($page['abstract']);
        $page['sider4fastf'] = preg_replace('/(\()|(\))|(\[)|(\])|(\{)|(\})|(\:)|(>)|(<)/',' ',$page['sider']);
        $this->assign('page',$page);
        // 左菜单
        $data = $this->croDb('fevent')->where('center_id',uInfo('cid'))->field('name,list_no,edittm')->select();
        $menu = '';
        foreach($data as $v){
            if($v['list_no'] == $listno) $menu .= '<li class="active"><a href="" title="编辑时间'.$v['edittm'].',当前选择的项目">'.$v['name'].'</a></li>';
            else $menu .= '<li><a href="/Conero/finance/fevthome?listno='.$v['list_no'].'" title="编辑时间'.$v['edittm'].'">'.$v['name'].'</a></li>';
        }
        //debugOut($data);        
        $this->assign([
            'menu'      => $menu,
            'logdata'   => $this->_logdata($listno)
        ]);
        $this->_JsVar('uInfo',uInfo());// 传值给前端
        $this->_fincset($listno);
        $this->_fincplan($listno);
        return $this->fetch();
    }
    private function _fincset($listno,$no=1,$row=1,$num=20)
    {
        $wh = 'center_id=\''.uInfo('cid').'\' and related_fn like \'%fevent.list_no = '.$listno.'%\'';            
        $count = $this->croDb('finc_setview')->where($wh)->count();  
        //$count = 0;
        if($count > 0){
            $data = $this->croDb('finc_setview')->where($wh)->order('use_date desc')->page($no,$num)->select();
            $cttBadge = ' <span class="badge">'.$count.'</span>';
            $list = '';
            $i = $row;
            foreach($data as $v)
            {
                $list .= '<tr><td>'.$i.'</td><td>'.$v['use_date'].'</td><td>'.$v['master'].'</td><td>'.$v['name'].'</td><td>'.$v['figure'].'</td><td>'.$v['type'].'</td><td>'.$v['plus_desc'].'</td><td>'.$v['sider'].'</td><td>'.$v['explanin'].'</td></tr>';
                $i ++;
            }                    
            if(empty($list)) $list = null;
            if(request()->isAjax()) return ['trs'=>$list,'no'=>$no];               
            $page = ceil($count/$num);
            $pageBar = null;
            if($page > 1){
                $pageBar = '<div><button type="button" id="fincset_more_load" data-loading-text="数据加载中..." class="btn btn-primary" autocomplete="off">更多</button><span dataname="currno">'.$no.'</span>/<span dataname="pages">'.$page.'</span></div>';

            }            
            $this->assign('fincset',['count'=>$cttBadge,'list'=>$list,'pageBar'=>$pageBar]);
        }
    }
    private function _fincplan($listno,$i=1,$feek=false)
    {
        $fincplan = [];//['count'=>null,'fincplan'=>null]
        $count = $this->croDb('finc_budget')->where('bud_no','fevent_'.$listno)->count();
        if($count > 0){
            $fincplan['count'] = ' <span class="badge">'.$count.'</span>';
            $data = $this->croDb('finc_budget')->where('bud_no','fevent_'.$listno)->order('createtm desc')->select();
            $trs = '';
            foreach($data as $v){
                $trs .= '<tr dataid="'.$v['bud_id'].'"><td>'.$i.'</td><td><a href="javascript:void(0);" class="fplan_about_link">'.$v['name'].'</a></td><td>'.$v['figure'].'</td><td>'.$v['listnum'].'</td><td>'.$v['createtm'].'</td><td><a href="javascript:void(0);" class="fplan_addcld_link">新增数据项</a><a href="javascript:void(0);" class="fplan_editcld_link">修改</a><a href="javascript:void(0);" class="fplan_delcld_link">删除</a></td></tr>';
                $i++;
            }
            if($trs) $fincplan['list'] = $trs;
        }
        if($feek) return $fincplan;
        $this->assign('fincplan',$fincplan);
    }
    private function _logdata($listno,$no=1,$row=1,$num=20)
    {
        $wh = 'b.user_code=\''.uInfo('code').'\' and b.related_fn like \'%fevent.list_no = '.$listno.'%\'';
        $count = $this->croDb('log_memord2cld')->alias('a')->join('log_memord b','a.log_no = b.log_no')->where($wh)->count();
        if($count == 0) return null;
        $data = $this->croDb('log_memord2cld')->alias('a')->join('log_memord b','a.log_no = b.log_no')->field('a.cld_no,a.log_no,a.name,a.keyword,a.edittm,a.date')->where($wh)->page($no,$num)->select(); 
        $trs = '';$i = $row;
        foreach($data as $v){
            $trs .= '<tr dataid="'.$v['cld_no'].'"><td>'.$i.'</td><td>'.$v['date'].'</td><td><a href="javascript:void(0);" class="logdata_about">'.$v['name'].'</a></td><td>'.$v['keyword'].'</td><td>'.$v['edittm'].'</td><td><a href="JavaScript:void(0);" class="logdata_edit_link">修改</a><a href="JavaScript:void(0);" class="logdata_del_link">删除</a></td></tr>';
            $i++;
        }
        if(empty($trs)) $trs = null;
        //debugOut($data);
        $cttBadge = ' <span class="badge">'.$count.'</span>';
        $res = ['count'=>$cttBadge,'list'=>$trs];
        return $res;
    }
    // 数据保存
    public function save()
    {
        $data = $_POST;
        $item = isset($data['item'])? $data['item']:'';
        if(isset($data['item'])) unset($data['item']);
        $ret = '';
        // 快速财务登账
        if(isset($data['fincset'])){
            $param = [];
            $sider = [];$master = [];
            $organ = $this->croDb('finc_organ')->where('center_id',uInfo('cid'))->field('id,name,type')->select();
            foreach($organ as $v){
                $sider[$v['id']] = $v['name'];
                if($v['type'] == '0M') $master[$v['id']] = $v['name'];
            }            
            $purpose = [];
            foreach($this->server_cro()->_const('finc_') as $v){
                $purpose[$v['plus_name']] = $v['plus_desc'];
            }
            $param = ['sider'=>$sider,'master'=>$master,'purpose'=>$purpose];
            fcset_parse($data['tpl'],function($data,$arg,$src){
                $uInfo = uInfo();             

                // 事务甲方全局匹配 - 严格匹配
                $master = false;
                foreach($arg['master'] as $k=>$v){
                    if(substr_count($data['master'],$v)>0){
                        $data['master'] = $k;
                        $master = true;
                        break;
                    }
                }
                $log = '';
                if($master == false){
                    $log = '['.$src.']事务甲方匹配失败，而造成数据无法提交！';
                }
                $data['related_fn'] = 'fevent.list_no = '.$_POST['fincset'];
                $data['center_id'] = $uInfo['cid'];
                $data['actbak'] = $uInfo['name'].' 于 '.sysdate().' 生成财务账单，生成方式是通过解析tpl快速模块 【'.$src.'】<br>';

                // 用途匹配-严格匹配(默认其他)
                $purpose = false;
                foreach($arg['purpose'] as $k=>$v){
                    if(substr_count($data['purpose'],$v) > 0){
                        $data['purpose'] = $k;
                        $purpose = true;
                        break;
                    }
                }
                if($purpose == false) $data['purpose'] = '00';

                // 事务乙方匹配
                foreach($arg['sider'] as $k=>$v){
                    if(substr_count($data['sider'],$v)>0){
                        $data['sider'] = $k;
                        break;
                    }
                }

                //$this->croDb('finc_organ')
                debugOut($data,true);
                //debugOut($arg,true);
            },$param);
            //debugOut($data,true);
        }
        elseif(isset($data['fincset_update'])){// 将已有的财物账单设置余该纪实关联
            $arr = explode(',',base64_decode($data['fincset_update']));
            $listno = $data['listno'];
            $i = 0;
            foreach($arr as $v){
                $actbak = $this->croDb('finc_set')->where('finc_no',$v)->value('actbak');
                $actbak .= uInfo('name').' 于 '.sysdate().' 将账单设置了关联<br>';
                $fset = [
                    'related_fn'=>'fevent.list_no = '.$listno,
                    'set_date'=>sysdate(),
                    'actbak'=>$actbak
                ];
                $ret = $this->croDb('finc_set')->where('finc_no',$v)->update($fset);
                if($ret) $i += 1;
            }
            if($i == 0) $ret = 'N';
            else{
                $ret = '本次数据维护情况【'.$i.'/'.count($arr).'】';
            }
            //debugOut($arr,true);
        }
        elseif($item == 'index4log'){
            if(isset($data['mode']) && isset($data['cld_no'])){// 数据删除修改
                $map = ['cld_no'=>$data['cld_no']];unset($data['cld_no']);
                $mode = $data['mode']; unset($data['mode']);
                if($mode == 'D'){// 删除
                    $this->pushRptBack('log_memord2cld',$map,true);                    
                    $res = $this->croDb('log_memord2cld')->where($map)->delete();
                    if($res) $ret = json_encode(['error'=>'0','desc'=>'日志成功删除！【'.$res.'】']);
                    else $ret = json_encode(['error'=>'1','desc'=>'日志删除失败！【'.$res.'】']);
                }
                else{// 修改
                    $res = $this->croDb('log_memord2cld')->where($map)->update($data);
                    if($res) $ret = json_encode(['error'=>'0','desc'=>'日志修改成功！【'.$res.'】']);
                    else $ret = json_encode(['error'=>'1','desc'=>'日志修改无效！【'.$res.'】']);
                }
            }
            else{// 数据新增
                $uInfo = uInfo();
                $listno = $data['related_fn'];
                $data['related_fn'] = 'fevent.list_no = '.$listno;
                $logNo = $this->croDb('log_memord')->where('related_fn',$data['related_fn'])->value('log_no');
                if($logNo){// 父名存在
                    $data['log_no'] = $logNo;
                }
                else{// 父名不存在
                    $fevent = $this->croDb('fevent')->where('list_no',$listno)->field('name,abstract,content')->select();
                    $fevent = $fevent[0];
                    $memord = [
                        'keyword'   => '财物纪事',
                        'name'      => '财物纪事之'.$fevent['name'].'日志报告',
                        'outline'   => $fevent['abstract'],
                        'detail'    => $fevent['content'],
                        'user_code' => $uInfo['code'],
                        'remark'    => '系统自动生成',
                        'related_fn'=> $data['related_fn']
                    ];
                    $logNo = $this->croDb('log_memord')->insert($memord);
                    //$logNo = $this->croDb('log_memord')->insertGetId($memord);// 无法获取返回ID                
                    if(!$logNo){json_encode(['error'=>'1','desc'=>'在新增日志时失败【'.$logNo.'】']);die;}
                    $logNo = $this->croDb('log_memord')->where('related_fn',$data['related_fn'])->value('log_no');// 无法获取返回ID       
                }
                unset($data['related_fn']);
                //
                if(isset($data['plandt_active'])) unset($data['plandt_active']);
                $ret = $this->croDb('log_memord2cld')->insert($data);
                if($ret) $ret = json_encode(['error'=>'0','desc'=>'日志新增成功！【'.$ret.'】']);
            }
        }
        echo $ret;
        return;
        $this->redirect('Fevthome/index');
    }
    // 财务计划编辑
    public function index_fplan()
    {
        $data = $_POST;
        $mode = $data['mode'];unset($data['mode']);
        $ret = [];
        if('A' == $mode){   // 财务计划 mode:'A',name:name,descrip:descrip
            $budget = $data;
            $listno = $data['listno'];  unset($budget['listno']);
            $checkMap = ['related_fn' => 'fevent.list_no = '.$listno,'name'=>$data['name'],'bud_no'=>'fevent_'.$listno,'center_id'=> uInfo('cid')];
            if($this->dbHaving('finc_budget',$checkMap)){
                $ret = ['error'=>1,'desc'=>'【'.$data['name'].'】才计划已经存在！'];
            }
            else{
                $budget = array_merge($budget,$checkMap,[
                    'figure'=>'0','listnum'=>'0','center_id'=> uInfo('cid')
                ]);
                $res = $this->croDb('finc_budget')->insert($budget);
                if($res) $ret = ['error'=>0,'desc'=>'财务计划新增成功，【'.$res.'】'];
                else $ret = ['error'=>1,'desc'=>'财务计划失败，【'.$res.'】'];
            }
        }
        elseif($mode == 'M' || $mode == 'D'){
            $dataid = $data['dataid'];
            if($mode == 'D'){
                $this->pushRptBack('finc_budget',['bud_id'=>$dataid],true);
                $res = $this->croDb('finc_budget')->where('bud_id',$dataid)->delete();
                if($res) $ret = ['error'=>0,'desc'=>'财物计划已经被成功·删除·，【'.$res.'】'];
                else $ret = ['error'=>1,'desc'=>'财务计划在·删除·时发生错误，请稍后重试【'.$res.'】'];
            }
            else{
                unset($data['dataid']);
                $res = $this->croDb('finc_budget')->where('bud_id',$dataid)->update($data);
                if($res) $ret = ['error'=>0,'desc'=>'财务计划已经成功·修改·，【'.$res.'】'];
                else $ret = ['error'=>1,'desc'=>'财务计划在·修改·时发生错误，请稍后重试【'.$res.'】'];
            }
        }
        if($ret) echo json_encode($ret);
        else echo '';
    }
    public function ajax()
    {
        if(!isset($_POST['item'])){
            //go('/conero/');
            utf8();echo '非法请求地址';die;
        }
        $item = $_POST['item'];$data = $_POST;unset($data['item']);
        $ret = '';
        if('index/fincsetList' == $item){
            $ret = $this->_fincset($data['listno'],$data['no'],$data['row']);
            if(is_array($ret)){
                $ret = json_encode($ret);
            }
            else $ret = 'N';
        }
        elseif('index/logdata_about' == $item){
            $res = $this->croDb('log_memord2cld')->where('cld_no',$data['dataid'])->find();
            $ctt = isset($data['unmatch'])? nl2br($res['content']): textMatchNumber(nl2br($res['content']));
            if(isset($data['getcontent'])) $ret = $ctt;
            else{
                $ret = '
                    <div>
                        <h2 class="bg-success">'.$res['name'].' <small>'.$res['date'].'</small></h2>
                        <div class="lead text-info"><ins>'.nl2br($res['keyword']).'</ins></div>
                        <p class="text-right" dataid="match"><a class="btn btn-default btn-xs" href="javascript:void(0);" role="button" onClick="app.logAboutToggle(\''.$res['cld_no'].'\',this)">修饰内容</a></p>
                        <article class="logdata_about">'.$ctt.'</article>
                        <footer class="bg-danger">'.$res['edittm'].'</footer>
                    </div>
                ';
            }
        }
        elseif('index/editlog' == $item){
            $res = $this->croDb('log_memord2cld')->where('cld_no',$data['cldno'])->field('name,date,keyword,content,plan_dt')->find();
            $ret = bsjson($res);
        }
        elseif('index/fplanEdit' == $item){
            $res = $this->croDb('finc_budget')->where('bud_id',$data['dataid'])->field('name,descrip')->find();
            $ret = bsjson($res);
        }
        echo $ret;
        die;
    }
}