<div id="main">
    <div id="hotcontent">
        <div class="ll">
            {novel_book limit=6 cid=[$category->id] order='recommendlevel asc'}
            <div class="item">
                <div class="image"><a href="{novel_book_link id=$item->id}"><img src="{$item->imgurl}" alt="{$item->title}"  width="120" height="150" /></a></div>
                <dl>
                    <dt><span>{$item->author}</span><a href="{novel_book_link id=$item->id}">{$item->title}</a></dt>
                    <dd>{$item->summary}</dd>
                </dl>
                <div class="clear"></div>
            </div>
            {/novel_book}
        </div>
    </div>


    <div id="newscontent">
        <div class="l">
            <h2>好看的{$category->title}最近更新列表</h2>
            <ul>
                {novel_book limit=50 cid=[$category->id] order='lastchaptertime desc'}
                <li><span class="s2">《<a href="{novel_book_link id=$item->id}" target="_blank">{$item->title}</a>》</span><span class="s3"><a href="{novel_chapter_link id=$item->lastchapterid}" target="_blank">{$item->lastchaptertitle}</a>({$item->lastchaptertime|date_format:"%m-%d"})</span><span class="s5">{$item->author}</span></li>
                {/novel_book}
            </ul>
        </div>
        <div class="r">
            <h2>好看的{$category->title}</h2>
            <ul>
                {novel_book limit=50 cid=[$category->id] order='recommendlevel asc'}
                <li><span class="s2"><a href="{novel_book_link id=$item->id}">{$item->title}</a></span><span class="s5">{$item->author}</span></li>
                {/novel_book}
            </ul>
        </div>
        <div class="clear"></div></div>
</div>