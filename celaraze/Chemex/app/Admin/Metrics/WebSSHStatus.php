<?php


namespace App\Admin\Metrics;


use App\Support\System;
use Dcat\Admin\Widgets\Metrics\Line;
use Illuminate\Http\Request;

class WebSSHStatus extends Line
{
    /**
     * 处理请求
     *
     * @param Request $request
     *
     * @return mixed|void
     */
    public function handle(Request $request)
    {
        $web_ssh_installed = System::checkWebSSHServiceInstalled();
        $web_ssh_service = System::checkWebSSHServiceStatus('http://127.0.0.1:8222');
        if ($web_ssh_service == 200) {
            $text = '正常';
            $color = '#00c054';
        } else {
            $text = '未启动';
            $color = '#997643';
        }
        if ($web_ssh_installed == 0) {
            $text = '未安装';
            $color = '#9f1447';
        }

        $this->withContent($text, $color);
    }

    /**
     * 设置卡片内容.
     *
     * @param $text
     * @param $color
     * @return $this
     */
    public function withContent($text, $color)
    {
        return $this->content(
            <<<HTML
<div class="d-flex justify-content-between align-items-center mt-1" style="margin-bottom: 2px">
    <h2 class="ml-1 font-lg-1" style="color: $color;">{$text}</h2>
</div>
HTML
        );
    }

    /**
     * 初始化卡片内容
     *
     * @return void
     */
    protected function init()
    {
        parent::init();

        $this->title('WebSSH服务')->height(120);
    }
}
