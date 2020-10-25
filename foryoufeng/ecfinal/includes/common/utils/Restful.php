<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/14/16
 * Time: 9:28 AM
 */
interface Restful {
    /**
     * 展示数据
     * @return mixed
     */
    public function index();

    /**
     * 获取数据
     * @return mixed
     */
    public function get_list();

    /**
     * 显示单条数据
     * @return mixed
     */
    public function show();

    /**
     * 创建表单
     * @return mixed
     */
    public function add();

    /**
     * 删除记录
     * @return mixed
     */
    public function destroy();

    /**
     * 编辑记录
     * @return mixed
     */
    public function edit();

    /**
     * ajax返回数据
     * @return mixed
     */
    public function query();
}

