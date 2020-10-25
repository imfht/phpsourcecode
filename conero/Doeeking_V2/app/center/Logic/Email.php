<?php
/* 站内邮箱 - 
 *
 */
namespace app\center\Logic;
use app\center\Logic\Controller;
use hyang\Util;
use hyang\Bootstrap;
class Email extends Controller
{
    public function init(&$opts,$action=null){
        if($action == 'index'){
            $js = $opts['js'];
            $js[] = 'index/email';
            if(isset($_GET['write'])){
                $opts['require'] = ['tinymce'];
            }
            $opts['js'] = $js;
        }
        elseif($action == 'edit'){
            $js = $opts['js'];
            $js[] = 'index/interedit';
            $opts['js'] = $js;
        }
    }
    // 首页
    public function main()
    {
        $emRel = model('EmailRel');
        $relData = $emRel->autoRegisterByCode();    // 自动保存数据
        $email = [];
        if($relData) list(,$name) = $relData;
        $email['name'] = $name;
        $type = 'ebox';$showNum = 40;
        if(isset($_GET['send'])) $type = 'send';
        elseif(isset($_GET['write'])) $type = 'write';
        elseif(isset($_GET['read'])) $type = 'read';
        elseif(isset($_GET['recycle'])) $type = 'recycle';
        switch($type){
            case 'ebox':            // 收件箱
                $rsvState = uLogic('Conero')->constKV('smrsv_state');
                Util::arrayRegister($rsvState);
                $emRsver = model('EmailRsver');
                $ctt = $emRsver->where(['rcv_email'=>$name,'state'=>'00'])->count();
                if($ctt>0) $email['eboxNewCtt'] = ' <span class="badge">'.$ctt.'</span>';
                $bstp = new Bootstrap();
                $page = $bstp->page_decode();
                $rsvData = $emRsver->where(['rcv_email'=>$name])->where('state<>"44"')->order('rcv_time desc')->page($page,$showNum)->select();
                $count = $emRsver->where(['rcv_email'=>$name])->where('state<>"44"')->count();
                $xhtml = $bstp->listGrid([
                    'col' => function($v){
                        $statc = Util::arrayVal($v['state']);
                        return '
                            <input type="checkbox" class="ebox_bck hidden" name="ebox_list" value="'.$v['listno'].'">
                            <a href="'.urlBuild('!center:','?email&read='.$v['listno']).'">['.$statc.'] '.$v['title'].'</a>
                            <span style="float:right;">'.$v['rcv_time'].'</span>
                        ';
                    },
                    'hasEnd' => false,
                    'type'   => 'warning'
                ],$rsvData);
                $pageXhtml = $bstp->pageBar(['count'=>$count,'num'=>$showNum]);
                $xhtml = $xhtml? '<ul class="list-group">'.$xhtml.'</ul>
                        '.$pageXhtml.'
                        <p class="bg-info" style="padding:5px;">
                            <input type="checkbox" class="ebox_bck hidden" id="ebox_bck_all">
                            <a href="javascript:void(0);" id="ebox_bck_toggle">选择器</a>
                            <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_push_bak" dataid="emrsv_rv2bk">移除列表到回收站</a>
                            <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_del_lnk" dataid="emrsv_rv2del">彻底删除</a>
                        </p>
                    ':'您还没有发送数据记录';
                $email['rsvList'] = $xhtml;
                break;
                break;
            case 'send':            // 发件箱
                $emSder = model('EmailSder');
                $bstp = new Bootstrap();
                $page = $bstp->page_decode();
                $sdData = $emSder->where(['sd_email'=>$name])->where('state<>"44"')->order('sd_time desc')->page($page,$showNum)->select();
                $count = $emSder->where(['sd_email'=>$name])->where('state<>"44"')->count();
                $xhtml = '';$num = 1;
                foreach($sdData as $v){
                    $xhtml .= '<li class="list-group-item list-group-item-success">
                        <input type="checkbox" class="ebox_bck hidden" name="ebox_list" value="'.$v['listno'].'">
                        '.$num.'. '.$v['title'].' ('.$v['rcv_email'].')
                        <span style="float:right;">'.$v['sd_time'].'</span></li>
                    ';
                    $num ++;
                }
                $pageXhtml = $bstp->pageBar(['count'=>$count,'num'=>$showNum]);
                $xhtml = $xhtml? '
                        <ul class="list-group">'.$xhtml.'</ul>'.$pageXhtml.'
                         <p class="bg-info" style="padding:5px;">
                            <input type="checkbox" class="ebox_bck hidden" id="ebox_bck_all">
                            <a href="javascript:void(0);" id="ebox_bck_toggle">选择器</a>
                            <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_push_bak" dataid="emsder_rv2bk">移除列表到回收站</a>
                            <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_del_lnk" dataid="emsder_rv2del">彻底删除</a>
                        </p>
                        '
                    :'您还没有发送数据记录';
                $email['sdList'] = $xhtml;
                break;
            case 'read':            // 信箱
                $emRsver = model('EmailRsver');
                $listno = $_GET['read'];                
                $rsvData = $emRsver->get($listno);
                if($rsvData->state == '00'){    // 阅读状态更新
                    $rsvData->state = '90';
                    $rsvData->save();
                }
                $email['navLiPlus'] = '<li role="presentation" dataid="read"><a href="javascript:void(0);">阅读邮件</a></li>';
                // $email['rdContent'] = $rsvData['content'];
                $this->assign('data',$rsvData->toArray());
                break;
            case 'recycle':         // 回收站
                $xhtml = '';
                $typeXhtml = '<p class="text-right">
                    <a href="'.urlBuild('!center:','?email&recycle=Y').'"'.($_GET['recycle'] == 'Y'? ' class="text-success"':'').'>收件箱回收站</a>
                    <a href="'.urlBuild('!center:','?email&recycle=sd').'"'.($_GET['recycle'] == 'Y'? '':' class="text-success"').'>发件箱回收站</a></p>';
                if($_GET['recycle'] == 'Y'){ // 收件回收站
                    $emRsver = model('EmailRsver');
                    $map = ['rcv_email'=>$name,'state'=>44];
                    $count = $emRsver->where($map)->count();
                    $bstp = new Bootstrap();
                    $page = $bstp->page_decode();
                    $rsvData = $emRsver->where($map)->order('rcv_time desc')->page($page,$showNum)->select();
                    $xhtml = '';$num = 1;
                    foreach($rsvData as $v){
                        $xhtml .= '<li class="list-group-item list-group-item-danger">'.$num.'.
                            <input type="checkbox" class="ebox_bck hidden" name="ebox_list" value="'.$v['listno'].'">
                            '.$v['title'].'
                            <span style="float:right;">'.$v['rcv_time'].'</span></li>';
                            $num++;
                    }
                    $pageXhtml = $bstp->pageBar(['count'=>$count,'num'=>$showNum]);
                    $xhtml = $xhtml? '<ul class="list-group">'.$xhtml.'</ul>
                            '.$pageXhtml.'
                            <p class="bg-info" style="padding:5px;">
                                <input type="checkbox" class="ebox_bck hidden" id="ebox_bck_all">
                                <a href="javascript:void(0);" id="ebox_bck_toggle">选择器</a>
                                <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_reset_bak" dataid="emrsv_rvRstBk">撤销列表到回收站</a>
                                <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_del_lnk" dataid="emrsv_rv2del">彻底删除</a>
                            </p>
                        ':'<p class="text-success">您还没有发送数据记录!</p>';
                }
                else{
                    $emRsver = model('EmailSder');
                    $map = ['sd_email'=>$name,'state'=>44];
                    $count = $emRsver->where($map)->count();
                    $bstp = new Bootstrap();
                    $page = $bstp->page_decode();
                    $rsvData = $emRsver->where($map)->order('sd_email desc')->page($page,$showNum)->select();
                    $xhtml = '';$num = 1;
                    foreach($rsvData as $v){
                        $xhtml .= '<li class="list-group-item list-group-item-danger">'.$num.'.
                            <input type="checkbox" class="ebox_bck hidden" name="ebox_list" value="'.$v['listno'].'">
                            '.$v['title'].'
                            <span style="float:right;">'.$v['sd_email'].'</span></li>';
                            $num++;
                    }
                    $pageXhtml = $bstp->pageBar(['count'=>$count,'num'=>$showNum]);
                    $xhtml = $xhtml? '<ul class="list-group">'.$xhtml.'</ul>
                            '.$pageXhtml.'
                            <p class="bg-info" style="padding:5px;">
                                <input type="checkbox" class="ebox_bck hidden" id="ebox_bck_all">
                                <a href="javascript:void(0);" id="ebox_bck_toggle">选择器</a>
                                <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_reset_bak" dataid="emsder_rvRstBk">撤销列表到回收站</a>
                                <a href="javascript:void(0);" class="ebox_bck text-danger hidden" id="ebox_del_lnk" dataid="emsder_rv2del">彻底删除</a>
                            </p>
                        ':'<p class="text-success">您还没有发送数据记录!</p>';
                }
                $xhtml .= $typeXhtml;
                $email['rycList'] = $xhtml;
                break;
            case 'write':
                $siteMail = uLogic('Conero')->_const('conero_admin.site_mail');
                $siteMail = $siteMail? $siteMail : 'yanghua@brximl.com';
                $xhtml = '<br><a href="javascript:void(0)" dataid="'.$siteMail.'" class="addRsvList_lnk">给站长发邮件</a>';
                $emRsver = model('EmailRsver');
                $data = $emRsver->getSendEmlist();
                foreach($data as $v){
                    $sdEmail = $v['sd_email'];
                    $xhtml .= '<br><a href="javascript:void(0)" dataid="'.$sdEmail.'" class="addRsvList_lnk">'.$sdEmail.'</a>';
                }
                $email['sbMateList'] = $xhtml;
                break;
        }
        
        $email['type'] = $type;
        $this->assign('email',$email);
        return $this->fetch('email');
    }
    // 编辑页
    public function edit($view){
        $this->viewInit($view);
        $editParam = [
            'navbar'    => '<li><a href="'.urlBuild('!center:','?email').'">邮箱</a></li>',
            'navActive' => '编辑'
        ];
        $this->editPageParam($editParam);
        $this->form($view);
    }
    public function save()
    {
        $app = $this->app;
        list($data,$mode,$map) = $app->_getSaveData('listno');
        $dataid = isset($data['dataid'])? $data['dataid']: '';
        if($dataid) unset($data['dataid']);
        // 数据推送到 回收箱
        if(in_array($dataid,['emrsv_rv2bk','emsder_rv2bk'])){
            $md = model($dataid == 'emrsv_rv2bk'? 'EmailRsver':'EmailSder');
            $vlist = explode(',',$data['vlist']);
            $ctt = 0;
            foreach($vlist as $v){
                $dlist = $md->get($v);
                $dlist->state = '44';
                if($dlist->save()) $ctt += 1;
            }
            $count = count($vlist);
            if($ctt == 0) $this->error('十分不幸，移入回收箱失败(0/'.$count.')！');
            else $this->success('成功将邮箱移入回收箱('.$ctt.'/'.$count.')！');
        }
        elseif(in_array($dataid,['emrsv_rv2del','emsder_rv2del'])){
            $md = model($dataid == 'emrsv_rv2del'? 'EmailRsver':'EmailSder');
            $td = $dataid == 'emrsv_rv2del'? 'sys_email_rsv':'sys_email_sd';
            $vlist = explode(',',$data['vlist']);
            $ctt = 0;
            foreach($vlist as $v){
                $dlist = $md->get($v);
                $app->pushRptBack($td,['listno'=>$v],true);
                if($dlist->delete()) $ctt += 1;
            }
            $count = count($vlist);
            if($ctt == 0) $this->error('十分不幸，邮件删除失败(0/'.$count.')！');
            else $this->success('恭喜你，已成功删除邮箱('.$ctt.'/'.$count.')！');
        }
        elseif(in_array($dataid,['emrsv_rvRstBk','emsder_rvRstBk'])){
            $md = model($dataid == 'emrsv_rvRstBk'? 'EmailRsver':'EmailSder');
            $vlist = explode(',',$data['vlist']);
            $ctt = 0;
            foreach($vlist as $v){
                $dlist = $md->get($v);
                $dlist->state = '90';
                if($dlist->save()) $ctt += 1;
            }
            $count = count($vlist);
            if($ctt == 0) $this->error('十分不幸，回收箱撤销失败(0/'.$count.')！');
            else $this->success('成功将邮箱从回收箱中撤销('.$ctt.'/'.$count.')！');
        }
        switch($dataid){
            case 'email_sd_data':       // 邮件发送
                $emSder = model('EmailSder');
                if($mode == 'A'){
                   $source = $emSder->sendEmail($data);
                   $msg = $source['msg'];
                   if($source['error'] === 0) $this->success($msg);
                   $this->error($msg);
                }
                break;
        }
        println($data,$mode,$map);
    }
}