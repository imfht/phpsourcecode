<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/11/3
 * Time: 下午5:28
 */

namespace Async;


use Inject\Injector;

class AsyncHandler
{

    static public function handle($cls,$act,$params){
        $inj = new Injector();
        return empty($cls) ? $inj->call($act,$params) : $inj->callInClass($cls,$act,$params);
    }

}