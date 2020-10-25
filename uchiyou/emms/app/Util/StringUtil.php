<?php
namespace App\Util;

class StringUtil{
	/*
	 * 需要和前端的 js 字符串拼接规则保持一致
	 * 返回一个数组
	 */
	public const DECOLLATOR = '#';
	public static function applyRecordApproversToArray($approvers){
		return explode(StringUtil::DECOLLATOR, $approvers);
	}
	public static function requestAddressToRecord($province,$city,$district,$detailAddress){
		return $province.'-'.$city.'-'.$district.'-'.$detailAddress;
	}
	public static function recordToRequestAddress($address){
		return explode('-',$address);
	}
	
}