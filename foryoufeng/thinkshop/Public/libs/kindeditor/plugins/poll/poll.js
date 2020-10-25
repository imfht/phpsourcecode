KindEditor.plugin('poll', function(K) {
    var self = this, id, name = 'poll', typeBox, div, pollAddJson = K.undef(self.pollAddJson, ''), pollGetJson = K.undef(self.pollGetJson, '');
    self.plugin.poll = {
        edit: function() {

            var html = '<div class="ke-poll-body" style="padding:20px;">' +
                    //选项
                    '<div id="preChoice">' +
                    '<div class="ke-dialog-row">' +
                    '<label style="width:20px;">1</label>' +
                    '<input class="ke-input-text choice" type="text" name="choice[]" value="" style="width:340px;" /></div>' +
                    '<div class="ke-dialog-row">' +
                    '<label style="width:20px;">2</label>' +
                    '<input class="ke-input-text choice" type="text" name="choice[]" value="" style="width:340px;" /></div>' +
                    '<div class="ke-dialog-row">' +
                    '<label style="width:20px;">3</label>' +
                    '<input class="ke-input-text choice" type="text" name="choice[]" value="" style="width:340px;" /></div>' +
                    '</div>' +
                    '<div id="extendChoice"></div>' +
                    //添加
                    '<div class="ke-dialog-row ke-clearfix">' +
                    '<a class="ke-right" id="addChoice" href="javascript:;">+ 增加选项</a></div>' +
                    '<div class="ke-dialog-row"">' +
                    '<label for="keType" style="width:80px;">投票方式：</label>' +
                    '<select id="keType" name="type"></select>' +
                    '</div>' +
                    //更多设置
                    '<div class="ke-dialog-row ke-clearfix">' +
                    '<a id="moreSetting" href="javascript:;">更多设置</a></div>' +
                    '<div id="moreSettingBox" style="display:none;">' +
                    //截至时间
                    '<div class="ke-dialog-row"">' +
                    '<label for="keType" style="width:80px;">截至时间：</label>' +
                    '<select class="week">' +
                    '<option value="week">一周</option>' +
                    '<option value="15day">十五日</option>' +
                    '<option value="month">一个月</option>' +
                    '<option value="diy">自定义</option>' +
                    '</select>' +
                    ' <select class="year"></select>' +
                    ' <select class="month"></select>' +
                    ' <select class="day"></select>' +
                    ' <select class="hour"></select>' +
                    '</div>' +
                    //结果可见
                    '<div class="ke-dialog-row"">' +
                    '<label style="width:80px;">投票结果：</label>' +
                    '<input class="ke-inline-block" type="radio" name="visible" checked="" value="1"> 任何人可见 ' +
                    '<input class="ke-inline-block" type="radio" name="visible" value="0"> 投票后可见' +
                    '</div>' +
                    '</div>' +
                    '</div>',
                    dialog = self.createDialog({
                        name: name,
                        width: 450,
                        autoScroll: false,
                        title: "投票选项(至少填两项，每个选项请不要超过20个字)",
                        body: html,
                        yesBtn: {
                            name: "发起投票",
                            click: function(e) {
                                if (dialog.isLoading) {
                                    return;
                                }
                                var params = [];
                                if (id > 0) {
                                    params.push('id=' + id);
                                }
                                //选项
                                K(".choice", div).each(function() {
                                    if (K.trim(K(this).val()) !== "") {
                                        params.push(encodeURIComponent(K(this).attr('name')) + '=' + encodeURIComponent(K(this).val()));
                                    }
                                });
                                if (params.length === 0) {
                                    alert("投票选项不能为空");
                                    return;
                                }
                                if (params.length === 1) {
                                    alert("投票选项不能只有1项");
                                    return;
                                }
                                //投票方式
                                params.push('type=' + K(typeBox).val());
                                //截止时间
                                var expiration;
                                var date = new Date();
                                switch (K('.week', div).eq(0).val()) {
                                    case "week":
                                        date.setDate(date.getDate() + 7);
                                        expiration = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours();
                                        break;
                                    case "15day":
                                        date.setDate(date.getDate() + 15);
                                        expiration = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours();
                                        break;
                                    case "month":
                                        date.setMonth(date.getMonth() + 1);
                                        expiration = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours();
                                        break;
                                    default:
                                        var a = K(".month", div).eq(0).val();
                                        if (((4 === a || 6 === a || 9 === a || 11 === a) && K(".day").eq(0).val() > 30) || (2 === a && K(".day").eq(0).val() > 28)) {
                                            alert('截止日期不存在');
                                            return;
                                        }
                                        expiration = K('.year', div).eq(0).val() + "-" + K('.month', div).eq(0).val() + "-" + K('.day', div).eq(0).val() + " " + K('.hour', div).eq(0).val();
                                }
                                params.push('expiration=' + expiration);

                                //结果可见性
                                K("[name='visible']", div).each(function() {
                                    if (this.checked) {
                                        params.push('visible=' + K(this).val());
                                    }
                                });

                                //POST
                                dialog.showLoading("请稍后……");
                                K.ajax(pollAddJson, function(re) {
                                    if (1 === re.status) {
                                        var img = self.themesPath + 'common/poll.jpg';
                                        var html = '<img class="ke-poll" data-poll="' + re.info + '" src="' + img + '" width="90" height="90" />';
                                        self.insertHtml(html).hideDialog().focus();
                                    } else {
                                        dialog.hideLoading();
                                        alert(re.info);
                                    }
                                }, 'POST', params.join("&"), 'json');

                            }
                        }
                    });
            div = dialog.div;
            div.height('auto');

            K("#moreSetting", div).bind('click', function() {
                if (K("#moreSettingBox").css('display') !== 'none') {
                    K("#moreSettingBox").css('display', 'none');
                } else {
                    K("#moreSettingBox").css('display', 'block');
                }
            });
            var date = new Date();
            var yearBox = K('.year', div).get(0);
            var year = date.getFullYear();
            for (var i = 0; i < 2; i++) {
                yearBox.options[i] = new Option(year + i + "年", i + year);
            }
            yearBox.style.display = "none";

            var monthBox = K('.month', div).get(0);
            for (var i = 0; i < 12; i++) {
                monthBox.options[i] = new Option(i + 1 + "月", i + 1);
            }
            monthBox.style.display = "none";
            var dayBox = K('.day', div).get(0);
            for (var i = 0; i < 31; i++) {
                dayBox.options[i] = new Option(i + 1 + "日", i + 1);
            }
            dayBox.style.display = "none";
            var hourBox = K('.hour', div).get(0);
            for (var i = 0; i < 24; i++) {
                hourBox.options[i] = new Option(i + "时", i);
            }
            hourBox.style.display = "none";
            var weekBox = K('.week', div).eq(0);
            weekBox.bind('change', function() {
                if (K(this).val() === 'diy') {
                    yearBox.style.display = "inline-block";
                    monthBox.style.display = "inline-block";
                    dayBox.style.display = "inline-block";
                    hourBox.style.display = "inline-block";
                } else {
                    yearBox.style.display = "none";
                    monthBox.style.display = "none";
                    dayBox.style.display = "none";
                    hourBox.style.display = "none";
                }
            });

            typeBox = K('select[name="type"]', div).get(0);
            freshType();
            K('.choice', div).bind('change', freshType);
            K('#addChoice').bind('click', function() {
                var i = K("#preChoice", div).children().length + 1;
                var container = K("#extendChoice", div);
                var num = container.children().length + i;
                var template = K('<div class="ke-dialog-row">' +
                        '<label style="width:20px;">' + num + '</label>' +
                        '<input class="ke-input-text choice" type="text" name="choice[]" value="" style="width:340px;" /><span class="ke-delete"></span></div>');
                container.append(template);
                K(".ke-delete", template).bind('click', function() {
                    K(this).parent().remove();
                    var container = K("#extendChoice", div);
                    K('div.ke-dialog-row', container).each(function() {
                        var index = K(this).index();
                        K('label', this).html(index + i);
                    });
                    freshType();
                });
                K('.choice', div).unbind();
                K('.choice', div).bind('change', freshType);
            });
            function freshType(selected) {
                typeBox.options.length = 0;
                typeBox.options[0] = new Option("单选", 1);
                var index = 0;
                K('.choice', div).each(function() {
                    if (K.trim(K(this).val()) !== "") {
                        index++;
                    }
                    if (index > 1) {
                        typeBox.options[index - 1] = new Option("最多选" + index + "项", index);
                        if (index === selected) {
                            typeBox.options[index - 1].selected = "selected";
                        }
                    }
                });
            }

            var img = self.plugin.getSelectedPoll();
            if (img) {
                id = K.trim(img.attr('data-poll'));
                dialog.showLoading("载入中……");
                K.ajax(pollGetJson, function(re) {
                    if (1 === re.status) {
                        re = re.info;
                        var container = K("#preChoice", div);
                        container.children().remove();
                        K.each(re.option, function(key, val) {
                            var template = K('<div class="ke-dialog-row">' +
                                    '<label style="width:20px;">' + (1 + key) + '</label>' +
                                    '<input class="ke-input-text choice" type="text" name="choice[index_' + val.id + ']" value="' + val.polloption + '" style="width:340px;" /></div>');
                            container.append(template);
                        });
                        freshType(parseInt(re.type));
                        weekBox.val('diy').change().hide();
                        K('.year', div).val(re.year);
                        K('.month', div).val(re.month);
                        K('.day', div).val(re.day);
                        K('.hour', div).val(re.hour);

                        K("[name='visible']", div).each(function() {
                            if (this.value == re.visible) {
                                this.checked = "checked";
                            }
                        });
                    } else {
                        id = 0;
                    }
                    dialog.hideLoading();
                }, 'POST', {id: id});
            }
        },
        'delete': function() {
            self.plugin.getSelectedPoll().remove();
            // [IE] 删除图片后立即点击图片按钮出错
            self.addBookmark();
        }

    };
    self.clickToolbar(name, self.plugin.poll.edit);
});
