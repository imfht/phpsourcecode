<?php
namespace Jykj\Invoice\Domain\Model;


/***
 *
 * This file is part of the "发票管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Shichang Yang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * 捐赠渠道表
 */
class Channels extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 名称
     * 
     * @var string
     */
    protected $name = '';

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
     * spaer3
     * 
     * @var string
     */
    protected $spaer3 = '';

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
     * Returns the spaer3
     * 
     * @return string $spaer3
     */
    public function getSpaer3()
    {
        return $this->spaer3;
    }

    /**
     * Sets the spaer3
     * 
     * @param string $spaer3
     * @return void
     */
    public function setSpaer3($spaer3)
    {
        $this->spaer3 = $spaer3;
    }
}
