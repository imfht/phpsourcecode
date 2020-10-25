$(function(){    
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var finsetReord;
    var budid = th.getUrlBind('regulars');
    this.pageInit = function(){
        // 财务记账
        $('.w2finance').click(function(){
            var tr = $(this).parents('tr');
            finsetReord = {};
            finsetReord['pdate'] = tr.find('td[dataid="date"]').text();
            finsetReord['type'] = tr.find('td[dataid="type"]').text();
            finsetReord['sumsingle'] = tr.find('td[dataid="figure"]').text();
            finsetReord['bud_id'] = budid;
            th.log(finsetReord);
            var panel = $('#finsetbar');
            var body = panel.find('div.panel-body');
            var name = $('#navBarActionName').text();
            /*
            var xhtml = '<form class="form-inline">'
                + ' <input type="text" name="use_date" value="'+finsetReord.date+'" class="form-control">'
                + ' <input type="text" name="type" value="'+finsetReord.type+'" class="form-control">'
                + ' <input type="text" name="name" value="'+name+'" class="form-control">'
                + ' <input type="text" name="figure" value="'+finsetReord.figure+'" class="form-control">'
                + ' <button class="btn btn-info">提交</button>'
                + '</form>'
                ;
            */
            var xhtml = th.formGroup({
                param:[
                    {name:'use_date',label:'日期',key:'pdate'},
                    {name:'type',label:'类型'},
                    {name:'figure',label:'金额',key:'sumsingle'},
                    {name:'budid',type:'hidden',key:'bud_id'},
                ],
                record:finsetReord
            });
            body.html(xhtml);
            panel.find('div.panel-heading').html(name+'/'+finsetReord.pdate+' <a href="javascript:app.selectFromSets();">从财务账单中选择</a>');
            panel.removeClass('hidden');
            location.href = location.pathname+'#finsetbar';
        });
        // 数据保存按钮
        $('#fstbar_save_btn').click(function(){
            var saveData = {};
            saveData['orig'] = th.bsjson(finsetReord);
            var formData = th.formJson($('#finsetbar').find('div.panel-body'));
            saveData['form'] = th.bsjson(formData);
            saveData['formid'] = 'plan2sets';
            th.post('/conero/finance/budget/regularsv.html',saveData);
        });
        // 例行账单- 详情
        $('.r2finance').click(function(){            
            var bar = $('#planaboutbar');
            var dataid = $(this).attr('dataid');
            $.post('/conero/finance/budget/ajax.html',{item:'regulars/aboutplan',list:dataid},function(data){
                var body = bar.find('div.panel-body');
                body.html(data);
                bar.removeClass('hidden');
                location.href = location.pathname+'#planaboutbar';
            });            
        });
        // 作图 mk_charts_btn
        $('#mk_charts_btn').click(function(){            
            var bar = $('#planchartbar');
            var pbody = bar.find('div.panel-body');
            bar.removeClass('hidden');
            var pgCloseId = th.progressGrid({html:pbody});
            $.post('/conero/finance/budget/ajax.html',{item:'regulars/chart',list:budid},function(data){                
                // clearInterval(;
                th.progressGrid('','close');
                /*
                var xhtml = '<div id=""></div>';
                bar.find('div.penal-body').html(xhtml);
                */
                // th.log(pbody.get(0));

                pbody.css({"width":'100%',"height":'100%','min-height':500});
                var chart = echarts.init(pbody.get(0));       
                
                /*
                pbody.html('<div style="height:600px;width:600px;" id="testddddd"></div>');
                var chart = echarts.init(document.getElementById('testddddd'));  
                */     

                data.grid = {};
                data.toolbox = {
                    show:true,orient:"horizontal",
                    feature:{
                        saveAsImage:{type:"png",name:"财务登账分析",show:true,title:"保存为图像"},
                        dataView : {show: true, readOnly: false},
                        magicType:{show:true,type:["line","bar"],title:"切换"}
                    }};
                data.tooltip = {trigger: "axis",axisPointer:{type:"shadow"}};
                data.yAxis = [
                        {name:"金额(元)",type:"value",nameGap:20},
                        {name:"事件差(天)",type:"value",position:"right"}
                    ];

                th.log(data);
                chart.setOption(data);
                // chart.setOption(app.getEchartOption());
            });
            location.href = location.pathname+'#planchartbar';
        });
    }
    // 从财务账单中选择
    this.selectFromSets = function(){
        var pupopId = 'get_fset_controller';
        th.pupop({
            title:"财务账单选择器",
            field:{'use_date':'日期',name:'名称',figure:'金额',sider:'事务乙方','finc_no':'hidden'},
            post:{table:'finc_set',order:'use_date desc',map:'center_id="'+th.getJsVar('cid')+'"'},
            pupopId:pupopId,
            single:true
        },{
            // 选择以后
            selected:function(){
                var tr = $(this).parents('tr.datarow');
                var fset_no = tr.find('td.hidden').text();
                var body = $('#finsetbar').find('div.panel-body');
                var el = body.find('input[name="fset_no"]');
                if(el.length > 0) el.val(fset_no);
                else body.append('<input type="hidden" name="fset_no" value="'+fset_no+'">');
                $('#'+pupopId).modal('hide');
                var text = '<strong>关联财务账单信息</strong> 日期：'+tr.find('td.use_date').text()+', 名称：'+tr.find('td.name').text()+', 金额：'+tr.find('td.figure').text()+', 事务乙方：'+tr.find('td.sider').text()+'.';
                var staticEl = body.find('div.alert-info');
                if(staticEl.length > 0) staticEl.html(text);
                else{
                    /*
                    var htmlTip = 
                        '<div class="form-group">'
                        +'    <label class="col-sm-2 control-label">关联财务账单信息</label>'
                        +'    <div class="col-sm-10">'
                        +'    <p class="form-control-static">'+text+'</p>'
                        +'    </div>'
                        +'</div>'
                        ;
                    */
                    var htmlTip = '<div class="alert alert-info" role="alert">'+text+'</text>';
                    body.append(htmlTip);
                }
            }
        });
    }
    // 刷新 - 例行财物账单
    this.flushPlanData = function(no){
        var data = {formid:'plan2setsflush',no:no};
        th.post('/conero/finance/budget/regularsv.html',data);
    }
    // 测试 echart - option
    this.getEchartOption = function(){
        return {
            "title":{
                "text":"echart-demo"
            },
            "tooltip":{},
            "legend": {
                "data":["销量"]
            },
            "xAxis": {
                "data": ["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"]
            },
            "yAxis": {},
            "series": [{
                "name": "销量",
                "type": "bar",
                "data": [5, 20, 36, 10, 10, 20]
            }]
        };
    }
});