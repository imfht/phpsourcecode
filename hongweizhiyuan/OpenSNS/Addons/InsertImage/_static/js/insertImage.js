var WEIBO_CONTENT_CLASS = '.weibo_post_box';

insert_image = {

    find: function (obj) {
        return $(this.obj).parents(WEIBO_CONTENT_CLASS).find(obj);
    },
    obj: 0,
    insertImage: function (obj) {
        if (insert_image.obj != 0)
            insert_image.close();
        insert_image.obj = obj;
        this.find('#insert_image').attr('onclick', 'insert_image.showBox()');

        var box_url = this.find('#box_url').val();


        $.post(box_url, {}, function (res) {
            var html = '<div class="XT_image XT_insert""><div class="triangle sanjiao" style="margin-left: 30px;"></div><div class="triangle_up sanjiao"  style="margin-left: 30px;"></div>' +
                '<div class="XT_face_main XT_insert_image" style="margin-left: 0px;"><div class="XT_face_title"><span class="XT_face_bt" style="float: left"><span>共&nbsp;<span id="upload_num_' + res.unid + '">0</span>&nbsp;张，还能上传&nbsp;<span id="total_num_' + res.unid + '">' + res.total + '</span>&nbsp;张（按住ctrl可选择多张）</span></span>' +
                '<a onclick="insert_image.close()" class="XT_face_close">X</a></div><div id="face" style="padding: 10px;">' + res.html + '</div></div></div>';
            insert_image.find('#hook_show').html(html);
        }, 'json');
    },
    /**
     * 移除图片接口
     * @param string unid ID的字符串
     * @param integer index 索引数
     * @param integer attachId 附件ID
     * @return void
     */
    removeImage: function (unid, index, attachId, obj) {

        // 移除附件ID数据
        insert_image.upAttachVal('del', attachId, obj);

        // 移除图像
        this.find('#li_' + unid + '_' + index).remove();
        // 移除附件ID项
        // ($('#ul_'+unid).find('li').length - 1 === 0) && $('#attach_ids').remove();
        // 动态设置数目
        insert_image.upNumVal(unid, 'dec');
    },
    /**
     * 更新附件表单值
     * @return void
     */
    upAttachVal: function (type, attachId) {
        var $attach_ids = this.find('#attach_ids');
        var attachVal = $attach_ids.val();
        var attachArr = attachVal.split(',');
        var newArr = [];

        for (var i in attachArr) {
            if (attachArr[i] !== '' && attachArr[i] !== attachId.toString()) {
                newArr.push(attachArr[i]);
            }
        }
        type === 'add' && newArr.push(attachId);
        $attach_ids.val(newArr.join(','));
    },
    /**
     * 更新上传显示数目
     * @param string unid 唯一ID
     * @param string type 更新类型，inc增加；dec减少
     * @return void
     */
    upNumVal: function (unid, type) {


        var $uploadNum = this.find('#upload_num_' + unid),
            $totalNum = this.find('#total_num_' + unid);
        switch (type) {
            case 'inc':
                // 动态设置数目 - 增加
                $uploadNum.html(parseInt($uploadNum.html()) + 1);
                $totalNum.html(parseInt($totalNum.html()) - 1);
                break;
            case 'dec':
                // 动态设置数目 - 减少
                $uploadNum.html(parseInt($uploadNum.html()) - 1);
                $totalNum.html(parseInt($totalNum.html()) + 1);
                break;
        }
    },
    close: function () {
        this.find('.XT_image').remove();
        this.find('.attach_ids').remove();
        this.find('#insert_image').attr('onclick', 'insert_image.insertImage(this)');
        this.obj = 0;
    },
    showBox: function () {
        $('.XT_image').css('z-index', '1005');
    },
    /**
     * 添加loading效果
     * @param string unid 唯一ID
     * @return void
     */
    addLoading: function (unid) {
        var loadingHtml = '<li id="loading_' + unid + '" class="load"><span><img src="' + window.Think.ROOT + '/Addons/InsertImage/_static/image/loading.gif" /></span></li>';
        this.find('#btn_' + unid).before(loadingHtml);
    },
    /**
     * 移除loading效果
     * @param string unid 唯一ID
     * @return void
     */
    removeLoading: function (unid) {
        this.find('#loading_' + unid).remove();
    }

}



