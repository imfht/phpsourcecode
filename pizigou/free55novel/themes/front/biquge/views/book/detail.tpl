<div class="dahengfu"></div>
<div class="box_con">
    <div class="con_top">
        <div id="bdshare" class="bdshare_b" style="line-height: 12px;"><img src="http://bdimg.share.baidu.com/static/images/type-button-7.jpg" /><a class="shareCount"></a></div>
        <a href="{Yii::app()->baseUrl}">{Yii::app()->name}</a> &gt; <a href="{novel_category_link id=$book->category->id}">{$book->category->title}</a>  &gt; {$book->title}最新章节列表
    </div>
    <div id="maininfo">
        <div id="info">
            <h1>{$book->title}</h1>
            <p>作&nbsp;&nbsp;&nbsp;&nbsp;者：{$book->author}</p>
            <p>动&nbsp;&nbsp;&nbsp;&nbsp;作：<a href="javascript:;" onclick="showpop('{$this->createUrl("user/addFavourite", [id=>$book->id])}');">加入书架</a>,  <a href="javascript:;" onclick="showpop('{$this->createUrl("book/like", [id=>$book->id])}');">投推荐票</a>,  <a href="#footer">直达底部</a></p>
            <p>最后更新：{$book->lastchaptertime|date_format:"%Y-%m-%d %H:%M:%S"}</p>
            {*<p>下&nbsp;&nbsp;&nbsp;&nbsp;载：<font color="gray">( TXT,CHM,UMD,JAR,APK,HTML )</font></p>*}
        </div>
        <div id="intro">
            <p>{$book->summary}</p>
            <p>各位书友要是觉得《{$book->title}》还不错的话请不要忘记向您QQ群和微博里的朋友推荐哦！</p>
        </div>
    </div>
    <div id="sidebar">
        <div id="fmimg"><img alt="{$book->title}" src="{H::getNovelImageUrl($book->imgurl)}" width="120" height="150" /><span class="b"></span></div>
    </div>
    {*<div id="listtj">&nbsp;推荐阅读：<a href="/4_4606/" target="_blank"><b>大主宰</b></a>、<a href="/0_171/" target="_blank">傲世九重天</a>、<a href="/4_4472/" target="_blank">罪恶之城</a>、<a href="/5_5249/" target="_blank"><b>完美世界</b></a>、<a href="/0_2/" target="_blank">绝世唐门</a>、<a href="/0_453/" target="_blank">武动乾坤</a>(完)、<a href="/5_5063/" target="_blank"><b>灵域</b></a>、<a href="/4_4361/" target="_blank">九星天辰诀</a>、<a href="/4_4539/" target="_blank">雪中悍刀行</a>、<a href="/4_4900/" target="_blank"><b>星河大帝</b></a>、<a href="/0_148/" target="_blank">剑道独尊</a>、<a href="/4_4597/" target="_blank">龙血战神</a>、<a href="/4_4555/" target="_blank"><b>我的贴身校花</b></a>  </div>*}
</div>

<div class="box_con">
<div id="list">
<dl>
{*<dt><b>《完美世界》最新章节</b>（提示：已启用缓存技术，最新章节可能会延时显示，登录书架即可实时查看。）</dt>*}
{*<dd><a href="/5_5249/1542302.html">第一百九十九章 喜悦</a></dd>*}
{*<dd><a href="/5_5249/1542084.html">三章，求月票与推荐票</a></dd>*}
{*<dd><a href="/5_5249/1541808.html">第一百九十八章 回石村</a></dd>*}
{*<dd><a href="/5_5249/1541356.html">第一百九十七章 八珍鸡</a></dd>*}
{*<dd><a href="/5_5249/1539342.html">第一百九十六章 落凰岭</a></dd>*}
{*<dd><a href="/5_5249/1537335.html">第一百九十五章 大劫落幕</a></dd>*}
{*<dd><a href="/5_5249/1536752.html">第一百九十四章 突围</a></dd>*}
{*<dd><a href="/5_5249/1535765.html">第一百九十三章 鼻祖</a></dd>*}
{*<dd><a href="/5_5249/1531343.html">第一百九十二章 上下一心</a></dd>*}

<dt>《{$book->title}》正文</dt>
    {foreach $book->chapter as $k => $v}
<dd><a href="{novel_chapter_link id=$v->id}">{$v->title}</a></dd>
    {/foreach}
</dl>
</div>
</div>