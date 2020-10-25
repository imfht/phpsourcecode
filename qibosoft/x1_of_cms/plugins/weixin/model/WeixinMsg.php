<?php

namespace plugins\weixin\model;
use think\Model;


//微信公众号客户留言信息记录
class WeixinMsg extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__WEIXINMSG__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	//protected $pk = 'id';

	public function format_list_data($type,$content,$url){
		if($type==1){
			$content = "<a href=\"https://api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$url\"><em class='pic'>图片</em></a>";
		}elseif($type==2){
			$content = "<a href=\"https://api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$url\"><em class='sound'>声音</em></a>";
		}elseif($type==3){
			$content = "<a href=\"https://api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$url\"><em class='video'>视频</em></a>";
		}else{
			$content = get_word($content,200);
		}
		return $content;
	}
	
}