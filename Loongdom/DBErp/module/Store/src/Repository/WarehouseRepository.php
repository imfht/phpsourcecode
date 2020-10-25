<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Store\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;

class WarehouseRepository extends EntityRepository
{
    /**
     * 获取全部仓库的sql语句
     * @return \Doctrine\ORM\Query
     */
    public function findAllWarehouse()
    {

        $query = $this->getEntityManager()->createQuery(
            '
                  SELECT w,
                  (SELECT COUNT(p.positionId) FROM Store\Entity\Position p WHERE p.warehouseId=w.warehouseId) number_p
                  FROM Store\Entity\Warehouse w
                  ORDER BY w.warehouseSort ASC
                  '
        );

        return $query;
    }
}