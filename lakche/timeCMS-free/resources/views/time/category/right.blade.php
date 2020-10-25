@if(isset($type) && $type->getCover() != '')
    <div class="ads">
        <img src="{{ $type->getCover() }}" alt="{{ $type->title }}">
    </div>
@endif

<div class="col-sm-12" style="margin: 24px 0 12px;">
    <form class="form-inline" action="/search" method="GET">
        <div class="form-group">
            <input type="text" class="form-control" style=" width: 120px;" id="search-key" name='key' placeholder="请输入关键字">
        </div>
        <button type="submit" class="btn btn-default">搜索</button>
    </form>
</div>
<div class="clearfix"></div>

<div class="panel panel-primary">
    <div class="panel-heading">关于荣誉殿堂</div>
    <div class="panel-body">
        <p class="info">凡是帮助我改进本系统或提出意见被采纳者，均可以加入荣誉殿堂，展示您的风采。</p>
        <p><a href="https://git.oschina.net/lakche/timeCMS-free.git" target="_blank" class="btn btn-default btn-block">开源中国仓库</a></p>
        <p><a href="https://github.com/lakche/timeCMS-free.git" target="_blank" class="btn btn-default btn-block">github仓库</a></p>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">关于静思堂</div>
    <div class="panel-body">
        <p class="info">在这里你可以把烦恼记录下来，经过一个星期的沉淀之后，你会发现一切都是浮云。</p>
        <p><a href="{{ url('page/building') }}" class="btn btn-default btn-block">开始静思</a></p>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">关于通天塔</div>
    <div class="panel-body">
        <p class="info">美丽的灵魂已安息在天堂，我们的思绪却久久不能平静，即使无数的岁月，总有一些人值得缅怀，总有一些记忆永远珍藏。</p>
        <p><a href="{{ url('page/building') }}" class="btn btn-default btn-block">开始缅怀</a></p>
    </div>
</div>
