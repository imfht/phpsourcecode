</div>
<!-- End Main -->

<script type="text/javascript">
    $(function () {
        $('body').on('click','[openDialog]',function () {
            ajax_dialog($(this).attr('title'), $(this).attr('openDialog'), $(this).attr("dialog-width"));
        });
    });
</script>
</body>
</html>