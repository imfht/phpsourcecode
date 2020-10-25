<?php
namespace plugins\area\admin;


class Province extends City
{
    protected $cfg_level = 1;               //当前属于第几级
    protected $cfg_flevel = 0;              //父类所属第几级
    protected $cfg_fname = null;        //父级名称
    protected $cfg_name = '省份';         //本级名称
    protected $cfg_sfile = 'city';           //子级文件名

}
