<?php
namespace Jykj\CaseTab\Domain\Model;


/***
 *
 * This file is part of the "应用案例" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 杨世昌 <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * 案例类型
 */
class Casetype extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 名称
     * 
     * @var string
     */
    protected $name = '';

    /**
     * 描述
     * 
     * @var string
     */
    protected $description = '';

    /**
     * 排序
     * 
     * @var int
     */
    protected $sort = 0;

    /**
     * spare1
     * 
     * @var string
     */
    protected $spare1 = '';

    /**
     * spare2
     * 
     * @var string
     */
    protected $spare2 = '';

    /**
     * spare3
     * 
     * @var string
     */
    protected $spare3 = '';

    /**
     * crdate
     *
     * @var \DateTime
     */
    protected $crdate = null;
    
    /**
     * contentarr[不是数据库字段]
     *查询对应类型的所有
     * @var array
     */
    protected $contentarr = null;
    
    /**
     * Returns the contentarr
     *
     * @return array $contentarr
     */
    public function getContentarr()
    {
        $this->contentarr = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_casetab_domain_model_casetab', 'deleted=0 and hidden=0 and casetype='.$this->uid, '', 'datetime desc');
        return $this->contentarr;
    }
    
    /**
     * Returns the crdate
     *
     * @return \DateTime $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
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
     * Returns the description
     * 
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     * 
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return string $spare1
     */
    public function getSpare1()
    {
        return $this->spare1;
    }

    /**
     * Sets the spare1
     * 
     * @param string $spare1
     * @return void
     */
    public function setSpare1($spare1)
    {
        $this->spare1 = $spare1;
    }

    /**
     * Returns the spare2
     * 
     * @return string $spare2
     */
    public function getSpare2()
    {
        return $this->spare2;
    }

    /**
     * Sets the spare2
     * 
     * @param string $spare2
     * @return void
     */
    public function setSpare2($spare2)
    {
        $this->spare2 = $spare2;
    }

    /**
     * Returns the spare3
     * 
     * @return string $spare3
     */
    public function getSpare3()
    {
        return $this->spare3;
    }

    /**
     * Sets the spare3
     * 
     * @param string $spare3
     * @return void
     */
    public function setSpare3($spare3)
    {
        $this->spare3 = $spare3;
    }
}
