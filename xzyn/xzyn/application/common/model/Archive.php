<?php
namespace app\common\model;

use think\Model;
use app\common\model\Arctype;
use app\common\model\ArchiveReply;
use app\common\model\ZanLog;

class Archive extends Model {
	// 新增自动完成列表
    protected $insert  = ['description', 'writer','status','keywords'];
    protected $update = [];
//	设置json类型字段
	protected $json = ['flag'];

    public function arctype()
    {
        return $this->hasOne('Arctype', 'id', 'typeid')->field('typename, mid, dirs');
    }

    public function arctypeMod()
    {
        return $this->hasOne('ArctypeMod', 'id', 'mid')->field('mod');
    }

    public function User()
    {
        return $this->hasOne('User', 'id', 'writer');
    }

    public function Image()		//关联图片表
    {
        return $this->hasOne('Image', 'fid', 'id')->field('imgurl');
    }

    public function UserInfo()
    {
        return $this->hasOne('UserInfo', 'uid', 'writer');
    }

    /**
     * 文章模型关联表
     */
    public function addonarticle()
    {
        return $this->hasOne('addonarticle', 'aid', 'id');
    }

    /**
     * 视频模型关联表
     */
    public function addonvideo()
    {
        return $this->hasOne('addonvideo', 'aid', 'id');
    }

    /**
     * 相册模型关联表
     */
    public function addonalbum()
    {
        return $this->hasOne('addonalbum', 'aid', 'id');
    }

    protected function getImgurlAttr($value,$data)	//image 图片字段 [获取器]
    {
    	if( !empty($this->Image->imgurl) ){
    		return $this->Image->imgurl;
    	}

    }

    protected function setDescriptionAttr($value)	//简介字段 [修改器]
    {
    	if($value){
    		return $value;
    	}else{
        	return auto_description($value, input('param.content'));
        }
    }

    protected function setKeywordsAttr($value) {	//关键字 字段 [修改器]
    	if($value){
    		return $value;
    	}else{
    		$titles = trimall(input('param.title'));//清除字符串中的空格和换行
			return csubstr($titles, 10, "", 0, false);//中文字符串截取长度
    	}
    }

    protected function setStatusAttr($value)	//状态(是否审核) [修改器]
    {
    	if($value){
            return $value;
        }else{
            return confv('is_addarticle_audit','system');
        }
    }

    protected function setWriterAttr($value)	//作者ID字段 [修改器]
    {
        if($value){
            return $value;
        }else{
            return session('userId');
        }
    }

    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }

    public function getCreateTimeAttr($value) {	// create_time 创建时间字段 [获取器]
        return time_line($value);
    }

    public function getReplyNumAttr($value,$data) {	// reply_num 评论数量 [获取器]
		$ArchiveReply = new ArchiveReply();
		$reply_num = 0;
		$reply_num = $ArchiveReply->where( ['aid'=>$data['id'], 'audit' => 1] )->count();
        return $reply_num;
    }

    public function getZanNumAttr($value,$data) {	// zan_num 赞数量 [获取器]
		$ZanLog = new ZanLog();
		$zan_num = 0;
		$zan_num = $ZanLog->where( ['a_id'=>$data['id']] )->count();
        return $zan_num;
    }

    public function getCollectNumAttr($value,$data) {	// collect_num 文章被收藏的数量 [获取器]
		$Collect = new \app\common\model\Collect;
		$collect_num = 0;
		$collect_num = $Collect->where( ['aid'=>$data['id']] )->count();
        return $collect_num;
    }

    public function getLitpicAttr($value,$data) {	// litpic 缩列图字段 [获取器]
		$litpic = '';
		if(!empty($this->Image->imgurl)){
			foreach ($this->Image->imgurl as $k => $v) {
				if( $v ){
					$litpic = $v;
				}
			}
		}else{
            if( !empty($this->addonarticle->content) ){
                $imgarr = getImgs($this->addonarticle->content);
                if( !empty($imgarr) ){
                    $litpic = $imgarr[0];
                }else{
                    $litpic = request()->domain().'/static/common/img/logo.jpg';
                }
            }else{
                $litpic = request()->domain().'/static/common/img/logo.jpg';
            }
		}
		return $litpic;
    }

    public function getArcurlAttr($value,$data) {	// arcurl 文章url地址 [获取器]
		$arcurl = url('detail/'.$this->arctype->dirs.'/'.$data['id']);
        if( !empty($data['jumplink']) ){
            $arcurl = $data['jumplink'];
        }
		return $arcurl;
    }

    /**
     * @Title: arclist
     * @Description: todo(查询栏目下的文章)
     * @param int $typeid 栏目ID（当前栏目下的所有[无限级]栏目ID）
     * @param int $limit 查询数量
     * @param string $flag 推荐[c] 置顶[a] 头条[h] 滚动[s] 图片[p] 跳转[j]
     * @param string $order 排序
     * @param Array $field 根据字段返回
     * @return array
     * @author 戏中有你
     * @date 2018年2月8日
     * @throws
     */
    public function arclist($typeid='', $limit='', $flag='', $order='id DESC',$field='')
    {
    	$arctype = new Arctype();
        if ( empty($typeid) ){
            $typeidStr = cache('ARCTYPE_ID_ARR');
	        if (!$typeidStr){
	            $typeidStr = $arctype->allChildArctype();
	            cache('ARCTYPE_ID_ARR', $typeidStr);
	        }
        }else{
        	$typeidStr = cache('ARCTYPE_ID_ARR_'.$typeid);
	        if (!$typeidStr){
	            $typeidStr = $arctype->allChildArctype($typeid);
	            cache('ARCTYPE_ID_ARR_'.$typeid, $typeidStr);
	        }
        }
        $where[] = ['typeid','in', $typeidStr];
        $list = $this->where($where)->where(['status'=>1])->limit($limit)->order($order)->select();
		$list = $this->api_return_arcdata($list,$field);
		$lists = [];
        if (!empty($flag)){
        	foreach ($list as $k => $v) {
        		if( !empty($v['flag']) && count($v['flag']) > 0 ){
    				if( in_array($flag, $v['flag']) ){
	        			$lists[$k] = $v;
	        		}
				}
        	}
        }else{
        	$lists = $list;
        }
        return $lists;
    }

    /**
     * @Title: prenext
     * @Description: todo(上一篇、下一篇)
     * @param array $archiveArr 当前文档数组
     * @return string
     * @author 戏中有你
     * @date 2018年1月17日
     * @throws
     */
    public function prenext($archiveArr)
    {
        $leftLabel = "<div class='x-t-no1'>";
        $rightLabel = "</div>";
        $preStr = $leftLabel."<span>上一篇：</span>";
        $nextStr = $leftLabel."<span>下一篇：</span>";
		$where[] = ['id','gt',$archiveArr['id']];
		$where[] = ['typeid','=',$archiveArr['typeid']];

        $pre = $this->where($where)->order('id ASC')->find();   //上
		$where[] = ['id','lt',$archiveArr['id']];
		$where[] = ['typeid','=',$archiveArr['typeid']];

        $next = $this->relation('arctype')->where($where)->order('id DESC')->find();   //下
        if(!empty($pre)){
        	if( empty($pre['flag']) ){
        		$flag_arr = [];
        	}else{
           		$flag_arr = $pre['flag'];
        	}
            if(in_array('j',$flag_arr) && !empty($pre['jumplink']) ){
                $preStr .= "<a href=\"".$pre['jumplink']."\" target=\"_blank\" >".$pre['title']."</a>".$rightLabel;
            }else{
                $preStr .= "<a href=\"".url("detail/".$archiveArr->arctype->dirs."/".$pre['id'])."\">".$pre['title']."</a>".$rightLabel;
            }
        }else{
            $preStr .= "没有了".$rightLabel;
        }
        if(!empty($next)){
        	if( empty($next['flag']) ){
        		$flag_arr = [];
        	}else{
           		$flag_arr = $next['flag'];
        	}
            if(in_array('j',$flag_arr) && !empty($next['jumplink']) ){
                $nextStr .= "<a href=\"".$next['jumplink']."\" target=\"_blank\" >".$next['title']."</a>".$rightLabel;
            }else{
                $nextStr .= "<a href=\"".url("detail/".$archiveArr->arctype->dirs."/".$next['id'])."\">".$next['title']."</a>".$rightLabel;
            }
        }else{
            $nextStr .= "没有了".$rightLabel;
        }
        return $preStr.$nextStr;
    }

    /**
     * @Title: click
     * @Description: todo(文档点击数+1)
     * @param array $archiveArr 当前文档数组
     * @author 戏中有你
     * @date 2018年1月17日
     * @throws
     */
    public function click($archiveArr)
    {
        return $this->where('id', $archiveArr->id)->setInc('click');
    }

    /**
     * API 获取文章列表
     * @access public
     * @param  Array  $where  判断条件
     * @param  String  $order  排序
     * @param  Integer  $page  页码
     * @param  Integer  $number  一页输出的数量
     * @param Array $field 根据字段返回
     * @author 戏中有你
     * @date 2018年2月12日
     * @return Array
     */
	public function api_arclist($where='',$order='id DESC', $page='', $number='',$field=''){
		if( empty($page) && empty($number) ){
			$arc_data = $this->where($where)->where(['status' => '1'])->order($order)->select();	// 文章列表数据
		}else{
			$arc_data = $this->where($where)->where(['status' => '1'])->order($order)->page($page,$number)->select();	// 文章列表数据
		}
		$arc_data = $this->api_return_arcdata($arc_data,$field);
		return $arc_data;
	}
    /**
     * API 添加文章的额外字段
     * @access public
     * @param  Array  $data  数据集
     * @param Array $field 根据字段返回
     * @author 戏中有你
     * @date 2018年2月12日
     * @return Array
     */
	public function api_return_arcdata($data='',$field=''){
		foreach ($data as $k => $v){
			$list[$k]['content'] = $v['addonarticle']['content'];
			$list[$k]['arcurl'] = $v['arcurl'];
        	$data[$k]['litpic'] = $v['litpic'];
        	$data[$k]['imgurl'] = $v['imgurl'];
			$data[$k]['typename'] = $v['arctype']['typename'];
			$data[$k]['username'] = $v['user']['name']?$v['user']['name']:$v['user']['username'];
			$data[$k]['avatar'] = $v['UserInfo']['avatar_turn'];	//头像
			$data[$k]['reply_num'] = $v['reply_num'];
			$data[$k]['zan_num'] = $v['zan_num'];
			$data[$k]['collect_num'] = $v['collect_num'];	// 文章被收藏的数量
        }
		if( !empty($field) ){
			$data = $data->visible($field)->toArray();	// 根据返回字段来返回
		}
		return $data;
	}


}