<?php
namespace wstmart\admin\model;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 静态资源空间业务处理
 */
use think\Db;
use Env;
class Resources extends Base{
	/**
	 * 获取静态资源空间概况
	 */
	public function summary(){
		$rs = Db::name('resources')->where(['dataFlag'=>1])->field('fromTable,isUse,sum(resSize) resSize')->group('fromTable,isUse')
		        ->order('fromTable asc')->select();
		//获取目录名称
		$rs2 = Db::name('datas')->where(['catId'=>3])->field('dataName,dataVal')->select();
		$imagesMap = [];
		foreach ($rs2 as $key =>$v){
			$imagesMap[$v['dataVal']] = $v['dataName'];
		}
	    $images = [];
		foreach ($rs as $key =>$v){
			if(!isset($images[$v['fromTable']]))$images[$v['fromTable']] = ['directory'=>'','data'=>['0'=>0,'1'=>0]];
			if(isset($imagesMap[$v['fromTable']]))$images[$v['fromTable']]['directory'] = $imagesMap[$v['fromTable']];
		    $images[$v['fromTable']]['data'][$v['isUse']] = round($v['resSize']/1024/1024,2);
		}
		$maxSize = 0;
		foreach ($images as $key =>$v){
			$size = (float)$v['data']['0']+(float)$v['data']['1'];
			if($maxSize<$size)$maxSize = $size;
		}
		$images['_WSTSummary_'] = $maxSize;
		return $images;
	}
	/*
	 * 获取要处理的图片信息
	 */
    public function getPicInfo(){
        $fromTable = input('key');
        $rs = Db::name('resources')->where(['dataFlag'=>1,'isUse'=>1,'fromTable'=>$fromTable])->field('resId')
            ->select();
        return WSTReturn("共".count($rs)."张图片需处理", 1,$rs);
    }
    /*
     * 图片处理
     */
    public function picHandle(){
        $type = (int)input('type');
        if(!in_array($type,[1,2]))return WSTReturn("无效的图片处理方式", -1);
        $id = (int)input('id');
        $filePath = Db::name('resources')->where(['dataFlag'=>1,'resId'=>$id])->value('resPath');
        //原图路径
        $imageSrc = $filePath;
        $name = substr(strrchr($imageSrc,"/"),1);
        $extension = substr(strrchr($name,"."),1);
        if(in_array($extension,['jpg','jpeg','gif','png','bmp'])){
            //打开原图
            $image = \image\Image::open($imageSrc);
            if($type==1){
                // 根据电脑端大图重新生成移动端图片

                //手机版原图宽高
                $mWidth = min($image->width(),(int)input('mWidth',700));
                $mHeight = min($image->height(),((int)input('mHeight',700)>$image->height())?$image->height():$image->height()/2);
                /****************************** 生成移动大图 *********************************/

                //是否需要生成移动版的大图
                $suffix = WSTConf("CONF.wstMobileImgSuffix");
                if(!empty($suffix)){
                    $mSrc = str_replace('.',"$suffix.",$imageSrc);
                    $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                }
                $rdata = ['status'=>1,'savePath'=>$filePath,'name'=>$name];
                hook('afterUploadPic',['data'=>&$rdata,'isLocation'=>(int)input('isLocation')]);
            }else{
                // 重新生成所有图片
                //缩略图路径 手机版原图路径 手机版缩略图路径
                $thumbSrc = $mSrc = $mThumb = null;
                //手机版原图宽高
                $mWidth = min($image->width(),(int)input('mWidth',700));
                $mHeight = min($image->height(),((int)input('mHeight',700)>$image->height())?$image->height():$image->height()/2);
                //手机版缩略图宽高
                $mTWidth = min($image->width(),(int)input('mTWidth',250));
                $mTHeight = min($image->height(),(int)input('mTHeight',250));
                /****************************** 生成缩略图 *********************************/

                // 检测是否需要翻转图片
                $image = checkImageOrientation($image, $imageSrc);

                //缩略图路径
                $thumbSrc = str_replace('.', '_thumb.', $imageSrc);
                $image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
                //是否需要生成移动版的缩略图
                $suffix = WSTConf("CONF.wstMobileImgSuffix");
                if(!empty($suffix)){
                    $image = \image\Image::open($imageSrc);
                    $mSrc = str_replace('.',"$suffix.",$imageSrc);
                    $mThumb = str_replace('.', '_thumb.',$mSrc);
                    $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                    $image->thumb($mTWidth, $mTHeight, 2)->save($mThumb,$image->type(),90);
                }

                /***************************** 添加水印 ***********************************/

                if((int)WSTConf('CONF.watermarkPosition')!==0){
                    //取出水印配置
                    $wmWord = WSTConf('CONF.watermarkWord');//文字
                    $wmFile = trim(WSTConf('CONF.watermarkFile'),'/');//水印文件
                    //判断水印文件是否存在
                    if(!file_exists(WSTRootPath()."/".$wmFile))$wmFile = '';
                    $wmPosition = (int)WSTConf('CONF.watermarkPosition');//水印位置
                    $wmSize = ((int)WSTConf('CONF.watermarkSize')!=0)?WSTConf('CONF.watermarkSize'):'20';//大小
                    $wmColor = (WSTConf('CONF.watermarkColor')!='')?WSTConf('CONF.watermarkColor'):'#000000';//颜色必须是16进制的
                    $wmOpacity = ((int)WSTConf('CONF.watermarkOpacity')!=0)?WSTConf('CONF.watermarkOpacity'):'100';//水印透明度
                    //是否有自定义字体文件
                    $customTtf = Env::get('root_path').WSTConf('CONF.watermarkTtf');
                    $ttf = is_file($customTtf)?$customTtf:Env::get('extend_path').'verify/verify/ttfs/3.ttf';
                    $image = \image\Image::open($imageSrc);
                    if(!empty($wmWord)){//当设置了文字水印 就一定会执行文字水印,不管是否设置了文件水印
                        // 文字偏移量
                        $offset = WSTConf('CONF.watermarkOffset');
                        if($offset!=''){
                            $offset = explode(',',str_replace('，', ',',$offset));
                            $offset = array_slice($offset,0,2);
                            $offset = array_map(function($val){return (int)$val;},$offset);
                            if(count($offset)<2)array_push($offset, 0);
                        }
                        //执行文字水印
                        $image->text($wmWord, $ttf, $wmSize, $wmColor, $wmPosition,$offset)->save($imageSrc);
                        if($thumbSrc!==null){
                            $image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
                        }
                        //如果有生成手机版原图
                        if(!empty($mSrc)){
                            $image = \image\Image::open($imageSrc);
                            $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                            $image->thumb($mTWidth, $mTHeight, 2)->save($mThumb,$image->type(),90);
                        }
                    }elseif(!empty($wmFile)){//设置了文件水印,并且没有设置文字水印
                        //执行图片水印
                        $image->water($wmFile, $wmPosition, $wmOpacity)->save($imageSrc);
                        if($thumbSrc!==null){
                            $image->thumb((int)input('width',min(300,$image->width())), (int)input('height',min(300,$image->height())),2)->save($thumbSrc,$image->type(),90);
                        }
                        //如果有生成手机版原图
                        if($mSrc!==null){
                            $image = \image\Image::open($imageSrc);
                            $image->thumb($mWidth, $mHeight)->save($mSrc,$image->type(),90);
                            $image->thumb($mTWidth, $mTHeight,2)->save($mThumb,$image->type(),90);
                        }
                    }
                }
                //判断是否有生成缩略图
                $thumbSrc = ($thumbSrc!=null)?str_replace('.','_thumb.', $name):'';
                $rdata = ['status'=>1,'savePath'=>$filePath,'name'=>$name,'thumb'=>$thumbSrc];
                hook('afterUploadPic',['data'=>&$rdata,'isLocation'=>(int)input('isLocation')]);
            }
        }
        return WSTReturn("", 1);
    }
	/**
	 * 获取记录
	 */
	public function pageQuery(){
		$key = input('keyword');
		$isUse = (int)input('isUse');
		$resType = (int)input('resType');
		$where = ['fromTable'=>$key,'a.dataFlag'=>1];
		if($isUse !=-1)$where['isUse'] = $isUse;
		if($resType !=-1)$where['resType'] = $resType;
		$page = $this->alias('a')->join('__USERS__ u','a.ownId=u.userId and fromType=0','left')
		            ->join('__SHOPS__ s','s.userId=u.userId','left')
		            ->join('__STAFFS__ sf','sf.staffId=a.ownId','left')
		            ->where($where)->field('a.resId,u.loginName,u.userType,fromType,sf.loginName loginName2,s.shopName,resPath,resSize,resType,isUse,a.createTime')
		            ->order('a.resId desc')->paginate(input('post.limit/d'))->toArray();
		foreach ($page['data'] as $key => $v){
			if($v['fromType']==1){
				$page['data'][$key]['loginName'] = $v['loginName2'];
			}
			$page['data'][$key]['resPath'] = WSTImg($v['resPath'],1);
			$page['data'][$key]['resSize'] = round($v['resSize']/1024/1024,2);
			unset($page['data'][$key]['loginName2']);
		}
		return $page;
	}
    /**
     * 删除图片
     */
    public function del(){
        $id = input('id');
        if(!is_array($id)){
            $id=(int)$id;
        }
        $image = $this->where('resId',"in",$id)->select();
        $rs = $this->where('resId',"in",$id)->update(['dataFlag'=>-1]);

        if(false !== $rs){
            foreach ($image as $k=>$v){
                $data=$this->delImageFile($v['resPath']);
            }
            return WSTReturn("删除成功", 1);
        }
        return WSTReturn("删除失败");
    }
    /**
     * 删除图片文件
     */
    public function delImageFile($image_path){
        $m = WSTConf('CONF.wstMobileImgSuffix');
        $timgPath =  str_replace('.','_thumb.',$image_path);
        $mimgPath =  str_replace('.',$m.'.',$image_path);
        $mtimgPath = str_replace('.',$m.'_thumb.',$image_path);
        if(WSTConf('CONF.ossService')==''){
            if(file_exists(WSTRootPath()."/".$image_path))unlink(WSTRootPath()."/".$image_path);
            if(file_exists(WSTRootPath()."/".$timgPath))unlink(WSTRootPath()."/".$timgPath);
            if(file_exists(WSTRootPath()."/".$mimgPath))unlink(WSTRootPath()."/".$mimgPath);
            if(file_exists(WSTRootPath()."/".$mtimgPath))unlink(WSTRootPath()."/".$mtimgPath);
        }else{
            hook('delPic',['images'=>[$image_path,$timgPath,$mimgPath,$mtimgPath]]);
        }

    }
}
