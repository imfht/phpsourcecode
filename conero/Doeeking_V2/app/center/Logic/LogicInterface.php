<?php
// 业务逻辑接口 - 2016年12月11日 星期日
namespace app\center\Logic;
interface  LogicInterface
{
    // 逻辑接口内容
    public function init(&$opts,$action);// 前段初始化工具
    public function main();// 主函数
    public function ajax();// ajax 请求
    public function save();// php 动态保存页面
    public function edit($view);// 编辑页面- 用于表单处理
}