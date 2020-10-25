<?php
/**
 * Paginator Query Builder Adapter
 *
 * @author Andres Gutierrez <andres@phalconphp.com>
 * @author Eduar Carvajal <eduar@phalconphp.com>
 * @author Wenzel Pünter <wenzel@phelix.me>
 * @version 1.2.6
 * @package Phalcon
 */
namespace Core\Db\Paginator;

use PDO;
use Phalcon\Paginator\Adapter;
use Phalcon\Paginator\Exception;
use stdClass;

/**
 * Phalcon\Paginator\Adapter\QueryBuilder
 *
 * Pagination using a PHQL query builder as source of data
 *
 *<code>
 *  $builder = $this->modelsManager->createBuilder()
 *                   ->columns('id, name')
 *                   ->from('Robots')
 *                   ->orderBy('name');
 *
 *  $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
 *      "builder" => $builder,
 *      "limit"=> 20,
 *      "page" => 1
 *  ));
 *</code>
 *
 * @see https://github.com/phalcon/cphalcon/blob/1.2.6/ext/paginator/adapter/querybuilder.c
 */
class QueryBuilder extends Adapter
{
    /**
     * Configuration
     *
     * @var null|array
     * @access protected
     */
    protected $_config;

    protected $_model;
    /**
     * Builder
     *
     * @var null|object
     * @access protected
     */
    protected $_builder;

    /**
     * Limit Rows
     *
     * @var null|int
     * @access protected
     */
    protected $_limitRows;

    /**
     * Page
     *
     * @var int
     * @access protected
     */
    protected $_page;

    protected $_ctorargs;

    /**
     * \Phalcon\Paginator\Adapter\QueryBuilder
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct($config)
    {
        if (is_array($config) === false) {
            throw new Exception('Invalid parameter type.');
        }

        $this->_config = $config;
        if (isset($config['builder']) === false) {
            throw new Exception("Parameter 'builder' is required");
        } else {
            //@note no further builder validation
            $this->_builder = $config['builder'];
        }
        if (isset($config['model']) && class_exists($config['model'])) {
            $this->_model = $config['model'];
        }
        if (isset($config['limit']) === false) {
            throw new Exception("Parameter 'limit' is required");
        } else {
            $this->_limitRows = $config['limit'];
        }
        if (isset($config['ctorargs'])) {
            $this->_ctorargs = $config['ctorargs'];
        } else {
            $this->_ctorargs = array();
        }
        if (isset($config['page']) === true) {
            $this->_page = $config['page'];
        }
    }

    /**
     * Set the current page number
     *
     * @param int $page
     * @throws Exception
     */
    public function setCurrentPage($currentPage)
    {
        if (is_int($currentPage) === false) {
            throw new Exception('Invalid parameter type.');
        }
        $this->_page = $currentPage;
    }

    /**
     * Returns a slice of the resultset to show in the pagination
     *
     * @return stdClass
     */
    public function getPaginate()
    {
        /* Clone the original builder */
        $builder = clone $this->_builder;
        $totalBuilder = clone $builder;

        $limit = $this->_limitRows;
        $numberPage = $this->_page;

        if (is_null($numberPage) === true) {
            $numberPage = 1;
        }

        $prevNumberPage = $numberPage - 1;
        $number = $limit * $prevNumberPage;

        //Set the limit clause avoiding negative offsets
        if ($number < $limit) {
            $builder->limit($limit);
        } else {
            $builder->limit($limit, $number);
        }
        $query = $builder->execute();
        $totalBuilder = $totalBuilder->execute();
        if ($this->_model) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->_model);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        //Obtain the result of the total query
        $rowcount = (int) $totalBuilder->numRows();

        $totalPages = (int) $rowcount / $limit;
        $intTotalPages = (int) $totalPages;

        if ($intTotalPages !== $totalPages) {
            $totalPages = $intTotalPages + 1;
        }

        $page = new stdClass();
        $page->first = 1;
        $page->before = ($numberPage === 1 ? 1 : ($numberPage - 1));
        $page->items = (object) $query->fetchAll();
        $page->next = ($numberPage < $totalPages ? ($numberPage + 1) : $totalPages);
        $page->last = $totalPages;
        $page->current = $numberPage;
        $page->total_pages = $totalPages;
        $page->total_items = (int) $numberPage;

        return $page;
    }
}
