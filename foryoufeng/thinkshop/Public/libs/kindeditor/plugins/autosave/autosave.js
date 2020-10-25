/*******************************************************************************
 * KindEditor - WYSIWYG HTML Editor for Internet
 * Copyright (C) 2006-2011 kindsoft.net
 *
 * @author Roddy <luolonghao@gmail.com>
 * @site http://www.kindsoft.net/
 * @licence http://www.kindsoft.net/license.php
 *******************************************************************************/

KindEditor.plugin('autosave', function(K) {
    var self = this, statusbar = self.statusbar, autoSaveInterval = K.undef(self.autoSaveInterval, 30), autosave, savedatat, savedatac;

    if (!self.autoSaveMode) {
        return;
    }
    function setcookie(cookieName, cookieValue, seconds, path, domain, secure) {
        if (cookieValue == '' || seconds < 0) {
            cookieValue = '';
            seconds = -2592000;
        }
        if (seconds) {
            var expires = new Date();
            expires.setTime(expires.getTime() + seconds * 1000);
        }
        document.cookie = escape(cookieName) + '=' + escape(cookieValue)
                + (expires ? '; expires=' + expires.toGMTString() : '')
                + (path ? '; path=' + path : '/')
                + (domain ? '; domain=' + domain : '')
                + (secure ? '; secure' : '');
    }

    function getcookie(name, nounescape) {
        var cookie_start = document.cookie.indexOf(name);
        var cookie_end = document.cookie.indexOf(";", cookie_start);
        if (cookie_start == -1) {
            return '';
        } else {
            var v = document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length));
            return !nounescape ? unescape(v) : v;
        }
    }

    function setAutosave() {
        autosave = !autosave;
        K('.tips', statusbar).html(autosave ? '数据自动保存已开启' : '数据自动保存已关闭');
        setcookie('editorautosave', autosave ? 1 : -1, 2592000);
        savedataTime();
    }

    function unloadAutoSave() {
        if (autosave) {
            saveData();
        }
    }

    function saveData() {
        var data = self.edit.html();
        if (data) {
            saveUserData('Kindeditor', data);
        }
    }

    function saveUserData(name, data) {
        try {
            if (window.localStorage) {
                localStorage.setItem(name, data);
            } else if (window.sessionStorage) {
                sessionStorage.setItem(name, data);
            }
        } catch (e) {
            if (BROWSER.ie) {
                if (data.length < 54889) {
                    with (document.documentElement) {
                        setAttribute("value", data);
                        save(name);
                    }
                }
            }
        }
    }

    function loadData() {
        var data = loadUserData('Kindeditor');
        self.edit.html(data);
    }

    function loadUserData(name) {
        if (window.localStorage) {
            return localStorage.getItem(name);
        } else if (window.sessionStorage) {
            return sessionStorage.getItem(name);
        } else if (BROWSER.ie) {
            with (document.documentElement) {
                load(name);
                return getAttribute("value");
            }
        }
    }


    function savedataTime() {
        if (!autosave) {
            K(".svdsecond", statusbar).html("开启自动保存");
            K(".svdsecond", statusbar).attr('title', "点击开启自动保存");
            return;
        }
        if (!savedatac) {
            savedatac = autoSaveInterval;
            saveData();
            d = new Date();
            var h = d.getHours();
            var m = d.getMinutes();
            h = h < 10 ? '0' + h : h;
            m = m < 10 ? '0' + m : m;
            K('.tips', statusbar).html('数据已于 ' + h + ':' + m + ' 保存');
        }
        K('.svdsecond', statusbar).html(savedatac + ' 秒后保存</a> ');
        savedatac -= 10;
    }

    function init() {
        if (loadUserData('Kindeditor')) {
            self.edit.div.before('<div class="ke-notice" ><a class="ke-dialog-icon-close" href="javascript:;" title="清除内容" ></a>您有上次未提交成功的数据 <a href="javascript:;" class="restore"><strong>恢复数据</strong></a></div>');
            K('.ke-notice .ke-dialog-icon-close', self.container).bind('click', function() {
                saveUserData('Kindeditor', '');
                K('.ke-notice', self.container).remove();
            });
            K('.ke-notice .restore', self.container).bind('click', function() {
                if (!loadUserData('Kindeditor')) {
                    alert('没有可以恢复的数据！');
                    return;
                }
                if (!confirm('此操作将覆盖当前帖子内容，确定要恢复数据吗？')) {
                    return;
                }
                loadData();
                K('.ke-notice', self.container).remove();
            });
        }
        statusbar.css('display', 'block');

        statusbar.append('<div class="tips"></div>');
        statusbar.append('<div class="handle"><a class="svdsecond" title="点击关闭自动保存" href="javascript:;">30 秒后保存</a> <a href="javascript:;" class="svd">保存数据</a> | <a href="javascript:;" class="rst">恢复数据</a></div>');
        autosave = !getcookie('editorautosave') || getcookie('editorautosave') == 1 ? 1 : 0;
        savedatac = autoSaveInterval;
        K(".svdsecond", statusbar).bind('click', setAutosave);
        K(".svd", statusbar).bind('click', function() {
            saveData();
            K('.tips', statusbar).html('数据已保存');
        });
        K(".rst", statusbar).bind('click', function() {
            if (!loadUserData('Kindeditor')) {
                alert('没有可以恢复的数据！');
                return;
            }
            if (!confirm('此操作将覆盖当前帖子内容，确定要恢复数据吗？')) {
                return;
            }
            loadData();
        });
        savedataTime();
        savedatat = setInterval(savedataTime, 10000);

        self.container.bind('clearData', function() {
            saveUserData('Kindeditor', '');
        });
    }

    if (self.isCreated) {
        init();
    } else {
        self.afterCreate(init);
    }
    self.beforeRemove(function() {
        K(".svdsecond", statusbar).unbind();
        K(".svd", statusbar).unbind();
        K(".rst", statusbar).unbind();
        clearInterval(savedatat);
        self.container.unbind('clearData');
    });
});
