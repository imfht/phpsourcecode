<?php
namespace app\index\controller;
class Index extends \app\index\controller\Common
{

    public function index()
    {

        // seo设置
        $this->assign('seo', [
            'title' => $this->seo['sitename'] . ' - ' . $this->seo['title'],
            'keywords' => $this->seo['keywords'],
            'description' => $this->seo['description'],
        ]);

        return $this->fetch();
    }

}