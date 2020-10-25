<?php
/*   2016年12月25日 星期日
 *   用于对网站版本的风格发发布的控制
*/
namespace app\index\controller;
trait IndexTrait{    
    // Boostarp 风格首页
    protected function iThemeBootstrap()
    {
        $this->loadScript([
            'title'=>'Conero','js'=>['index/ibootstrap'],'css'=>['index/ibootstrap'],'bootstrap'=>true
        ]);
        $uInfo = uInfo();        
        $themeSelect = function(){
            $html = '<li role="presentation" class="dropdown-header">版本风格</li>';
            // class="disabled"
            $disable = $this->reportThemeChooseRpt();
            foreach($this->hometheme as $k=>$v){
                $html .= '<li'.($disable == $k? ' class="disabled"':'').'><a href="?theme='.bsjson(['theme'=>$k,'day'=>sysdate('date')]).'">'.$v.'</a></li>';
            }
            $html .= '<li><a href="/conero/">重载页面</a></li>';
            return $html;
        };
        $page = [
            'year'=>date('Y'),
            'logined' => $this->uLoginCkeck()? 'Y':'N'
            ,'nick' => isset($uInfo['nick'])? $uInfo['nick']:null
            ,'themeSelect' => $themeSelect()
            ,'visit_count'=>$this->aboutVisit()
        ];
        // 开发者-最高权限
        if(uInfo('admin') == 'DEV'){
            $page['navList'] = '<li><a href="/conero/admin.html" target="_blank">系统管理</a></li>';
        }
        // 系统首页
        $topText = uLogic('Conero')->sysInfor('10','conero_top_text');
        if($topText){
            $page['topCttTxt'] = $topText['content'];
            $page['topCttTitle'] = $topText['title'];
        }
         // 登录信息
        $this->syslogin($page);
        $this->assign('page',$page);
        if(isset($uInfo['nick'])){
            $this->_JsVar('nick',$uInfo['nick']);
        }
        // 信息发布
        $this->printInformation();
        return $this->fetch('ibootstrap');
    }
    // 登录信息
    protected function syslogin(&$page)
    {
        $uInfo = uInfo();
        if(empty($uInfo)) return;
        $photo = isset($uInfo['code'])? $this->croDb('sys_file')->where(['user_code'=>$uInfo['code'],'file_use'=>'P0'])->value('url_name'):null; // 头像
        if($photo) $page['photo'] = '/conero/files/'.$photo;
        $syslogin = $this->croDb('syslogin')->where('uid',uInfo('uid'))->find();
        if($syslogin){
            $log = '';
            if($syslogin['location']){
                $location = bsjson($syslogin['location']);
                $log = '<dt>登录信息</dt><dd>您所处地区：<em>'.$location['country'].'.'.$location['area'].'地区</em>'
                        .'  城市：<em>'.$location['city'].'.'.$location['region'].'</em>'
                        .'  IP: <em>'.$location['ip'].'('.$location['isp'].')</em>'
                        .'</dd>'
                ;     
            }
            $log .= '<dt>登录时间</dt><dd>'.$syslogin['edittm'].'</dd>'
                    .'<dt>登录次数</dt><dd>'.$syslogin['login_count'].'</dd>';
            $page['location'] = $log;
        }
        //任务提醒
        $data = $this->croDb('sys_taskrpt')->where(['user_code'=>$uInfo['code'],'end_mk'=>'N'])->order('task_stime desc')->limit(10)->select();
        $ctt = 1;
        $tlist = '';
        foreach($data as $v){
            $dt = ($v['dateline'] && $v['end_mk'] == 'N')? getDays($v['dateline'],date('Y-m-d')):0;
            $dateline = $dt > 0? '('.$v['dateline'].'/'.$dt.') ':'';
            $tlist .= '<li class="list-group-item">
                    <a href="'.($v['task_url']? $v['task_url']:urlBuild('!center:index/edit/task/'.$v['listno'])).'" class="log_detail_link" dataid="28">'.$ctt.'. '.$dateline.$v['task'].'</a>
                    <span style="float: right;">'.$v['task_stime'].'</span></li>';
            $ctt++;
        }
        if($tlist) $page['tasklist'] = '<h4><a href="/conero/center.html?task" target="_blank" class="text-success">任务提醒</a></h4><ul class="list-group">'.$tlist.'</ul>';
        // 网络账号
        $data = $this->croDb('sys_organs')->where(['user_code'=>$uInfo['code']])->where('url is not null')->order('visit_count desc')->limit(50)->select();
        $xhtml = '';
        foreach($data as $v){
            $xhtml .= '<a href="'.urlBuild('!center:index/internet','?url='.base64_encode($v['url'])).'&uid='.bsjson(['type'=>'updateCtt','sys_no'=>$v['sys_no']]).'" target="_blank" class="col-md-3" title="访问量共'.$v['visit_count'].'，最近一次访问时间'.($v['last_vtime']? $v['last_vtime']:'无').'">'.$v['name'].'</a>';
        }
        if($xhtml){
            $xhtml = '<div class="row"><h4 class="col-md-12"><a href="'.urlBuild('!center:','?internet').'" target="_blank" class="text-success">我的网站账号</a></h4>'.$xhtml.'</div>';
            $page['organList'] = $xhtml;
        }
    }
    // 信息发布
    protected function printInformation()
    {
        $infor = [];
        // type - 30
        $html = '';
        $data = $this->croDb('sys_infor')->where('type','30')->limit(10)->select();        
        foreach($data as $v){
            $html .= '<a href="/conero/index/yang/infor.html?uid='.base64_encode($v['no'].'='.sysdate()).'" class="list-group-item">'.$v['title'].'<span style="float:right;"><em>'.$v['mtime'].'</em></span></a>';
        }
        if($html) $infor['conero'] = $html;
        // type - 20 - url 暂时未修改
        $html = '';
        $data = $this->croDb('sys_infor')->where('type','20')->limit(10)->select();
        foreach($data as $v){
            $html .= '<a href="/conero/index/yang/infor.html?uid='.base64_encode($v['no'].'='.sysdate()).'" class="list-group-item">'.$v['title'].'<span style="float:right;"><em>'.$v['mtime'].'</em></span></a>';
        }
        if($html) $infor['sitenews'] = $html;
        if($infor) $this->assign('infor',$infor);
    }
    /******************************************************************************************************************************************************/
    // 第一版本
    protected function iThemeDefault(){
        $this->loadScript([
            'title'=>'Conero','js'=>['index/app','index/index'],'css'=>['index/index']
        ]);
        $this->_JsVar('key','854447');
        $p = [
            'visit_count'=>$this->aboutVisit(),'themeselect' => $this->themeSelectOption()
        ];
        // 地区信息显示
        $location = $this->croDb('syslogin')->where('uid',uInfo('uid'))->value('location');
        if($location){
            $location = bsjson($location);
            $p['location'] = '<div>您所处地区：<span>'.$location['country'].'.'.$location['area'].'地区</span>'
                    .'<br>城市：<span>'.$location['city'].'.'.$location['region'].'</span>'
                    .'<br>IP: <span>'.$location['ip'].'('.$location['isp'].')'
                    .'</div>'
            ;            
        }
        $this->assign('p',$p);

        $uLogin = 'Y';
        if(!$this->uLoginCkeck()) $uLogin = 'N';
        $this->_JsVar('ulogin',$uLogin);
        $this->assign([
            'uLogin' => $uLogin,
            'name'   => uInfo('name')
        ]);
        // 开发者-最高权限
        if(uInfo('admin') == 'DEV') $this->_dev();        
        return $this->fetch('index');
    }
    protected function themeSelectOption($option=null,$name=null)
    {
        $name = $name? $name:(isset($option['name'])? $option['name']:null);
        $html = isset($option['select'])? $option['select']:(is_string($option)? $option:'<select id="themeSelectOption">');
        $selected = isset($option['selected'])? $option['selected']:$this->reportThemeChooseRpt();
        //echo $selected;die;
        foreach($this->hometheme as $k=>$v){
            $html .= '<option value="'.($k == 'default'? '':bsjson(['theme'=>$k,'day'=>sysdate('date')])).'"'.($selected == $k? ' selected':'').'>'.$v.'</option>';
        }
        $html .= '</select>';        
        if($name){
            $this->assign($name,$html);
        }
        return $html;
    }    
}