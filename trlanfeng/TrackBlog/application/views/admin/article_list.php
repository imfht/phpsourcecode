<!-- content start -->
<div class="admin-content">

    <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">栏目</strong> /
            <small><?php echo $catname; ?></small>
        </div>
    </div>

    <div class="am-g">
        <div class="am-u-sm-12 am-u-md-6">
            <div class="am-btn-toolbar">
                <div class="am-btn-group am-btn-group-xs">
                    <a href="/admin/article/add" class="am-btn am-btn-default"><span class="am-icon-plus"></span> 新增
                    </a>
                </div>
            </div>
        </div>
        <div class="am-u-sm-12 am-u-md-3">
            <div class="am-form-group">
                <select data-am-selected="{btnSize: 'sm'}">
                    <option value="0">所有类别</option>
                    <?php foreach ($catlist as $cat): ?>
                        <option value="<?php echo $cat['id'] ?>"><?php echo $cat['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="am-u-sm-12 am-u-md-3">
            <div class="am-input-group am-input-group-sm">
                <input type="text" class="am-form-field">
          <span class="am-input-group-btn">
            <button class="am-btn am-btn-default" type="button">搜索</button>
          </span>
            </div>
        </div>
    </div>

    <div class="am-g">
        <div class="am-u-sm-12">
            <form class="am-form">
                <table class="am-table am-table-striped am-table-hover table-main">
                    <thead>
                    <tr>
                        <th class="table-check"><input type="checkbox"/></th>
                        <th class="table-id">ID</th>
                        <th class="table-title">标题</th>
                        <th class="table-type">类别</th>
                        <th class="table-date am-hide-sm-only">修改日期</th>
                        <th class="table-set">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($articleList as $row): ?>
                        <tr>
                            <td><input type="checkbox"/></td>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <a href="<?php echo '/admin/article/edit/' . $row['id']; ?>"><?php echo $row['name']; ?></a>
                            </td>
                            <td><?php echo $row['catname']; ?></td>
                            <td class="am-hide-sm-only"><?php echo $row['datetime']; ?></td>
                            <td>
                                <div class="am-btn-toolbar">
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-xs"
                                           href="<?php echo '/article/' . $row['id']; ?>"><span
                                                class="am-icon-copy"></span> 查看
                                        </a>
                                        <a class="am-btn am-btn-default am-btn-xs am-text-secondary"
                                           href="<?php echo '/admin/article/edit/' . $row['id']; ?>"><span
                                                class="am-icon-pencil-square-o"></span> 编辑
                                        </a>
                                        <a class="am-btn am-btn-default am-btn-xs am-text-danger"
                                           href="<?php echo '/admin/article/delete/' . $row['id']; ?>">
                                            <span class="am-icon-trash-o"></span> 删除
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="am-cf">
                    共 15 条记录
                    <div class="am-fr">
                        <ul class="am-pagination">
                            <li class="am-disabled"><a href="#">«</a></li>
                            <li class="am-active"><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                            <li><a href="#">»</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<!-- content end -->