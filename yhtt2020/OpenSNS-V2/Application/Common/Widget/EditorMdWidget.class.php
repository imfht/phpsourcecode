<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/16 0016
 * Time: 下午 2:38
 */
namespace Common\Widget ;

use Think\Controller ;

class EditorMdWidget extends Controller{

    /**editor.md  增加编辑页面
     * @param string $id
     * @param string $name
     * @param string $default
     * @param string $config
     * @author szh(施志宏) szh@ourstu.com
     */
    public function markDown($id = 'content', $name = 'content', $default='', $config='') {
        $this->assign('id', $id) ;
        $this->assign('name', $name) ;
        $this->assign('default', $default) ;
        if($config == ''){
            $config = "width: \"100%\",
            height: 600,
            path : \"./Public/static/editor.md/lib/\", // Autoload modules mode, codemirror, marked... dependents libs path
            codeFold : true,
            saveHTMLToTextarea : true,
            searchReplace : true,
            htmlDecode : \"style,script,iframe|on*\",
            emoji : true,
            taskList : true,
            tocm            : true,         // Using [TOCM]
            tex : true,                   // 开启科学公式TeX语言支持，默认关闭
            flowChart : true,             // 开启流程图支持，默认关闭
            sequenceDiagram : true,       // 开启时序/序列图支持，默认关闭,
            imageUpload : true,
            imageFormats : [\"jpg\", \"jpeg\", \"gif\", \"png\", \"bmp\", \"webp\"],
            imageUploadURL : U('Core/File/markDownUpload')" ;
        }
        $this->assign('config', trim($config, ',')) ;
        $this->display(T('Application://Common@Widget/markdown')) ;
    }

    /**页面展示编辑好的文章
     * <code>
     *      {:W('Common/EditorMd/showMarkDown',array($content))}
     * </code>
     * @param $content       文章内容
     * @param string $id     获取文章所在盒子的id
     * @param string $class  样式名称
     * @param boolean $load  判断载入js与css
     * @author szh(施志宏) szh@ourstu.com
     */
    public function showMarkDown($content, $id='html-markdown', $class='', $load=true) {
        $this->assign('content', $content) ;
        $this->assign('id', $id) ;
        $this->assign('class', $class) ;
        $this->assign('load', $load) ;
        $this->display(T('Application://Common@Widget/showMarkDown')) ;
    }

}