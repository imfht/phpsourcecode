<?php
namespace Jykj\Dicts\Domain\Model;


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
 * 字典小类
 */
class Dictitem extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 小类名称
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
     * parentuid
     * 
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $parentuid = null;

    /**
     * dicttype
     * 
     * @var \Jykj\Dicts\Domain\Model\Dicttype
     */
    protected $dicttype = null;

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

    /**
     * Returns the parentuid
     * 
     * @return \Jykj\Dicts\Domain\Model\Dictitem $parentuid
     */
    public function getParentuid()
    {
        return $this->parentuid;
    }

    /**
     * Sets the parentuid
     * 
     * @param \Jykj\Dicts\Domain\Model\Dictitem $parentuid
     * @return void
     */
    public function setParentuid(\Jykj\Dicts\Domain\Model\Dictitem $parentuid)
    {
        $this->parentuid = $parentuid;
    }

    /**
     * Returns the dicttype
     * 
     * @return \Jykj\Dicts\Domain\Model\Dicttype $dicttype
     */
    public function getDicttype()
    {
        return $this->dicttype;
    }

    /**
     * Sets the dicttype
     * 
     * @param \Jykj\Dicts\Domain\Model\Dicttype $dicttype
     * @return void
     */
    public function setDicttype(\Jykj\Dicts\Domain\Model\Dicttype $dicttype)
    {
        $this->dicttype = $dicttype;
    }
}
