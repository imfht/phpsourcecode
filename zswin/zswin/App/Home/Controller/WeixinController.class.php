<?php
namespace Home\Controller;
use Common\Weixin\Wechat;
use Admin\Controller\CommonController;
use Common\Weixin\Wechatext;

class WeixinController extends HomeController {
   
    public $wx;
    public $rdata;
    public function index(){
    	
    
    	$options = array(
    			'token'=>C('TOKEN'), //填写你设定的key
    			'encodingAesKey'=>C('AESKEY'), //填写加密用的EncodingAeSKey
    			'appid'=>C('WXAPPID'), //填写高级调用功能的app id
    			'appsecret'=>C('WXAPPSECRET'), //填写高级调用功能的密钥
    				
    	);
    	$this->wx = new \Common\Weixin\Wechat($options);
    	 
    	$this->wx->valid();
    	
        $cateid= C('WXCATEID');
        $mapcate['id']=array('in',$cateid);
      
       $catearr= M('cate')->where($mapcate)->select();
       $catecount= M('cate')->where($mapcate)->count();

      
   
      foreach ($catearr as $key =>$vo){
      
      	$cate[$key]=array('type'=>'view','url'=>(is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.C('WEB_DIR').'/'.ZSU('/artlist/'.$vo['id'],'Index/artlist',array('cid'=>$vo['id'])),'name'=>$vo['name']);
      }
      $newmenu =  array(
      		   		"button"=>
      		   			array(
      				   				array('type'=>'click','name'=>'每日精选','key'=>'newblog'),
      				   				array( 'name' => '推荐分类','sub_button' => $cate),
                   
      		   					),
      		  		);
     
      
      
     
     // $weObj->valid();
      if(C('CREATMENU')==1){
        	
        	$msg1=$this->wx->createMenu($newmenu);
        	
      }
     
      
        
      $f = $this->wx->getRev()->getRevFrom();
      $t = $this->wx->getRevType();
     
     // $this->wx->text($f.$t.':data:'.var_export($this->wx->getRevData(),true).':event:'.var_export($this->wx->getRevEvent(),true))->reply();
     switch ($t){
     	 
     	case 'text':
     		$this->rdata = $this->wx->getRevData();
     		
     		 $this->wxtext($this->rdata);
     		 
     		break;
     	case 'event':
     		$this->rdata = $this->wx->getRevEvent();
     		
     		 $this->wxevent($this->rdata);
     		break;
     
     
     
     
     
     }
   
      $this->display();
        
    }
    public function sendwx(){
    	
    	$cpass=think_decrypt(C('WXPASS'),UC_AUTH_KEY);
    	
    	$wtoptions = array(
    			'account'=>C('WXUSER'),
    			'password'=>$cpass,
    			'datapath'=>'../Data/cookie_',
    			'debug'=>true,
    			'logcallback'=>'logdebug'
    	);
    	$wt= new Wechatext($wtoptions);
    	
    	if ($wt->checkValid()) {
    	$userlist=$wt->getUserList(0,10);
    	foreach ($userlist as $key =>$uvo){
    	
    	
    		$wt->send($uvo['id'],'每日推荐更新了，快去看看吧。输入推荐或者点击下方的每日精选！');
    	
    	}
    	}
    	
    	
    	
    	
    	
    }
    
    
    public function wxtext($data){
    	$artid= C('WXARTID');
    	$mapart['id']=array('in',explode(',', $artid));
    	$artarr= M('article')->where($mapart)->order('tj desc')->select();
    	$art=array();
    	foreach ($artarr as $key =>$vo){
    		$art[$key]=array('Title'=>$vo['title'],'Description'=>cutstr_html(op_t($vo['description']),50),'PicUrl'=>getImgs($vo['description'],0),'Url'=>(is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.C('WEB_DIR').'/'.ZSU('/artc/'.$vo['id'],'Index/artc',array('id'=>$vo['id'])));
    	
    	
    	
    	}
    	
    	$result = array(
    			'MsgType' => 'text',
    			'Content' => "如果您不是留言的话……\n\r\n您可以发送“help”或者“？”获取使用帮助。\n\r\n如果您确定要留言，您放心，您的留言我已经收到，我会尽快回复你的。\n\r\n谢谢您的支持！^_^"
    	);
    	/* 以下为API函数调用处理 */
    	$lastdo = $this->getValue('do');
    	
    	if ( $lastdo == 'search' ) {
    		 
    		$map['title']=array('like','%'.$data['Content'].'%');
    		$map['status']=1;
    		$sartarr=M('article')->where($map)->order('tj desc')->select();
    		$sart=array();
    		foreach ($sartarr as $key =>$svo){
    			$sart[$key]=array('Title'=>$svo['title'],'Description'=>cutstr_html(op_t($svo['description']),50),'PicUrl'=>getImgs($svo['description'],0),'Url'=>(is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.C('WEB_DIR').'/'.ZSU('/artc/'.$svo['id'],'Index/artc',array('id'=>$svo['id'])));
    			 
    	
    	
    		}
    		 
    		if(M('article')->where($map)->count()>0){
    			$result = array(
    					'MsgType' => 'news',
    					'Content' => $sart,
    					 
    			);
    			
    			 
    		}else{
    			$result = array(
    					'MsgType' => 'text',
    					'Content' => '暂无相关内容！',
    					 
    			);
    		}
    		 
    		 
    	}
    	if ( $data['Content'] == 'help'||$data['Content'] == '？'||$data['Content'] == '?') {
    		$result = array(
    				'MsgType' => 'text',
    				'Content' => "发送“推荐”可发送当日精选博客\n\r\n发送“搜索”可搜索博客内容\n\r\n发送“退出搜索”可退出搜索\n\r\n",
    				 
    		);
    	}
    	 
    	if ( $data['Content'] == '推荐') {
    	
    		$result = array(
    				'MsgType' => 'news',
    				'Content' => $art,
    	
    		);
    	
    	}
    	 
    	if ( $data['Content'] == '退出搜索' ) {
    		$this->setValue('do',null);
    		$result = array(
    				'MsgType' => 'text',
    				'Content' => '已经退出搜索模式！'
    		);
    		 
    	}
    	if ( $data['Content'] == '搜索' ) {
    		$this->setValue('do', 'search');
    		$result = array(
    				'MsgType' => 'text',
    				'Content' => '请输入关键词（只允许一个关键词）'
    		);
    		 
    	}
    	
    	
    	
    
    	
    	 
    	switch ($result['MsgType']){
    		 
    		case 'text':
    			$this->wx->text($result['Content'])->reply();
    	
    			break;
    		case 'news':
    			$this->wx->news($result['Content'])->reply();
    			break;
    			 
    			 
    			 
    			 
    			 
    	}
    }
    public function wxevent($data){

    	$artid= C('WXARTID');
    	$mapart['id']=array('in',explode(',', $artid));
    	$artarr= M('article')->where($mapart)->order('tj desc')->select();
    	$art=array();
    foreach ($artarr as $key =>$vo){
    		$art[$key]=array('Title'=>$vo['title'],'Description'=>cutstr_html(op_t($vo['description']),50),'PicUrl'=>getImgs($vo['description'],0),'Url'=>(is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.C('WEB_DIR').'/'.ZSU('/artc/'.$vo['id'],'Index/artc',array('id'=>$vo['id'])));
    	
    	
    	
    	}
    	 
    	
    	if ( $data['event'] == 'subscribe' ) {
    		$result = array(
    				'MsgType' => 'text',
    				'Content' => C('WXWELCOME')
    		);
    	}
    	if ( $data['event'] == 'CLICK' ) {
    		if ( $data['key'] == 'newblog' ) {
    	
    	
    	
    	
    				
    			$result = array(
    					'MsgType' => 'news',
    					'Content' => $art
    			);
    		}
    	}
    	
    	switch ($result['MsgType']){
    		 
    		case 'text':
    			$this->wx->text($result['Content'])->reply();
    			 
    			break;
    		case 'news':
    			$this->wx->news($result['Content'])->reply();
    			break;
    	
    	
    	
    	
    	
    	}
    	
    	
    	
    	
    }
    /**
     * 设置值
     * @param [type] $key   [description]
     * @param [type] $value [description]
     * @param [type] $time  [description]
     */
    public function setValue($key, $value, $time = NULL)
    {
    	
    	if ( $time != NULL ) {
    		S($this->rdata['ToUserName'].$this->rdata['FromUserName'].'_'.$key, $value, $time);
    	} else {
    		S($this->rdata['ToUserName'].$this->rdata['FromUserName'].'_'.$key, $value);
    	}
    
    }
    
    /**
     * 获取缓存值
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function getValue($key)
    {
    	
    	if ( S($this->rdata['ToUserName'].$this->rdata['FromUserName'].'_'.$key) ) {
    		return S($this->rdata['ToUserName'].$this->rdata['FromUserName'].'_'.$key);
    	} else {
    		return false;
    	}
    }
}