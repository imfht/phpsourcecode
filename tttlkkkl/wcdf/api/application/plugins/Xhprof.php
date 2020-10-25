<?php

/**
 *
 * Date: 16-10-6
 * Time: 下午11:42
 * author :李华 yehong0000@163.com
 */
class XhprofPlugin extends Yaf\Plugin_Abstract
{
    public function routerStartup(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
        xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    }

    public function dispatchLoopShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response)
    {
        $data = xhprof_disable();
        Yaf\Loader::getInstance()->import(Yaf\Registry::get('config')->xhprof->dir.'xhprof_lib/utils/xhprof_lib.php');
        Yaf\Loader::getInstance()->import(Yaf\Registry::get('config')->xhprof->dir.'xhprof_lib/utils/xhprof_runs.php');

        $objXhprofRun = new XHProfRuns_Default();
        $run_id = $objXhprofRun->save_run($data, "xhprof");
        $domain=Yaf\Registry::get('config')->xhprof->domain;
        $url="{$domain}?run={$run_id}&source=xhprof";
        if($request->getParam('x')==1){
            $response->setRedirect($url);
        }else{
            echo $url;
        }
    }
}