<?php
namespace Jykj\Filemanage\Domain\Model;


/***
 *
 * This file is part of the "文件管理系统" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 yangshichang <yangshichang@ngoos.org>, 极益科技
 *
 ***/
/**
 * 文件类型
 */
class Filetypes extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     * 
     * @var string
     */
    protected $name = '';

    /**
     * discript
     * 
     * @var string
     */
    protected $discript = '';

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
     * Returns the discript
     * 
     * @return string $discript
     */
    public function getDiscript()
    {
        return $this->discript;
    }

    /**
     * Sets the discript
     * 
     * @param string $discript
     * @return void
     */
    public function setDiscript($discript)
    {
        $this->discript = $discript;
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
}
