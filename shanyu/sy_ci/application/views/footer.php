</div>

<div class="footer clearfix">
    <div class="pull-right">spend <strong>{elapsed_time}</strong> seconds.</div>
</div>

</div>
</div>

</div>
<script src="/static/js/jquery.pjax.min.js"></script>
<script>
    $.pjax({
            selector: 'a',
        container: '#container', //内容替换的容器
        show: false,  //展现的动画，支持默认和fade, 可以自定义动画方式，这里为自定义的function即可。
        cache: 0,  //是否使用缓存
        storage: false,  //是否使用本地存储
        title: $('#title').text(), //标题后缀
        filter: function(){},
        callback: function(){}
    });
</script>
</body>
</html>