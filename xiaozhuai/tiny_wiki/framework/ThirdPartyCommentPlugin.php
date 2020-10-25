<?php
    $thirdPartyInvoke = null;
?>


<?php
if(isset($BOOK["duoshuo"]) && $BOOK["duoshuo"]!="") {
    $thirdPartyInvoke = "loadDuoshuo";
?>
    <script>var duoshuoQuery = {short_name: "<?php echo $BOOK["duoshuo"]; ?>"};</script>
    <script src="http://static.duoshuo.com/embed.js"></script>
    <script>
        function loadDuoshuo(path) {
            var t = $("#book-menu-list a[href='#"+path+"']").html().replace(/[&nbsp;|└|─|├|\│]*/, "");
            var el = document.createElement('div');
            el.setAttribute('class', 'ds-thread');
            el.setAttribute('data-thread-key', path);
            el.setAttribute('data-title', t);
            el.setAttribute('data-url', window.location.href);
            DUOSHUO.EmbedThread(el);
            $(".content-side").append(el);
        }
    </script>
<?php
}
?>


<!-- add another plugin -->
<!-- ... -->


<!-- invoke -->
<script>
    function loadThirdPartyCommentPlugin(path) {
        <?php if($thirdPartyInvoke==null){
            echo "/* no third party comment plugin enable, do nothing */";
        }else{
            echo $thirdPartyInvoke."(path);";
        }
        ?>
    }
</script>

