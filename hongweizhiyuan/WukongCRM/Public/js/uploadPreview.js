/*
*兼容浏览器(IE 谷歌 火狐) 不支持safari
*参数说明: Img:图片ID;Width:预览宽度;Height:预览高度;ImgType:支持文件类型;Callback:选择文件后回调方法;
*/
jQuery.fn.extend({
    uploadPreview: function (opts) {
        var _self = this, _this = $(this);
        opts = jQuery.extend({
            Img: "ImgPr",
            Width: 100,
            Height: 100,
            ImgType: ["gif", "jpeg", "jpg", "bmp", "png"],
            Callback: function () { }
        }, opts || {});
        _self.getObjectURL = function (file) {
            var url = null;
            if (window.createObjectURL != undefined) {
                url = window.createObjectURL(file);
            } else if (window.URL != undefined) {
                url = window.URL.createObjectURL(file);
            } else if (window.webkitURL != undefined) {
                url = window.webkitURL.createObjectURL(file);
            }
            return url;
        }
        _this.change(function () {
            if (this.value) {
                if (!RegExp("\.(" + opts.ImgType.join("|") + ")$", "i").test(this.value.toLowerCase())) {
                    alert("选择文件错误,图片类型必须是" + opts.ImgType.join("，") + "中的一种");
                    this.value = "";
                    return false;
                }
                if (navigator.userAgent.indexOf("MSIE") > -1) {
                    try {
                        $("#" + opts.Img).attr('src', _self.getObjectURL(this.files[0]));
                    } catch (e) {
                        var src = "";
                        var obj = $("#" + opts.Img);
                        var div = obj.parent("div")[0];
                        _self.select();
                        if (top != self) {
                            window.parent.document.body.focus();
                        } else {
                            _self.blur();
                        }
                        src = document.selection.createRange().text;
                        document.selection.empty();
                        obj.hide();
                        obj.parent("div").css({
                            'filter': 'progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)',
                            'width': opts.Width + 'px',
                            'height': opts.Height + 'px'
                        });
                        div.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = src;				
						
						files_name = this.files[0].name;
						files_size = (obj[0].fileSize/1024).toFixed(2);
						$("#" + opts.Img+"_name").html(files_name);
						$("#" + opts.Img+"_size").html(files_size);
                    }
                } else {
                    $("#" + opts.Img).attr('src', _self.getObjectURL(this.files[0]));
					files_name = this.files[0].name;
					files_size = (this.files[0].size/1024).toFixed(2);
					$("#" + opts.Img+"_name").html(files_name);
					$("#" + opts.Img+"_size").html(files_size);
                }
                opts.Callback();
            }
        });
    }
});

var seq_item = 1;
//增加一个文件
$('#btn_add_files').click(function(){
	var add_file_html = '';
	add_file_html += '<table  id="tables_files_'+seq_item+'" class="table table-striped">';
	add_file_html += '<tbody class="files">';
	add_file_html += '<tr class="template-upload fade in">';
	add_file_html += '<td width="20%">';
	add_file_html += '<div class="thumbnail thumb80">';
	add_file_html += '<img id="sec_pic_'+seq_item+'_prev" class="thumb80"/>';
	add_file_html += '</div>';
	add_file_html += '</td>';
	add_file_html += '<td width="35%">';
	add_file_html += '<p><span id="sec_pic_'+seq_item+'_prev_name">无</span></p>';
	add_file_html += '</td>';
	add_file_html += '<td width="25%">';
	add_file_html += '<p><span id="sec_pic_'+seq_item+'_prev_size">0</span> KB</p>';
	add_file_html += '</td>';
	add_file_html += '<td width="30%">';
	add_file_html += '<div class="btn btn-success fileinput-button">';
	add_file_html += '<span>选择文件</span>';
	add_file_html += '<input type="file" name="sec_pic[]" id="sec_pic_'+seq_item+'">';
	add_file_html += '</div>&nbsp;';
	add_file_html += '<a class="btn btn-warning fileinput-button btn_reduce_files">';
	add_file_html += '<span>取消</span>';
	add_file_html += '</a>';
	add_file_html += '</td>';
	add_file_html += '</tr>';
	add_file_html += '</tbody>';
	add_file_html += '</table>';
	$('.div_add').before(add_file_html);
	
	seq_item ++;
});

//去掉一个文件
$('body').on('click','.btn_reduce_files', function(){
	var selector_id = $(this).parents('table').attr('id');
	var item = selector_id.substr(13);
	$('#tables_files_'+item).remove();
});