$(function(){
    distributeMagager = {
        loadedSubscriberList: false,
        loadedQuestionnaireList: false,

        start: function () {
            this._delegateEvents();

            $('a[href=#tab-subscriber]').tab('show');
            //$('a[href=#tab-news]').tab('show');
        },

        _delegateEvents: function () {
            var self = this;

            $(document).delegate('a[href=#tab-subscriber]', 'shown.bs.tab', function(){
                if( !self.loadedSubscriberList ){
                    self.loadSubscriberTable()
                        .then(function(){
                            self.renderSubscriberTable();
                            self.loadedSubscriberList = true;
                        });
                }
            });

            $(document).delegate('a[href=#tab-questionnaire]', 'shown.bs.tab', function(){
                if( !self.loadedQuestionnaireList ){
                    self.loadQuestionnaireTable()
                        .then(function(){
                            self.renderQuestionnaireTable();
                            self.loadedQuestionnaireList = true;
                        });
                }
            });

            $(document).delegate('#all-subscribers', 'change', function(){
                self.toggleSubscribers($(this).get(0).checked);
            });
        },

        loadSubscriberTable: function () {
            var self = this;

            return $.ajax({
                type: 'GET',
                url: '/WebService/subscribers.json',
                dataType: 'json'
            }).then(function(response){
                var $table = $('  \
                            <table id="subscriber-table" class="table table-striped table-hover table-condensed datatable"> \
                                <thead> \
                                    <tr>    \
                                        <th><input id="all-subscribers" type="checkbox"/> 选定</th> \
                                        <th>头像</th> \
                                        <th>昵称</th> \
                                        <th>性别</th> \
                                        <th>关注日期</th>   \
                                    </tr>   \
                                </thead>    \
                                <tbody> \
                                </tbody>    \
                            </table>    \
                ');
                var $tbody = $table.find('tbody');

                for(var openid in response ){
                    var $tr = $('   \
                                <tr>    \
                                    <td><input name="subscribers[]" type="checkbox" value="'+ openid +'" /></td> \
                                    <td><image src="'+ response[openid]["headimgurl"] +'" /></td>   \
                                    <td>'+ response[openid]["nickname"] +'</td> \
                                    <td>'+ response[openid]["sex"] +'</td>  \
                                    <td>'+ response[openid]["subscribe_time"] +'</td>   \
                                </tr>   \
                    ');

                    $tbody.append($tr);
                }

                $("#tab-subscriber").empty().append($table);
            });
        },

        loadQuestionnaireTable: function () {
            var self = this;

            return $.ajax({
                type: 'GET',
                url: '/WebService/questionnaires.json',
                dataType: 'json'
            }).then(function(response){
                var $table = $('  \
                            <table id="questionnaire-table" class="table table-striped table-hover table-condensed datatable"> \
                                <thead> \
                                    <tr>    \
                                        <th>选定</th> \
                                        <th>类型</th> \
                                        <th>名称</th> \
                                        <th>创建日期</th> \
                                        <th>失效日期</th>   \
                                    </tr>   \
                                </thead>    \
                                <tbody> \
                                </tbody>    \
                            </table>    \
                ');
                var $tbody = $table.find('tbody');

                for(var questionnaireID in response ){
                    var $tr = $('   \
                                <tr>    \
                                    <td><input name="questionnaireID" type="radio" value="'+ questionnaireID +'" /></td> \
                                    <td>'+ ((response[questionnaireID]["type"]=='exam') ? (' <span class="label label-warning"><i class="fa fa-tag"></i>考试卷</span>') : (' <span class="label label-danger"><i class="fa fa-tag"></i>调研卷</span>')) +'</td>   \
                                    <td>'+ response[questionnaireID]["name"] +'</td> \
                                    <td>'+ response[questionnaireID]["create_date"] +'</td>  \
                                    <td>'+ response[questionnaireID]["expire_date"] +'</td>   \
                                </tr>   \
                    ');

                    $tbody.append($tr);
                }

                $("#tab-questionnaire").empty().append($table);
            });
        },

        renderSubscriberTable: function () {
            $("#subscriber-table").dataTable({
                "iDisplayLength": 50,
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [0, 1] },
                ],
                "oLanguage":{
                    "sSearch": "搜索：",
                    "sLengthMenu": "每页显示 _MENU_ 条记录",
                    "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
                    "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
                    "oPaginate": {
                        "sPrevious": "前一页",
                        "sNext": "后一页"
                    },
                    "sZeroRecords": "抱歉， 没有检索到数据",
                    "sInfoEmpty": "没有数据"
                }
            });
        },

        renderQuestionnaireTable: function () {
            $("#questionnaire-table").dataTable({
                "iDisplayLength": 50,
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [0] },
                ],
                "oLanguage":{
                    "sSearch": "搜索：",
                    "sLengthMenu": "每页显示 _MENU_ 条记录",
                    "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
                    "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
                    "oPaginate": {
                        "sPrevious": "前一页",
                        "sNext": "后一页"
                    },
                    "sZeroRecords": "抱歉， 没有检索到数据",
                    "sInfoEmpty": "没有数据"
                }
            });
        },

        toggleSubscribers: function (toCheck) {
            $('#tab-subscriber tbody input[name=subscribers]').each(function(){
                this.checked = toCheck;
            });
        }
    };


    distributeMagager.start();
});