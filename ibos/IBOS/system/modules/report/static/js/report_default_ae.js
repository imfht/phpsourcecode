define([
    app.assetUrl + '/js/util.js',
    'moment',
    'userSelect',
    'datetimepicker',
    'webUploader',
    'webUploadHandler',
    'ueditor'
], function (util, moment, userSelect, datatimepicker, webUploader){
    var component = {
        shortText: function(i, data){
            var tpl = '<div class="span6">' +
                        '<p class="mbm">' +
                            '<span><%= data.fieldname %></span>' +
                            '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                        '</p>' +
                        '<input type="text" placeholder="请输入内容" value="<%= data.content %>" name="fields['+ i +'][content]" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>>' +
                        '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                        '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                    '</div>';
            return  $.template(tpl, {data: data});
        },
        longText: function(i, data){
            var tpl = '<div class="row mb">' +
                        '<div class="span12">' +
                            '<p class="mbm">' +
                                '<span><%= data.fieldname %></span>' +
                                '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                            '</p>' +
                            '<textarea rows="5" placeholder="请输入内容" name="fields['+ i +'][content]" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>><%= data.content.replace(/<br >/g, "") %></textarea>' +
                            '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                            '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                            '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                            '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                            '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                        '</div>' +
                    '</div>';
            return  $.template(tpl, {data: data});
        },
        number: function(i, data){
            var tpl = '<div class="span6">' +
                        '<p class="mbm">' +
                            '<span><%= data.fieldname %></span>' +
                            '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                        '</p>' +
                        '<input type="text" data-type="number" value="<%= data.content %>" placeholder="请输入数字" name="fields['+ i +'][content]" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>>' +
                        '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                        '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                    '</div>';
            return  $.template(tpl, {data: data});
        },
        select: function(i, data){
            var tpl = '<div class="span6">' +
                        '<p class="mbm">' +
                            '<span><%= data.fieldname %></span>' +
                            '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                        '</p>' +
                        '<select name="fields['+ i +'][content]" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>>'+
                            '<% for(var i=0; i<data.fieldvalue.length; i++){ %>'+
                            '<option <% if(data.content == data.fieldvalue[i]){ %>selected<% } %> value="<%= data.fieldvalue[i] %>"><%= data.fieldvalue[i] %></option>'+
                            '<% } %>'+
                        '</select>'+
                        '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                        '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                    '</div>';
            return  $.template(tpl, {data: data});
        },
        date: function(i, data){
            var tpl = '<div class="span6">' +
                        '<p class="mbm">' +
                            '<span><%= data.fieldname %></span>' +
                            '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                        '</p>' +
                        '<div class="datepicker">' +
                            '<a href="javascript:;" class="datepicker-btn"></a>' +
                            '<input type="text" value="<%= data.content %>" placeholder="请输入日期" data-type="date" name="fields['+ i +'][content]" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>>' +
                        '</div>' +
                        '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                        '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                    '</div>';
            return  $.template(tpl, {data: data});
        },
        time: function(i, data){
            var tpl = '<div class="span6">' +
                        '<p class="mbm">' +
                            '<span><%= data.fieldname %></span>' +
                            '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                        '</p>' +
                        '<div class="datepicker">' +
                            '<a href="javascript:;" class="datepicker-btn"></a>' +
                            '<input type="text" value="<%= data.content %>" placeholder="请输入时间" data-type="time" name="fields['+ i +'][content]" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>>' +
                        '</div>' +

                        '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                        '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                    '</div>';
            return  $.template(tpl, {data: data});
        },
        date_time: function(i, data){
            var tpl = '<div class="span6">' +
                        '<p class="mbm">' +
                            '<span><%= data.fieldname %></span>' +
                            '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                        '</p>' +
                        '<div class="datepicker">' +
                            '<a href="javascript:;" class="datepicker-btn"></a>' +
                            '<input type="text" value="<%= data.content %>" placeholder="请输入日期和时间" data-type="date_time" name="fields['+ i +'][content]" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>>' +
                        '</div>' +
                        '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                        '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                        '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                    '</div>';
            return  $.template(tpl, {data: data});
        },
        editor: function(i, data){
            var tpl = '' +
                    '<div class="row mb">' + 
                        '<div class="span12">' +
                            '<p class="mbm">' +
                                '<span><%= data.fieldname %></span>' +
                                '<% if( data.iswrite == "1" ){ %><span class="xcr">*</span><% } %>' +
                            '</p>' +
                            '<div class="rp-editor">'+ 
                                '<script data-type="editor" id="editor_'+ i +'" name="fields['+ i +'][content]" type="text/plain" <% if( data.iswrite == "1" ){ %>data-required="true"<% } %>><%= data.content %></script>' + 
                            '</div>' +  
                            '<input type="hidden" name="fields['+ i +'][iswrite]" value="<%= data.iswrite %>">'+
                            '<input type="hidden" name="fields['+ i +'][fieldtype]" value="<%= data.fieldtype %>">'+
                            '<input type="hidden" name="fields['+ i +'][fieldid]" value="<%= data.fieldid %>">'+
                            '<input type="hidden" name="fields['+ i +'][fieldname]" value="<%= data.fieldname %>">'+
                            '<input type="hidden" name="fields['+ i +'][recordid]" value="<%= data.recordid %>">'+
                        '</div>' + 
                    '</div>';

            data.content = U.entity.unescape(data.content);
            return  $.template(tpl, {data: data});
        }
    };

    var report = {
        tmplCreate: function(data){
            return '<div class="fill">'+
                        '<form>' +
                            this.tmplHeader(data.template)  + 
                            this.switchComponent(data.templateField) + 
                            this.tmplFooter(data.template) + 
                        '</form>' +
                    "</div>";
        },
        tmplHeader: function(data){
            var tpl =   '<h2 class="xac xcm">' +
                            '<%= data.tname %>' +
                            '<input type="hidden" name="subject" value="<%= data.tname %>">' +
                            '<input type="hidden" name="tid" value="<%= data.tid %>">' +
                            '<input type="hidden" name="repid" value="<%= data.repid %>">' +
                        '</h2>';
            return  $.template(tpl, {data: data});
        },
        // 1表示长文本，2表示短文本，3表示数字，4表示日期与时间，5表示时间，6表示日期，7表示下拉，8表示编辑器
        switchComponent: function(data){
            var tpl = "",
                j = 0,
                type = ['', 'longText', 'shortText', 'number', 'date_time', 'time', 'date', 'select', 'editor'];
            for (var i = 0; i < data.length; i++) {
                data[i].content = data[i].content || "";
                data[i].fieldname = U.entity.unescape(data[i].fieldname);
                fieldtype = +data[i].fieldtype;
                switch ( fieldtype ) {
                    case 1:
                        if( j === 1 ){
                            tpl += '</div>';
                            j = 0;
                        }
                        tpl += component.longText(i, data[i]);
                        break;
                    case 8:
                        if( j === 1 ){
                            tpl += '</div>';
                            j = 0;
                        }
                        tpl += component.editor(i, data[i]);
                        break;
                    default:
                        if( j === 0 ){
                            tpl += '<div class="row mb">' + component[type[fieldtype]](i, data[i]);
                            j = 1;
                        }else if( j === 1){
                            tpl += component[type[fieldtype]](i, data[i]) + '</div>';
                            j = 0;
                        }
                        break;
                }
            }
            if( j === 1 ){
                tpl += '</div>';
                j = 0;
            }
            return tpl;
        },
        tmplFooter: function(data){
            data.toid = data.defaultuid || data.toid;
            data = $.extend({remark: '', toid: '', attachmentid: '', attach: []} ,data);
            var tpl =   '<div class="mb">'+
                            '<p class="mbm">' +
                                '<span>备注</span>' +
                            '</p>' +
                            '<textarea rows="5" placeholder="请输入内容" name="remark"><%= remark.replace(/<br >/g, "") %></textarea>' +
                        '</div>'+
                        '<div class="mb">'+
                            '<p class="mbm">' +
                                '<span>发布范围</span>' +
                            '</p>' +
                            '<input type="text" value="<%= toid.replace(/\\d+/g, function(match){return "u_" + match}) %>" name="toid">' +
                        '</div>'+
                        '<div id="type_report" class="tab-pane active">' +
                            '<div class="att bdbs mb">' +
                                '<div class="mb">' +
                                    '<div id="upload_btn"></div>' +
                                    '<input type="hidden" id="attachmentid" name="attachmentid" value="<%= attachmentid %>"><span>&nbsp;&nbsp;&nbsp;&nbsp;文件大小限制20MB</span>' +
                                '</div>' +
                                '<div>' +
                                    '<div class="attl" id="file_target">'+
                                        '<% for(var i=0; i<attach.length; i++ ){ %>'+
                                        '<div class="attl-item" data-node-type="attachItem">' +
                                            '<a href="javascript:;" title="删除附件" class="cbtn o-trash" data-id="<%= attach[i].aid %>" data-node-type="attachRemoveBtn"></a>' +
                                            '<i class="atti"><img width="44" height="44" src="<%= attach[i].iconsmall %>" alt="<%= attach[i].filename %>" title="<%= attach[i].filename %>"></i>' +
                                            '<div class="attc"><%= attach[i].filename %></div>' +
                                            '<a class="mlm" target="_blank" href="<%= attach[i].downurl %>">下载</a>' +
                                            '<a class="mlm" href="javascript:;" data-action="viewOfficeFile" data-param="{&quot;href&quot;: &quot;<%= attach[i].officereadurl %>&quot;}" title="查看">查看</a>' +
                                            // <a class="mlm" href="javascript:;" data-action="editOfficeFile" data-param="{&quot;href&quot;: &quot;<%= attach[i].aid %>&quot;}" title="编辑">
                                            //     编辑
                                            // </a>
                                        '</div>' +
                                        '<% } %>'+
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<a href="javascript:history.back();" class="btn btn-large btn-submit">返回</a>'+
                        '<div class="pull-right">'+
                        '<button type="button" data-evt="save" class="btn btn-large btn-submit mrs">保存</button>'+
                        '<button type="submit" class="btn btn-large btn-primary btn-submit mrs">发布</button>'+
                        '</div>';
            return  $.template(tpl, data);
        },
        init: function(){
            var types = appView.find("[data-type]");
            types.each(function(index, item){
                var type = item.getAttribute('data-type');
                var $parent = $(item).closest('div'),
                    data = {
                        pickTime: true,
                        pickSeconds: false,
                    };
                switch(type){
                    case 'time':
                        data.format = "hh:ii";
                        $parent.datepicker(data);
                        break;
                    case 'date':
                        data.format = "yyyy-mm-dd";
                        $parent.datepicker(data);
                        break;
                    case 'date_time':
                        data.format = "yyyy-mm-dd hh:ii";
                        $parent.datepicker(data);
                        break;
                }
            });
            Ibos.upload.attach({
                "module": "report",
                custom_settings: {
                    containerId: "file_target",
                    inputId: "attachmentid"
                }
            });
            appView.find('[name="toid"]').userSelect({
                data: Ibos.data.get("user"),
                type: "user"
            });

            var editor = appView.find('[data-type="editor"]');
            $.each(editor, function(i, e){
                var eId = $.attr(this, "id"),
                    content = $(e).html();

                // 单页情况下UE.instances保存之前已初始化实例，所以每次进入页面时先删除之前实例，触发render
                UE.delEditor(eId);

                var ue = new UE.getEditor(eId, {
                    initialFrameWidth: 738,
                    minFrameWidth: 738,
                    toolbars: UEDITOR_CONFIG.mode.simple
                });

                // 当编辑器内容为空时，通过setContent,触发生成textarea(触发生成textarea带有编辑器的name值)
                if(!content){
                    ue.ready(function() {
                        ue.setContent("");
                    });
                }
            });

            this.bind();
        },
        checkElement: function(){
            var verify = true;
            var requireds = appView.find("[data-required]");
            requireds.each(function(index, item){
                if( !$.trim( $(item).val() ) ){
                    $(item).blink().focus();
                    verify = false;
                    return false;
                }
            });
            return verify;
        },
        bind: function(){
            var self = this;
            appView.find('[data-evt="save"]').on('click', function(){
                self.save(0);
            });

            appView.find('form').on('submit', function(){
                self.save(1);
                return false;
            });
        },
        save: function(status){
            var $form = appView.find('form'),
                self = this;
            if( report.checkElement( ) && !this.loading ) {
                self.loading = true;
                var data = $form.serializeObject();
                data.toid = data.toid.replace(/u_/g, '');
                data.status = status;
                util.fetch('report/api/savereport', {
                    data: JSON.stringify(data)
                }).done(function(res){
                    self.loading = false;
                    if(res.isSuccess){
                        Ui.tip(res.msg);
                        location.hash = "send";
                    }else{
                        Ui.tip(res.msg, 'danger');
                    }
                });
            }
        }
    };
    return report;
});
