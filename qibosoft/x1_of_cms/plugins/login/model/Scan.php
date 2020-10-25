<?php
namespace plugins\login\model;
use think\Model;

//扫码登录,比如微信或APP使用
class Scan extends Model
{
    protected $table = '__SCANLOGIN__';
}