<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Pagination;
use Tang\I18n\II18n;
use Tang\Interfaces\ISetConfig;

/**
 * 分页接口
 * Interface IPaginator
 * @package Tang\Pagination
 */
interface IPaginator
{
    /**
     * 设置语言包
     * @param II18n $II18n
     * @return void
     */
    public function setl18n(II18n $II18n);
    /**
     * 设置总量
     * @param $total
     * @return void
     */
    public function setTotal($total);

    /**
     * 设置每页数量
     * @param $pageNumber
     * @return void
     */
    public function setPageNumber($pageNumber);

    /**
     * 获取每页数量
     * @return int
     */
    public function getPageNumber();

    /**
     * 获取最大页数
     * @return int
     */
    public function getMaxPage();

    /**
     * 获取当前页
     * @return int
     */
    public function getNowPage();

    /**
     * 获取分页数组
     * 返回的结果数组为array(
     *       array('name'=>'第一页','page' => 1),
     * array('name'=>'1','page' => 1),
     * array('name'=>'2','page' => 2),
     *      array('name'=>'下一页','page' => 2)
     * )形式
     * @param int $nowPage 当前页
     * @param int $total 总数量
     * @param int $pageNumber 一页数量
     * @return array
     */
    public function getPages ($nowPage,$total = 0,$pageNumber = 0);
}