<?php
namespace plugins\area\admin;

class Zone extends City
{
    protected $cfg_level = 3;                   //当前属于第几级
    protected $cfg_flevel = 2;                   //父类所属第几级
    protected $cfg_fname = '城市';            //父级名称
    protected $cfg_name = '县(区域)';       //本级名称
    protected $cfg_sfile = street;             //子级文件名
}
