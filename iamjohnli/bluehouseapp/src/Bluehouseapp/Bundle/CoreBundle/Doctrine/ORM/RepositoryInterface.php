<?php


namespace  Bluehouseapp\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Model repository interface.
 *

 */
interface RepositoryInterface extends ObjectRepository
{
    /**
     * Create a new resource
     *
     * @return mixed
     */
    public function createNew();

    /**
     * Get paginated collection
     *
     * @param array $criteria
     * @param array $orderBy
     *
     * @return mixed
     */
    public function createPaginator(array $criteria = null, array $orderBy = null);
}
