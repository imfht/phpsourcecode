<?php
namespace Ext;
/**
 *
 * @author Zjmainstay
 * @website http://zjmainstay.cn
 * @copyright GPL
 * @version 1.0
 * @year 2014
 *
 */
/*
    //display error in runtime
    ini_set('display_errors','on');
    error_reporting(E_ALL);
    
    require '../markdown.class.php';
    
    header("Content-type: text/html; charset=utf-8"); 
    $obj             = new \Ext\Markdown();
    $md = <<<md
    # 欢迎使用 Cmd - 在线 Markdown 编辑阅读器

---

我们理解您需要更便捷更高效的工具记录思想，整理笔记、知识，并将其中承载的价值传播给他人，**Cmd Markdown** 是我们给出的答案 —— 我们为记录思想和分享知识提供更专业的工具。

您可以使用 Cmd Makrdown：

> * 整理知识，学习笔记
> * 发布日记，杂文，所见所想
> * 撰写发布技术文稿（代码支持）

这是一份 Cmd Markdown 的欢迎稿兼使用说明，请保留，如需撰写 **新稿件**，点击顶部工具栏右侧的 `新建文本` 图标或者使用快捷键 `Ctrl+Alt+N`。

---

## 什么是 Markdown

Markdown 是一种方便记忆、书写的纯文本标记语言，用户可以使用这些标记符号以最小的输入代价生成极富表现力的文档：譬如您正在阅读的这份文档。它使用简单的符号标记不同的标题，分割不同的段落，**粗体** 或者 *斜体* 某些文字，更棒的是，它还可以

* 或者高亮一段代码[^code]：

```python
@requires_authorization
class SomeClass:
    pass

if __name__ == '__main__':
    # A comment
    print 'hello world'
```

想要了解更详细的语法说明，可以参考我们准备的 [Cmd Markdown 简明语法手册][1]，进阶用户可以参考 [Cmd Markdown 高阶语法手册][2] 以使用更多功能。

总而言之，不同于其它 *所见即所得* 的编辑器：您只需使用键盘专注于书写文本内容，就可以生成印刷级的排版格式，省却在键盘和工具栏之间来回切换，调整内容和格式的麻烦。**Markdown 在流畅的书写和印刷级的阅读体验之间找到了平衡。** 它目前已经成为世界上最大的技术分享网站 GitHub 和 技术问答网站 StackOverFlow 的御用书写格式。

---

## 什么是 Cmd Markdown

您可以使用很多工具书写 Markdown，但是 Cmd Markdown 是这个星球上我们已知的、最好的 Markdown 工具——没有之一 ：）因为深信文字的力量，所以我们和你一样，对流畅书写，分享思想和知识，以及阅读体验有极致的追求，我们把对于这些诉求的回应整合在 Cmd Markdown，并且一次，两次，三次，乃至无数次地提升这个工具的体验，最终将它演化成一个 **编辑/发布/阅读** Markdown 的在线平台——您可以在任何地方，任何系统/设备上管理这里的文字。

### 1. 实时同步预览

我们将 Cmd Markdown 的主界面一分为二，左边为**编辑区**，右边为**预览区**，在编辑区的操作会实时地渲染到预览区方便查看最终的版面效果，并且如果你在其中一个区拖动滚动条，我们有一个巧妙的算法把另一个区的滚动条同步到等价的位置，超酷！

### 2. 编辑工具栏

也许您还是一个 Markdown 语法的新手，在您完全熟悉它之前，我们在 **编辑区** 的顶部放置了一个如下图所示的工具栏，您可以使用鼠标在工具栏上调整格式，不过我们仍旧鼓励你使用键盘标记格式，提高书写的流畅度。

![tool-editor](https://www.zybuluo.com/static/img/toolbar-editor.png)

### 3. 编辑模式

完全心无旁骛的方式编辑文字：点击 **编辑工具栏** 最右测的拉伸按钮或者按下 `Ctrl + M`，将 Cmd Markdown 切换到独立的编辑模式，这是一个极度简洁的写作环境，所有可能会引起分心的元素都已经被挪除，超清爽！

### 4. 实时的云端文稿

为了保障数据安全，Cmd Markdown 会将您每一次击键的内容保存至云端，同时在 **编辑工具栏** 的最右侧提示 `已保存` 的字样。无需担心浏览器崩溃，机器掉电或者地震，海啸——在编辑的过程中随时关闭浏览器或者机器，下一次回到 Cmd Markdown 的时候继续写作。

### 5. 离线模式

在网络环境不稳定的情况下记录文字一样很安全！在您写作的时候，如果电脑突然失去网络连接，Cmd Markdown 会智能切换至离线模式，将您后续键入的文字保存在本地，直到网络恢复再将他们传送至云端，即使在网络恢复前关闭浏览器或者电脑，一样没有问题，等到下次开启 Cmd Markdown 的时候，她会提醒您将离线保存的文字传送至云端。简而言之，我们尽最大的努力保障您文字的安全。

### 6. 管理工具栏

为了便于管理您的文稿，在 **预览区** 的顶部放置了如下所示的 **管理工具栏**：

![tool-manager](https://www.zybuluo.com/static/img/toolbar-manager-2.png)

工具栏上的五个图标依次为：

* `立即发布`：将当前的文稿生成到固定链接，在网络上发布这个链接，分享您的文稿
* `新建文本`：开始撰写一篇新的文稿
* `删除文本`：删除当前的文稿
* `导出文本`：将当前的文稿转化为 Markdown 文本或者 Html 格式，并导出到本地
* `文稿列表`：所有新增和过往的文稿都可以在这里查看、操作

### 7. 阅读工具栏

![tool-manager](https://www.zybuluo.com/static/img/toolbar-reader-2.png)

通过 **预览区** 右上角的 **阅读工具栏**，可以查看当前文稿的目录并增强阅读体验。

工具栏上的四个图标依次为：

* `内容目录`：快速导航当前文稿的目录结构以跳转到感兴趣的段落
* `主题切换`：内置了黑白两种模式的主题，试试 **黑色主题**，超炫！
* `阅读模式`：心无旁骛的阅读模式提供超一流的阅读体验
* `全屏模式`：简洁，简洁，再简洁，一个完全沉浸式的写作和阅读环境

### 8. 阅读模式

在 **阅读工具栏** 点击 `阅读模式` 图标或者按下 `Ctrl+Alt+M` 随即进入独立的阅读模式界面，我们在版面渲染上的每一个细节：字体，字号，行间距，前背景色都倾注了大量的时间，努力提升您阅读的体验和品质。尝试进入阅读模式，感受这份诚意。

### 9. 文稿发布和分享

在您使用 Cmd Markdown 记录，创作，整理，阅读文稿的同时，我们不仅希望它是一个有力的工具，更希望您的思想和知识通过这个平台，连同优质的阅读体验，将他们分享给有相同志趣的人，进而鼓励更多的人来到这里记录分享他们的思想和知识，尝试点击 `立即发布` 图标发布这份文档给好友吧！

---

再一次感谢您花费时间阅读这份欢迎稿，`新建文本(Ctrl+Alt+N)` 开始撰写新的文稿吧！祝您在这里记录、阅读、分享愉快！

文档来自 [@ghosert][3]     
于 2014 年 03月 07日    
上海

[^code]: 代码高亮功能支持包括 Java, Python, JavaScript 在内的，**四十一**种主流编程语言。

[1]: https://www.zybuluo.com/mdeditor?url=https://www.zybuluo.com/static/editor/md-help.markdown
[2]: https://www.zybuluo.com/mdeditor?url=https://www.zybuluo.com/static/editor/md-help.markdown#cmd-markdown-高阶语法手册
[3]: http://weibo.com/ghosert


md;
    $html = <<<html
    <h1> 欢迎使用 Cmd - 在线 Markdown 编辑阅读器</h1>
<hr />
<p>我们理解您需要更便捷更高效的工具记录思想，整理笔记、知识，并将其中承载的价值传播给他人，<strong>Cmd Markdown</strong> 是我们给出的答案 —— 我们为记录思想和分享知识提供更专业的工具。</p>
<p>您可以使用 Cmd Makrdown：</p><blockquote><ul>
<li>整理知识，学习笔记</li>
<li>发布日记，杂文，所见所想</li>
<li>撰写发布技术文稿（代码支持）</li>
</ul></blockquote>
<p>这是一份 Cmd Markdown 的欢迎稿兼使用说明，请保留，如需撰写 <strong>新稿件</strong>，点击顶部工具栏右侧的 <code>新建文本</code> 图标或者使用快捷键 <code>Ctrl+Alt+N</code>。</p>
<hr />
<h2> 什么是 Markdown</h2>
<p>Markdown 是一种方便记忆、书写的纯文本标记语言，用户可以使用这些标记符号以最小的输入代价生成极富表现力的文档：譬如您正在阅读的这份文档。它使用简单的符号标记不同的标题，分割不同的段落，<strong>粗体</strong> 或者 <em>斜体</em> 某些文字，更棒的是，它还可以<ul></p>
<li>或者高亮一段代码<a href="#fn:code" id="fnref:code" title="查看注脚" class="footnote">[1]</a>：</li>
</ul><pre class="python"><ol>
<li><code>@requires_authorization</code></li>
<li><code>class SomeClass:</code></li>
<li><code>    pass</code></li>
<li><code>if __name__ == '__main__':</code></li>
<li><code>    # A comment</code></li>
<li><code>    print 'hello world'</code></li>
</ol></pre>
<p>想要了解更详细的语法说明，可以参考我们准备的 <a target="_blank" href="https://www.zybuluo.com/mdeditor?url=https://www.zybuluo.com/static/editor/md-help.markdown">Cmd Markdown 简明语法手册</a>，进阶用户可以参考 <a target="_blank" href="https://www.zybuluo.com/mdeditor?url=https://www.zybuluo.com/static/editor/md-help.markdown#cmd-markdown-高阶语法手册">Cmd Markdown 高阶语法手册</a> 以使用更多功能。</p>
<p>总而言之，不同于其它 <em>所见即所得</em> 的编辑器：您只需使用键盘专注于书写文本内容，就可以生成印刷级的排版格式，省却在键盘和工具栏之间来回切换，调整内容和格式的麻烦。<strong>Markdown 在流畅的书写和印刷级的阅读体验之间找到了平衡。</strong> 它目前已经成为世界上最大的技术分享网站 GitHub 和 技术问答网站 StackOverFlow 的御用书写格式。</p>
<hr />
<h2> 什么是 Cmd Markdown</h2>
<p>您可以使用很多工具书写 Markdown，但是 Cmd Markdown 是这个星球上我们已知的、最好的 Markdown 工具——没有之一 ：）因为深信文字的力量，所以我们和你一样，对流畅书写，分享思想和知识，以及阅读体验有极致的追求，我们把对于这些诉求的回应整合在 Cmd Markdown，并且一次，两次，三次，乃至无数次地提升这个工具的体验，最终将它演化成一个 <strong>编辑/发布/阅读</strong> Markdown 的在线平台——您可以在任何地方，任何系统/设备上管理这里的文字。</p>
<h3> 1. 实时同步预览</h3>
<p>我们将 Cmd Markdown 的主界面一分为二，左边为<strong>编辑区</strong>，右边为<strong>预览区</strong>，在编辑区的操作会实时地渲染到预览区方便查看最终的版面效果，并且如果你在其中一个区拖动滚动条，我们有一个巧妙的算法把另一个区的滚动条同步到等价的位置，超酷！</p>
<h3> 2. 编辑工具栏</h3>
<p>也许您还是一个 Markdown 语法的新手，在您完全熟悉它之前，我们在 <strong>编辑区</strong> 的顶部放置了一个如下图所示的工具栏，您可以使用鼠标在工具栏上调整格式，不过我们仍旧鼓励你使用键盘标记格式，提高书写的流畅度。</p>
<img src="https://www.zybuluo.com/static/img/toolbar-editor.png" alt="tool-editor" title="" />
<h3> 3. 编辑模式</h3>
<p>完全心无旁骛的方式编辑文字：点击 <strong>编辑工具栏</strong> 最右测的拉伸按钮或者按下 <code>Ctrl + M</code>，将 Cmd Markdown 切换到独立的编辑模式，这是一个极度简洁的写作环境，所有可能会引起分心的元素都已经被挪除，超清爽！</p>
<h3> 4. 实时的云端文稿</h3>
<p>为了保障数据安全，Cmd Markdown 会将您每一次击键的内容保存至云端，同时在 <strong>编辑工具栏</strong> 的最右侧提示 <code>已保存</code> 的字样。无需担心浏览器崩溃，机器掉电或者地震，海啸——在编辑的过程中随时关闭浏览器或者机器，下一次回到 Cmd Markdown 的时候继续写作。</p>
<h3> 5. 离线模式</h3>
<p>在网络环境不稳定的情况下记录文字一样很安全！在您写作的时候，如果电脑突然失去网络连接，Cmd Markdown 会智能切换至离线模式，将您后续键入的文字保存在本地，直到网络恢复再将他们传送至云端，即使在网络恢复前关闭浏览器或者电脑，一样没有问题，等到下次开启 Cmd Markdown 的时候，她会提醒您将离线保存的文字传送至云端。简而言之，我们尽最大的努力保障您文字的安全。</p>
<h3> 6. 管理工具栏</h3>
<p>为了便于管理您的文稿，在 <strong>预览区</strong> 的顶部放置了如下所示的 <strong>管理工具栏</strong>：</p>
<img src="https://www.zybuluo.com/static/img/toolbar-manager-2.png" alt="tool-manager" title="" />
<p>工具栏上的五个图标依次为：<ul></p>
<li><code>立即发布</code>：将当前的文稿生成到固定链接，在网络上发布这个链接，分享您的文稿</li>
<li><code>新建文本</code>：开始撰写一篇新的文稿</li>
<li><code>删除文本</code>：删除当前的文稿</li>
<li><code>导出文本</code>：将当前的文稿转化为 Markdown 文本或者 Html 格式，并导出到本地</li>
<li><code>文稿列表</code>：所有新增和过往的文稿都可以在这里查看、操作</li>
</ul>
<h3> 7. 阅读工具栏</h3>
<img src="https://www.zybuluo.com/static/img/toolbar-reader-2.png" alt="tool-manager" title="" />
<p>通过 <strong>预览区</strong> 右上角的 <strong>阅读工具栏</strong>，可以查看当前文稿的目录并增强阅读体验。</p>
<p>工具栏上的四个图标依次为：<ul></p>
<li><code>内容目录</code>：快速导航当前文稿的目录结构以跳转到感兴趣的段落</li>
<li><code>主题切换</code>：内置了黑白两种模式的主题，试试 <strong>黑色主题</strong>，超炫！</li>
<li><code>阅读模式</code>：心无旁骛的阅读模式提供超一流的阅读体验</li>
<li><code>全屏模式</code>：简洁，简洁，再简洁，一个完全沉浸式的写作和阅读环境</li>
</ul>
<h3> 8. 阅读模式</h3>
<p>在 <strong>阅读工具栏</strong> 点击 <code>阅读模式</code> 图标或者按下 <code>Ctrl+Alt+M</code> 随即进入独立的阅读模式界面，我们在版面渲染上的每一个细节：字体，字号，行间距，前背景色都倾注了大量的时间，努力提升您阅读的体验和品质。尝试进入阅读模式，感受这份诚意。</p>
<h3> 9. 文稿发布和分享</h3>
<p>在您使用 Cmd Markdown 记录，创作，整理，阅读文稿的同时，我们不仅希望它是一个有力的工具，更希望您的思想和知识通过这个平台，连同优质的阅读体验，将他们分享给有相同志趣的人，进而鼓励更多的人来到这里记录分享他们的思想和知识，尝试点击 <code>立即发布</code> 图标发布这份文档给好友吧！</p>
<hr />
<p>再一次感谢您花费时间阅读这份欢迎稿，<code>新建文本(Ctrl+Alt+N)</code> 开始撰写新的文稿吧！祝您在这里记录、阅读、分享愉快！</p>
<p>作者 <a target="_blank" href="http://weibo.com/ghosert">@ghosert</a> <br></p>
<p>于 2014 年 03月 07日<br></p>
<p>上海<div class="footnotes"><hr><small></p>
<span id="fn:code">[1] </span>代码高亮功能支持包括 Java, Python, JavaScript 在内的，<strong>四十一</strong>种主流编程语言。<a class="reversefootnote" title="回到文稿" href="#fnref:code"><-</a><br></small></div>
html;
    

    
    $md2html        = $obj->parseMarkdown($md);
    file_put_contents('md2html.html', $md2html);        //md => html

    
    $html2md        = $obj->parseHtml($html);
    file_put_contents('html2md.md', $html2md);        //html => md

    
    echo 'Done';
 */
class Markdown{
    function parseMarkdown($doc) {
        //tab to space
        $doc = str_replace("\t", str_repeat(' ', 4), $doc);
        
        //\r to \n
        $doc = "\n" . str_replace("\r", '', $doc) . "\n";
        
        //remove empty line
        $doc = preg_replace('#\n+#i', "\n", $doc);
        
        //pre code preReplace, just replac to a tag to escape <p> tag replace
        $preCodeTpl     = "\n<preCode %s>";    //< for p skip
        $preCodePattern = '#```([a-z]+)?(.*?)```#is';
        if(preg_match_all($preCodePattern, $doc, $preCodes)) {
            foreach($preCodes[0] as $key => $value) {
                //every pre code into <preCode [index]> tag
                $doc = preg_replace($preCodePattern, sprintf($preCodeTpl, $key), $doc, 1);
            }
        }
        
        //space code preReplace, first one should have 8 space, and the next should only more then 4
        $spaceCodePattern     = '#\n[ ]{8}[^\n]*?(?=\n)(?:\n[ ]{4}[^\n]*?(?=\n))*#is';
        $spaceCodeTpl        = "\n<spaceCode %d>";
        if(preg_match_all($spaceCodePattern, $doc, $spaceCodes)) {
            foreach($spaceCodes[0] as $key => $value) {
                $doc = preg_replace($spaceCodePattern, sprintf($spaceCodeTpl, $key), $doc, 1);
            }
        }
        
        //blockquote preReplace
        $blockquotePattern     = '#(?:\n> \*.*?(?=\n))+#is';
        $blockquoteTpl        = "\n<blockquote %d>";
        if(preg_match_all($blockquotePattern, $doc, $blockquotes)) {
            foreach($blockquotes[0] as $key => $value) {
                $doc = preg_replace($blockquotePattern, sprintf($blockquoteTpl, $key), $doc, 1);
            }
        }
        
        //hr
        $doc = preg_replace('#----*[ ]*(\n)#is', '<hr />\1', $doc);
        
        //h1/h2/h3
        $doc = preg_replace('/(\n)###(.*?)#*(?=\n)/is', '\1<h3>\2</h3>', $doc);
        $doc = preg_replace('/(\n)##(.*?)#*(?=\n)/is', '\1<h2>\2</h2>', $doc);
        $doc = preg_replace('/(\n)#(.*?)#*(?=\n)/is', '\1<h1>\2</h1>', $doc);
        
        //strong
        $doc = preg_replace('#\*\*([^\n]*?)\*\*#is', '<strong>\1</strong>', $doc);
        
        //em
        $doc = preg_replace('#\*([^\n]*?)\*#is', '<em>\1</em>', $doc);
        
        //code
        $doc = preg_replace('#`(.*?)`#is', '<code>\1</code>', $doc);
        
        //ul li
        $liPattern = '#(?:\n\* [^\n]*?(?=\n))+#is';
        if(preg_match_all($liPattern, $doc, $lis)) {
            foreach($lis[0] as $key => $value) {
                $ul = '<ul>%s</ul>';
                $lis        = preg_replace('#(\n)\* ([^\n]*?)(?=\n)#is', '\1<li>\2</li>', "\n" . $value . "\n");
                $ul         = sprintf($ul, $lis);
                $doc = preg_replace($liPattern, $ul, $doc, 1);
            }
        }
        
        //img
        $doc = preg_replace('#!\[([^\]]*?)\]\(([^\s]*?)(?: "([^"]*?)")?\)#is', '<img src="\2" alt="\1" title="\3" />', $doc);
        
        //a simple
        if(preg_match_all('#\[([^\]]*?)\]\(([^\s]*?)(?:\s"([^"]*?)")?\)#is', $doc, $links)) {
            $linkTpl             = '<a target="_blank" href="%s" title="%s">%s</a>';
            foreach($links[0] as $key => $value) {
                $doc = str_replace($links[0][$key], sprintf($linkTpl, $links[2][$key], $links[3][$key], $links[1][$key]), $doc);
            }
        }
        
        //a normal
        if(preg_match_all('#\[([^\]]*?)\]\[(\d+)\]#is', $doc, $links)) {
            $linkTpl             = '<a target="_blank" href="%s">%s</a>';
            foreach($links[0] as $key => $value) {
                if(preg_match(sprintf('#\n\[%d\]: (.*?)(?=\n)#is', $links[2][$key]), $doc, $linkHref)) {
                    $doc = str_replace($links[0][$key], sprintf($linkTpl, $linkHref[1], $links[1][$key]), $doc);
                }
            }
        }
        //remove all link href
        $doc = preg_replace('#\n\[\d+\]:.*?(?=\n)#is', '', $doc);
        
        //a footnote
        if(preg_match_all('#\[\^(.*?)\]#is', $doc, $footnotes)) {
            $footnoteTpl = '<a href="#fn:%s" id="fnref:%s" title="go to footnote" class="footnote">[%s]</a>';
            $footnoteReplaced = array();
            foreach($footnotes[0] as $key => $value) {
                $footnoteId = $footnotes[1][$key];
                if(isset($footnoteReplaced[$footnoteId])) continue;
                
                $footnoteReplaced[$footnoteId] = true;
                $index            = $key+1;
                $footnoteHash     = sprintf('<span id="fn:%s">[%s] </span>', $footnoteId, $index);
                $footnoteBack     = sprintf('<a class="reversefootnote" title="go back to content" href="#fnref:%s"><-</a><br>', $footnoteId);
                //match footnote by id
                if(preg_match(sprintf('#(\n)\[\^%s\]: (.*?)(?=\n)#is', $footnoteId), $doc, $footnote)) {
                    //footnote link
                    $doc = preg_replace(sprintf('#\[\^%s\]#is', $footnoteId), sprintf($footnoteTpl, $footnoteId, $footnoteId, $index), $doc, 1);
                    //footnote desc
                    $doc = str_replace($footnote[0], '<-fs->' . $footnote[1]. $footnoteHash . $footnote[2] . $footnoteBack . '<-fe->', $doc);
                }
            }
        }
        //put it into footnotes div
        $doc = preg_replace('#<-fs->(.+)<-fe->#is', '<div class="footnotes"><hr><small>\1</small></div>', $doc);
        //remove the unnecessary tags
        $doc = str_replace(array('<-fs->','<-fe->'), '', $doc);
        
        //br
        $doc = preg_replace('#[ ]{4}(?=\n)#is', '<br>', $doc);
        
        //p not before other tag, and not after pre
        $doc = preg_replace('#\n+#i', "\n", $doc);    //remove empty line [important]
        $doc = preg_replace('#(\n)([^<].*?)(?=\n)#i', '\1<p>\2</p>', $doc);
        
        //pre code replace
        if(!empty($preCodes[0])) {
            foreach($preCodes[0] as $key => $value) {
                $preCode    = '<pre class="'.$preCodes[1][$key].'"><ol>%s</ol></pre>';
                $lines      = preg_replace('#(\n)(.*?)(?=\n)#is', '\1<li><code>\2</code></li>', htmlspecialchars($preCodes[2][$key]));
                $preCode    = sprintf($preCode, $lines);
                $doc = str_replace(sprintf($preCodeTpl, $key), $preCode, $doc);
            }
        }
        
        //space code
        if(!empty($spaceCodes[0])) {
            foreach($spaceCodes[0] as $key => $value) {
                $spaceCode    = sprintf('<pre><code>%s%s</code></pre>', htmlspecialchars($value), "\n");
                $doc         = str_replace(sprintf($spaceCodeTpl, $key), $spaceCode, $doc);
            }
        }
        
        //blockquote replace
        if(!empty($blockquotes[0])) {
            foreach($blockquotes[0] as $key => $value) {
                $blockquote = '<blockquote><ul>%s</ul></blockquote>';
                $lis        = preg_replace('#(\n)> \* (.*?)(?=\n)#is', '\1<li>\2</li>', $value . "\n");
                $blockquote = sprintf($blockquote, $lis);
                $doc = str_replace(sprintf($blockquoteTpl, $key), $blockquote, $doc);
            }
        }
        
        return trim($doc, "\n");
    }
    
    function parseHtml($html) {
        //\r to \n
        $html = "\n" . str_replace("\r", '', $html) . "\n";
    
        //h1/h2/h3
        $html = preg_replace('#<h1[^>]*?>(.*?)</h1>#is', '#\1', $html);
        $html = preg_replace('#<h2[^>]*?>(.*?)</h2>#is', '##\1', $html);
        $html = preg_replace('#<h3[^>]*?>(.*?)</h3>#is', '###\1', $html);
        
        //hr
        $html = preg_replace('#<hr\s*/?>#is', '---', $html);
        
        //em
        $html = preg_replace('#<em[^>]*?>(.*?)</em>#is', '*\1*', $html);
        
        //pre code
        if(preg_match_all('#<pre><code[^>]*?>(.*?)</code></pre>#is', $html, $tabCodes)) {
            foreach($tabCodes[0] as $key => $value) {
                if(preg_match_all('#.*?\n#is', $tabCodes[1][$key], $lines)) {
                    $space8 = str_repeat(' ', 8);
                    $tabCodes[1][$key] = '';
                    foreach($lines[0] as $k => $v) {
                        $tabCodes[1][$key] .= $space8 . $v;
                    }
                }
                $html = str_replace($tabCodes[0][$key], $tabCodes[1][$key], $html);
            }
        }
        
        //pre ol code
        $liPattern = '#[ ]*<li>(.*?)</li>[ ]*#is';
        $preOlPattern = '#<pre class="([^"]*?)"><ol>(.*?)</ol></pre>#is';
        if(preg_match_all($preOlPattern, $html, $preOls)) {
            foreach($preOls[0] as $key => $value) {
                //li
                if(preg_match_all($liPattern, $preOls[2][$key], $lis)) {
                    foreach($lis[0] as $k => $v) {
                        $index = $k + 1;
                        $html = str_replace($v, strip_tags($lis[1][$k]), $html);
                    }
                }
                $html = preg_replace($preOlPattern, sprintf('```%s\2```', $preOls[1][$key]), $html, 1);
            }
        }
        
        //code
        $html = preg_replace('#<code[^>]*?>(.*?)</code>#is', '`\1`', $html);
        
        //strong
        $html = preg_replace('#<strong[^>]*?>(.*?)</strong>#is', '**\1**', $html);
        
        //img
        $html = preg_replace('#<img src="([^"]*?)" alt="([^"]*?)" title="([^"]*?)"\s*/?>#is', '![\2](\1 "\3")', $html);
        $html = preg_replace('#(!\[.*?\]\(.*?) ""(\))#is', '\1\2', $html);
        
        //a
        $linkPattern = '#<a (?:target="_blank" )?href="([^"]*?)">(.*?)</a>#is';
        if(preg_match_all($linkPattern, $html, $links)) {
            $html .= "\n";
            foreach($links[0] as $key => $value) {
                $index = $key + 1;
                $html = str_replace($value, sprintf('[%s][%d]', $links[2][$key], $index), $html);
                $html .= sprintf("[%d]: %s\n", $index, $links[1][$key]);
            }
        }
        
        //a 注脚
        $footnotePattern = '#<a href="\#fn:([^"]*?)" id="fnref:\1" title="[^"]*?" class="footnote">\[(\d+)\]</a>#is';
        if(preg_match_all($footnotePattern, $html, $footnotes)) {
            foreach($footnotes[0] as $key => $value) {
                $footnote = $footnotes[1][$key];        //code
                $footnoteNum = $footnotes[2][$key];        //1
                $footnoteStr = sprintf('[^%s]', $footnote);
                //part 1
                $html = str_replace($value, $footnoteStr, $html);
                //part 2
                $html = str_ireplace(sprintf('<span id="fn:%s">[%s] </span>', $footnote, $footnoteNum), $footnoteStr . ': ', $html);
                //part 3
                $html = preg_replace(sprintf('#<a href="\#fnref:%s" title="[^"]*?" class="reversefootnote">.*?</a>#is', $footnote), '', $html);
            }
        }
        $html = preg_replace('#<div class="footnotes">\s*---\s*<small>(.*?)</small>\s*</div>#is', '\1', $html);
        $html = preg_replace('#<a class="reversefootnote".*?><-</a><br>#is', '', $html);
        
        //blockquote
        $blockPattern = '#<blockquote[^>]*?>\s*<ul>(.*?)</ul>\s*</blockquote>#is';
        if(preg_match_all($blockPattern, $html, $blocks)) {
            foreach($blocks[0] as $key => $value) {    //Every blockquote
                //li
                if(preg_match_all($liPattern, $blocks[1][$key], $lis)) {
                    foreach($lis[0] as $k => $v) {
                        $html = str_replace($v, '> * ' . $lis[1][$k], $html);
                    }
                }
            }
        }
        
        //All blockquote html
        $html = preg_replace($blockPattern, '\1', $html);
        
        //ul/li
        $ulPattern = '#<ul>(.*?)</ul>#is';
        if(preg_match_all($ulPattern, $html, $uls)) {
            foreach($uls[0] as $key => $value) {    //Every blockquote
                //li
                if(preg_match_all($liPattern, $uls[1][$key], $lis)) {
                    foreach($lis[0] as $k => $v) {
                        $html = str_replace($v, '* ' . $lis[1][$k], $html);
                    }
                }
            }
        }
        //All ul html
        $html = preg_replace($ulPattern, '\1', $html);

        //ol/li
        $olPattern = '#<ol>(.*?)</ol>#is';
        if(preg_match_all($olPattern, $html, $ols)) {
            foreach($ols[0] as $key => $value) {
                //li
                if(preg_match_all($liPattern, $ols[1][$key], $lis)) {
                    foreach($lis[0] as $k => $v) {
                        $index = $k + 1;
                        $html = str_replace($v, sprintf('%d.  %s', $index, $lis[1][$k]), $html);
                    }
                }
            }
        }
        //All ol html
        $html = preg_replace($olPattern, '\1', $html);
        
        //br
        $html = preg_replace('#<br\s*/?>#is', str_repeat(' ', 4), $html);
        
        //p not before pre
        $html = preg_replace('#<p[^>]*?>(.*?)</p>#is', '\1', $html);
        
        return $html;
    }

}
