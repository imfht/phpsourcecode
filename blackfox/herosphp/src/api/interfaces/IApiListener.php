<?php
/**
 * api 访问拦截器接口.
 * -------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2017-03-27 v2.0.0
 */
namespace herosphp\api\interfaces;

interface  IApiListener {

    /**
     * API authorization interception processing
     * @param $params
     * @return bool
     */
    public function authorize($params=null);

    /**
     * determine if a request need authrization
     * @param $url
     * @return bool
     */
    public function needAuthrize($url);
}