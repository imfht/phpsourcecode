<?php
/**
 * 文字对象
 * -------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2017-05-29 v2.0.0
 */
namespace herosphp\image;

class Text {

    const FONT_YAHEI = '/fonts/YaHei.ttf'; //字体：微软雅黑
    const FONT_YAHEI_BOLD = '/fonts/YaHeiBold.ttf'; //字体：微软雅黑粗体
    const FONT_ARIBLK = '/fonts/ariblk.ttf'; //字体：Ariblk
    /**
     * @var int 字体大小
     */
    private $fontsize = 24;

    /**
     * @var string 文字内容
     */
    private $content;

    /**
     * @var string 字体文件绝对路径
     */
    private $font = self::FONT_YAHEI;

    /**
     * @var string|array 字体颜色,支持RGB颜色数组和16进制颜色字符串
     */
    private $color = "#FFFFFF";

    /**
     * @var int 绘制文字的横坐标
     */
    private $startX = 0;

    /**
     * @var int 绘制文字的纵坐标
     */
    private $startY = 0;

    /**
     * @var int 文字倾斜的角度
     */
    private $angle = 0;

    /**
     * @var bool 是否是垂直文本
     */
    private $vertical = false;

    /**
     * @var int 文字的行间距
     */
    private $lineHeight = 20;

    /**
     * @var string 文字对齐方式
     */
    private $align = 'left';

    /**
     * @var int 文字行的最大宽度，默认为0（文字的起始横坐标到文字的右侧边的间距）
     */
    private $maxLineWidth = 0;

    /**
     * @var int 透明度，取值范围（0-127）数值越小透明度越低
     */
    private $alpha = 0;

    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getFontsize()
    {
        return $this->fontsize;
    }

    /**
     * @param int $fontsize
     * @return $this
     */
    public function setFontsize($fontsize)
    {
        $this->fontsize = $fontsize;
        return $this;
    }

    /**
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * @param string $fontPath
     * @return $this
     */
    public function setFont($fontPath)
    {
        $this->font = $fontPath;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param array|string $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return int
     */
    public function getStartX()
    {
        return $this->startX;
    }

    /**
     * @param int $startX
     * @return $this
     */
    public function setStartX($startX)
    {
        $this->startX = $startX;
        return $this;
    }

    /**
     * @return int
     */
    public function getStartY()
    {
        return $this->startY;
    }

    /**
     * @param int $startY
     * @return $this
     */
    public function setStartY($startY)
    {
        $this->startY = $startY;
        return $this;
    }

    /**
     * @return int
     */
    public function getAngle()
    {
        return $this->angle;
    }

    /**
     * @param int $angle
     * @return $this
     */
    public function setAngle($angle)
    {
        $this->angle = $angle;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVertical()
    {
        return $this->vertical;
    }

    /**
     * @param bool $vertical
     * @return $this
     */
    public function setVertical($vertical)
    {
        $this->vertical = $vertical;
        return $this;
    }

    /**
     * @return int
     */
    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    /**
     * @param int $lineHeight
     * @return $this
     */
    public function setLineHeight($lineHeight)
    {
        $this->lineHeight = $lineHeight;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * @param string $align
     * @return $this
     */
    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLineWidth()
    {
        return $this->maxLineWidth;
    }

    /**
     * @param int $maxLineWidth
     * @return $this
     */
    public function setMaxLineWidth($maxLineWidth)
    {
        $this->maxLineWidth = $maxLineWidth;
        return $this;
    }

    /**
     * @return int
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * @param int $alpha
     * @return $this
     */
    public function setAlpha($alpha)
    {
        $this->alpha = 127-$alpha;
        return $this;
    }


}