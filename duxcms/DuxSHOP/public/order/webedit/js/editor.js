;(function ($) {
    $.fn.duxWebEditor = function (config) {

        //定义配置
        config = $.extend({
            label : {
                '{receive_name}' : '收货人{receive_name}',
                '{receive_tel}' : '收件人手机{receive_tel}',
                '{receive_address}' : '收件人地址{receive_address}',
                '{send_name}' : '寄件人{send_name}',
                '{send_tel}' : '寄件人电话{send_tel}',
                '{send_address}' : '寄件人地址{send_address}',
                '{money}' : '订单金额{money}',
                '{remark}' : '订单备注{remark}'
            }
        }, config);
        
        //定义标签
        var labelHtml = '';
        $.each(config.label, function (key, val) {
            labelHtml += '<option value="'+ key +'">'+val+'</option>';
        });

        //定义HTML
        var editHtml = '<div class="dux-web-editor">' +
            '<ul class="edit-tools">' +
            '<li class="active" data-type="select" data-model="true"><a href="javascript:;">选择</a></li>' +
            '<li class="" data-type="add" data-model="true"><a href="javascript:;" >添加</a></li>' +
            '<li data-type="edit" data-model="true"><a href="javascript:;">编辑文本</a></li>' +
            '<li data-type="del"><a href="javascript:;">删除</a></li>' +
            '<li>' +
            'x <input type="text" data-x="">' +
            'y <input type="text" data-y="">' +
            'w <input type="text" data-w="">' +
            'h <input type="text" data-h="">' +
            '</li>' +
            '<br>' +
            '<li data-type="size">' +
            '<select>' +
            '<option value="10px">10px</option>' +
            '<option value="12px" selected>12px</option>' +
            '<option value="14px">14px</option>' +
            '<option value="16px">16px</option>' +
            '<option value="18px">18px</option>' +
            '<option value="20px">20px</option>' +
            '</select>' +
            '</li>' +
            '<li data-type="content">' +
            '<select>' +
            '<option value="">标签内容</option>' +
            labelHtml +
            '</select>' +
            '</li>' +
            '</ul>' +
            '<div class="h-rule"></div>' +
            '<div class="v-rule"></div>' +
            '<div class="edit-body"></div>' +
            '</div>';
        $(this).html(editHtml);

        //定义基本元素
        var $webEditor = $(this).find('.dux-web-editor'),    //编辑器
            $editTools = $webEditor.find('.edit-tools'),
            $editBody = $webEditor.find('.edit-body'),
            $dataX = $editTools.find('[data-x]'),
            $dataY = $editTools.find('[data-y]'),
            $dataW = $editTools.find('[data-w]'),
            $dataH = $editTools.find('[data-h]'),
            $dataSize = $editTools.find('[data-type="size"]'),
            $dataContent = $editTools.find('[data-type="content"]');
        //定义工具变量
        var model = 'select';
        //定义坐标变量
        var startX,
            startY,
            diffX,
            diffY;
        //禁止拖动
        var dragging = false;

        /**
         * 初始化工具栏
         */
        var toolsInit = function () {
            $editTools.find('li > a').bind('click', function () {
                var $li = $(this).parent('li');
                var type = $li.data('type');
                var isModel = $li.data('model');
                if (!isModel) {
                    switch (type) {
                        case 'del':
                            toolDel();
                            break;
                    }
                } else {
                    $editTools.find('li').removeClass('active');
                    $li.addClass('active');
                    model = type;
                    modelSwitch();
                }
            });
            $editTools.find('li > select').bind('change', function () {
                var $li = $(this).parent('li');
                var type = $li.data('type');
                switch (type) {
                    case 'size':
                        toolSize($(this).val());
                        break;
                    case 'content':
                        toolContent($(this).val());
                        break;
                }
            });
            $dataX.bind('input textare', function () {
                $editBody.find('.box.active').css('left', $(this).val() + 'px');
            });
            $dataY.bind('input textare', function () {
                $editBody.find('.box.active').css('top', $(this).val() + 'px');
            });

            $dataW.bind('input textare', function () {
                $editBody.find('.box.active').width($(this).val());
            });
            $dataH.bind('input textare', function () {
                $editBody.find('.box.active').height($(this).val());
            });
        };

        /**
         * 初始化工具栏
         */
        toolsInit();

        /**
         * 内容标签
         */
        var toolContent =function (val) {
            if(val == '') {
                val = '请编辑内容';
            }
            $editBody.find('.active').text(val);
        };

        /**
         * 字体大小
         */
        var toolSize = function (val) {
            $editBody.find('.active').css('font-size', val);
        };

        /**
         * 删除元素
         */
        var toolDel = function () {
            $editBody.find('.active').remove();
        };

        /**
         * 模式切换
         */
        var modelSwitch = function () {
            $editBody.find('.active').removeClass('active');
            $editBody.removeClass('select add edit');
            $editBody.addClass(model);
            $editBody.unbind();

            switch (model) {
                case 'edit':
                    modelEdit();
                    break;
                case 'add':
                    modelAdd();
                    break;
                case 'select':
                default:
                    modelSelect();
                    break;
            }
        };

        /**
         * 编辑模式
         */
        var modelEdit = function () {
            $editBody.on('click', '.box', function (e) {
                $box = this;
                if ($($box).find('textarea').length > 0) {
                    return false;
                }
                $($box).addClass('active');
                var $textarea = $('<textarea></textarea>');
                $textarea.val($($box).html());
                $textarea.on('blur', function () {
                    $($box).removeClass('active');
                    $($box).text($(this).val());
                });
                $($box).html($textarea);
                $textarea.focus();

            });
        };

        /**
         * 选择模式
         */
        var modelSelect = function () {
            $el = $webEditor.find('.select');

            $el.bind('mousedown', function (e) {
                startX = e.pageX - parseInt($editBody.offset().left);
                startY = e.pageY - parseInt($editBody.offset().top);
                if (!$(e.target).hasClass('box')) {
                    $editBody.find('.active').removeClass('active');
                    return false;
                }
                dragging = true;
                if ($editBody.find('.active').length > 0) {
                    $editBody.find('.active').removeClass('active');
                }
                $(e.target).addClass('active');
                diffX = startX - e.target.offsetLeft;
                diffY = startY - e.target.offsetTop;

                $dataX.val(startX);
                $dataY.val(startY);
                $dataW.val($(e.target).width());
                $dataH.val($(e.target).height());

                var fontSize = $editBody.find('.active').css('font-size');
                $dataSize.find('select').val(fontSize);
                console.log(fontSize);

            });
            $el.bind('mousemove', function (e) {
                if (!dragging) {
                    return true;
                }
                var mvBox = $editBody.find('.active');
                var left = e.pageX - parseInt($editBody.offset().left) - diffX;
                var top = e.pageY - parseInt($editBody.offset().top) - diffY;
                mvBox.css('left', left + 'px');
                mvBox.css('top', top + 'px');
                $dataX.val(left);
                $dataY.val(top);

            });
            $el.bind('mouseup', function (e) {
                dragging = false;
                $dataContent.find('select').val('');
            });
            $editBody.bind('selectstart', function (e) {
                return false;
            });

        };

        /**
         * 添加模式
         */
        var modelAdd = function () {
            $el = $webEditor.find('.add');
            $el.bind('mousedown', function (e) {
                startX = e.pageX - parseInt($editBody.offset().left);
                startY = e.pageY - parseInt($editBody.offset().top);
                var box = $('<div>请编辑内容</div>');
                box.addClass('box active');
                box.css('top', startY + 'px');
                box.css('left', startX + 'px');
                $editBody.append(box);
                box = null;
                $dataX.val(startX);
                $dataY.val(startY);

            });
            $el.bind('mousemove', function (e) {
                var acBox = $editBody.find('.active');
                acBox.css('width', e.pageX - parseInt($editBody.offset().left) - startX + 'px');
                acBox.css('height', e.pageY - parseInt($editBody.offset().top) - startY + 'px');
                $dataX.val(e.pageX - parseInt($editBody.offset().left));
                $dataY.val(e.pageY - parseInt($editBody.offset().top));
                $dataW.val($(e.target).width());
                $dataH.val($(e.target).height());

            });
            $el.bind('mouseup', function (e) {
                if ($editBody.find('.active').length > 0) {
                    var acBox = $editBody.find('.active');
                    acBox.removeClass('active');
                    if (acBox.width() < 10 || acBox.height() < 10) {
                        acBox.remove();
                    }
                }
            });
            $editBody.bind('selectstart', function (e) {
                return false;
            });
        };
    };
})(jQuery);
