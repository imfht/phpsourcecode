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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * 应用案例
 */
class Casetab extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 标题
     * 
     * @var string
     */
    protected $title = '';

    /**
     * 内容简介（未用）
     * 
     * @var string
     */
    protected $content = '';

    /**
     * 图片
     * 
     * @var string
     */
    protected $image = '';

    /**
     * 发布时间
     * 
     * @var \DateTime
     */
    protected $datetime = null;

    /**
     * 内容详情
     * 
     * @var string
     */
    protected $spare1 = '';

    /**
     * 网站地址
     * 
     * @var string
     */
    protected $spare2 = '';

    /**
     * 项目背景
     * 
     * @var string
     */
    protected $spare3 = '';

    /**
     * 应用场景
     * 
     * @var string
     */
    protected $spare4 = '';

    /**
     * 应用效果
     * 
     * @var string
     */
    protected $spare5 = '';

    /**
     * 技术要点
     * 
     * @var string
     */
    protected $spare6 = '';

    /**
     * 产品分类 可以多选 以,1,2,3,形式展示
     * 
     * @var string
     */
    protected $product = '';

    /**
     * 产品分类，数据字典，逗号分隔
     * 
     * @var string
     */
    protected $labels = '';

    /**
     * 浏览次数
     * 
     * @var int
     */
    protected $hits = 0;

    /**
     * casetype
     * 
     * @var \Jykj\CaseTab\Domain\Model\Casetype
     */
    protected $casetype = null;

    /**
     * 行业分类
     * 
     * @var \Jykj\Dicts\Domain\Model\Dictitem
     */
    protected $industry = null;
    
    /**
     * tagname
     * 获取多个标签的名称[不是数据库字段]
     * @var array
     */
    protected $tagname=array();
    
    /**
     * proname
     * 产品分类的名称[不是数据库字段]
     * @var array
     */
    protected $proname=array();
    
    /**
     * allimage
     * 第一张图片路径[不是数据库字段]
     * @var array
     */
    protected $firstimg=array();

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
     * Returns the content
     * 
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets the content
     * 
     * @param string $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * Returns the datetime
     * 
     * @return \DateTime $datetime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Sets the datetime
     * 
     * @param \DateTime $datetime
     * @return void
     */
    public function setDatetime(\DateTime $datetime)
    {
        $this->datetime = $datetime;
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

    /**
     * Returns the spare4
     * 
     * @return string $spare4
     */
    public function getSpare4()
    {
        return $this->spare4;
    }

    /**
     * Sets the spare4
     * 
     * @param string $spare4
     * @return void
     */
    public function setSpare4($spare4)
    {
        $this->spare4 = $spare4;
    }

    /**
     * Returns the spare5
     * 
     * @return string $spare5
     */
    public function getSpare5()
    {
        return $this->spare5;
    }

    /**
     * Sets the spare5
     * 
     * @param string $spare5
     * @return void
     */
    public function setSpare5($spare5)
    {
        $this->spare5 = $spare5;
    }

    /**
     * Returns the spare6
     * 
     * @return string $spare6
     */
    public function getSpare6()
    {
        return $this->spare6;
    }

    /**
     * Sets the spare6
     * 
     * @param string $spare6
     * @return void
     */
    public function setSpare6($spare6)
    {
        $this->spare6 = $spare6;
    }

    /**
     * Returns the product
     * 
     * @return string $product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Sets the product
     * 
     * @param string $product
     * @return void
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Returns the labels
     * 
     * @return string $labels
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Sets the labels
     * 
     * @param string $labels
     * @return void
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    /**
     * Returns the hits
     * 
     * @return int $hits
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Sets the hits
     * 
     * @param int $hits
     * @return void
     */
    public function setHits($hits)
    {
        $this->hits = $hits;
    }

    /**
     * Returns the casetype
     * 
     * @return \Jykj\CaseTab\Domain\Model\Casetype $casetype
     */
    public function getCasetype()
    {
        return $this->casetype;
    }

    /**
     * Sets the casetype
     * 
     * @param \Jykj\CaseTab\Domain\Model\Casetype $casetype
     * @return void
     */
    public function setCasetype(\Jykj\CaseTab\Domain\Model\Casetype $casetype)
    {
        $this->casetype = $casetype;
    }

    /**
     * Returns the industry
     * 
     * @return \Jykj\Dicts\Domain\Model\Dictitem $industry
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Sets the industry
     * 
     * @param \Jykj\Dicts\Domain\Model\Dictitem $industry
     * @return void
     */
    public function setIndustry(\Jykj\Dicts\Domain\Model\Dictitem $industry)
    {
        $this->industry = $industry;
    }
    
    /**
     * Returns the tagname
     *
     * @return array $tagname
     */
    public function getTagname(){
        //$lbs=\json_decode($this->labels,TRUE);//json转数组
        $lbs=trim($this->labels,",");
        if($lbs!=""){
            //$taguids = implode(",", $lbs);//数组转字符串。形式为1,2,3
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_dicts_domain_model_dictitem');
            $datalist = $queryBuilder
            ->select('*')
            ->from('tx_dicts_domain_model_dictitem')
            ->where(
                $queryBuilder->expr()->in('uid', explode(",",$lbs))
            )
            ->orderBy("sort","ASC")
            ->execute()
            ->fetchAll();
            
            if(count($datalist)>0){
                $strname=array();
                foreach ($datalist as $v){
                    $strname[]=array("uid"=>$v["uid"],"name"=>$v["name"]);
                }
                $this->tagname=$strname;
            }else{
                $this->tagname=array();
            }
        }else{
            $this->tagname=array();
        }
        return $this->tagname;
    }
    
    /**
     * Returns the proname
     *
     * @return array $proname
     */
    public function getProname(){
        $pro=trim($this->product,",");
        if($pro!=""){
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_dicts_domain_model_dictitem');
            $datalist = $queryBuilder
            ->select('*')
            ->from('tx_dicts_domain_model_dictitem')
            ->where(
                $queryBuilder->expr()->in('uid', explode(",",$pro))
            )
            ->orderBy("sort","ASC")
            ->execute()
            ->fetchAll();
            if(count($datalist)>0){
                $strname=array();
                foreach ($datalist as $v){
                    $strname[]=array("uid"=>$v["uid"],"name"=>$v["name"]);
                }
                $this->proname=$strname;
            }else{
                $this->proname=array();
            }
        }else{
            $this->proname=array();
        }
        return $this->proname;
    }
    
    /**
     * Returns the allimage
     *
     * @return string $allimage
     */
    public function getAllimage(){
        $imgArr=explode(";",$this->image);
        if(!empty($imgArr)){
            $this->allimage=$imgArr;
        }else{
            $this->allimage=array();
        }
        return $this->allimage;
    }
}
