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
 * 文件管理
 */
class Filemanage extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * fileimg
     * 
     * @var string
     */
    protected $fileimg = '';

    /**
     * title
     * 
     * @var string
     */
    protected $title = '';

    /**
     * filepath
     * 
     * @var string
     */
    protected $filepath = '';

    /**
     * senddate
     * 
     * @var string
     */
    protected $senddate = '';

    /**
     * sort
     * 
     * @var int
     */
    protected $sort = 0;

    /**
     * filetypeid
     * 
     * @var int
     */
    protected $filetypeid = 0;

    /**
     * filetypes
     * 
     * @var \Jykj\Filemanage\Domain\Model\Filetypes
     */
    protected $filetypes = null;

    /**
     * Returns the title
     * 
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     * 
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the filepath
     * 
     * @return string $filepath
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Sets the filepath
     * 
     * @param string $filepath
     * @return void
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * Returns the fileimg
     * 
     * @return string $fileimg
     */
    public function getFileimg()
    {
        return $this->fileimg;
    }

    /**
     * Sets the fileimg
     * 
     * @param string $fileimg
     * @return void
     */
    public function setFileimg($fileimg)
    {
        $this->fileimg = $fileimg;
    }

    /**
     * Returns the senddate
     * 
     * @return string $senddate
     */
    public function getSenddate()
    {
        return $this->senddate;
    }

    /**
     * Sets the senddate
     * 
     * @param string $senddate
     * @return void
     */
    public function setSenddate($senddate)
    {
        $this->senddate = $senddate;
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
     * Returns the filetypeid
     * 
     * @return int $filetypeid
     */
    public function getFiletypeid()
    {
        return $this->filetypeid;
    }

    /**
     * Sets the filetypeid
     * 
     * @param int $filetypeid
     * @return void
     */
    public function setFiletypeid($filetypeid)
    {
        $this->filetypeid = $filetypeid;
    }

    /**
     * Returns the filetypes
     * 
     * @return \Jykj\Filemanage\Domain\Model\Filetypes $filetypes
     */
    public function getFiletypes()
    {
        return $this->filetypes;
    }

    /**
     * Sets the filetypes
     * 
     * @param \Jykj\Filemanage\Domain\Model\Filetypes $filetypes
     * @return void
     */
    public function setFiletypes(\Jykj\Filemanage\Domain\Model\Filetypes $filetypes)
    {
        $this->filetypes = $filetypes;
    }
}
