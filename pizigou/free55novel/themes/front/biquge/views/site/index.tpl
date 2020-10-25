<div id="main">
<div id="hotcontent">


<div class="l">
    {novel_book limit=4}
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
  

    <div class="r">
     <h2>公告牌</h2>
		 		<ul>
                    {novel_news}
			<li><span class="s1">[公告]</span><span class="s2"><a href="{novel_news_link id=$item->id}">{$item->title|truncate:10}</a></span></li>
                    {/novel_news}

		</ul> 
	 <h2>上期强推</h2>
	    <ul>
            {novel_book_rank type='week' limit=4}
            <li><span class="s1">[{$item->book->category->title}]</span><span class="s2"><a href="{novel_book_link id=$item->book->id}">{$item->book->title}</a></span><span class="s5">{$item->book->author}</span></li>
            {/novel_book_rank}
		</ul>
    </div>
    <div class="clear"></div>
  </div>
  

{*<div class="dahengfu"><script type="text/javascript">list1();</script></div>*}
 

<div class="novelslist">

<div class="content">
  {novel_category id=1}
  <h2>{$item->title}</h2>
  {/novel_category}
  
<div class="top">
    {novel_book limit=1 cid=[1] recommend=[1]}
    <div class="image"><img src="{$item->imgurl}" alt="{$item->title}"  width="67" height="82" /></div>
    <dl>
        <dt><a href="{novel_book_link id=$item->id}">{$item->title}</a></dt>
        <dd>{$item->summary}</dd>
    </dl>
    {/novel_book}
    <div class="clear"></div>
</div>
<ul>
    {novel_book limit=12 cid=[1]}
    <li><a href="{novel_book_link id=$item->id}">{$item->title}</a>/{$item->author}</li>
    {/novel_book}
</ul>
  </div>

<div class="content">
    {novel_category id=3}
        <h2>{$item->title}</h2>
    {/novel_category}
  
<div class="top">
    {novel_book limit=1 cid=[3] recommend=[1]}
        <div class="image"><img src="{$item->imgurl}" alt="{$item->title}"  width="67" height="82" /></div>
        <dl>
            <dt><a href="{novel_book_link id=$item->id}">{$item->title}</a></dt>
            <dd>{$item->summary}</dd>
        </dl>
    {/novel_book}
<div class="clear"></div></div>
<ul>
    {novel_book limit=12 cid=[3]}
        <li><a href="{novel_book_link id=$item->id}">{$item->title}</a>/{$item->author}</li>
    {/novel_book}
</ul>
</div>



<div class="content border">
    {novel_category id=7}
        <h2>{$item->title}</h2>
    {/novel_category}

    <div class="top">
        {novel_book limit=1 cid=[7] recommend=[1]}
            <div class="image"><img src="{$item->imgurl}" alt="{$item->title}"  width="67" height="82" /></div>
            <dl>
                <dt><a href="{novel_book_link id=$item->id}">{$item->title}</a></dt>
                <dd>{$item->summary}</dd>
            </dl>
        {/novel_book}
        <div class="clear"></div></div>
    <ul>
        {novel_book limit=12 cid=[7]}
            <li><a href="{novel_book_link id=$item->id}">{$item->title}</a>/{$item->author}</li>
        {/novel_book}
    </ul>
  </div>


<div class="clear"></div>
</div>


<div id="newscontent">

<div class="l">
<h2>最近更新小说列表</h2>
<ul>
    {novel_book limit=30 order='lastchaptertime desc'}
    <li><span class="s1">[{$item->category->title}]</span><span class="s2"><a href="{novel_book_link id=$item->id}" target="_blank">{$item->title}</a></span><span class="s3"><a href="{novel_chapter_link id=$item->lastchapterid}" target="_blank">{$item->lastchaptertitle}</a></span><span class="s4">{$item->author}</span><span class="s5">{$item->lastchaptertime|date_format:"%m-%d"}</span></li>
    {/novel_book}
</ul>
</div>



<div class="r">
<h2>最新入库小说</h2>
<ul>
    {novel_book limit=30 order='createtime desc'}
    <li><span class="s1">[{$item->category->title}]</span><span class="s2"><a href="{novel_book_link id=$item->id}">{$item->title}</a></span><span class="s5">{$item->author}</span></li>
    {/novel_book}
</ul>
</div><div class="clear"></div>

</div>
</div>