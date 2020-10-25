<?php
namespace App\Model;
/**
 * 系统菜单模型
 * @package App\Model
 */
class SysFile extends \App\Component\BaseModel
{
    public $primary = 'fileId';
    /**
     * 表名
     * @var string
     */
    public $table = 'sys_file';
}