<?php
/**
 * 工作台欢迎页
 */
namespace Bench\Controller;
use Think\Controller;
class WelcomeController extends Controller {
    public function _empty() {
        C('FRAME_ACTIVE', ACTION_NAME);
        $this->display('Welcome/bench');
    }

    public function themeAction() {
        if(IS_AJAX) {
            $theme = I('post.theme');
            if(!empty($theme)) {
                cookie('template_theme', $theme, 31536000);
            }
        }
    }
}