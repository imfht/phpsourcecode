<?php

namespace plugins\weixin\model;
use think\Model;


//微信关键字回复
class WeixinAutoreply extends Model
{
	
    // 设置当前模型对应的完整数据表名称weixinword

    protected $table = '__WEIXINWORD__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
	//主键不是ID,要单独指定
	protected $pk = 'id';
	
	//列表页使用
	public function format_list_show($type,$content){
		if($type){
			$answers = unserialize($content);
			$lists = '';
			foreach($answers AS $rss){
				$rss[title] = $rss[title] ? $rss[title] : ($rss[desc] ? $rss[desc] : $rss[link]) ;
				$lists .= '<div>'.get_word($rss[title],80).'</div>';
			}
			$content = $lists;
		}else{		
			$content = get_word(preg_replace('/<([^<]*)>/is','',$content),80);
		}
		return $content;
	}
	
	//获取关键字列表
	public static function get_keyword(){
	    $datalist = getArray(self::where([])->order('list','desc')->select());	     
	     foreach($datalist AS $rs){	    
	        $rs['ask'] = str_replace('　',' ',$rs['ask']);
	        $detail = explode(' ',$rs['ask']);
	        foreach($detail AS $key=>$value){
	            if($value){
	                $array[$value]=$rs['id'];
	            }
	        }
	    }
	    return $array;
	}
	
	//提交表单使用
	public function format_answer($type,$answers,$answer){
		$shoptypes1 = '';
		if($type==1){
			$i=0;
			foreach($answers AS $key=>$rs){
				if($rs['link']){
					$listdb[$i]['title']=filtrate($rs['title']);
					$listdb[$i]['desc']=filtrate($rs['desc']);
					$listdb[$i]['pic']=filtrate($rs['pic']);
					$listdb[$i]['link']=filtrate($rs['link']);
					$i++;
				}
			}
			if($i>0){
				$shoptypes1 = serialize($listdb);
			}
			
		}else{
			$shoptypes1 = $answer;
		}	
		return $shoptypes1;
	}
	
}