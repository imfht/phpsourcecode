<?php
namespace plugins\area\admin;

class Street extends City
{
    protected $cfg_level = 4;               //当前属于第几级
    protected $cfg_flevel = 3;              //父类所属第几级
    protected $cfg_fname = '县(区域)';    //父级名称
    protected $cfg_name = '镇(街道)';      //本级名称
    protected $cfg_sfile = null;            //子级文件名
}