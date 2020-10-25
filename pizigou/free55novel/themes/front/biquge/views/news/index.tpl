<div id="main">

    <div id="newscontent">
        <br />

        <div class="l">
            <h2>好看的{$category->title}新闻列表</h2>
            <ul>
                {foreach $list as $item}
                <li><span class="s2"><a href="{novel_news_link id=$item->id}" target="_blank">{$item->title}</a></span><span class="s3">{$item->createtime|date_format:"%m-%d"})</span><span class="s5"></span></li>
                {/foreach}
            </ul>
        </div>

        <div>
            {* 分页  *}
            {$page}
        </div>

        <div class="clear"></div>
    </div>
</div>