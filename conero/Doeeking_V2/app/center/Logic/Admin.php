<?php
namespace app\center\Logic;
use app\center\Logic\Controller;
use Exception;
class Admin extends Controller{
    public function init(&$opts,$action){
        $js = $opts['js'];$css = $opts['css'];
        $js[] = 'index/admin';
        $opts['js'] = $js;
        $css[] = 'index/admin';
        $opts['css'] = $css;
    }
    public function main()
    {   
        $this->codeInfo();
        $this->app->_JsVar('uinfo',uInfo());
        return $this->fetch('admin');
    }
    // 账号信息
    private function codeInfo()
    {
        $uType = uInfo('admin');
        $app = $this->app;$html = '';
        if($uType == 'CUSE'){
            $count = 6;
            $uData = $app->croDb('net_user')->where('user_code',uInfo('code'))->find();
            $pData = $app->croDb('net_user')->where('user_code',$uData['editor'])->find();
            $this->assign('cuser','您当账户为['.$pData['user_nick'].']子账号');
            $html = $pData['user_nick'].' 于 '.$uData['on_date'].'新增了您的账户。给他/她发<a href="?email">邮件</a>';
        }
        else{
            // 子账号
            $count = $app->croDb('pers_center')->where('center_id',uInfo('cid'))->count();
            $data = $app->croDb('net_user')->alias('a')->join('pers_center b','a.user_code=b.user_code','left')->where('a.editor',uInfo('code'))->field('a.user_code,a.loginTimes,a.user_nick,a.user_name')->select();
            $html = '';                
            foreach($data as $v){
                $badge = ($v['loginTimes'] > 0)? '<a href="javascript:void(0);">申请冻结用户</a>':'<a href="javascript:void(0);" class="delete_link">删除</a>';
                $html .= '<li class="list-group-item" dataid="'.$v['user_code'].'"><a href="javascript:void(0);">'.$v['user_nick'].'/'.$v['user_name'].'</a><span class="badge">'.$badge.'</span></li>';
            }
            $html = '<div class="panel panel-default">
            <div class="panel-heading">子账户列表</div>
            <div class="panel-body">
                您当前包含的子账户
            </div>
            <ul class="list-group">
                '.$html.'
            </ul>
            </div>';            
        }
        $this->assign('childuser',$html);
        $this->assign('count',$count);
    }
    /*
        bug: pers_center 触发器无效 - 2016年12月18日 星期日
    */
    public function save()
    {
        $data = $_POST;$app = $this->app;$ret = '非法请求地址！！';
        //debugOut($data,true);die;
        $formid = '';
        if(isset($data['formid'])){$formid = $data['formid'];unset($data['formid']);}
        if($formid == 'childuesr'){            
            $user = $data;unset($user['command_chk']);
            if($data['user_code']){}
            else{   // 新增子账号
                unset($data['user_code']);  
                $user = array_merge($user,[
                    'last_ip' => request()->ip(),
                    'admin'   => 'CUSE',
                    'editor'  => uInfo('code')
                ]);
                $user['command'] = $app->_password($user['command'],$user['user_nick']);
                if($app->croDb('net_user')->insert($user)) $ret = '子账号新增时成功插入用户库；';
                $ucode = $app->croDb('net_user')->where('user_nick',$user['user_nick'])->value('user_code');
                if($ucode){
                    $center = [
                        'center_id' => uInfo('cid'),
                        'user_code' => $ucode
                    ];
                    try{
                        // 中心码新增失败
                        if($app->croDb('pers_center')->insert($center)) $ret .= '中心码分配成功.';
                        else $ret .= '中心码分配时失败.';
                    }catch(Exception $e){
                        $report = "\r\n--子账号新增出错--\r\n错误信息： ".($e->getMessage())."\r\n";
                        $report .= $e->getTraceAsString();
                        debugOut($report);                        
                        try{    // 新增失败- 尝试修改
                            if($app->croDb('pers_center')->where('user_code',$ucode)->update(['center_id'=>uInfo('cid')]))
                                $ret .= ',经过修改尝试用户新增成功!!';
                            else $ret .= ',数据再次尝试修改失败！';
                        }catch(Exception $e){
                            $report = "\r\n--子账号新增出错，然后再次尝试修改依然无效--\r\n错误信息： ".($e->getMessage())."\r\n";
                            $report .= $e->getTraceAsString();
                            debugOut($report);
                            $ret .= ',用户再注册中心码时出错！';
                        }
                    }
                }
                else $ret .= '中心码分配时无效.';
            }
        }
        elseif(isset($_GET['mode'])){
            if(isset($_GET['uid']) && $_GET['uid'] != uInfo('uid')){
                $ret = '本次数据维护过程中，因为非法请求参数已经阻止本次操作！';
            }
            else{
                $mode = base64_decode($_GET['mode']);
                if(substr_count($mode,'detele_') > 0){
                    $dataid = $_GET['dataid'];
                    try{
                        $ret = '';
                        $app->pushRptBack('pers_center',['user_code'=>$dataid],true);
                        $app->pushRptBack('net_user',['user_code'=>$dataid],true);
                        if($app->croDb('pers_center')->where('user_code',$dataid)->count() > 0 && 
                            $app->croDb('pers_center')->where('user_code',$dataid)->delete()) $ret .= '用户已经从用户中心注销，';
                        if($app->croDb('net_user')->where('user_code',$dataid)->delete()) $ret .= '用户已经从网站中删除!';
                    }catch(Exception $e){
                        $report = "\r\n错误信息： ".($e->getMessage())."\r\n";
                        $report .= $e->getTraceAsString();
                        debugOut($report);
                        $ret = '本次数据删除过程中出错！';
                    }
                }
                else $ret = '请求参数删除有误！';
            }
        }
        $this->success($ret);
    }
}