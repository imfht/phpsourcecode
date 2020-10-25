<?php
namespace app\system\controller;
/*
*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/5/2
*/
use app\base\controller\System;
use app\system\model\Menu;

class Fast extends System
{
    /**
     * 快速生成列表
     * @return string
     */
    public function fastlist()
    {
        if($this->request->isAjax()){
            $post_data = $this->request->param();
            $dir = explode('/',$post_data['menu_dir']);
            $file_path = '..'.DS.'application'.DS.$dir[0].DS.'view'.DS.$dir[1];
            if(!is_dir($file_path)){mkdir($file_path,0777,true);}//判断目录是否存在，不存在则创建
            $file_name = $dir[2];
            $code1 = <<<CHUXIN
{extend name="base@:style" /}
{block name="body"}
<div class="container-fluid larry-wrapper">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <!--头部搜索-->
            <section class="panel panel-padding">
                <form class="layui-form" action='{:url("{$post_data['json_url']}")}'>
                    <div class="layui-form">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input class="layui-input" name="keyword" placeholder="关键字">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <button lay-submit class="layui-btn" lay-filter="search">查找</button>
                        </div>
                    </div>
                </form>
            </section>
            <!--列表-->
            <section class="panel panel-padding">
                <div class="group-button">
                    <button class="layui-btn layui-btn-small modal-catch"
                            data-params='{"content": ".add-subcat", "title": "添加{$post_data['listname_zh']}","type":"1","area":"500px","data":"id=0"}'>
                        <i class="iconfont">&#xe649;</i> 添加
                    </button>

                    <button class="layui-btn layui-btn-small layui-btn-danger ajax-all" data-name="checkbox"
                            data-params='{"url": "allDelete","data":"model={$post_data['module']}"}'>
                        <i class="iconfont">&#xe626;</i> 删除
                    </button>
                </div>
                <div id="list" class="layui-form"></div>

                <div class="text-right" id="page"></div>
            </section>
        </div>
    </div>
</div>
<div class="add-subcat">
    <form id="form1" class="layui-form layui-form-pane" action="save">
        
        <div class="layui-form-item">
            <label class="layui-form-label">模板代码</label>
            <div class="layui-input-block">
                <input type="text" name="name" required jq-verify="required" jq-error="错误提示信息" placeholder="提示信息"
                       autocomplete="off" class="layui-input ">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-inline">
                <input type="text" name="orders" required jq-verify="number" value="99" jq-error="只有数字才能排序"
                       placeholder="数字越小越排在前面" autocomplete="off" class="layui-input ">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-inline">
                <input type="radio" name="state" title="显示" value="1" checked/>
                <input type="radio" name="state" title="隐藏" value="0"/>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" jq-submit jq-filter="submit">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
        <input type="hidden" name="id" value="0"/>
    </form>
</div>
{/block}
{block name="each"}
<script id="list-tpl" type="text/html" data-params='{"url":"{:url("{$post_data['json_url']}")}","data":"{$post_data['json_data']}","dataName":"{$file_name}CatData","pageid":"#page"}'>
    <table id="example" class="layui-table lay-even" data-name="{$file_name}CatData" data-tplid="list-tpl">
        <thead>
        <tr>
            <th width="30"><input type="checkbox" id="checkall" data-name="checkbox" lay-filter="check"
                                  lay-skin="primary"></th>
            <th width="60">序号</th>
CHUXIN;
        $code2 = <<<CHUXIN
            <th width="100">排序</th>
            <th width="80">状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        {{# layui.each(d.list, function(index, item){ }}
        <tr>
            <td><input type="checkbox" name="checkbox" value="{{ item.id}}" lay-skin="primary"></td>
            <td>{{ item.id}}</td>
CHUXIN;
        $code3 = <<<CHUXIN
            <td><input type="text" class="layui-input ajax-blur" name="orders" value="{{ item.orders}}"
                       data-params='{"url":"changeOrder","data":"model={$post_data['module']}&id={{ item.id}}"}'></td>
            <td><input type="checkbox" name="state" lay-skin="switch" lay-text="ON|OFF" {{#if (item.state){
                       }}checked="checked" {{# } }} lay-filter="ajax"
                       data-params='{"url":"changeState","data":"model={$post_data['module']}&id={{item.id}}"}'></td>
            <td>
                <button class="layui-btn layui-btn-mini modal-catch"
                        data-params='{"content": ".add-subcat","area":"500px","title":"编辑 {{ item.name}}","key":"id={{ item.id}}","type":"1"}'>
                    <i class="iconfont">&#xe653;</i>编辑
                </button>
                <button class="layui-btn layui-btn-mini layui-btn-danger ajax"
                        data-params='{"url": "forceDelete","confirm":"true","data":"model={$post_data['module']}&id={{item.id}}"}'>
                    <i class="iconfont">&#xe626;</i>删除
                </button>
            </td>

        </tr>
        {{# }); }}
        </tbody>

    </table>
</script>
{/block}

{block name="js"}
<script>
    layui.use('list');
</script>
{/block}
CHUXIN;
                $th =   '';
                $td =   '';
                $list_field = preg_split('/[,;\r\n]+/', trim($post_data['list_field'], ",;\r\n"));
                foreach ($list_field as $v){
                    $content = explode('|',$v);
                    $th .= '<th width="'.$content[2].'">'.$content[0].'</th>'."\n";
                    $td .= '<td>{{ item.'.$content[1].'}}</td>'."\n";
                }
                $body = $code1.$th.$code2.$td.$code3;
                if(!is_file($file_path.DS.$file_name.'.html')){
                    file_put_contents($file_path.DS.$file_name.'.html',$body);
                    return getMsg("快速生成成功！");
                }else{
                    return getMsg("列表文件已存在，为防止冲突，请删除后重试");
                }
        }else{
            $menu = Menu::all(['link'=>['<>','无']]);
            $this->view->assign('menu_dir',$menu);
            return $this->view->fetch();
        }
    }
}
