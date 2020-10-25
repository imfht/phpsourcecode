KindEditor.plugin('downremoteimg', function(K) {
    var self = this, name = 'downremoteimg',
            remoteImgSaveUrl = K.undef(self.remoteImgSaveUrl, self.basePath + 'php/down_json.php');

    self.plugin.downremoteimg = {
        download: function() {
            var img = self.plugin.getSelectedImage();
            var src = img ? img.attr('src') : '';

            var html = self.html();
            var dialog = self.createDialog({
                name: name,
                width: 400,
                height: 250,
                title: "请稍后",
                body: "<div></div>",
                shadowMode: true
            });
            dialog.showLoading("远程图片下载中……");
            //POST
            K.ajax(remoteImgSaveUrl, function(res) {
                if (res.error === 0) {
                    self.html(res.info);
                    self.cmd.selection(true);
                    self.addBookmark();
                } else {
                    alert(res.info);
                }
                self.hideDialog();
            }, 'POST', {html: html, src: src}, 'json');
        }
    };

    self.clickToolbar(name, self.plugin.downremoteimg.download);
});
