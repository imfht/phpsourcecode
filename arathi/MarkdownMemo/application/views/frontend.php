<!DOCTYPE html>
<html>
    <head>
        <title>Markdown Memo</title>
        <meta charset='utf-8'>
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        <meta name="description" content="玛氪宕·梦魔(Markdown Memo)，使用Markdown的云端备忘录，百度IFE的RIA启航班的不合格的作业，才……才没有什么阴谋呢！">
        <meta name="keywords" content="MarkdownMemo,玛氪宕·梦魔,玛氪宕,梦魔,markdown,memo,云备忘录,云笔记">
        <?php
        if ($production) {
        ?>
        <!-- for production -->
        <link rel="stylesheet" href="<?php echo $jqueryMobileCssCDN; ?>">
        <script src="<?php echo $jqueryCDN; ?>"></script>
        <script src="<?php echo $jqueryMobileJsCDN; ?>"></script>
        <script src="<?php echo $showdownCDN; ?>"></script>
        <?php } else { ?>
        <!-- for development -->
        <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/jquery.mobile.css">
        <script src="<?php echo $baseUrl; ?>/assets/jquery.js"></script>
        <script src="<?php echo $baseUrl; ?>/assets/jquery.mobile.js"></script>
        <script src="<?php echo $baseUrl; ?>/assets/showdown.js"></script>
        <?php }?>
    </head>
    <body>
        <script type="text/javascript">
            function hereDoc(func) {
                return func.toString().split('\n').slice(1, -1).join('\n');
            }
            function markupPanel() {
                $('#panel_user').panel();
                $("ul#userPanel").listview();
            }
        </script>
        
        <script>
            function onLogin(){
                $('h3#login-status').html('登陆中...');
                //TODO 从文本框获取
                var username = $('#un').val();
                var password = $('#pw').val();
                var hashedPWD = password;
                var params = 'username='+username+"&password="+hashedPWD;
                $.ajax({
                    url: '<?php echo $siteUrl; ?>/rest/user/login?'+params,
                    type: 'GET',
                    datatype: "json",
                    //data: params,
                    success: function (json) {
                        var loggedFlag = json.logged;
                        if (loggedFlag){
                            var username = json.username;
                            var uid = json.uid;
                            $('li#username').html(username);
                            $('h3#login-status').html("<font color='#00FF00'>登陆成功！");
                            $('span.username').html(username);
                            $('a#userlink').attr("href", "#panel_user");
                            $('.popupLogin').popup("close");
                        }
                        else {
                            //TODO 处理登陆失败
                            $('h3#login-status').html("<font color='#FF0000'>登陆失败！");
                        }
                    },
                    error: function (msg) {
                        alert(arguments[1])
                    }
                });
            }
        </script>
        <div data-role="popup" id="popupLogin" data-theme="a" class="popupLogin ui-corner-all">
            <div style="padding:10px 20px;">
                <h3 id='login-status' align="center">登陆</h3>
                <label for="un" class="ui-hidden-accessible">用户名</label>
                <input type="text" name="user" id="un" value="" placeholder="用户名" data-theme="a">
                <label for="pw" class="ui-hidden-accessible">密码</label>
                <input type="password" name="pass" id="pw" value="" placeholder="密码" data-theme="a">
                <button onclick="onLogin()" class="ui-btn ui-corner-all ui-shadow ui-btn-b">登陆</button>
            </div>
        </div>
        
        <div data-role="page" data-theme="<?php echo $theme;?>" id="memobook">
            <div data-role="header">
                <div data-role="navbar">
                    <ul>
                        <li>
                            <!-- <a href="#popupLogin" data-ref="popup" data-position-to="window" data-transition="fade" class="ui-corner-all ui-shadow ui-btn-inline ui-icon-check ui-btn-a" data-iconpos="top" data-icon="user" id="userlink"><span id="username">用户</span></a> -->
                            <a href="#popupLogin1" data-rel="popup" data-position-to="window" data-transition="pop" data-iconpos="top" data-icon="user" id="userlink"><span class="username">未登录</span></a>
                            <div data-role="popup" id="popupLogin1" data-theme="a" class="popupLogin ui-corner-all">
                                <div style="padding:10px 20px;">
                                    <h3 id='login-status' align="center">登陆</h3>
                                    <label for="un" class="ui-hidden-accessible">用户名</label>
                                    <input type="text" name="user" id="un" value="" placeholder="用户名" data-theme="a">
                                    <label for="pw" class="ui-hidden-accessible">密码</label>
                                    <input type="password" name="pass" id="pw" value="" placeholder="密码" data-theme="a">
                                    <button onclick="onLogin()" class="ui-btn ui-corner-all ui-shadow ui-btn-b">登陆</button>
                                </div>
                            </div>
                        </li>
                        <li><a href="#" data-iconpos="top" data-icon="bullets" class="ui-btn-active ui-state-persist">笔记本</a></li>
                        <li><a href="#editmemo" data-iconpos="top" data-icon="edit" data-transition="none">编辑笔记</a></li>
                        <li><a href="#viewmemo" data-iconpos="top" data-icon="eye" data-transition="none">显示笔记</a></li>
                    </ul>
                </div>
            </div>

            <div data-role="content" id="memobook-content">
                <div data-role="collapsible-set" data-collapsed-icon="carat-r" data-expanded-icon="carat-d">
                    <div id="memobooks">
                        <div id="sentinel"></div>
                    </div>
                    <div class="ui-grid-a" id="memobookControl">
                        <div class="ui-block-a">
                            <a href="#" data-role="button" id="addMemobook" data-icon="plus">新增笔记本</a>
                        </div>
                        <div class="ui-block-b">
                            <a href="#" data-role="button" id="refreshMemobooks" data-icon="refresh">重新加载笔记列表</a>
                        </div>
                    </div>
                    
                    <script type="text/javascript">
                        function editMemo(){
                            var memo_id = $(this).attr('memoid');
                            //通过AJAX获取到memo的数据，比如标题以及Markdown内容
                            var params = '';
                            $.ajax({
                                url:'<?php echo $siteUrl; ?>/rest/memo/content/'+memo_id,
                                type:'GET',
                                datatype: "json",
                                //data:params,
                                success:function(json){
                                    $('input#memotitle').val(json.title);
                                    $('textarea#memoeditor').val(json.content);
                                },
                                error:function(msg){
                                    alert(arguments[1])
                                }
                            });
                        }
                        $('a.memolink').click(editMemo);
                        function memobookTemplate() {/*
<div data-role="collapsible" class="memobook" id="{$memobookID}">
    <h3>{$memobookName}<span class="ui-li-count">{$memoAmount}</span></h3>
    <p>
    <ul data-role="listview" data-icon="edit" id="memolist">
        {$memos}
    </ul>
    </p>
    <div class="ui-grid-a">
        <div class="ui-block-a">
            <a href="#" data-role="button" class="addMemo" data-icon="plus" data-mini="true">新增笔记</a>
        </div>
        <div class="ui-block-b">
            <a href="#" data-role="button" class="removeMemo" data-icon="minus" data-mini="true">删除笔记</a>
        </div>
    </div>
</div>
*/};
                        var memoTemplate = "<li><a href='#editmemo' class='memolink' memoid='{$memoID}'>{$memoTitle}</a></li>";
                        
                        function refreshMemobooks(){
                            $.ajax({
                                url:'<?php echo $siteUrl; ?>/rest/memo/index/',
                                type:'GET',
                                datatype: "json",
                                success:function(json){
                                    $('#memobooks').html("<div id='sentinel'></div>");
                                    //alert(data);
                                    for (var bookIndex in json.memobooks){
                                        var book = json.memobooks[bookIndex];
                                        var bookHtml = hereDoc(memobookTemplate);
                                        var memos = '';
                                        for (var memoIndex in book.memos){
                                            var memo = book.memos[memoIndex];
                                            var memoHtml = memoTemplate;
                                            memoHtml = memoHtml.replace('{$memoID}',memo.id);
                                            memoHtml = memoHtml.replace('{$memoTitle}',memo.title);
                                            memos += memoHtml;
                                        }
                                        bookHtml = bookHtml.replace('{$memobookID}',book.id);
                                        bookHtml = bookHtml.replace('{$memobookName}',book.name);
                                        bookHtml = bookHtml.replace('{$memoAmount}',book.amount);
                                        bookHtml = bookHtml.replace('{$memos}',memos);
                                        $('#sentinel').append(bookHtml);
                                    }
                                },
                                error:function(msg){
                                    alert(arguments[1])
                                },
                                complete:function(){
                                    $(".memobook").collapsible();
                                    $("ul#memolist").listview();
                                    $(".addMemo").buttonMarkup();
                                    $(".removeMemo").buttonMarkup();
                                    $('a.memolink').click(editMemo);
                                }
                            });
                        }
                        
                        $('a#refreshMemobooks').click(refreshMemobooks);
                        $(this).ready(refreshMemobooks);
                        
                        $('a#userlink').click(markupPanel);
                    </script>
                </div>
            </div>
        </div>

        <div data-role="page" data-theme="<?php echo $theme;?>" id="editmemo">
            <div data-role="header">
                <div data-role="navbar">
                    <ul>
                        <li>
                            <a href="#popupLogin2" data-rel="popup" data-position-to="window" data-transition="pop" data-iconpos="top" data-icon="user" id="userlink"><span class="username">未登录</span></a>
                            <div data-role="popup" id="popupLogin2" data-theme="a" class="popupLogin ui-corner-all">
                                <div style="padding:10px 20px;">
                                    <h3 id='login-status' align="center">登陆</h3>
                                    <label for="un" class="ui-hidden-accessible">用户名</label>
                                    <input type="text" name="user" id="un" value="" placeholder="用户名" data-theme="a">
                                    <label for="pw" class="ui-hidden-accessible">密码</label>
                                    <input type="password" name="pass" id="pw" value="" placeholder="密码" data-theme="a">
                                    <button onclick="onLogin()" class="ui-btn ui-corner-all ui-shadow ui-btn-b">登陆</button>
                                </div>
                            </div>
                        </li>
                        <li><a href="#memobook" data-iconpos="top" data-icon="bullets" data-transition="none">笔记本</a></li>
                        <li><a href="#" data-iconpos="top" data-icon="edit" class="ui-btn-active ui-state-persist">编辑笔记</a></li>
                        <li><a href="#viewmemo" data-iconpos="top" data-icon="eye" data-transition="none" id="viewmemo">显示笔记</a></li>
                    </ul>
                </div>
            </div>
            <div data-role="content" id="editmemo-content">
                <input name="memotitle" value="" id="memotitle"/>
                <textarea name="memoeditor" id="memoeditor"></textarea>
            </div>
            <script type="text/javascript">
                $("a#viewmemo").click(function(){
                    var markdown  = $('textarea#memoeditor').val();
                    var converter = new showdown.Converter();
                    var html      = converter.makeHtml(markdown);
                    $('#viewmemo-content').html(html);
                    var title     = $('input#memotitle').val();
                    $('h1#memotitle').html(title);
                });
                $('a#userlink').click(markupPanel);
            </script>
        </div>

        <div data-role="page" data-theme="<?php echo $theme;?>" id="viewmemo">
            <div data-role="header">
                <div data-role="navbar">
                    <ul>
                        <li>
                            <a href="#popupLogin3" data-rel="popup" data-position-to="window" data-transition="pop" data-iconpos="top" data-icon="user" id="userlink"><span class="username">未登录</span></a>
                            <div data-role="popup" id="popupLogin3" data-theme="a" class="popupLogin ui-corner-all">
                                <div style="padding:10px 20px;">
                                    <h3 id='login-status' align="center">登陆</h3>
                                    <label for="un" class="ui-hidden-accessible">用户名</label>
                                    <input type="text" name="user" id="un" value="" placeholder="用户名" data-theme="a">
                                    <label for="pw" class="ui-hidden-accessible">密码</label>
                                    <input type="password" name="pass" id="pw" value="" placeholder="密码" data-theme="a">
                                    <button onclick="onLogin()" class="ui-btn ui-corner-all ui-shadow ui-btn-b">登陆</button>
                                </div>
                            </div>
                        </li>
                        <li><a href="#memobook" data-iconpos="top" data-icon="bullets">笔记本</a></li>
                        <li><a href="#editmemo" data-iconpos="top" data-icon="edit" data-transition="none">编辑笔记</a></li>
                        <li><a href="#" data-iconpos="top" data-icon="eye" data-transition="none" class="ui-btn-active ui-state-persist">显示笔记</a></li>
                    </ul>
                </div>
            </div>
            <div data-role="header">
                <h1 id="memotitle">无标题Memo</h1>
            </div>
            <div data-role="content" id="viewmemo-content">
            </div>
            <script>
                $('a#userlink').click(markupPanel);
            </script>
        </div>
        
        <script type="text/javascript">
        </script>
        
        <div data-role="panel" id="panel_user" data-display="push" data-position="left" data-theme="b">
            <ul data-role="listview" id="userPanel">
                <li data-role="list-divider">用户信息</li>
                <li id="username"><a href="#login" id='loginBtn'>未登录</a></li>
            </ul>
        </div>

    </body>
</html>
