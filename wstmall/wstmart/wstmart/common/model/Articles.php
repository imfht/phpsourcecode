<?php
namespace wstmart\common\model;
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
 *  文章类
 */
class Articles extends Base{
	protected $pk = 'articleId';

	/**
	* 获取咨询中中心所有文章
	*/
	public function getArticles(){
		$pagesize = input("pagesize");
		$catId = input("catId");
		$rs = $this->alias('a')
			  ->field('a.*')
			  ->join('__ARTICLE_CATS__ ac','a.catId=ac.catId','inner')
			  ->where(['a.catId'=>$catId,
			  	       'a.isShow'=>1,
			  	       'a.dataFlag'=>1,
			  		   'ac.dataFlag'=>1,
			  		   'ac.isShow'=>1,
			  		   'ac.catType'=>0,
			  		   ])
			  ->order('a.catSort asc,a.createTime desc')
			  ->paginate($pagesize)
			  ->toArray();
		return $rs;
	}

	/**
	*  根据id获取资讯文章
	*/
	public function getNewsById($isAddVisitorNum=true){
		$id = (int)input('id');
        if($isAddVisitorNum)WSTArticleVisitorNum($id);// 统计文章访问量
		$rs = $this->alias('a')
					->field('a.articleId,a.articleContent,a.articleDesc,a.articleKey,a.createTime,a.articleTitle,a.likeNum')
					->join('__ARTICLE_CATS__ ac','a.catId=ac.catId','inner')
					->where('ac.catType=0 and a.dataFlag=1 and a.articleId='.$id)
					->find();

		$rs['articleContent'] = str_replace('${DOMAIN}',WSTConf('CONF.resourcePath'),$rs['articleContent']);
		$articleId = cookie("like_articleId");
		$articleId = is_array($articleId)?$articleId:[];
		$rc = !empty($articleId)?in_array($id,$articleId):'';
		if($rc){
         $rs['likeState'] = 1;
		}else{
         $rs['likeState'] = 0;
		}
        return $rs;
	}

	/**
	 * 点赞
	 */
	public function like(){
	    $id = (int)input('id');
		$articleId = cookie("like_articleId");
		$articleId = is_array($articleId)?$articleId:[];
		$rc = !empty($articleId)?in_array($id,$articleId):'';
		if($rc)return WSTReturn("点赞成功", -1);
		//判断记录是否存在
		$rs = $this->where(['isShow'=>1,'dataFlag'=>1,'articleId'=>$id])->setInc('likeNum',1);
		//判断是否点赞成功
		 if(false !== $rs){
		 	array_push($articleId,$id);
			cookie("like_articleId",$articleId,25920000);
            return WSTReturn("点赞成功", 1);
        }else{
             return WSTReturn("点赞失败", -1);
        }
	}
	
    /**
	* 获取资讯中心的子集分类
	*/
	public function getChildInfos(){
		$infos = cache('NEW_INFOS');
		$i = 0;
		if(!$infos){
			$data = Db::name('article_cats')->order('catSort asc')->cache(true)->select();
			foreach($data as $k=>$v){
				if($v['parentId']== 8){
					$infos[$i]['catId'] = $v['catId'];
					$infos[$i]['catName'] = $v['catName'];
					$i++;
				}
			}
            cache('NEW_INFOS',$infos);
		}
		return $infos;
	}

	/**
	 * 获取指定的记录
	 */
	function getById($articleId){
		$rs = $this->where(['articleId'=>$articleId,'dataFlag'=>1,'isShow'=>1])->find();
		$rs['articleContent'] = htmlspecialchars_decode(str_replace('${DOMAIN}',WSTConf('CONF.resourceDomain'),$rs['articleContent']));
		return $rs;
	}
}
