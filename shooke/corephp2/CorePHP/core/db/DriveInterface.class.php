<?php
namespace Core\Db;
/**
 * @author shooke
 * 数据库操作接口类 
 */
Interface DriveInterface {	
    //事务开始
	public function begin();
	//事务提交
	public function commit();
	//事务回滚
	public function rollBack();
	//执行sql查询
	public function query($sql, $params = array());
	//执行sql命令
	public function execute($sql, $params = array());
	//从结果集中取得一行作为关联数组，或数字数组，或二者兼有
	public function fetch($result_type = PDO::FETCH_ASSOC);
	//从结果集中取得所有行作为关联数组，或数字数组，或二者兼有
	public function fetchAll($result_type = PDO::FETCH_ASSOC);
	//取得前一次 MySQL 操作所影响的记录行数
	public function affectedRows();
	//获取上一次插入的id
	public function lastId();
	//获取SQL语句
	public function getSql();
	//获取数据库表
	public function getTables($database);
	//获取表结构
	public function getFields($table);
	//获取行数
	public function count($table,$where,$field='*');
	//取得数据库版本
    public function version();
	//解析待添加或修改的数据
	public function parseData($options, $type);
	//解析查询条件
	public function parseCondition($options);
    //返回链接状态成功返回true
    public function success();
	//输出错误信息
	public function error();
	//释放资源
	public function free();
    //断开链接
	public function close();	

}