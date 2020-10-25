<div id="main">
    {*<div id="hotcontent">*}
    {*<div class="ll">*}
    {*{novel_book limit=6 order='recommendlevel asc'}*}
    {*<div class="item">*}
    {*<div class="image"><a href="{novel_book_link id=$item->id}"><img src="{$item->imgurl}" alt="{$item->title}"  width="120" height="150" /></a></div>*}
    {*<dl>*}
    {*<dt><span>{$item->author}</span><a href="{novel_book_link id=$item->id}">{$item->title}</a></dt>*}
    {*<dd>{$item->summary}</dd>*}
    {*</dl>*}
    {*<div class="clear"></div>*}
    {*</div>*}
    {*{/novel_book}*}
    {*</div>*}
    {*</div>*}

    <div id="newscontent" style="margin-top:10px">
        <div class="l">
            <h2>{if $type == 1}我的推荐{else}我的书架{/if}</h2>
            <ul>
                {foreach $list as $item}
                    <li><span class="s2">《<a href="{novel_book_link id=$item->bookid}" target="_blank">{$item->title}</a>》</span><span class="s3"><a href="{novel_chapter_link id=$item->book->lastchapterid}" target="_blank">{$item->book->lastchaptertitle}</a>({$item->book->lastchaptertime|date_format:"%m-%d"})</span><span class="s5">{$item->book->author}</span></li>
                {/foreach}
            </ul>
        </div>
        <div class="r">
            <h2>热门小说榜单</h2>
            <ul>
                {novel_book limit=50 order='recommendlevel asc'}
                    <li><span class="s2"><a href="{novel_book_link id=$item->id}">{$item->title}</a></span><span class="s5">{$item->author}</span></li>
                {/novel_book}
            </ul>
        </div>
        <div class="clear"></div>
        <div>
            {* 分页  *}
            {$page}
        </div>
    </div>
</div>