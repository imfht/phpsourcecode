<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');
?>
<style>
.apriv .add-on {
    display: block;
    float: left;
}
</style>
<link rel="stylesheet" href="./app/admincp/ui/jquery/treeview-0.1.0.css" type="text/css" />
<script type="text/javascript" src="./app/admincp/ui/template-3.0.js"></script>
<script type="text/javascript" src="./app/admincp/ui/jquery/treeview-0.1.0.js"></script>
<script type="text/javascript" src="./app/admincp/ui/jquery/treeview-0.1.0.async.js"></script>
<script id="mpriv_item" type="text/html">
    <div class="input-prepend input-append">
  <span class="add-on"><input type="checkbox" name="config[mpriv][]" value="{{priv}}"></span>
  {{if caption=='-'}}
  <span class="add-on tip" title="分隔符权限,仅为UI美观">────────────</span>
  {{else}}
  <span class="add-on">{{caption}}</span>
  {{/if}}
</div>
</script>
<script type="text/javascript">
$(function() {
    var mpriv = <?php echo json_encode($rs->config['mpriv']);?>,
        cpriv = <?php echo json_encode($rs->config['cpriv']);?>;

    set_select(mpriv, '#<?php echo admincp::$APP_NAME;?>-mpriv');
    set_select(cpriv, '#<?php echo admincp::$APP_NAME;?>-cpriv');
    set_select(<?php echo json_encode($rs->config['apriv']);?>, '#<?php echo admincp::$APP_NAME;?>-apriv');
});

function get_tree(url, e, callback) {
    return $("#" + e + "_tree").treeview({
        tpl: e + '_item',
        url: url,
        callback: callback,
        collapsed: false,
        animated: "medium",
        control: "#" + e + "_treecontrol"
    });
}

function set_select(vars, el) {
    if (!vars) return;

    $.each(vars, function(i, val) {
        var input = $('input[value="' + val + '"]', $(el));
        input.prop("checked", true)
        $.uniform.update(input);
    });
}
var doc = $(document);
doc.on("click", '.cpriv_all', function() {
    var target,
        dv = $(this).attr('data-value'),
        ty = $(this).attr('data-type'),
        checkedStatus = $(this).prop("checked");
    // $(".checkAll").prop("checked", checkedStatus);
    if (ty == 'v') {
        target = $("[value$='" + dv + "']");
    } else if (ty == 'r') {
        var cids = $.parseJSON(dv);
        $.each(cids, function(index, val) {
            var el = $('input:checkbox', $("[cid='" + val + "']"));
            el.each(function() {
                this.checked = checkedStatus;
                $.uniform.update($(this));
            });
        });
        var pp = $(this).parents('tr');
        target = $('input:checkbox', pp);
    } else {
        target = $("[value^='" + dv + "']");
    }
    target.each(function() {
        this.checked = checkedStatus;
        $.uniform.update($(this));
    });
});
doc.on("click", '.menupriv', function() {
    menu_priv(this)
});
function menu_priv(a){
    var target, mid = $(a).attr('mid'),
        pid = $(a).attr('pid'),
        checkedStatus = $(a).prop("checked");
    target = $('[pid="' + mid + '"]');
    target.each(function() {
        this.checked = checkedStatus;
        $.uniform.update($(this));
        menu_priv(this);
    });
}

</script>
<style>
    .separator .checker{margin-top: -20px !important;}
</style>
<div id="<?php echo admincp::$APP_NAME;?>-mpriv" class="tab-pane hide">
    <div class="input-prepend input-append">
        <span class="add-on">全选</span>
        <span class="add-on">
            <input type="checkbox" class="checkAll checkbox" data-target="#<?php echo admincp::$APP_NAME;?>-mpriv" />
        </span>
        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i> 提交</button>
    </div>
    <div class="clearfloat mb10"></div>
    <div class="input-prepend input-append">
        <span class="add-on"><i class="fa fa-cog"></i> 全局权限</span>
        <span class="add-on">::</span>
        <span class="add-on"><input type="checkbox" name="config[mpriv][]" value="ADMINCP" /> 允许登陆后台</span>
    </div>
    <div class="clearfloat mb10"></div>
    <span class="alert alert-danger">注:此处权限设置为后台的菜单权限是否显示,设置后还要设置具体的相关应用权限,否刚有可能出错</span>
    <div class="clearfloat mb10"></div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>菜单</th>
            </tr>
        </thead>
        <tbody>
<?php
    function menu_priv($M,$level,$child,$pid){
      $ltag = ($level=='1'?"":"├ ");
      $name = $M['children']?'<b>'.$M['caption'].'</b>':$M['caption'];
      echo '
      <tr>
        <td>　'.str_repeat("│　", $level-1).$ltag.'<input type="checkbox" class="menupriv" mid="'.$M['id'].'" pid="'.$pid.'" name="config[mpriv][]" value="'.$M['priv'].'">'.$name.'</td>
      </tr>
      ';
    }
    menu::$callback['func'] = 'menu_priv';
?>
<?php menu::func();?>
        </tbody>
    </table>
</div>
<div id="<?php echo admincp::$APP_NAME;?>-cpriv" class="tab-pane hide">
    <div class="input-prepend input-append">
        <span class="add-on">全选</span>
        <span class="add-on">
            <input type="checkbox" class="checkAll checkbox" data-target="#<?php echo admincp::$APP_NAME;?>-cpriv" />
        </span>
        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i> 提交</button>
    </div>
    <div class="clearfloat mb10"></div>
    <div class="input-prepend input-append">
        <span class="add-on"><i class="fa fa-cog"></i> 全局权限</span>
        <span class="add-on">::</span>
        <span class="add-on">允许添加顶级栏目</span>
        <span class="add-on"><input type="checkbox" name="config[cpriv][]" value="0:a" /></span>
    </div>
    <div class="clearfloat mb10"></div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>栏目名</th>
                <th>APPID</th>
                <th>#</th>
                <th rowspan="1" colspan="4">栏目权限</th>
                <th>#</th>
                <th rowspan="1" colspan="4">内容权限</th>
            </tr>
        </thead>
        <tbody>
            <td></td>
            <td></td>
            <td>-</td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":s"></td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":a" /></td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":e" /></td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":d" /></td>
            <td>-</td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":cs" /></td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":ca" /></td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":ce" /></td>
            <td><input type="checkbox" class="cpriv_all" data-type="v" data-value=":cd" /></td>
<?php
    $GLOBALS['appArray'] = iCache::get('app/idarray');
    function category_priv($C,$level,$child){
      $ltag = ($level=='1'?"":"├ ");
      $name = $C['rootid']?$C['name']:'<b>'.$C['name'].'</b>';
      $app  = $GLOBALS['appArray'][$C['appid']];
      if(!$C['rootid'] && $child){
        $checkbox = '<input type="checkbox" class="cpriv_all" data-type="r" data-value=\''.json_encode(array_values($child)).'\'>';
      }
      echo '
    <tr cid="'.$C['cid'].'">
      <td>'.str_repeat("│　", $level-1).$ltag.$name.' [cid:'.$C['cid'].']</td>
      <td>'.$app['name'].' [appid:'.$C['appid'].']</td>
      <td style="text-align: right;">'.$checkbox.' <input type="checkbox" class="cpriv_all" data-type="h" data-value="'.$C['cid'].':"></td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':s">查询</td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':a" />添加子级</td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':e" />编辑</td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':d" />删除</td>
      <td>-</td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':cs" />查询</td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':ca" />添加</td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':ce" />编辑</td>
      <td><input type="checkbox" name="config[cpriv][]" value="'.$C['cid'].':cd" />删除</td>
    </tr>
      ';
    };
    category::$callback['func'] = 'category_priv';
?>
            <?php category::func();?>
        </tbody>
    </table>
</div>
<div id="<?php echo admincp::$APP_NAME;?>-apriv" class="tab-pane hide apriv">
    <div class="input-prepend input-append">
        <span class="add-on">全选</span>
        <span class="add-on">
            <input type="checkbox" class="checkAll checkbox" data-target="#<?php echo admincp::$APP_NAME;?>-apriv" />
        </span>
        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i> 提交</button>
    </div>
    <div class="clearfloat"></div>
    <table class="table table-bordered table-condensed table-hover">
        <thead>
            <tr>
                <th><i class="fa fa-arrows-v"></i></th>
                <th style="width:36px;">appid</th>
                <th style="width:72px;">应用</th>
                <th style="width:90px;">应用</th>
                <th>权限</th>
                <th>附加权限</th>
            </tr>
        </thead>
        <tbody>
            <?php function apps_priv($priv,$value){
                if(empty($priv)) return;
                ksort($priv);
                echo '<tr id="'.$value['app'].'_apriv">';
                echo '<td><input type="checkbox" class="checkAll checkbox" data-target="#'.$value['app'].'_apriv"/></td>';
                echo '<td>'.$value['id'].'</td>';
                echo '<td>'.$value['app'].'</td>';
                echo '<td>'.$value['name'].'</td>';
                echo '<td>';
                echo '<div class="input-prepend input-append">';
                echo implode('', $priv);
                echo '</div>';
                echo '</td>';
                echo '<td>';
                echo '<div class="input-prepend input-append">';
                echo '  <span class="add-on"><input type="checkbox" name="config[apriv][]" value="'.$value['app'].'.VIEW" /> 查看所有'.$value['title'].'</span>';
                echo '  <span class="add-on"><input type="checkbox" name="config[apriv][]" value="'.$value['app'].'.EDIT" /> 编辑所有'.$value['title'].'</span>';
                echo '  <span class="add-on"><input type="checkbox" name="config[apriv][]" value="'.$value['app'].'.DELETE" /> 删除所有'.$value['title'].'</span>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }?>
            <?php apps_hook::$callback['app_priv'] = 'apps_priv';?>
            <?php echo apps_hook::get_app_priv();?>
        </tbody>
    </table>
</div>
