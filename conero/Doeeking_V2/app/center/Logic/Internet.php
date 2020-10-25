<?php
namespace app\center\Logic;
use app\center\Logic\Controller;
class Internet extends Controller
{
    public function init(&$opts,$action=null){
        if($action == 'edit'){
            $js = $opts['js'];
            $js[] = 'index/interedit';
            $opts['js'] = $js;
        }
    }
    // 主页面
    public function main()
    {
        $app = $this->app;
        $bstp = $app->bootstrap($this);
        $wh = $bstp->getSearchWhere('code');
        $count = $app->croDb('sys_organs')->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'名称','company'=>'公司','type'=>'类型','app_type'=>'种类','purpose'=>'用途','edittm'=>'编辑时间'],'ipts'=>'<input type="hidden" name="internet">']);
        $bstp->tableGrid(['__viewTr__'=>'trs'],[
                'table'=>'sys_organs',
                'cols'=>[
                    function($record){return '<a href="/conero/center/index/edit/internet/'.$record['sys_no'].'.html" title="点击编辑数据信息">'.$record['name'].'</a>';},
                'company','type','app_type','purpose',
                function($record){
                    if(!empty($record['url'])) return '<a href="'.urlBuild('!.index/internet','?url='.base64_encode($record['url'])).'&uid='.bsjson(['type'=>'updateCtt','sys_no'=>$record['sys_no']]).'" target="_blank">'.$record['url'].'</a>';
                },
                'edittm'
                ]
            ],
            function($db) use ($wh,$bstp){
                $page = $bstp->page_decode();
                return $db->where($wh)->order('edittm desc')->page($page,30)->select();
        });
        $bstp->pageBar($count);
        // return $html;
        return $this->fetch('internet');
    }
    // 编辑页面
    public function edit($view)
    {
        $this->viewInit($view);

        $editParam = [
            'navbar'    => '<li><a href="/conero/center.html?internet">网络账号</a></li>',
            'navActive' => '编辑'
            // ,'navSelf'=>'855555'
        ];
        $this->app->_JsVar('code',uInfo('code'));
        $sysId = getUrlBind('internet');
        if($sysId){
            $data = $this->app->croDb('sys_organs')->where('sys_no',$sysId)->find();
            $data['pk'] = '<input type="hidden" name="sys_no" value="'.$sysId.'">';
            $data['passw'] = '__fake__';
            $this->assign('data',$data);
        }
        $typeOpts = '';
        foreach(uLogic('Conero')->_const('type_organs') as $v){
            $typeOpts .= '<option value="'.$v['plus_name'].'">'.$v['plus_desc'].'</option>';
        }
        $this->assign('typeOpts',$typeOpts);
        $this->editPageParam($editParam);
        $this->form($view);
    }
    // 数据保存
    public function save()
    {
        $data = $_POST? $_POST:$_GET;
        if(isset($data['map'])) $data = bsjson($data['map']);
        $mode = isset($data['mode'])? $data['mode']:'';
        $map = isset($data['map'])? $data['map']:['user_code'=>uInfo('code')];
        if(empty($mode)){
            $mode = isset($data['sys_no'])? 'M':'A';
            if('M' == $mode) $map = ['sys_no'=>$data['sys_no']];
        }
        $app = $this->app;
        if('A' == $mode){
            $data = array_merge($data,$map);
            /*
            $crp = new \hyang\Crypt;
            $aes = $crp->Algorithm('Aes');
            $password = sha1($data['user_code']);
            $data['passw'] = $aes->encrypt($data['passw'],$password);
            */
            $data['passw'] = $this->pswdHelper($data['passw']);
            if($app->croDb('sys_organs')->insert($data)) $this->success('成功新增一条数据！');
            else $this->success('数据新增失败');
        }
        elseif($mode == 'M'){
            // 修改时密码处理
            if(empty($data['passw']) || ($data['passw'] && '__fake__' == $data['passw'])) unset($data['passw']);
            else $data['passw'] = $this->pswdHelper($data['passw']);
            if($app->croDb('sys_organs')->where($map)->update($data)) $this->success('成功更新一条数据！');
            else $this->success('数据更新失败');
        }
        elseif($mode == 'D'){
            $app->pushRptBack('sys_organs',['sys_no'=>$data['sys_no']],true);
            if($app->croDb('sys_organs')->where('sys_no',$data['sys_no'])->delete()) $this->success('成功删除一条数据！',urlBuild('!.','?internet'));
            else $this->success('数据删除失败');
        }
        println($data);
    }
    public function ajax()
    {
        $app = $this->app;
        list($item,$data) = $app->_getAjaxData();
        $ret = "";
        switch($item){
            case 'show_me_by_pswd':
                // echo json_encode($data);die;
                $pswd = $data['pswd'];
                $upswd = $app->croDb('net_user')->where('user_code',uInfo('code'))->value('command');
                if($upswd == $app->_password($pswd,uInfo('nick'))){
                    $pswd = $app->croDb('sys_organs')->where('sys_no',$data['sysno'])->value('passw');
                    if($pswd){
                        $pswd = $this->pswdHelper($pswd,true);
                        $ret = json_encode(['error'=>1,'desc'=>base64_encode($pswd)]);
                    }
                    else
                        $ret = json_encode(['error'=>-1,'desc'=>'密码获取失败！']);
                }
                else
                 $ret = json_encode(['error'=>-1,'desc'=>'认证失败，输入密码有误！']);
                break;
        }
        echo $ret;
    }
    // 密码处理
    // $pswd 输入密码
    // $iv   初始向量
    private function pswdHelper($pswd,$authed=null){
        // ? iv
        $crp = new \hyang\Crypt;
        $aes = $crp->Algorithm('Aes');
        $delimiter = '[?]';
        $password = sha1(uInfo('code'));
        // 认证
        if($authed){
            if(substr_count($pswd,$delimiter) > 0){
                list($pswdEncrypt,$iv) = explode($delimiter,$pswd);
                return $aes->decrypt($pswdEncrypt,$password,$iv);
            }
        }
        // 加密
        else{            
            $pswdEncrypt = $aes->encrypt($pswd,$password);
            return $pswdEncrypt.$delimiter.($aes->getIv());
        }
    }
}