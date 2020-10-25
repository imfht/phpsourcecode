<?php
namespace app\common\paginator;
use think\paginator\driver\Bootstrap;
class HomePage extends Bootstrap{
    /**
     * 渲染分页html
     * @return mixed
     */
    public function render(){
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<ul class="pager">%s %s</ul>',
                    $this->getPreviousButton(),
                    $this->getNextButton()
                );
            } else {
                return sprintf(
                    '%s %s %s %s %s',
                    $this->getFirstButton(),
                    $this->getPreviousButton(),
                    $this->getLinks(),
                    $this->getNextButton(),
                    $this->getLastButton()
                );
            }
        }
    }
    /**
     * 首页按钮
     * @param string $text
     * @return string
     */
    protected function getFirstButton($text = "首页")
    {
        $url = $this->url(1);
        return $this->getPageLinkWrapper($url, $text);
    }
    /**
     * 末页按钮
     * @param string $text
     * @return string
     */
    protected function getLastButton($text = "末页")
    {
        $url = $this->url($this->lastPage);
        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "上一页")
    {
        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }
        $url = $this->url(
            $this->currentPage() - 1
        );
        return $this->getPageLinkWrapper($url, $text);
    }
    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '下一页')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text);
    }
    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<a href="' . htmlentities($url) . '">' . $page . '</a>';
    }
    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<a href="#">' . $text . '</a>';
    }
    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<span class="laypage-curr">' . $text . '</span>';

    }
}