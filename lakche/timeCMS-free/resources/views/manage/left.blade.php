<div class="list-group">
    <a class="list-group-item active">控制面板</a>
    <a class="list-group-item" href="{{ url('admin') }}">系统统计</a>
    <a class="list-group-item" href="{{ route('admin.system.index') }}">系统设置</a>
    <a class="list-group-item" href="{{ route('admin.category.index') }}">分类管理</a>
    <a class="list-group-item" href="{{ route('admin.articles.index') }}">文章管理</a>
    <a class="list-group-item" href="{{ route('admin.projects.index') }}">项目管理</a>
    <a class="list-group-item" href="{{ route('admin.persons.index') }}">人物管理</a>
    <a class="list-group-item" href="{{ route('admin.pages.index') }}">单页管理</a>
    <a class="list-group-item" href="{{ route('admin.users.index') }}">用户管理</a>
    <a class="list-group-item" href="{{ route('admin.menus.index') }}">菜单管理</a>
    <a class="list-group-item" href="{{ route('admin.friendLinks.index') }}">友情链接</a>
    <a class="list-group-item" href="{{ route('admin.adspaces.index') }}">广告位管理</a>
    <a class="list-group-item" href="{{ route('admin.adimages.index') }}">广告管理</a>
    <a class="list-group-item" href="{{ route('admin.comments.index') }}">留言管理</a>
    <a class="list-group-item" href="{{ url('admin/logs') }}">系统日志</a>
    <a class="list-group-item" href="{{ url('auth/logout') }}">退出</a>
</div>
