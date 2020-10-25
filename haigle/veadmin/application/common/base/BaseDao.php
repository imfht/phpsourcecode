<?php
namespace app\common\base;

interface BaseDao
{
    public function getFind($data);             //获取单条数据
    public function getAllList();               //获取全部数据
    public function getAllListByDate($data);    //获取同一类型数据
    public function insertDate($data);          //插入数据
    public function saveDate($data);            //保存数据
    public function updateBuild($data);         //更新数据
    public function deleteDate($id);            //删除数据
    public function deleteOnDate($data);        //假删除数据
}