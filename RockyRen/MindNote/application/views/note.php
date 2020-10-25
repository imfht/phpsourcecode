

<div id="note">
  <!------------------------导航栏-------------------->
  <div id="note-nav">
    <a class="logo" href="<?=site_url('home/index')?>"><span class="mind-text">Mind</span><span class="note-text">Note</span></a>
    <span class="nav-right">
      <a class="username">你好， <?=$username?></a> |
      <a class="logout" href="<?=site_url('home/logout')?>">注销</a>
    </span>
  </div>
  <!------------------------内容---------------------->
  <div id="note-content">
    <span class="glyphicon glyphicon-arrow-right group-toggle right-arrow-icon"></span>
    <!------------------------笔记本目录---------------------->
    <div id="notebook-catalogue">
      <div class="group-toggle-line note-oper-line">
        <span class="toggle-title">笔记本</span>
        <span class="glyphicon glyphicon-arrow-left group-toggle left-arrow-icon"></span>
      </div>

      <div id="catalogue-content">
        <div class="root-group notebook-group">
          <div class="note-row">
            <span class="glyphicon glyphicon-folder-open folder-icon"></span>
            <span class="notebook-name">全部笔记本</span>
            <!--<span class="note-count badge">99</span>-->
          </div>

        </div>

        <div class="notebook-group">
          <div class="note-row">
            <span class="glyphicon glyphicon-chevron-down open-expand-icon"></span>
            <span class="glyphicon glyphicon-chevron-right close-expand-icon"></span>
            <span class="glyphicon glyphicon-folder-open folder-icon"></span>
            <span class="notebook-name">默认笔记本</span>
            <span class="glyphicon glyphicon-triangle-bottom menu-icon group-menu-icon"></span>
          </div>

        </div>

        <?php foreach($catalogue as $group):?>
        <div class="notebook-group" data-group_id="<?=$group['id']?>">
          <div class="group-row note-row">
            <span class="glyphicon glyphicon-chevron-down open-expand-icon"></span>
            <span class="glyphicon glyphicon-chevron-right close-expand-icon"></span>
            <span class="glyphicon glyphicon-folder-open folder-icon"></span>
            <span class="name"><?=$group['name']?></span>
            <span class="note-count badge"><?=count($group['notebooks'])?></span>
            <span class="glyphicon glyphicon-triangle-bottom menu-icon group-menu-icon"></span>
          </div>


          <ul class="group-list">
            <?php foreach($group['notebooks'] as $notebook):?>
            <li class="notebook-item note-row" data-notebook_id="<?=$notebook['id']?>" data-group_id="<?=$group['id']?>">
              <span class="glyphicon glyphicon-book book-icon"></span>
              <span class="name"><?=$notebook['name']?></span>
              <!--<span class="note-count badge">99</span>-->
              <span class="glyphicon glyphicon-triangle-bottom menu-icon notebook-menu-icon"></span>
            </li>
            <?php endforeach?>
          </ul>
        </div>
        <?php endforeach?>

        <!--
        <div class="notebook-group">
          <div class="note-row">
            <span class="glyphicon glyphicon-chevron-down open-expand-icon"></span>
            <span class="glyphicon glyphicon-chevron-right close-expand-icon"></span>
            <span class="glyphicon glyphicon-folder-open folder-icon"></span>
            <span class="name">笔记本2</span>
            <span class="glyphicon glyphicon-triangle-bottom menu-icon"></span>
          </div>


          <span class="notebook-menu"></span>
          <ul class="group-list">
            <li class="notebook-item note-row">
              <span class="glyphicon glyphicon-book book-icon"></span>
              <span class="name">笔记本1</span>
              <span class="note-count badge">99</span>
              <span class="glyphicon glyphicon-triangle-bottom menu-icon"></span>


            </li>

            <li class="notebook-item note-row">
              <span class="glyphicon glyphicon-book book-icon"></span>
              <span class="name">笔记本1</span>
              <span class="note-count badge">99</span>
              <span class="glyphicon glyphicon-triangle-bottom menu-icon"></span>
            </li>
            <li class="notebook-item note-row">
              <span class="glyphicon glyphicon-book book-icon"></span>
              <span class="name">笔记本1</span>
              <span class="note-count badge">99</span>
              <span class="glyphicon glyphicon-triangle-bottom menu-icon"></span>
            </li>
          </ul>
        </div>-->

      </div>
    </div>
    <!------------------------笔记目录---------------------->
    <div id="note-catalogue">
      <div class="note-add note-oper-line">
        <div class="btn-group">
          <button class="btn btn-sm oper-button" data-toggle="dropdown">
            <!--<span class="glyphicon glyphicon-plus"></span>-->
            新建笔记
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li><a data-toggle="modal" data-target="#addNoteModal">新建笔记</a></li>
            <li><a href="#">新建笔记本</a></li>
          </ul>

        </div>
      </div>
      <ul class="note-list">
        <li class="note-item active">
          <div class="item-title">
            <span class="glyphicon glyphicon-list-alt note-icon"></span>
            <span class="note-title">笔记本标题</span>
          </div>
          <div class="time">2015-05-01 22:22:22</div>
        </li>

        <li class="note-item">
          <div class="item-title">
            <span class="glyphicon glyphicon-list-alt note-icon"></span>
            <span class="note-title">笔记本标题</span>
          </div>
          <div class="time">2015-05-01 22:22:22</div>
        </li>
      </ul>
    </div>
    <!------------------------笔记主内容---------------------->
    <div id="note-main">
      <div class="note-operation note-oper-line">
        <button class="btn btn-default btn-sm oper-button note-edit-button"><span class="glyphicon glyphicon-pencil"></span> 编辑</button>
        <button class="btn btn-default btn-sm oper-button note-cancel-button"><span class="glyphicon glyphicon-remove"></span> 删除</button>
      </div>

      <div class="note-edit">

      </div>
    </div>
  </div>


</div>



<ul class="note-menu group-menu">
  <li><a class="addNotebook addNew menu-item" data-toggle="modal" data-target="#commonModal">新建笔记本</a></li>
  <li><a class="reName menu-item" data-toggle="modal" data-target="#commonModal">重命名</a></li>
  <li><a class="cancel menu-item" data-toggle="modal" data-target="#commonModal">删除</a></li>
</ul>

<ul class="note-menu notebook-menu">
  <li><a class="reName menu-item" data-toggle="modal" data-target="#commonModal">重命名</a></li>
  <li><a class="cancel menu-item" data-toggle="modal" data-target="#commonModal">删除</a></li>
  <li><a class="addNote addNew menu-item" data-toggle="modal" data-target="#commonModal">新建笔记</a></li>
  <li><a class="translateTo">移动到</a></li>
</ul>


<div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">

        <div class="commonCancel">
          你确定要删除<span class="notebook-name"></span>笔记本（组）？
        </div>

        <div class="form-group commonInputGroup">
          <label>标题</label>
          <input class="form-control" type="text" id="commonInput"/>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary commonConfirm" data-dismiss="modal">保存</button>
      </div>
    </div>
  </div>
</div>


