<?php

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/13
 * Time: 14:41
 */
class Request
{

	/**
	 * 获取GET参数
	 * @param string $key 键名
	 * @param int $raw 是否过滤输入
	 * @return mixed
	 */
	public function get($key = '',$raw = 0){
		if(empty($key)&&$key===''){
			return $_GET;
		}
		if($raw){
			return $_GET[$key];
		}else{
			return $this->filter($_GET[$key]);
		}
	}

	/**
	 * 获取POST参数
	 * @param string $key 键名
	 * @param int $raw 是否过滤输入
	 * @return mixed
	 */
	public function post($key = '',$raw = 0){
		if(empty($key)&&$key===''){
			return $_POST;
		}
		if($raw){
			return $_POST[$key];
		}else{
			return $this->filter($_POST[$key]);
		}
	}
	/**
	 * 获取输入流参数
	 * @param string $key 键名
	 * @param int $raw 是否过滤输入
	 * @return mixed
	 */
	public function raw($key = '',$raw = 0){
		parse_str(file_get_contents('php://input'), $data);
		if(empty($key)&&$key===''){
			return $data;
		}
		if($raw){
			return $data[$key];
		}else{
			return $this->filter($data[$key]);
		}
	}

	/**
	 * 过滤用户输入
	 * @param string $uri 访问路径
	 * @param string $ext 拓展名 .php .html
	 */
	public function filter($value)
	{
		if(is_array($value)){
			foreach($value as &$v){
				$v = htmlspecialchars(trim($v));
			}
		}else{
			htmlspecialchars(trim($value));
		}
		return $value;
	}
}