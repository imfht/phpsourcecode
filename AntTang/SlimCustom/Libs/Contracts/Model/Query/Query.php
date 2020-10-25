<?php
/**
 * @package     Query.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年7月17日
 */

namespace SlimCustom\Libs\Contracts\Model\Query;

/**
 * Query
 *
 * @author Jing Tang <tangjing3321@gmail.com>
 */
interface Query
{

    /**
     * 查询
     * 
     * @param array $pairs
     */
    public function select($pairs);

    /**
     * 插入
     * 
     * @param array $pairs
     */
    public function insert($pairs);

    /**
     * 更新
     * 
     * @param array $pairs
     */
    public function update($pairs);

    /**
     * 删除
     * 
     * @param string $table
     */
    public function delete($table);
    
    /**
     * 查询声明
     * 
     * @param object $statement
     */
    public function statementResolve($statement);
    
    /**
     * 关闭
     */
    public function close();
}