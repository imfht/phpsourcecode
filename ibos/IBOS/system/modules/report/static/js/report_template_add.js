var app = Ibos.app;
define(['text!'+ app.assetUrl +'/templates/template_add.html',
        app.assetUrl +'/js/util.js',
        "userSelect"
], function(tpl, util){
    var Template = {
        init: function(){
            this.getCharge();
        },
        data: null,
        getTemplate: function(id){
            return util.fetch('report/api/formtemplate&tid=' + id, {
                type: 'get'
            });
        },
        getCharge: function(){
            var self = this;
            util.fetch('report/api/getcharge').done(function(res){
                res.isSuccess && (self.charges = res.data);
            });
        },
        charges: null,
        getIcons: function(){
            return util.fetch('report/api/getpicture', {
                type: 'get'
            });
        },
        fieldsFilter: function(data){
            var fields = [];
            $.map(data.fields, function(item, index){
                if( item == null || $.trim(item.fieldname) === "" ){return;}
                var fieldvalue = item.fieldvalue,
                    str = "";
                if( fieldvalue ){
                    for(var i=0, len = fieldvalue.length; i<len; i++){
                        if( !(fieldvalue[i] == null || $.trim(fieldvalue[i]) === "") ){
                            str += fieldvalue[i] + ',';
                        }
                    }
                    item.fieldvalue = str.slice(0,-1);
                }
                fields.push(item);
            });
            data.fields = fields;
        },
        formatData: function(){
            var self = this;
            var ret = [];
            var $elems = $("#nav_main_list > li");
            $.each($elems, function(index, elem){
                var $elem = $(elem);
                var fieldname = $.trim($elem.find('[name$="[fieldname]"]').val());
                if( fieldname ){
                    var parentData = {
                        fieldname: U.entity.escape($elem.find('[name$="[fieldname]"]').val()),
                        fieldtype: $elem.find('[name$="[fieldtype]"]').val(),
                        iswrite: +$elem.find('[name$="[iswrite]"]').val(),
                        fieldsort: index,
                        fieldvalue: ''
                    };
                    if( parentData.fieldtype === "7" ){
                        var str = "",
                            value = "";
                        $elem.find(".nav-child-list input").each(function(index, elem){
                            value = $.trim(elem.value);
                            if( value ){
                                str += value + ",";
                            }
                        });
                        parentData.fieldvalue = str.slice(0,-1);
                    }
                    ret.push(parentData);
                }
            });
            return ret;
        },
        save: function(data){
            if( this.loading ) return;
            var self = this;
            this.loading = true;
            data.template.autonumber = U.entity.escape(data.template.autonumber);
            data.template.tname = U.entity.escape(data.template.tname);
            data.fields = this.formatData();
            util.fetch("report/api/savetemplate", {
                type: 'post',
                data: JSON.stringify(data),
                contentType: "application/json;utf-8"
            }).done(function(res) {
                self.loading = false;
                if(res.isSuccess){
                    Ui.tip(res.msg);
                    location.hash = "#manager/index";
                }else{
                    Ui.tip(res.msg, 'danger');
                }
            });
        }
    };
    Template.init();
    
    var Field = function() {
        this.$list = $("#nav_main_list");
        this.init();
    };
    Field.prototype.init = function() {
        this.field = new Ibos.OrderList(this.$list, "new_main_nav");
        this.bind();
    };
    Field.prototype.bind = function() {
        var self = this;
        this.$list
            .on("click", ".o-trash", function() {
                self.$list.find('[data-id="'+ this.getAttribute('data-id') +'"]').remove();
            })
            .on("click", ".operate-group", function() {
                self.addChildItem( this.getAttribute('data-id') );
            })                
            .on("click", ".o-close", function() {
                var id = this.getAttribute('data-child-id'),
                    $node = $('[data-child-id="'+ id +'"]'),
                    $ul = $node.closest('ul');
                $node.remove();
                $ul.find('li[data-child-id]:last').addClass('msts-last');
            })
            .on("change", "[name$='[fieldtype]']", function() {
                var $list = self.field.updateItem({
                    id: this.getAttribute('data-id'),
                    fieldtype: this.value,
                    fieldname: $(this).closest('li').find('[name$="[fieldname]"]').val()
                });
                $list.find('[data-toggle="switch"]').iSwitch();
            })
            .on("change", "[data-toggle='switch']", function() {
                this.value = +this.checked;
            });
    };
    Field.prototype.add = function(data) {
        data = $.extend({
            fieldname: "",
            fieldtype: 1,
            iswrite: "",
            maxselectnum: "",
            fieldvalue: ''
        }, data);

        var $li = this.field.addItem(data);
        $li.find('[data-toggle="switch"]').iSwitch();
    };
    Field.prototype.addChildItem = function(id){
        var $childItem = $('#sys_child_'+ id +'_body').find('[data-act="childItem"]'),
            child_id = new Date().getTime();
        var $lastChild = $.tmpl('new_nav', {
            index: child_id,
            id: id
        });
        $childItem.before( $lastChild ).siblings(':not([data-child-id="'+ child_id +'"])').removeClass('msts-last');
    };

    var userInfo = Ibos.data.getUser("u_"+ Ibos.app.g('userid'));
    var tnamePreview = {
        init: function(){
            this.$autonumber = $('[name="template[autonumber]"]');
            this.$tname = $('[name="template[tname]"]');
            this.$perview= $('#tname_preview span');

            this.bind();
            this.preview(this.$autonumber.val());
        },
        preview: function(value){
            var self = this;
            value = U.entity.escape(value);
            this.$perview.html(value.replace(/{.*?}/g, function(match){
                return self.autoType[match] ? self.autoType[match] : (match === '{T}' ? self.$tname.val() : match);
            }));
        },
        bind: function(){
            var self = this;
            this.$tname.on('input propertychange', function(){
                self.preview(self.$autonumber.val());
            });
            this.$autonumber.on('input propertychange', function(){
                self.preview(this.value);
            });
        },
        autoType: {
            "{Y}": new Date().getFullYear(),
            "{M}": (function(){
                var month = new Date().getMonth() + 1;
                return month > 9 ? month : '0' + month;
            })(),
            "{D}": (function(){
                var day = new Date().getDate();
                return day > 9 ? day : '0' + day;
            })(),
            "{H}": Ibos.data.get("department").department[userInfo.deptid].text,
            "{U}": userInfo.text
        }
    };

    return function(){
        var args = arguments,
            tId = typeof args[0] === 'string' ? args[0] : '',
            done = typeof args[0] === 'function' ? args[0] : args[1],
            type = tId ? 'edit' : 'add';

        var field;

        util.queue([function(done){
            appView.html( tpl );
            if( type === 'add' ){
                appView.append( $.template('template_add_tpl', {
                    tname: "",
                    pictureurl: "default",
                    tid: "",
                    autonumber: ""
                }) );
                done();
            }   

            if( type === 'edit' ){
                Template.getTemplate(tId).done(function(res){
                    if( res.isSuccess ){
                        Template.data = res.data;
                        appView.append( $.template('template_add_tpl', Template.data.template) );
                        done();
                    }else{
                        Ui.tip(res.msg, 'danger');
                    }
                });
            }
        }, function(done){
            tnamePreview.init();
            appView.find('[data-toggle="tooltip"]').tooltip();

            field = new Field();

            //初始化主导航拖拽
            field.$list.sortable({
                handle: ".drap-area",
                cursor: "move",
                connectWith: "#nav_main_list, .nav-child-list",
                placeholder: "sortable-placeholder",
                tolerance: "pointer",
                revert: true
            }).disableSelection();

            if( type === 'edit' ){
                var fields = Template.data.fields;
                for(var i=0; i<fields.length; i++){
                    fields[i].fieldname = U.entity.unescape(fields[i].fieldname);
                    field.add(fields[i]);
                }
            }
            if( type === 'add' ){
                field.add();    
            }

            appView.on("click", '[data-act="add_main"]', function(){
                field.add();
            });
            done();
        }], function(){
            done();
        });

        Ibos.evt.add({
            tplIcon: function(param, elem){
                var dialog = Ui.dialog({
                    title: "图标库",
                    lock: true,
                    padding: 0,
                    zIndex: 5000,
                    init: function(){
                        var $parent = this.DOM.main,
                            self = this,
                            breforeNode = $({});
                        $parent.on("click", "li", function(){
                            breforeNode.removeClass('active');
                            var $this = $(this);
                            $this.addClass('active');
                            self.activeName = $this.data('name');
                            breforeNode = $this;
                        });
                    },
                    ok: function(){
                        if( this.activeName ){
                            $(elem).attr('class', 'o-rp-template icon-middle ' + this.activeName).siblings('input').val( this.activeName );
                        }
                    },
                    cancel: true
                });
                Template.getIcons().done(function(res){
                    if( res.isSuccess ){
                        dialog.content( $.template('tpl_icons_tpl', res) );
                    }else{
                        dialog.content('');
                        Ui.tip(res.msg, 'danger');
                    }
                });
            },
            "sure": function(param, elem, evt){
                if( $.trim(tnamePreview.$tname.val()) ){
                    var data = $("#tmpl_create").serializeObject();
                    var fields = Template.formatData();
                    if( fields.length === 0 ){
                        Ui.tip('请至少填写一个字段名称', 'danger');
                        return;
                    }
                    if( data.template.tid ){
                        Template.save(data);
                        return;
                    }
                    require(['text!'+ app.assetUrl +'/templates/access_set.html'], function(tmpl){
                        Ui.dialog({
                            title: "设置模板",
                            lock: true,
                            zIndex: 5000,
                            content: $.template(tmpl, {
                                charges: Template.charges,
                                set: {},
                                isChecked: function() {                          
                                    return false;
                                }
                            }),
                            init: function(){
                                var $parent = this.DOM.main;
                                $parent.find("[name='template[uid]'], [name='template[upuid]']").userSelect({
                                    type: "user",
                                    data: Ibos.data.get("user")
                                });
                                $parent.find("[type='checkbox']").label();
                            },
                            ok: function(){
                                var $parent = this.DOM.main;
                                var convert = $.extend(true, data, {template: {uptype : ""} }, $parent.find("form").serializeObject());
                                convert.template.uid = convert.template.uid.replace(/u_/g, '');
                                convert.template.upuid = convert.template.upuid.replace(/u_/g, '');
                                Template.save( convert );
                                return false;
                            },
                            okVal: "完成",
                            cancel: true
                        });
                    });
                }else{
                    tnamePreview.$tname.blink().focus();
                }
            }
        });
    };
});