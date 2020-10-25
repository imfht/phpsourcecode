<?php
/*  2017年2月24日 星期五
 *  家谱应用公共访问页面
 */
namespace app\clan\controller;
use think\Controller;
class Index extends Controller
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'title'=>'祖公源居 - Conero','bootstrap'=>true
        ]);
        $isLogin = $this->uLoginCkeck()? 'Y':'N';
        if('Y' == $isLogin){
            $gcter = model('Gcenter');
            $data = $gcter->where('user_code',uInfo('code'))->field('gen_no,gen_title,mtime')->select();
            if($data){
                $html = '<ul class="list-group">';
                $ctt = 0;
                foreach($data as $v){
                    $ctt += 1;
                    $html .= '<li class="list-group-item">'.($ctt).'. <a href="'.urlBuild('!.center/index/'.$v['gen_no']).'" target="_blank">'.$v['gen_title'].'</a><span style="float: right;">'.$v['mtime'].'</span></li>';
                }
                $html .= '</ul>';
                $this->assign('genList',$html);
            }
        }
        $pages = [
            'count' => $this->aboutVisit(),
            'isLogin'   => $isLogin
        ];
        $abtCinfo = uLogic('Conero')->sysInfor('10','about_clan_info');
        if($abtCinfo) $pages['abtcinfo'] = [
            'title' => $abtCinfo['title'],
            'content' => $abtCinfo['content']
        ];
        $this->assign('pages',$pages);
        return $this->fetch();
    }    
    // 家谱编辑
    public function edit()
    {
        $this->loadScript([
            'title'=>'Conero-祖公源居','bootstrap'=>true,'js'=>['index/edit']
        ]);
        if($this->uLoginCkeck() == false) $this->error('您还没有登入系统！');
        $genno = getUrlBind('edit');
        // println($genno);
        $data = ['mode'=>'A'];
        if($genno){
            $gcter = model('Gcenter');
            $ctt = $gcter->where(['user_code'=>uInfo('code'),'gen_no'=>$genno])->count();
            if(empty($ctt)) $this->error('非法请求地址','index/index');
            $data = $gcter->get($genno);
            $data['mode'] = 'M';
            $data['pk'] = '<input type="hidden" name="gen_no" value="'.$data['gen_no'].'">';
        }
        $this->assign('data',$data);
        return $this->fetch();
    }
    // 数据保存界面
    public function save()
    {
        list($data,$mode,$map) = $this->_getSaveData('gen_no');
        // println($data,$mode,$map,$_FILES);die;
        if('A' == $mode){
            $uInfo = Uinfo();
            $nick = $uInfo['nick'];
            $data['editlog'] = $nick.' 于 '.sysdate().' 新建了家谱（'.$data['gen_title'].'）,操作IP: '.(request()->ip()).'<br>';
            $data['user_name'] = $nick;
            $data['user_code'] = $uInfo['code'];
            if(model('Gcenter')->insert($data)){
                $this->success('您新增一条数据！','index/index');
            }
        }
        elseif('M' == $mode){
            $fData = $_FILES;
            $ret = '';
            if(count($fData)>0){
                $sysfile = uLogic('Sysfile');
                $sysfile->uploadPlusData([
                    'file_use'      => 'G0',
                    'file_group'    => 'gencenter',
                    'grp_more'      => 'gen_no',
                    'grp_moreMk'    => $map['gen_no'],
                    'file_own'      => ''
                ]);    
                $files = $sysfile->upload();
                if($files) $ret .= '文件上传成功！';
                // println($files);die;
            }
            unset($data['file_name']);
            unset($data['fremark']);
            $ret .= (model('Gcenter')->where($map)->update($data))? '数据更新成功.':'遗憾，数据更新失败.';
            $this->success($ret,urlBuild('!.index/edit/'.$map['gen_no']));
        }        
    }
    // 保存来自互联网的图片
    public function svnetimg()
    {
        list($data) = $this->_getSaveData();
        try{
            $url = $data['url'];         
            $saveData = [
                'file_use'      => 'G0',
                'file_group'    => 'gencenter',
                'grp_more'      => 'gen_no',
                'grp_moreMk'    => $data['gen_no'],
                'file_own'      => ''
            ];
            if(empty($data['remark'])) $saveData['remark'] = '图片来源网站: '.$url;

            $name = $data['name'];
            $name = $name? $name:null;

            $sf = uLogic('Sysfile');
            $sf->uploadPlusData($saveData);
            if($sf->fromUrl($url,$name)) $this->success('图片保存成功');
        }
        catch(\Exception $e){
            debugOut($e->getTraceAsString());
        }
        $this->success('图片获取失败！');
    }
}

