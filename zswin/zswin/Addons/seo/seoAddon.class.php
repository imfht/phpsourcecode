<?php

namespace Addons\seo;
use Common\Controller\Addon;

/**
 * seo设置插件
 * @author zswin
 */

    class seoAddon extends Addon{

        public $info = array(
            'name'=>'seo',
            'title'=>'seo设置',
            'description'=>'方便站长自定义seo关键词等',
            'status'=>1,
            'author'=>'zswin',
            'version'=>'0.1'
        );

        public function install(){
        	$this->getisHook($this->info['name'], $this->info['name'], $this->info['description']);
            return true;
        }

        public function uninstall(){
        	$this->deleteHook($this->info['name']);
            return true;
        }

     public function seo($param)
    {
    	$id=I('id');
    	$userid=I('uid',$_SESSION['zs_home']['user_auth']['uid'],'int');
    	 $cid=I('cid',0,'int');
    	 $keyword=I('keyword','','strip_tags');
    	
    	
    	
        $this->assign($param);
        $config = $this->getConfig();
        $cname=strtolower(CONTROLLER_NAME);
        $aname=strtolower(ACTION_NAME);
     
           
       foreach($config as $key => $vo){
        	
        	$lsstr='';
        	$lsstr=$vo;
        	$lsstr=str_replace('[title]',  C('WEB_SITE_TITLE'), $lsstr);
        	$lsstr=str_replace('[keyword]',  C('WEB_SITE_KEYWORD'), $lsstr);
        	$lsstr=str_replace('[description]',C('WEB_SITE_DESCRIPTION'), $lsstr);
        	
        	$config[$key]=$lsstr;
        }
     	$seotitle=C('WEB_SITE_TITLE');
        $seokeyword=C('WEB_SITE_KEYWORD');
        $seodescription=C('WEB_SITE_DESCRIPTION');
        	
        
        if($cname=='index'&&$aname=='artc'){
        	$artinfo=D('Home/Article')->get_info($id);
        	
        	$catename=get_cate_nameByid($artinfo['cid']);
        	
        	$config['contitle']=str_replace('[arttitle]',  $artinfo['title'], $config['contitle']);
        	$config['contitle']=str_replace('[artcate]',  $catename, $config['contitle']);
        	$config['conkey']=str_replace('[arttitle]',  $artinfo['title'], $config['conkey']);
        	$config['conkey']=str_replace('[artcate]',  $catename, $config['conkey']);
        	$config['condes']=str_replace('[arttitle]',  $artinfo['title'], $config['condes']);
        	$config['condes']=str_replace('[artcate]',  $catename, $config['condes']);
        	
        	
        	$seotitle=$config['contitle'];
        	$seokeyword=$config['conkey'];
        	$seodescription=$config['condes'];
        	
        	
        	
        }
    if($cname=='index'&&$aname=='zanart'){
        	
        	$seotitle=$config['zantitle'];
        	$seokeyword=$config['zankey'];
        	$seodescription=$config['zandes'];
        }
    if($cname=='index'&&$aname=='hotart'){
        
        	$seotitle=$config['hottitle'];
        	$seokeyword=$config['hotkey'];
        	$seodescription=$config['hotdes'];
        }
    if($cname=='index'&&$aname=='gzart'){
        
        	$seotitle=$config['gztitle'];
        	$seokeyword=$config['gzkey'];
        	$seodescription=$config['gzdes'];
        }
    if($cname=='index'&&$aname=='index'){
        
        	$seotitle=$config['indextitle'];
        	$seokeyword=$config['indexkey'];
        	$seodescription=$config['indexdes'];
        	
        	
        }
     if($cname=='index'&&$aname=='search'){
     	
     	
     	$config['sltitle']=str_replace('[slname]', $keyword, $config['sltitle']);
        $config['slkey']=str_replace('[slname]', $keyword, $config['slkey']);
        $config['sldes']=str_replace('[slname]', $keyword, $config['sldes']);
        $seotitle=$config['sltitle'];
        	$seokeyword=$config['slkey'];
        	$seodescription=$config['sldes'];
        }
     if($cname=='index'&&$aname=='alltag'){
        	
        	$seotitle=$config['attitle'];
        	$seokeyword=$config['atkey'];
        	$seodescription=$config['atdes'];
        }
     if($cname=='index'&&$aname=='tagart'){
     	$map['id']=$id;
        	 $taginfo=M('tags')->where($map)->find();
        	 
        	 
        	 
        	 
        	 
        	 
        	 $config['tltitle']=str_replace('[tagname]', $taginfo['title'], $config['tltitle']);
        	$config['tltitle']=str_replace('[tagdes]',  $taginfo['des'], $config['tltitle']);
        	 $config['tlkey']=str_replace('[tagname]', $taginfo['title'], $config['tlkey']);
        	$config['tlkey']=str_replace('[tagdes]',  $taginfo['des'], $config['tlkey']);
        	 $config['tldes']=str_replace('[tagname]', $taginfo['title'], $config['tldes']);
        	$config['tldes']=str_replace('[tagdes]',  $taginfo['des'], $config['tldes']);
        	$seotitle=$config['tltitle'];
        	$seokeyword=$config['tlkey'];
        	$seodescription=$config['tldes'];
        	
        	
        	
        }
    if($cname=='index'&&$aname=='artlist'){
        	$cateinfo=M('cate')->where(array('id'=>$cid))->find();
        	
        	
            $config['cltitle']=str_replace('[catename]',  $cateinfo['name'], $config['cltitle']);
        	$config['cltitle']=str_replace('[catedes]',  $cateinfo['des'], $config['cltitle']);
        	$config['clkey']=str_replace('[catename]',  $cateinfo['name'], $config['clkey']);
        	$config['clkey']=str_replace('[catedes]',  $cateinfo['des'], $config['clkey']);
        	$config['cldes']=str_replace('[catename]',  $cateinfo['name'], $config['cldes']);
        	$config['cldes']=str_replace('[catedes]',  $cateinfo['des'], $config['cldes']);
        	
        	
        	$seotitle=$config['cltitle'];
        	$seokeyword=$config['clkey'];
        	$seodescription=$config['cldes'];
        	
        	
        }
        
        
        
    if($cname=='user'&&$aname=='register'){
        	
        	$seotitle=$config['regtitle'];
        	$seokeyword=$config['regkey'];
        	$seodescription=$config['regdes'];
        }
    if($cname=='user'&&$aname=='login'){
        	
        	$seotitle=$config['logintitle'];
        	$seokeyword=$config['loginkey'];
        	$seodescription=$config['logindes'];
        }
    if($cname=='user'&&$aname=='mi'){
        	
        	$seotitle=$config['pstitle'];
        	$seokeyword=$config['pskey'];
        	$seodescription=$config['psdes'];
        }

        
   if($cname=='ucenter'){
        	$cxuser=query_user(array('username','nickname'),$userid);
      	
        	switch ($aname){
        		
        		case 'index':
        			$appname='个人资料';
        			break;
        		case 'useravatarset':
        			$appname='头像设置';
        			break;
        		case 'userfocus':
        			$appname='关注的用户';
        			break;
        		case 'usertagfocus':
        			$appname='关注的标签';
        			break;
        		case 'usersc':
        			$appname='收藏文章列表';
        			break;
        		case 'changepwd':
        			$appname='修改密码';
        			break;
        		case 'yzmail':
        			$appname='验证邮件';
        			break;
        		case 'usermail':
        			$appname='用户消息';
        			break;
        		case 'usersendmail':
        			$appname='发送私信';
        			break;
        		case 'userart':
        			$appname='用户文章列表';
        			break;
        		case 'artadd':
        			$appname='添加文章';
        			break;
        		case 'artedit':
        			$appname='编辑文章';
        			break;
        		
        		
        		
        		
        	}
        	$config['uctitle']=str_replace('[username]',  $cxuser['username'], $config['uctitle']);
        	$config['uctitle']=str_replace('[nickname]',  $cxuser['nickname'], $config['uctitle']);
        	$config['uctitle']=str_replace('[appname]',  $appname, $config['uctitle']);
        	$config['uckey']=str_replace('[username]',  $cxuser['username'], $config['uckey']);
        	$config['uckey']=str_replace('[nickname]',  $cxuser['nickname'], $config['uckey']);
        	$config['uckey']=str_replace('[appname]',  $appname, $config['uckey']);
        	$config['ucdes']=str_replace('[username]',  $cxuser['username'], $config['ucdes']);
        	$config['ucdes']=str_replace('[nickname]',  $cxuser['nickname'], $config['ucdes']);
        	$config['ucdes']=str_replace('[appname]',  $appname, $config['ucdes']);
        	$seotitle=$config['uctitle'];
        	$seokeyword=$config['uckey'];
        	$seodescription=$config['ucdes'];
        	
        	
        	
        }

        
        
        

       
        
       $this->assign('seotitle',$seotitle);
       $this->assign('seokeyword',$seokeyword);
       $this->assign('seodescription',$seodescription);
        
      
        $this->assign('config',$config);
        $this->display('seo');
    }
    
    
    
    
    }