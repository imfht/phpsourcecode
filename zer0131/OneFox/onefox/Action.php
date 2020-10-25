<?php
/**
 * 抽象Action类
 * @author ryan<zer0131@vip.qq.com>
 */

namespace onefox;

abstract class Action extends Controller {

    abstract public function excute();

}