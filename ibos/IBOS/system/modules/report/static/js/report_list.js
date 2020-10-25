define(['text!' + app.assetUrl + '/templates/default_list.html',
    app.assetUrl + '/js/util.js',
    app.assetUrl + '/js/report_detail.js',
    "userSelect"
], function(list_tpl, util, ReportDetail) {
    var local = Ibos.local;
    var List = {
        init: function(config) {
            var self = this;

            this.$page = appView.find(".initpage");

            config = config || {};
            this.offset = config.offset || 0;
            this.type = config.type || 'send';
            this.limit = local.get('report_list_size_' + this.type) || 10;
            this.keyword = config.keyword || {};
            this.first = true;
            this.getList(function(data) {
                self.render(data);
            });
        },
        getList: function(callback) {
            var self = this;
            util.fetch("report/api/getlist", {
                data: JSON.stringify({
                    limit: self.limit,
                    offset: self.offset,
                    type: self.type,
                    keyword: self.keyword
                })
            }).done(function(res) {
                if (res.isSuccess) {
                    callback && callback(res.data);
                } else {
                    Ui.tip(res.msg, 'danger');
                }
            });
        },
        draw: function(config) {
            var self = this;

            config = config || {};
            this.offset = config.offset || 0;
            this.type = config.type || this.type;
            this.limit = config.limit || this.limit;
            this.keyword = config.keyword || this.keyword;

            local.set('report_list_size_' + this.type, this.limit);

            this.first = true;

            this.getList(function(data) {
                self.render(data);
            });
        },
        render: function(data) {
            var count = data.count = +data.count;
            if (this.first) {
                if( count > this.limit ){
                    this.$page.show();
                    this.pagination(this.$page, count);
                }else{
                    this.$page.hide();
                }
            }
            this.renderList(data.list);
        },
        renderList: function(list) {
            var tpl = $.template(list_tpl, {
                list: list,
                type: this.type
            });
            appView.find(".page-list-mainer ul").html(tpl);
            appView.find(".page-list-mainer ul").find('[name="report[]"]').label();
            this.ajaxPopover();
        },
        pagination: function($page, count) {
            var self = this;
            var _settings = {
                items_per_page: this.limit,
                num_display_entries: 5,
                prev_text: false,
                next_text: false,
                renderer: "ibosRenderer",
                allow_jump: true,
                callback: function(page, elem) {
                    if (!self.first) {
                        self.offset = self.limit * page;
                        self.getList(function(data) {
                            self.render(data);
                        });
                    }
                    self.first = false;
                }
            };

            if (!$.fn.pagination) {
                $.getScript(Ibos.app.getStaticUrl("/js/lib/jquery.pagination.js"))
                    .done(function() {
                        $page.pagination(count, _settings);
                    });
            } else {
                $page.pagination(count, _settings);
            }
        },
        ajaxPopover: function() {
            appView.find("[data-node-type='loadReader']").each(function() {
                $(this).ajaxPopover(Ibos.app.url("report/api/getreviewcomment", {
                    type: "review",
                    repid: $.attr(this, "data-id")
                }));
            });

            //点评人员ajax
            appView.find("[data-node-type='loadCommentUser']").each(function() {
                $(this).ajaxPopover(Ibos.app.url("report/api/getreviewcomment", {
                    type: "comment",
                    repid: $.attr(this, "data-id")
                }));
            });
        }
    };
    var Info = {
        init: function(type, callback) {
            var self = this;
            this.type = type;
            this.getCount(function(data) {
                self.render(data);
                callback && callback();
            });
        },
        getCount: function(callback) {
            util.fetch('report/api/getcount').done(function(res) {
                if (res.isSuccess) {
                    callback && callback(res.data);
                } else {
                    Ui.tip(res.msg, 'danger');
                }
            });
        },
        render: function(data) {
            var size = local.get('report_list_size_' + this.type) || 10;
            appView.append($.template($("#report_detail").html(), $.extend(data, {
                size: size,
                type: this.type
            })));
        },
        search: function() {
            appView.find("#mn_search").search(function(val) {
                List.draw({
                    keyword: {
                        subject: val
                    }
                });
            }, function() {
                Ui.dialog({
                    title: "高级搜索",
                    content: $.template('high_search_tpl'),
                    lock: true,
                    zIndex: 5000,
                    init: function() {
                        var $start = $("#start_time"),
                            $end = $("#end_time");
                        $start.datepicker({
                            target: $end,
                            pickTime: true,
                            pickSeconds: false,
                            format: "yyyy-mm-dd hh:ii"
                        });
                        $end.datepicker({
                            pickTime: true,
                            pickSeconds: false,
                            format: "yyyy-mm-dd hh:ii"
                        });
                        $("#author").userSelect({
                            data: Ibos.data.get("user"),
                            type: "user"
                        });
                    },
                    ok: function() {
                        var $form = this.DOM.main.find("form");
                        List.draw({
                            keyword: $form.serializeObject()
                        });
                        this.close();
                    }
                });
            });
        }
    };

    appView.off("click.send").on("click.send", ".page-num-select li", function () {
        var $this = $(this),
            text = $this.text(),
            limit = text.match(/\d+/)[0];
        $(".page-num-select span").html(text);
        $this.addClass("active").siblings().removeClass("active");
        List.draw({
            limit: limit
        });
    });
    Ibos.events.add({
        showReportDetail: function(param, elem){
            var $el = $(elem),
                $item = $el.closest("li"),
                $detail = $item.find(".rp-detail"),
                hasInit = $item.attr("data-init") === "1" ? true : false;
                isDraft = param.status === '0',
                repid = param.id;
            // 若已缓存，则直接显示
            // 否则AJAX读取内容后，缓存并显示
            if (!hasInit) {
                $item.waiting(null, 'normal');

                var queue = [
                function(done){
                    ReportDetail.setConfig(repid, param.origin, param.type);
                    ReportDetail.showPort(function(data){
                        $detail.append( data );
                        done();
                    });
                },function(done){
                    ReportDetail.getCommentView(function(data){
                        $detail.append( data );
                        done();
                    });
                },function(done){
                    ReportDetail.getReader(function(data){
                        $detail.append( data );
                        done();
                    });
                }, function(done){
                    ReportDetail.stamp($detail, repid, function(){
                        done();
                    });
                }];
                util.queue(isDraft ? [queue[0]] : queue, function(){
                    ReportDetail.toggle($el, 'show');
                    $detail.show();
                    $item.attr("data-init", "1");
                    $item.waiting(false);
                });
            } else {
                ReportDetail.toggle($el, "show");
            }
        },
        hideReportDetail: function(param, elem){
            ReportDetail.toggle($(elem), "hide");
        }
    });
    return {
        List: List,
        Info: Info
    };
});
