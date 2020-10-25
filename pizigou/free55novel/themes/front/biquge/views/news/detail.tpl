<div class="content_read">
    {*<script>read1();</script>*}
    <div class="box_con">
        <div class="con_top">
            <script>textselect();</script>
            {*<a href="{Yii::app()->baseUrl}">{Yii::app()->name}</a> &gt; <a href="{novel_category_link id=$news->book->category->id}">穿越小说</a>  &gt; <a href="{novel_book_link id=$news->book->id}">{$news->book->title}</a> &gt; {$news->title}*}
        </div>
        <div class="bookname">
            <h1>{$news->title}</h1>
            <div class="bottem1">
                {*<a onclick="showpop('{$this->createUrl("book/like", ["id" => $news->book->id])}');" href="javascript:;">投推荐票</a> <a href="{novel_chapter_link id=$prevChapter->id}">上一章</a> ← <a href="{novel_book_link id=$news->book->id}">章节目录</a> → <a href="{novel_chapter_link id=$nextChapter->id}">下一章</a> <a onclick="showpop('{$this->createUrl("user/addFavourite", ["id" => $news->book->id])}');" href="javascript:;">加入书签</a>*}
            </div>
            {*<div class="lm">&nbsp;热门推荐：<a target="_blank" href="/4_4573/">悍戚</a>、<a target="_blank" href="/4_4536/"><b>宰执天下</b></a>、<a target="_blank" href="/4_4985/">1949我来自未来</a>、<a target="_blank" href="/4_4413/">大明官</a>、<a target="_blank" href="/5_5065/">大唐暴力宅男</a>、<a target="_blank" href="/4_4572/"><b>伐清</b></a>、<a target="_blank" href="/4_4363/">三国第一强兵</a>、<a target="_blank" href="/0_119/">醉枕江山</a>、<a target="_blank" href="/0_213/">唐砖</a>、<a target="_blank" href="/5_5264/"><b>大唐第一庄</b></a>、<a target="_blank" href="/0_422/">赘婿</a>、<a target="_blank" href="/4_4504/">吞噬苍穹</a>、<a target="_blank" href="/4_4385/">天骄无双</a>、<a target="_blank" href="/4_4386/">人皇</a>、<a target="_blank" href="/4_4489/">一等家丁</a>
            </div>*}
        </div>
        {*<script>read2();</script>*}

        <div id="content">{$news->content}</div>
        {*<script>read3();</script>*}
        <script>bdshare();</script>{*<div class="bdshare_t bds_tools get-codes-bdshare" id="bdshare"><span class="bds_more">分享本书到：</span><a class="bds_mshare" title="分享到一键分享" href="#">一键分享</a><a class="bds_tsina" title="分享到新浪微博" href="#">新浪微博</a><a class="bds_qzone" title="分享到QQ空间" href="#">QQ空间</a><a class="bds_sqq" title="分享到QQ好友" href="#">QQ好友</a><a class="bds_tieba" title="分享到百度贴吧" href="#">百度贴吧</a><a class="bds_tqq" title="分享到腾讯微博" href="#">腾讯微博</a><a class="bds_baidu" title="分享到百度云收藏" href="#">百度搜藏</a><a class="bds_bdhome" title="分享到百度新首页" href="#">百度新首页</a><a class="bds_copy" title="分享到复制网址" href="#">复制网址</a></div>*}

        {*<div class="bottem2">*}
            {*<a onclick="showpop('{$this->createUrl("book/like", ["id" => $news->book->id])}');" href="javascript:;">投推荐票</a> <a href="{novel_chapter_link id=$prevChapter->id}">上一章</a> ← <a href="{novel_book_link id=$news->book->id}">章节目录</a> → <a href="{novel_chapter_link id=$nextChapter->id}">下一章</a> <a onclick="showpop('{$this->createUrl("user/addFavourite", ["id" => $news->book->id])}');" href="javascript:;">加入书签</a>*}
        {*</div>*}
        {*<script>read4();</script>*}

    </div>
</div>