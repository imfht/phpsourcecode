<?php
namespace Jykj\Dicts\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/***
 *
 * This file is part of the "数据字典" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * 字典大类
 */
class Dicttype extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 大类名称
     * 
     * @var string
     */
    protected $name = '';

    /**
     * 简称
     * 
     * @var string
     */
    protected $shortname = '';

    /**
     * 代码
     * 
     * @var string
     */
    protected $code = '';

    /**
     * 说明
     * 
     * @var string
     */
    protected $remarks = '';

    /**
     * 图片
     * 
     * @var string
     */
    protected $image = '';

    /**
     * 排序
     * 
     * @var int
     */
    protected $sort = 0;

    /**
     * spare1
     * 
     * @var int
     */
    protected $spare1 = 0;
    
    /**
     * itemnum
     * 小类的数量[不是字段]
     * @var int
     */
    protected $itemnum=0;

    /**
     * Returns the itemnum
     *
     * @return int $itemnum
     */
    public function getItemnum()
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_dicts_domain_model_dictitem');
        $this->itemnum = $queryBuilder
        ->count('uid')
        ->from('tx_dicts_domain_model_dictitem')
        ->where(
            $queryBuilder->expr()->eq('dicttype', $queryBuilder->createNamedParameter($this->uid))
        )
        ->execute()
        ->fetchColumn(0);
        return $this->itemnum;
    }
    
    /**
     * Returns the name
     * 
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     * 
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the shortname
     * 
     * @return string $shortname
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    /**
     * Sets the shortname
     * 
     * @param string $shortname
     * @return void
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;
    }

    /**
     * Returns the code
     * 
     * @return string $code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the code
     * 
     * @param string $code
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Returns the remarks
     * 
     * @return string $remarks
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Sets the remarks
     * 
     * @param string $remarks
     * @return void
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     * Returns the image
     * 
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image
     * 
     * @param string $image
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Returns the sort
     * 
     * @return int $sort
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Sets the sort
     * 
     * @param int $sort
     * @return void
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * Returns the spare1
     * 
     * @return int $spare1
     */
    public function getSpare1()
    {
        return $this->spare1;
    }

    /**
     * Sets the spare1
     * 
     * @param int $spare1
     * @return void
     */
    public function setSpare1($spare1)
    {
        $this->spare1 = $spare1;
    }
}
