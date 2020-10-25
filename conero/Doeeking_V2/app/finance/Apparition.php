<?php
namespace app\finance;
use hyang\Evil;
class Apparition extends Evil{
    public function pageInit()
    {
        $this->OptAction([
            'title' => '财务系统','home'=>'/conero/finance.html'
        ]);
    }
    public function app_nav()
    {
        return '            
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/finance/fincset">财物登帐</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/finance/budget">财务计划</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/finance/organ">财物机构</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/finance/flist">子项清单</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/finance/fevent">财务纪事</a>
            <a href="javascript:void(0);" class="app_nav" dataid="/Conero/finance/tool">财物工具</a>
        ';
    }
}