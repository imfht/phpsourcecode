<?php
namespace app\reply\validate;

use think\Validate;
use app\reply\model\WxReply;

/**
 * Index 类
 * 
 * @author WangWei
 * @version 1.0.0.0
 */
class Index extends Validate {
	// 规则
	protected $rule = [ 
			"keyword| 关键词" => "require|myUnique",
			'text| 文本 ' => 'requireif:msg_type,text',
			'img| 图片 ' => 'requireif:msg_type,img',
			'voice| 音频 ' => 'requireif:msg_type,voice',
			'video| 视频 ' => 'requireif:msg_type,video',
			'news' => 'checkNews' 
	];
	// 自定义错误提示
	protected $message = [ 
			'keyword.myUnique' => '该关键词已存在', 
			'text.requireif' => '文本不能为空',
			'img' =>'图片未选择',
			'voice'=>'音频未选择',
			'video'=>'视频未选择',
			'news' => '图文 不能为空',
	];
	/**
	 * 构造函数
	 * 初始化设置：开启批量验证
	 */
	public function __construct() {
		// 开启批量验证
		$this->batch ();
	}
	/**
	 * 验证文本
	 * @param String $value 当前值
	 * @param String $rule 规则
	 * @param Array $data 全部数据
	 * @return boolean 是否通过验证
	 */
	protected function checkText($value, $rule, $data){
		if("text"==$data["msg_type"]){
			return false;
			if($data["is_ok"]){
				$value = array_filter ( $value );
				if (empty ( $value ))
					return false;
				else
					return true;
			}
		}
		return true;
	}
	/**
	 * 验证某个字段等于某个值的时候必须
	 * <pre>
	 * 	重写规则
	 * </pre>
	 * @access protected
	 * @param mixed     $value  字段值
	 * @param mixed     $rule  验证规则
	 * @param array     $data  数据
	 * @return bool
	 */
	protected function requireIf($value, $rule, $data) {
		list($field, $val) = explode(',', $rule);
		if ($this->getDataValue($data, $field) == $val) {
			if("text"==$data["msg_type"]){
				if(1==$data["is_ok"]){
					return !empty($value);
				}
				return true;
			}else{
				return !empty($value);
			}
		} else {
			return true;
		}
	}
	/**
	 * checkNews
	 * 
	 * @param mixed $value 前台录入的值
	 * @param mixed $rule 规则
	 * @param array $data 前台录入的所有数据
	 * @return boolean
	 */
	protected function checkNews($value, $rule, $data) {
		if ($data ['msg_type'] == 'news') {
			$value = array_filter ( $value );
			if (empty ( $value ))
				return false;
			else
				return true;
		} else {
			return true;
		}
	}
	/**
	 * myUnique
	 * 自定义判断唯一性方法
	 * @param mixed $value 输入的数据
	 * @param mixed $rule 规则
	 * @param mixed $data 输入的完整数据
	 * @return boolean 是否通过验证
	 */
	protected function myUnique($value, $rule, $data) {
		$WxReply = new WxReply ();
		if(isset($data['id'])){ //更新时不必验证唯一性
			return true;
		}
		$where ['keyword'] = $value;
		$where ['aid'] = $data ['aid'];
		$res = $WxReply->where ( $where )->find ();
		return empty ( $res ) ? true : false;
	}
	//定义场景
	protected $scene = [ 
			'NotKeyword' => [ 
					'text',
					'img',
					'voice',
					'video',
					'news' 
			] 
	];
}