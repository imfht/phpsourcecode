<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\controller\ControllerBase;
use app\common\logic\File as LogicFile;
use Qiniu\json_decode;
use app\common\logic\Common as LogicCommon;
/**
 * 文件控制器
 */
class File extends ControllerBase
{
    
    // 文件逻辑
    private static $fileLogic = null;
    private static $commonLogic = null;
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class);
       self::$fileLogic = get_sington_object('fileLogic', LogicFile::class);
    }
    
    /**
     * 图片上传
     */
    public function pictureUpload()
    {
        
        $result = self::$fileLogic->pictureUpload();
        
        return json($result);
    }
    /**
     * 文件上传
     */
    public function fileUpload()
    {
        
        $result = self::$fileLogic->fileUpload();
        
        return json($result);
    }
    public function getFileInfo(){
    	
    	$result = self::$fileLogic->getFileInfo(['id' => $this->param['id']]);
    	
    	return json($result);
    	
    }
    
    
    public function downloadFile()
    {
    	$fileid=decrypt($this->param['id']);
    	
    	$result = self::$fileLogic->getFileInfo(['id' =>$fileid ]);
    	$id= decrypt($this->param['tid']);
    	
    	
    	$uid=is_login();
    
    	
    	$userinfo = self::$commonLogic->getDataInfo('user',['id'=>$uid]);
    	
    	$info=model('doccon')->where(['id'=>$id])->find();
    	
    	if($info['fileid']!=$fileid){
    		
    		$this->jump([RESULT_ERROR, '非法参数',url('doc/doccon',array('id'=>$id))]);
    	}
    	
    	if($userinfo['point']<$info['score']&&$info['uid']!=$userinfo['id']){
    		$this->jump([RESULT_ERROR, '积分不足',url('doc/doccon',array('id'=>$id))]);
    	}else{
    		if($info['uid']!=$userinfo['id']){
    			//扣除积分
    			
    			if(model('point_note')->where(['uid'=>$userinfo['id'],'itemid'=>$id,'controller'=>'docdown','scoretype'=>'point'])->count()==0){
    				
    				point_change($userinfo['id'],'point',$info['score'],2,'docdown',$id,$info['uid']);
    				
    				point_change($userinfo['id'],'expoint1',$info['score'],2,'docdown',$id,$info['uid']);
    				
    				
    				point_change($info['uid'],'point',$info['score'],1,'docdown',$id,$userinfo['id']);
    				
    			}
    		
    			
    			
    			
    			
    		}
    		self::$commonLogic->setDataValue('doccon',['id'=>$id], 'down', array('exp','down+1'));
    		
    		
    		doccz($userinfo['id'],$id,1);//下载记录
    		
    		
    		$url=array(PATH_FILE.$result['savepath']);
    		 
    		self::$fileLogic->download($url,$result['name'],1);
    		
    		
    	}

    	
    	
    	
    	
    
    	
    }
}