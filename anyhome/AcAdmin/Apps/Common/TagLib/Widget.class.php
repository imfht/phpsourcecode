<?php    
namespace Common\TagLib;
use Think\Template\TagLib;
defined('THINK_PATH') or exit();
class Widget extends TagLib {
    protected $tags = array(
        'show' => array('attr' => 'col,actions,title,icon,bg,cls,hdcls,bdcls','level'=>5,'alias'=>'iterate'),
        'action' => array('attr' => 'col,actions,title,icon,bg,cls,hdcls,bdcls','level'=>5,'alias'=>'iterate'),
    );

    public function _show($attr,$content) {
        extract($attr);

        $_col = "col col-md-12 col-sm-21 col-xs-12 col-lg-12";
        if ($col) $_col = 'col col-md-'.$col.' col-sm-'.$col.' col-xs-'.$col.' col-lg-'.$col;


        if ($title) $caption = '<div class="caption">'.$title.'</div>';


        
        $actions_tpl = '';
        if ($actions) {
            $acs_arr = explode(',', $actions);
            foreach ($acs_arr as $k) {
                $actions_tpl .= $_actions[$k];
            }
            if ($actions_tpl) {
                $actions_tpl = '<div class="tools">'.$actions_tpl.'</div>';
            }
        }
        
        // $actions_tpl = $this->tpl->parse($actions_tpl);


        $_cls = 'block';
        if ($cls) $_cls .= ' '.$cls;

        $_bdcls = 'block-content-outer';
        if ($bdcls) $_bdcls .= ' '.$bdcls;

        $regAction   = '/<Widget:action.*?>(.*?)<\/Widget:action>/is';
        $actions_tpl = '';
        if (preg_match_all($regAction,$content,$matches)) {
            // $content = preg_replace($regAction,"", $content);
            $v = $matches[0];
            for ($i=0; $i < count($v); $i++) { 
                $actions_tpl .= $this->tpl->parse($v[$i]);
                // $content = preg_replace($v[$i],"", $content);
                $content = str_replace($v[$i],"", $content);
            }
        }

        $content = $this->tpl->parse($content);
        $hd = $icon.$caption.$actions_tpl;

        $_hdcls = 'block-heading';
        if($id)  $_id = 'id="'.$id.'"';
        if ($hdcls) $_hdcls = $hdcls;
        if ($hd) {
            $hd_str = '<div '.$_id.' class="'.$_hdcls.'">
                        <div class="main-text h2">
                        '.$icon.$caption.'</div>'
                        .$actions_tpl.'
                    </div>';
        }
        $str = '<div class="'.$_col.'">
                    <div class="'.$_cls.'">
                        '.$hd_str.'
                        <div class="'.$_bdcls.'">
                        <div class="block-content-inner">
                            '.$content.'
                        </div>
                        </div>
                    </div>
                </div>';
        return $str;
    }

    public function _action($attr,$content){
        extract($attr);

        if($id)  $_id = 'id="'.$id.'"';
        $content = $this->tpl->parse($content);
        $_cls = "block-controls";
        if($cls == 'tools') $_cls = "block-controls";
        if ($content) {
            $str = '<div '.$_id.' class="'.$_cls.'">'.$content.'</div>';
        }
        return $str;
    }

    
}