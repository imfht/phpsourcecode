<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\Articles as validate;
use think\Db;
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
 * 文章业务处理
 */
class Articles extends Base{
	protected $pk = 'articleId';
	/**
	 * 分页
	 */
	public function pageQuery(){
		$key = input('key');
		$catId = (int)input('catId');
		$sort = input('sort');
		$catIds = [];
		if($catId>0){
		    $catIds = model('ArticleCats')->getChild($catId);
        }
        $where[] = ['a.isHide','=',0];
		$where[] = ['a.dataFlag','=',1];
		if(count($catIds)>0)$where[] = ['a.catId','in',$catIds];
		if($key!='')$where[] = ['a.articleTitle','like','%'.$key.'%'];
		$order = 'a.articleId desc';
		if($sort){
			$sort =  str_replace('.',' ',$sort);
			$order = $sort;
		}
		$page = Db::name('articles')->alias('a')
		->join('__ARTICLE_CATS__ ac','a.catId= ac.catId','left')
		->join('__STAFFS__ s','a.staffId= s.staffId','left')
		->where($where)
		->field('a.articleId,a.catId,a.articleTitle,a.isShow,a.articleContent,a.articleKey,a.createTime,a.catSort,ac.catName,s.staffName')
		->order($order)
		->paginate(input('post.limit/d'))->toArray();
		if(count($page['data'])>0){
			foreach ($page['data'] as $key => $v){
				$page['data'][$key]['articleContent'] = strip_tags(htmlspecialchars_decode($v['articleContent']));
			}
		}
		return $page;
	}

	/**
	 * 显示是否显示/隐藏
	 */
	public function editiIsShow(){
		$id = input('post.id/d');
		$isShow = (input('post.isShow/d')==1)?1:0;
		$result = $this->where(['articleId'=>$id])->update(['isShow' => $isShow]);
		if(false !== $result){
			WSTClearAllCache();
			return WSTReturn("操作成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	
	/**
	 * 获取指定对象
	 */
	public function getById($id){
		$single = $this->where(['articleId'=>$id,'dataFlag'=>1])->find();
		$singlec = Db::name('article_cats')->where(['catId'=>$single['catId'],'dataFlag'=>1])->field('catName')->find();
		$single['catName']=$singlec['catName'];
		$single['articleContent'] = htmlspecialchars_decode($single['articleContent']);
		$single['articleContent'] = str_replace('${DOMAIN}',WSTConf('CONF.resourcePath'),$single['articleContent']);
		
		return $single;
	}
	
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		WSTUnset($data,'articleId,dataFlag');
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		$data['createTime'] = date('Y-m-d H:i:s');
		Db::startTrans();
        try{
            //对文章描述进行处理,去掉换行
            $data['articleDesc'] = str_replace(PHP_EOL,'',($data['articleDesc']));
        	//对图片域名进行处理
        	$data['articleContent'] = WSTRichEditorFilter($_POST['articleContent']);
        	$data['articleContent'] = str_replace(WSTConf('CONF.resourcePath').'/upload/','${DOMAIN}/upload/',$data['articleContent']);
        	$validate = new validate();
		    if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
        	$result = $this->allowField(true)->save($data);
        	if(false !== $result){
        		WSTClearAllCache();
        		// 预览图
				WSTUseResource(1, $this->articleId, $data['coverImg']);
				//文章描述图片
				WSTEditorImageRocord(1, $this->articleId, '',$data['articleContent']);
	        	Db::commit();
				return WSTReturn("新增成功", 1);
			}else{
                return WSTReturn($this->getError(),-1);
			}
        }catch(\Exception $e){
			Db::rollback();
        }	
        return WSTReturn("新增失败");
    }
	
	/**
	 * 编辑
	 */
	public function edit(){
		$articleId = input('post.id/d');
		$data = input('post.');
		WSTUnset($data,'articleId,dataFlag,createTime');
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		Db::startTrans();
        try{
            //对文章描述进行处理,去掉换行
            $data['articleDesc'] = str_replace(PHP_EOL,'',($data['articleDesc']));
        	//对图片域名进行处理
        	$data['articleContent'] = WSTRichEditorFilter($_POST['articleContent']);
        	$data['articleContent'] = str_replace(WSTConf('CONF.resourcePath').'/upload/','${DOMAIN}/upload/',$data['articleContent']);
        	// 预览图
			WSTUseResource(0, $articleId, $data['coverImg'],'articles','coverImg');
        	//文章描述图片
			$oldArticleContent = $this->where('articleId',$articleId)->value('articleContent');// 旧描述
			WSTEditorImageRocord(1, $articleId, $oldArticleContent,$data['articleContent']);
			$validate = new validate();
		    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
        	$result = $this->allowField(true)->save($data,['articleId'=>$articleId]);
        	if(false !== $result){
        		WSTClearAllCache();
				Db::commit();
				return WSTReturn("修改成功", 1);
			}
		}catch(\Exception $e){
			Db::rollback();
        }
        return WSTReturn("修改失败");
	}
	
	/**
	 * 删除
	 */
	public function del(){
		$id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
			$result = $this->where(['articleId'=>$id])->update($data);
			if(false !== $result){
				WSTClearAllCache();
				// 预览图
				WSTUnuseResource('articles','coverImg',$id);
	        	//文章描述图片
				$oldArticleContent = $this->where('articleId',$id)->value('articleContent');// 旧描述
				WSTEditorImageRocord(1, $id, $oldArticleContent,'');
				Db::commit();
				return WSTReturn("删除成功", 1);
			}
		}catch (\Exception $e) {
            Db::rollback();
			return WSTReturn('删除失败',-1);
        }
	}
	/**
	 * 批量删除
	 */
	public function delByBatch(){
		$ids = explode(',',WSTFormatIn(',',input('post.ids')));
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
			$result = $this->where([['articleId','in',$ids]])->update($data);
			if(false !== $result){
				$oldArticleContent = $this->field('articleId,articleContent')->where([['articleId','in',$ids]])->select();// 旧描述
				foreach($oldArticleContent as $k=>$v){
					// 预览图
					WSTUnuseResource('articles','coverImg',$v['articleId']);
					//文章描述图片
					WSTEditorImageRocord(1, $v['articleId'], $v['articleContent'],'');
				}
				WSTClearAllCache();
				Db::commit();
				return WSTReturn("删除成功", 1);
			}
		}catch (\Exception $e) {
            Db::rollback();
			return WSTReturn('删除失败',-1);
        }
	}
	/**
	 * 修改排序
	 */
	public function changeSort(){
		$id = (int)input('id');
		$catSort = (int)input('catSort');
		$result = $this->setField(['articleId'=>$id,'catSort'=>$catSort]);
		if(false !== $result){
			WSTClearAllCache();
			return WSTReturn("操作成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/*******************************************************************************
     *                             商家公告、商家帮助、入驻协议等文章的操作
     *******************************************************************************/
	/**
	 * 列表
	 */
	public function pageQueryByOther($catId,$filter = []){
		$key = input('key');
		$sort = input('sort');
		$where = [];
		if(!empty($filter))$where[] = ['a.articleId','not in',$filter];
		$where[] = ['a.dataFlag','=',1];
		$where[] = ['a.catId','=',$catId];
		if($key!='')$where[] = ['a.articleTitle','like','%'.$key.'%'];
		$order = 'a.articleId desc';
		if($sort){
			$sort =  str_replace('.',' ',$sort);
			$order = $sort;
		}
		$page = Db::name('articles')->alias('a')
		->join('__ARTICLE_CATS__ ac','a.catId= ac.catId','left')
		->join('__STAFFS__ s','a.staffId= s.staffId','left')
		->where($where)
		->field('a.articleId,a.catId,a.articleTitle,a.isShow,a.articleContent,a.articleKey,a.createTime,a.catSort,ac.catName,s.staffName')
		->order($order)
		->paginate(input('post.limit/d'))->toArray();
		if(count($page['data'])>0){
			foreach ($page['data'] as $key => $v){
				$page['data'][$key]['articleContent'] = strip_tags(htmlspecialchars_decode($v['articleContent']));
			}
		}
		return $page;
	}
	/**
	 * 新增
	 */
	public function addOther($catId,$scene){
		$data = input('post.');
		WSTUnset($data,'articleId,dataFlag');
		$data['isHide'] = 1;
		$data["catId"] = $catId;
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		$data['createTime'] = date('Y-m-d H:i:s');
		Db::startTrans();
        try{
        	//对图片域名进行处理
        	$data['articleContent'] = WSTRichEditorFilter($_POST['articleContent']);
        	$data['articleContent'] = str_replace(WSTConf('CONF.resourcePath').'/upload/','${DOMAIN}/upload/',$data['articleContent']);
        	$validate = new validate();
		    if(!$validate->scene($scene)->check($data))return WSTReturn($validate->getError());
        	$result = $this->allowField(true)->save($data);
        	if(false !== $result){
        		WSTClearAllCache();
        		// 预览图
				if(isset($data['coverImg']))WSTUseResource(1, $this->articleId, $data['coverImg']);
				//文章描述图片
				WSTEditorImageRocord(1, $this->articleId, '',$data['articleContent']);
	        	Db::commit();
				return WSTReturn("新增成功", 1);
			}else{
                return WSTReturn($this->getError(),-1);
			}
        }catch(\Exception $e){
			Db::rollback();
        }	
        return WSTReturn("新增失败");
    }
    /**
	 * 编辑
	 */
	public function editOther($catId,$scene){
		$articleId = input('post.id/d');
		$data = input('post.');
		WSTUnset($data,'articleId,dataFlag,createTime');
		$data['isHide'] = 1;
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		Db::startTrans();
        try{
        	//对图片域名进行处理
        	$data['articleContent'] = WSTRichEditorFilter($_POST['articleContent']);
        	$data['articleContent'] = str_replace(WSTConf('CONF.resourcePath').'/upload/','${DOMAIN}/upload/',$data['articleContent']);
        	
        	// 预览图
			if(isset($data['coverImg']))WSTUseResource(0, $articleId, $data['coverImg'],'articles','coverImg');
        	//文章描述图片
			$oldArticleContent = $this->where('articleId',$articleId)->value('articleContent');// 旧描述
			WSTEditorImageRocord(1, $articleId, $oldArticleContent,$data['articleContent']);
			$validate = new validate();
		    if(!$validate->scene($scene)->check($data))return WSTReturn($validate->getError());
        	$result = $this->allowField(true)->save($data,['articleId'=>$articleId,'catId'=>$catId]);
        	if(false !== $result){
        		WSTClearAllCache();
				Db::commit();
				return WSTReturn("修改成功", 1);
			}
		}catch(\Exception $e){
			Db::rollback();
        }
        return WSTReturn("修改失败");
	}
	/**
	 * 删除
	 */
	public function delOther($catId){
		$id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
			$result = $this->where(['articleId'=>$id])->update($data);
			if(false !== $result){
				WSTClearAllCache();
				// 预览图
				WSTUnuseResource('articles','coverImg',$id);
	        	//文章描述图片
				$oldArticleContent = $this->where(['articleId'=>$id,'catId'=>$catId])->value('articleContent');// 旧描述
				WSTEditorImageRocord(1, $id, $oldArticleContent,'');
				Db::commit();
				return WSTReturn("删除成功", 1);
			}
		}catch (\Exception $e) {
            Db::rollback();
			return WSTReturn('删除失败',-1);
        }
	}
	/**
	 * 批量删除
	 */
	public function delByBatchOther($catId){
		$ids = explode(',',WSTFormatIn(',',input('post.ids')));
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
			$result = $this->where([['articleId','in',$ids]])->update($data);
			if(false !== $result){
				$oldArticleContent = $this->field('articleId,articleContent')->where([['articleId','in',$ids],['catId','=',$catId]])->select();// 旧描述
				foreach($oldArticleContent as $k=>$v){
					// 预览图
					WSTUnuseResource('articles','coverImg',$v['articleId']);
					//文章描述图片
					WSTEditorImageRocord(1, $v['articleId'], $v['articleContent'],'');
				}
				WSTClearAllCache();
				Db::commit();
				return WSTReturn("删除成功", 1);
			}
		}catch (\Exception $e) {
            Db::rollback();
			return WSTReturn('删除失败',-1);
        }
	}

	/**
	 * 编辑入驻协议
	 */
	public function editAgreement($articleId=0,$catId=0){
		$articleId = ($articleId>0)?$articleId:109;
		$catId = ($catId>0)?$catId:53;
		$data = input('post.');
		WSTAllow($data,'articleContent');
		$data['isHide'] = 1;
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		Db::startTrans();
        try{
        	//对图片域名进行处理
        	$data['articleContent'] = WSTRichEditorFilter($_POST['articleContent']);
        	$data['articleContent'] = str_replace(WSTConf('CONF.resourcePath').'/upload/','${DOMAIN}/upload/',$data['articleContent']);
        	
        	//文章描述图片
			$oldArticleContent = $this->where('articleId',$articleId)->value('articleContent');// 旧描述
			WSTEditorImageRocord(1, $articleId, $oldArticleContent,$data['articleContent']);
        	$result = $this->allowField(true)->save($data,['articleId'=>$articleId,'catId'=>$catId]);
        	if(false !== $result){
        		WSTClearAllCache();
				Db::commit();
				return WSTReturn("保存成功", 1);
			}
		}catch(\Exception $e){
			Db::rollback();
        }
        return WSTReturn("保存失败");
	}
	/**
	 * 编辑用户注册协议
	 */
	public function editUserAgreement(){
		$articleId = (int)input('articleId');
		$data = input('post.');
		WSTAllow($data,'articleContent');
		$data['isHide'] = 1;
		$data["staffId"] = (int)session('WST_STAFF.staffId');
		Db::startTrans();
        try{
        	//对图片域名进行处理
        	$data['articleContent'] = WSTRichEditorFilter($_POST['articleContent']);
        	$data['articleContent'] = str_replace(WSTConf('CONF.resourcePath').'/upload/','${DOMAIN}/upload/',$data['articleContent']);
        	
        	//文章描述图片
			$oldArticleContent = $this->where('articleId',$articleId)->value('articleContent');// 旧描述
			WSTEditorImageRocord(1, $articleId, $oldArticleContent,$data['articleContent']);
        	$result = $this->allowField(true)->save($data,['articleId'=>$articleId]);
        	if(false !== $result){
				Db::commit();
				return WSTReturn("保存成功", 1);
			}
		}catch(\Exception $e){
			Db::rollback();
        }
        return WSTReturn("保存失败");
	}
}