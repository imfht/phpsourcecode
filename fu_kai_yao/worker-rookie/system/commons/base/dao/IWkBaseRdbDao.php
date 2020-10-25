<?php
namespace system\commons\base\dao;

/**
 * 悟空saas系统，rdb基础接口, 主要用于商户数据隔离，每个接口必须携带zId
 * @author fukaiyao
 */
interface IWkBaseRdbDao
{
    /**
     * 添加一条记录
     * @param int $zId      - 商户Id
     * @param array $info         - 详细字段参考对应表字段，插入一条[数据]，插入多条[[数据][数据]]
     * @return boolean | int      - 添加成功返回自增ID(不存在自增id返回0，多条插入返回true)，失败返回false
     */
    public function add($zId, $info);
    
    /**
     * 更新信息
     * @param int   $zId       - 商户Id
     * @param mixed       $id            - 主键id
     * @param array  $info          - 详细字段参考对应表字段
     * @return boolean                - 更新是否成功
     */
    public function updateById($zId, $id, array $info);
    
    /**
     * 删除一条记录
     * @param int $zId         - 商户Id
     * @param mixed       $id             - 主键id
     * @return boolean                 - 删除是否成功
     */
    public function deleteById($zId, $id);

    /**
     * 批量删除记录
     * @param int $zId         - 商户Id
     * @param array|string $ids             - 主键ids
     * @return boolean                 - 删除是否成功
     */
    public function deleteByIds($zId, $ids);
    
    /**
     *  获取一条记录
     * @param int $zId             - 商户Id
     * @param mixed       $id            - 主键id
     * @param mixed $fields           - 返回字段，多个字段逗号分隔, 为空返回全部 (支持以数组的形式传递字段)
     * @param boolean $isLock         - 是否对读取的数据强制加上for update
     * @return null | array            - 找到返回一条记录(详细字段请参考对应的表)，找不到返回null
     */
    public function getInfoById($zId, $id, $fields = null, $isLock = false);
    
    /**
     *  根据主键ID数组批量获取信息
     * @param int $zId         - 商户Id
     * @param array  $ids             - 主键id数组
     * @param mixed $fields           - 返回字段，多个字段逗号分隔, 为空返回全部 (支持以数组的形式传递字段)
     * @param boolean $isLock         - 是否对读取的数据强制加上for update
     * @return null | array            - 找到返回一条记录(详细字段请参考对应的表)，找不到返回null
     */
    public function getInfoByIds($zId, $ids, $fields = null, $isLock = false);

    /**
     * 更新信息
     * @param int   $zId       - 商户Id
     * @param mixed       $ids            - 主键id数组
     * @param array  $info          - 详细字段参考对应表字段
     * @return boolean                - 更新是否成功
     */
    public function updateByIds($zId, $ids, $info);

    /**
     * 根据zid获取信息
     * @param int $zId -企业id
     * @param string $fields -字段
     * @param array $order -排序
     * @return false | array
     */
    public function getByZid($zId, $fields=null, array $order=[]);
    /**
     * 获取数量
     * @param int $zId -企业id
     * @param $params               -查询条件 如：[zid=>1,...]
     * @return int                  -返回数量
     */
    public function getCountByParams($zId, $params);

    /**
     * 根据params的条件返回信息
     * @param int $zId -企业id
     * @param $params               -查询条件 如：[zid=>1,...]
     * @param string $fields 查询字段
     * @param int|bool $page -页数，为false则不分页
     * @param int|bool $pageSize
     * @param array $order 排序
     * @param array $group 分组
     * @return false | array
     */
    public function getByParams($zId, $params, $fields=null, $page=false, $pageSize=false, array $order=[], array $group=[]);

    /**
     * 根据params的条件获取一条信息
     * @param int $zId -企业id
     * @param $params   -查询条件 如：[zid=>1,...]
     * @param string $fields 查询字段
     * @param array $order 排序
     * @param bool $isLock 是否锁表
     * @param array $group 分组
     * @return false | array
     */
    public function getOneByParams($zId, $params, $fields=null, array $order=[], $isLock=false, array $group = []);

    /**
     * 根据params的条件更新信息
     * @param int $zId -企业id
     * @param $params               -查询条件 如：[zid=>1,...]
     * @param  array $info 更新字段
     * @return bool
     */
    public function updateByParams($zId, $params, $info);

    /**
     * 根据params的条件增加字段值(mysql原子级)
     * @param int $zId -企业id
     * @param $params               -查询条件 如：[zid=>1,...]
     * @param  string $field 更新字段
     * @param  int $num 增加的数量
     * @return bool
     */
    public function incByParams($zId, $params, $field, $num = 1);

}