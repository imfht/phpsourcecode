<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 模板解析器
 * @author sigmazel
 * @since v1.0.2
 */
class _ilinei{
    //获取模板页面，前台缓存
    public function load($theme){
        //加载模板页面缓存
        $pages = cache_read('pages');
        if(!empty($pages)) return $pages;

        $pages = array();

        $theme = strtr($theme, '\\', '/');
        $info_xml = (array)simplexml_load_file(ROOTPATH."/{$theme}/_info.xml");
        $_info_xml_pages = (array)$info_xml['pages'];

        if(is_array($_info_xml_pages['page'])){
            foreach($_info_xml_pages['page'] as $key => $page){
                $page = (array)$page;
                //取出当前结点，指定类型为页面
                $page = $page['@attributes'];
                $page['type'] = 'page';
                $pages[$page['file']] = $page;
            }
        }else{
            $page = (array)$_info_xml_pages['page'];

            //取出当前结点，指定类型为页面
            $page = $page['@attributes'];
            $page['type'] = 'page';

            $pages[$page['file']] = $page;
        }

        //写入缓存
        cache_write('pages', $pages);

        return $pages;
    }

    //获取当前模板的页面+块，后台编辑暂不缓存了。
    public function fetch($path){
        global $setting;

        //模板路径指定文件夹为page
        if($path != '/page') return array();

        $themes = explode('/', $setting['SiteTheme']);

        //如果当前模板无描述文件，拜拜吧
        if(!is_file(ROOTPATH."/tpl/{$themes[1]}/_info.xml")) return array();

        $pages = array();
        $info_xml = (array)simplexml_load_file(ROOTPATH."/tpl/{$themes[1]}/_info.xml");
        $_info_xml_pages = (array)$info_xml['pages'];

        if(is_array($_info_xml_pages['page'])){
            foreach($_info_xml_pages['page'] as $key => $page){
                $page = (array)$page;

                //取出当前结点，指定类型为页面
                $page = $page['@attributes'];
                $page['type'] = 'page';

                $pages[$page['file']] = $page;
            }
        }else{
            $page = (array)$_info_xml_pages['page'];

            //取出当前结点，指定类型为页面
            $page = $page['@attributes'];
            $page['type'] = 'page';

            $pages[$page['file']] = $page;
        }

        if(is_array($_info_xml_pages['block'])){
            foreach($_info_xml_pages['block'] as $key => $page){
                $page = (array)$page;

                //取出当前结点，指定类型为块
                $page = $page['@attributes'];
                $page['type'] = 'block';

                $pages[$page['file']] = $page;
            }
        }else{
            $page = (array)$_info_xml_pages['block'];

            //取出当前结点，指定类型为块
            $page = $page['@attributes'];
            $page['type'] = 'block';

            $pages[$page['file']] = $page;
        }

        return $pages;
    }

    //取出页面+块头部描述
    public function desc($file){
        $lines = get_file_lines($file, 2);

        if(substr($lines[0], 0, 10) == '<!--{@page') return substr(str_replace('}-->', '', $lines[0]), 10);
        elseif(substr($lines[0], 0, 11) == '<!--{@block') return substr(str_replace('}-->', '', $lines[0]), 11);

        return '';
    }

    //添加页面
    public function append($pages, $page){
        global $setting;

        $info_xml = (array)simplexml_load_file(ROOTPATH."/{$setting[SiteTheme]}/_info.xml");

        $info_xml_content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<theme>
	<application>{$info_xml[application]}</application>
	<name>{$info_xml[name]}</name>
	<version>{$info_xml[version]}</version>
	<author>{$info_xml[author]}</author>
	<thumb>{$info_xml[thumb]}</thumb>
	<pages>";

        foreach($pages as $key => $item){
            if(empty($item['file'])) continue;

            $info_xml_content .= "
        <{$item[type]} file=\"{$item[file]}\" name=\"{$item[name]}\"/>";
        }

        $info_xml_content .= "
        <{$page[type]} file=\"{$page[file]}\" name=\"{$page[name]}\"/>
    </pages>
</theme>";

        //写入_info.xml文件
        file_put_contents(ROOTPATH."/{$setting[SiteTheme]}/_info.xml", $info_xml_content);

        //删除缓存
        cache_delete('pages');
    }

    //删除页面
    public function delete($pages, $page){
        global $setting;

        $info_xml = (array)simplexml_load_file(ROOTPATH."/{$setting[SiteTheme]}/_info.xml");

        $info_xml_content = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<theme>
	<application>{$info_xml[application]}</application>
	<name>{$info_xml[name]}</name>
	<version>{$info_xml[version]}</version>
	<author>{$info_xml[author]}</author>
	<thumb>{$info_xml[thumb]}</thumb>
	<pages>";

        foreach($pages as $key => $item){
            if(empty($item['file']) || $item['file'] == $page) continue;

            $info_xml_content .= "
        <{$item[type]} file=\"{$item[file]}\" name=\"{$item[name]}\"/>";
        }

        $info_xml_content .= "
    </pages>
</theme>";

        //写入_info.xml文件
        file_put_contents(ROOTPATH."/{$setting[SiteTheme]}/_info.xml", $info_xml_content);

        //删除缓存
        cache_delete('pages');
    }

    //TAG模板
    public function blocks($cache = false){
        //如果当前模板无描述文件，拜拜吧
        if(!is_file(ROOTPATH."/tpl/_res/block/_info.xml")) return array();

        //加载TAG模板缓存
        if($cache){
            $blocks = cache_read('blocks');
            if(!empty($blocks)) return $blocks;
        }

        $blocks = array();
        $block_list = array();

        $info_xml = (array)simplexml_load_file(ROOTPATH."/tpl/_res/block/_info.xml");
        if(is_array($info_xml['block'])) $block_list = $info_xml['block'];
        else $block_list[] = (array)$info_xml['block'];

        foreach($block_list as $key => $block){
            $tmp = is_array($block) ? $block : (array)$block;

            $tmp['params'] = (array)$tmp['params'];
            if(is_array($tmp['params']['param'])) $tmp['_params'] = $tmp['params']['param'];
            else $tmp['_params'][] = (array)$tmp['params']['param'];

            $tmp['params'] = array();
            foreach($tmp['_params'] as $key => $param){
                $param = is_array($param) ? $param : (array)$param;
                $param = $param['@attributes'];
                if($param['id'] == 'model') $param['value'] = str_replace('\\', '\\\\', $param['value']);
                $tmp['params'][] = $param;
            }

            $tmp['themes'] = (array)$tmp['themes'];
            if(is_array($tmp['themes']['theme'])) $tmp['_themes'] = $tmp['themes']['theme'];
            else $tmp['_themes'][] = (array)$tmp['themes']['theme'];

            $tmp['themes'] = array();
            foreach($tmp['_themes'] as $key => $theme){
                $theme = is_array($theme) ? $theme : (array)$theme;
                $theme = $theme['@attributes'];
                $tmp['themes'][] = $theme;
            }

            unset($tmp['_params']);
            unset($tmp['_themes']);

            //只有参数和样式才有效！！
            if(count($tmp['params']) > 0 && count($tmp['themes']) > 0) $blocks[$tmp['key']] = $tmp;

            unset($tmp);
        }

        //写入缓存
        if($cache) cache_write('blocks', $blocks);

        return $blocks;
    }

    //获取TAG的model,method,var,theme
    public function block($params){
        $rtn = array(
            'class' => '', //模型类
            'model' => '', //模型名称
            'method' => '', //模型TAG方法
            'var' => '', //返回变量名
            'theme' => '' //风格模板
        );

        $blocks = $this->blocks(true);
        $block = $blocks[$params['key']];

        foreach($block['params'] as $key => $param){
            if($param['id'] == 'model'){
                $rtn['class'] = str_replace('\\\\', '\\', $param['value']);
                $rtn['model'] = substr($param['value'], strrpos($param['value'], '\\') + 1);
            }elseif($param['id'] == 'method'){
                $rtn['method'] = $param['value'];
            }elseif($param['id'] == 'var'){
                $rtn['var'] = $param['value'];
            }
        }

        foreach($block['themes'] as $key => $theme){
            if($theme['id'] == $params['theme']){
                $rtn['theme'] = $theme['file'];
                break;
            }
        }

        return $rtn;
    }

    //解析TAG
    public function parse($params){
        if($params['key'] == 'blank') return '<!--{block file=""}-->';
        if($params['key'] == 'block') return '<!--{block file="'.$params['file'].'"}-->';

        $blocks = $this->blocks();

        //不存在TAG
        if(!$blocks[$params['key']]) return '';

        $block = 'key="'.$params['key'].'" ';

        $block_params = $blocks[$params['key']]['params'];
        foreach($block_params as $key => $param){
            if($param['readonly']){
                $block .= $param['id'].'="'.$param['value'].'" ';
            }elseif($param['type'] == 'string' || $param['type'] == 'number' || $param['type'] == 'page'){
                $block .= $param['id'].'="'.$params[$param['id']].'" ';
            }elseif($param['type'] == 'checkbox'){
                if(!empty($params[$param['id']])){
                    $block .= $param['id'].'="true" ';
                }
            }
        }

        $block .= 'theme="'.$params['theme'].'"';
        $block = str_replace('\\\\', '\\', $block);

        return '<!--{block '.$block.'}-->';
    }

    //块TAG
    public static function block_tags($var){
        global $_var;

        $GLOBALS['_ILINEI_ID'] = $GLOBALS['_ILINEI_ID'] + 1;

        if(strexists($var, 'file="meta"')) return "<!--{TAG_BLOCK file=\"meta\"}-->";

        $code_prev = $code = $code_next =  "";

        $code_prev =  "";
        foreach($_var["gp__ilinei_id_{$GLOBALS[_ILINEI_ID]}_prev"] as $key => $prev){
            $temp = str_replace(array('{block', '}'), '', stripcslashes($prev));
            $code_prev .= "<!--{TAG_BLOCK{$temp}}-->\r\n";
            unset($temp);
        }

        $code_next = "";
        foreach($_var["gp__ilinei_id_{$GLOBALS[_ILINEI_ID]}_next"] as $key => $next){
            $temp = str_replace(array('{block', '}'), '', stripcslashes($next));
            $code_next .= "<!--{TAG_BLOCK{$temp}}-->\r\n";
            unset($temp);
        }

        //当前TAG
        if($_var["gp__ilinei_id_{$GLOBALS[_ILINEI_ID]}"]){
            $code = stripcslashes($_var["gp__ilinei_id_{$GLOBALS[_ILINEI_ID]}"]);
            $code = str_replace(array('{block', '}'), '', $code);
            $code = "<!--{TAG_BLOCK{$code}}-->";
        }

        return $code_prev.$code.$code_next;
    }

    //保存解析
    public function save($file){
        global $_var;

        if(!@$fp = fopen($file, 'r')) return '';
        $template = @fread($fp, filesize($file));
        fclose($fp);

        $content = $template;
        $content = preg_replace("/([\n\r]+)\t+/s", "\\1", $content);

        $template = preg_replace("/\<\!\-\-\{block(.+?)\}\-\-\>/s", "{block\\1}", $content);
        $template = preg_replace_callback("/\{block\s+(.+?)\}/is", create_function('$matches', 'return \admin\model\_ilinei::block_tags($matches[1]);'), $template);

        $body_prev =  "";
        foreach($_var["gp_body_prev"] as $key => $prev){
            $temp = str_replace(array('{block', '}'), '', stripcslashes($prev));
            $body_prev .= "<!--{TAG_BLOCK{$temp}}-->\r\n";
            unset($temp);
        }

        $body_next = "";
        foreach($_var["gp_body_next"] as $key => $next){
            $temp = str_replace(array('{block', '}'), '', stripcslashes($next));
            $body_next .= "<!--{TAG_BLOCK{$temp}}-->\r\n";
            unset($temp);
        }

        //这个是页面，还有一个是块头
        if(strpos($template, '<body>') === false){
            $lines = explode("\r\n", $template);

            if(strexists($lines[0], '@block')) $template = $lines[0]."\r\n".$body_prev.implode("\r\n", array_slice($lines, 1));
            else $template = $body_prev.$template;
        }else $template = str_replace('<body>', "<body>{$body_prev}", $template);

        if(strpos($template, '</body>') === false) $template = $template.$body_next;
        else $template = str_replace('</body>', $body_next."</body>", $template);

        $template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
        $template = str_replace('TAG_BLOCK', 'block', $template);

        return $template;
    }
}
?>