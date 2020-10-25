<?php
namespace Jykj\Echarts\Domain\Model;


/***
 *
 * This file is part of the "统计数据图表" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 王宏彬 <wanghongbin816@gmail.com>, 宁夏极益科技邮箱公司
 *
 ***/
/**
 * 图表数据
 */
class Echarts extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * 图表
     * 
     * @var string
     */
    protected $echart = '';

    /**
     * 图表标题
     * 
     * @var string
     */
    protected $title = '';

    /**
     * 图表标题显示位置
     * 
     * @var string
     */
    protected $titlepos = '';

    /**
     * 图表副标题
     * 
     * @var string
     */
    protected $subtitle = '';

    /**
     * 副标题超链接
     * 
     * @var string
     */
    protected $sublink = '';

	/**
     * 图表容器宽度
     * 
     * @var string
     */
    protected $width = '';

	/**
     * 图表容器高度
     * 
     * @var string
     */
    protected $height = '';

    /**
     * 图表在页面上的显示位置
     * 
     * @var string
     */
    protected $alignment = '';

    /**
     * 提示框组件
     * 
     * @var string
     */
    protected $tooltip = '';

    /**
     * 工具栏组件
     * 
     * @var string
     */
    protected $toolbox = '';

    /**
     * 调色盘:默认为：
     * 
     * ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622',
     * '#bda29a','#6e7074', '#546570', '#c4ccd3']
     * 
     * @var string
     */
    protected $color = '';

    /**
     * 字体样式
     * 
     * @var string
     */
    protected $textstyle = '';

    /**
     * 创建人
     * 
     * @var string
     */
    protected $author = '';
    
    /**
     * 图表主题
     * 
     * @var string
     */
    protected $theme = '';

    /**
     * 图标代码
     * 
     * @var string
     */
    protected $code = '';

    /**
     * 图表数据
     * 
     * @var string
     */
    protected $datas = '';

    /**
     * 图表主题
     * 
     * @var string
     */
    protected $listtheme = '';
    
    /**
     * crdate
     * 图表创建时间
     * @var \DateTime
     */
    protected $crdate = null;

    /**
     * 最后修改时间
     * 
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * Returns the echart
     * 
     * @return string $echart
     */
    public function getEchart()
    {
        return $this->echart;
    }

    /**
     * Sets the echart
     * 
     * @param string $echart
     * @return void
     */
    public function setEchart($echart)
    {
        $this->echart = $echart;
    }


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
     * Returns the titlepos
     * 
     * @return string $titlepos
     */
    public function getTitlepos()
    {
        return $this->titlepos;
    }

    /**
     * Sets the titlepos
     * 
     * @param string $titlepos
     * @return void
     */
    public function setTitlepos($titlepos)
    {
        $this->titlepos = $titlepos;
    }

    /**
     * Returns the subtitle
     * 
     * @return string $subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Sets the subtitle
     * 
     * @param string $subtitle
     * @return void
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }
    
    /**
     * Returns the sublink
     * 
     * @return string $sublink
     */
    public function getSublink()
    {
        return $this->sublink;
    }

    /**
     * Sets the sublink
     * 
     * @param string $sublink
     * @return void
     */
    public function setSublink($sublink)
    {
        $this->sublink = $sublink;
    }

    /**
     * Returns the tooltip
     * 
     * @return string $tooltip
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * Sets the tooltip
     * 
     * @param string $tooltip
     * @return void
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;
    }

    /**
     * Returns the toolbox
     * 
     * @return string $toolbox
     */
    public function getToolbox()
    {
        return $this->toolbox;
    }

    /**
     * Sets the toolbox
     * 
     * @param string $toolbox
     * @return void
     */
    public function setToolbox($toolbox)
    {
        $this->toolbox = $toolbox;
    }

    /**
     * Returns the color
     * 
     * @return string $color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Sets the color
     * 
     * @param string $color
     * @return void
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * Returns the textstyle
     * 
     * @return string $textstyle
     */
    public function getTextstyle()
    {
        return $this->textstyle;
    }

    /**
     * Sets the textstyle
     * 
     * @param string $textstyle
     * @return void
     */
    public function setTextstyle($textstyle)
    {
        $this->textstyle = $textstyle;
    }

    /**
     * Returns the author
     * 
     * @return string $author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the author
     * 
     * @param string $author
     * @return void
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Returns the theme
     * 
     * @return string $theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Sets the theme
     * 
     * @param string $theme
     * @return void
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
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
     * Returns the datas
     * 
     * @return string $datas
     */
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     * Sets the datas
     * 
     * @param string $datas
     * @return void
     */
    public function setDatas($datas)
    {
        $this->datas = $datas;
    }

    /**
     * Returns the width
     * 
     * @return string $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the width
     * 
     * @param string $width
     * @return void
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Returns the height
     * 
     * @return string $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the height
     * 
     * @param string $height
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Returns the alignment
     * 
     * @return string $alignment
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * Sets the alignment
     * 
     * @param string $alignment
     * @return void
     */
    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    /**
     * Returns the listtheme
     * 
     * @return string $listtheme
     */
    public function getListtheme()
    {
        $this->listtheme = end(explode('/', $this->theme));
        return $this->listtheme;
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
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }
}
