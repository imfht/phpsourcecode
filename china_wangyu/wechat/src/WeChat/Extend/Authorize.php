<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/30 Time: 14:54
 */

namespace WeChat\Extend;


/**
 * 微信授权接口
 * Interface Authorize
 * @package WeChat\Extend
 */
interface Authorize
{

    /**
     * 首次关注
     * @param \WeChat\Core\Authorize->returnData 返回数据数组
     * @param \WeChat\Core\Authorize->config 微信数据包
     * @return mixed
     */
    public function follow();

    /**
     * 扫码关注
     * @param \WeChat\Core\Authorize->returnData 返回数据数组
     * @param \WeChat\Core\Authorize->config 微信数据包
     * @return mixed
     */
    public function scanFollow();

    /**
     * 点击事件
     * @param \WeChat\Core\Authorize->returnData 返回数据数组
     * @param \WeChat\Core\Authorize->config 微信数据包
     * @return mixed
     */
    public function click();

    /**
     * 扫描商品
     * @param \WeChat\Core\Authorize->returnData 返回数据数组
     * @param \WeChat\Core\Authorize->config 微信数据包
     * @return mixed
     */
    public function scanProduct();

    /**
     * 扫码事件
     * @return mixed
     */
    public function scan();


    /**
     * 用户输入
     * @return mixed
     */
    public function input();
}