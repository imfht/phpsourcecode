<?php
/**
 * Created by PhpStorm.
 * User: zhangenrui
 * Date: 2017/8/12
 * Time: 下午4:16
 */

namespace actions\index;

use onefox\Action;

class Test extends Action {
    public function excute() {
        dumper('actions test');
    }
}