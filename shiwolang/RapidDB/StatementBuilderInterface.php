<?php
/**
 * Created by zhouzhongyuan.
 * User: zhou
 * Date: 2015/11/27
 * Time: 11:41
 */

namespace shiwolang\db;


interface StatementBuilderInterface
{
    /**
     * @return string
     */
    public function getPrepareStatement();

    /**
     * @return array
     */
    public function getPrepareStatementParams();
}