<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Common\Controller;

class BaseController extends \Think\Controller {

	protected function _initialize(){
		/* 读取数据库中的配置 */
		$config =   S('DB_CONFIG_DATA');
		if(!$config){
			$config =   api('Config/lists');
			S('DB_CONFIG_DATA',$config);
		}
		$this->setSeo();
		C($config); //添加配置
	}

	protected function getModelFieldRule($model){
		$field = D('Attribute')->where(array('id'=>array('IN',$model['field_list']),'status'=>1))->select();
		foreach ($field as $key => $value) {
			if($value['is_must']){
				$data['validate'][] = array($value['name'],'require',$value['title'].'不能为空！');
			}
			if ($value['validate_type'] && $value['validate_rule']) {
				$data['validate'][] = array($value['name'],$value['validate_rule'],$value['error_info'],$value['validate_time'],$value['validate_type']);
			}
			if ($value['auto_type'] && $value['auto_rule']) {
				$data['auto'][] = array($value['name'],$value['auto_rule'],$value['auto_time'],$value['auto_type']);
			}
		}
		return $data;
	}

	protected function setSeo($title = null,$keywords = null,$description = null){
		$seo = array(
			'title'       => $title,
			'keywords'    => $keywords,
			'description' => $description,
		);
		//获取还没有经过变量替换的META信息
		$meta = D('SeoRule')->getMetaOfCurrentPage($seo);
		foreach ($seo as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					$meta[$key] = str_replace("[".$k."]", $v, $meta[$key]);
				}
			}else{
				$meta[$key] = str_replace("[".$key."]", $value, $meta[$key]);
			}
		}

		$data = array(
			'title'       => $meta['title'],
			'keywords'    => $meta['keywords'],
			'description' => $meta['description'],
		);
		$this->assign($data);
	}


	protected function getField($model){
		$attr = D('Attribute');
		$field_group = parse_config_attr($model['field_group']);
		$field_sort = json_decode($model['field_sort'],true);

		if ($model['extend'] > 1) {
			$map['model_id'] = $model['id'];
		}else{
			$model_id[] = $model['id'];
			$model_id[] = 1;
			$map['model_id'] = array('IN',$model_id);
		}
		if (ACTION_NAME == 'add') {
			$map['is_show'] = array('in',array('1','2'));
		}elseif(ACTION_NAME == 'edit'){
			$map['is_show'] = array('in',array('1','3'));
		}

		//获得数组的第一条数组
		$first_key = array_keys($field_group);
		$fields = $attr->getFields($map);
		if (!empty($field_sort)) {
			foreach ($field_sort as $key => $value) {
				foreach ($value as $index) {
					$groupfield[$key][] = $fields[$index];
					unset($fields[$index]);
				}
			}
		}
		//未进行排序的放入第一组中
		$fields[] = array('name'=>'model_id','type'=>'hidden');    //加入模型ID值
		$fields[] = array('name'=>'id','type'=>'hidden');    //加入模型ID值
		foreach ($fields as $key => $value) {
			$groupfield[$first_key[0]][] = $value;
		}

		foreach ($field_group as $key => $value) {
			if ($groupfield[$key]) {
				$data[$value] = $groupfield[$key];
			}
		}
		return $data;
	}

	protected function getFirst($array){
		if (empty($array)) {
			return false;
		}
		$keys = array_keys($array);
		$key = array_shift($keys);
		$value = array_shift($array);
		return array($key=>$value);
	}
}