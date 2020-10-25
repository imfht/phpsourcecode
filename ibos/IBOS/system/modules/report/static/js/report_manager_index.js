define(['text!' + app.assetUrl + '/templates/manager_index.html',
    app.assetUrl + '/js/util.js',
    "userSelect"
], function(tpl, util) {
    var rootScope = app.g('rootScope');
    var managerList = {
        dom: {},
        data: null,
        init: function() {
            this.dom.$tmplList = $("#tmpl_list");
            this.render();
            this.getCharge();
        },
        getTmplList: function(callback) {
            util.fetch("report/api/managertemplate", {
                type: "post",
                data: {
                    apiType: "web"
                }
            }).done(function(res) {
                if (res.isSuccess) {
                    callback && callback(res.data);
                } else {
                    Ui.tip(res.msg, 'danger');
                }
            });
        },
        render: function(data) {
            var self = this;
            this.getTmplList(function(data) {
                self.data = self.convert(data);
                self.dom.$tmplList.html($.template("templ_item_tpl", {
                    data: data
                }));
            });
        },
        convert: function(data) {
            for (var i = 0, ilen = data.length; i < ilen; i++) {
                for (var j = 0, field, jlen = data[i].field.length; j < jlen; j++) {
                    field = data[i].field[j];
                    field.fieldtext = util.fieldType[field.fieldtype - 1];
                }
            }
            return data;
        },
        dataFilter: function(id) {
            var data = this.data;
            for (var i = 0; i < data.length; i++) {
                if (data[i].tid === id) {
                    return data[i];
                }
            }
        },
        access: function(id, type) {
            var data = this.dataFilter(id);
            if (type === 'del') {
                return data['deltemplate'];
            }
            if (type === "edit") {
                return data['edittemplate'];
            }
            if (type === "set") {
                return data['settemplate'];
            }
        },
        del: function(elem, id) {
            var isDel = this.access(id, 'del');
            if (isDel) {
                Ui.confirm("删除模板后不会影响到之前用此模板发出的汇报，确认要删除吗？", function() {
                    util.fetch('report/api/deltemplte', {
                        data: JSON.stringify({
                            tid: id
                        })
                    }).done(function(res) {
                        Ui.tip(res.msg, res.isSuccess ? "" : "danger");
                        if (res.isSuccess) {
                            elem.closest("li").remove();
                        }
                    });
                });
            } else {
                Ui.tip('没权限删除该模板', 'danger');
            }
        },
        edit: function(elem, id) {
            var isEdit = this.access(id, 'edit');
            if (isEdit) {
                location.hash = "#template/edit/" + id;
            } else {
                Ui.tip('没权限编辑该模板', 'danger');
            }
        },
        set: function(elem, id) {
            var isSet = this.access(id, 'set'),
                self = this;
            if (isSet) {
                require(['text!' + app.assetUrl + '/templates/access_set.html'], function(content) {
                    util.fetch("report/api/settemplate", {
                        data: JSON.stringify({
                            tid: id
                        })
                    }).done(function(setRes) {
                        content = $.template(content, {
                            charges: self.charges,
                            set: setRes.data,
                            isChecked: function(currentChargeUpType) {
                                for(var i = 0; i < setRes.data.uptype.length; i++) {
                                    var item = setRes.data.uptype[i];
                                    if (item.uptype.toString() === currentChargeUpType.toString()) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                        });
                        var dialog = Ui.dialog({
                            title: "设置模板",
                            lock: true,
                            zIndex: 5000,
                            content: content,
                            init: function() {
                                var $parent = this.DOM.main;
                                $parent.find("[name='template[uid]']").userSelect({
                                    type: "user",
                                    data: Ibos.data.get("user")
                                });
                                $parent.find("[name='template[upuid]']").userSelect({
                                    type: "user",
                                    data: Ibos.data.get("user")
                                });
                                $parent.find("[type='checkbox']").label();
                                $parent.find('[data-toggle="tooltip"]').tooltip({
                                    placement: 'right'
                                });
                            },
                            ok: function() {
                                var $parent = this.DOM.main;
                                var data = $parent.find("form").serializeObject().template;
                                data.upuid = data.upuid.replace(/u_/g, '');
                                data.uid = data.uid.replace(/u_/g, '');
                                util.fetch("report/api/settemplate", {
                                    data: JSON.stringify($.extend(data, {
                                        tid: id,
                                        uptype: U.getCheckedValue("template[uptype]", $parent)
                                    }))
                                }).done(function(res) {
                                    if (res.isSuccess) {
                                        Ui.tip(res.msg);
                                        dialog.close();
                                    } else {
                                        Ui.tip(res.msg, 'danger');
                                    }
                                });
                                return false;
                            },
                            okVal: "完成",
                            cancel: true
                        });
                    });
                });
            } else {
                Ui.tip('没权限设置该模板', 'danger');
            }
        },
        preview: function(elem, id) {
            location.hash = 'preview/' + id;
        },
        charges: [],
        getCharge: function() {
            var self = this;
            util.fetch('report/api/getcharge').done(function(res) {
                res.isSuccess && (self.charges = res.data);
            });
        }
    };
    var storeTmpl = {
        getShopList: function(callback) {
            util.fetch("report/api/shoplist", {
                type: "post"
            }).done(function(res) {
                if (res.isSuccess) {
                    callback && callback(res.data);
                } else {
                    Ui.tip(res.msg, 'danger');
                }
            });
        },
        getTmplDeatil: function(id) {
            util.fetch("report/api/shoplist", {
                type: "post"
            }).done(function(res) {
                if (res.isSuccess) {
                    callback && callback(res.data);
                } else {
                    Ui.tip(res.msg, 'danger');
                }
            });
        },
        convert: function(data) {
            for (var i = 0, field, ilen = data.field.length; i < ilen; i++) {
                field = data.field[i];
                field.fieldtext = util.fieldType[field.fieldtype - 1];
            }
            return data;
        },
        getTplItem: function(id) {
            var data = this.data;
            for (var i = 0; i < data.length; i++) {
                for (var j = 0; j < data[i].template.length; j++) {
                    var template = data[i].template[j];
                    if (template.tid == id) {
                        return this.convert(template);
                    }
                }
            }
        }
    };
    return function(done) {
        util.queue([
            function(done) {
                appView.html(tpl);
                appView.append($.template('templ_tpl', {
                    rootScope: rootScope
                }));
                done();
            },
            function(done) {
                managerList.init();
                appView.off('click.tpl').on('click.tpl', '[data-evt]', function() {
                    var id = this.getAttribute('data-id'),
                        evtName = this.getAttribute('data-evt');

                    managerList[evtName].call(managerList, this, id);
                    return false;
                });
                done();
            }
        ], function() {
            done();
        });
        Ibos.events.add({
            addTmpl: function(param, elem) {
                var dialog = Ui.dialog({
                    title: "模板商城",
                    lock: true,
                    padding: 0,
                    zIndex: 5000,
                    init: function() {
                        var $parent = this.DOM.content,
                            $detail,
                            id;
                        $parent.off('click').on("click", ".tmpl-store-item", function() {
                            $detail = $detail ? $detail : $parent.find(".tmpl-store-detail");
                            id = this.getAttribute("data-id");

                            if ($(this).hasClass("exist")) {
                                return;
                            }
                            $detail.html($.template("store_tmpl_detail", {
                                data: storeTmpl.getTplItem(id)
                            })).animate({
                                right: 0
                            });
                        }).on("click", ".tmpl-detail-close", function() {
                            $detail.animate({
                                right: -300
                            });
                        }).on("click", ".template-add", function() {
                            var id = this.getAttribute("data-id"),
                                _this = this;
                            if (this.loading) return;
                            this.loading = true;
                            util.fetch('report/api/addtemplate', {
                                data: JSON.stringify({
                                    tid: id
                                })
                            }).done(function(res) {
                                Ui.tip(res.msg, res.isSuccess ? "" : "danger");
                                if (res.isSuccess) {
                                    $parent.find('.tmpl-store-item[data-id="' + id + '"]').addClass("exist");
                                    $detail.animate({
                                        right: -300
                                    }, function() {
                                        _this.loading = false;
                                    });
                                    managerList.render();
                                } else {
                                    _this.loading = false;
                                }
                            });
                        }).on("scroll", function() {
                            $detail = $detail ? $detail : $parent.find(".tmpl-store-detail");
                            $detail.css("top", $(this).scrollTop());
                        });
                    }
                });
                storeTmpl.getShopList(function(data) {
                    storeTmpl.data = data;
                    dialog.content($.template("tmpl_store_tpl", {
                        data: data
                    }));
                });
            }
        });
    };
});