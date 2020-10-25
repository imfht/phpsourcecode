$(function(){
    app.pageInit();
});
var Cro = new Conero();
var app = Cro.extends(function(th){
    var _self = this;
    var _pid;
    this.pageInit = function()
    {
        // 父类加载时间
        th.uWin().pWin(function(win,req){
            var url = win.location.pathname;
            // iframe 模式
            if(url != location.pathname){
                $('.independence').hide();
            }
            // 非 iframe 引入模式
            else $('#cperson_dance').attr('class','container');
        });
        // 人物选择按钮
        $('#sel_pid_btn').click(function(){
            var id = 'fid_setter_pop';
            th.pupop({
                title:'人物选择',
                field: {pers_id:'hidden',name:'名称',mtime:'编辑时间'},
                post: {table:'gen_node',order:'mtime desc',map:'gen_no="'+_self.getGenNo()+'"'},
                pupopId: id,
                single: true
            },{
                selected:function(){
                    var datarow = $(this).parents('tr.datarow');
                    _pid = datarow.find('td.hidden').text();
                    var name = datarow.find('td.name').text();
                    $('#sel_pid_ipter').val(_pid);
                    $('#sel_pname_ipter').val(name);
                    $('#'+id).modal('hide');
                }
            });
        });
        // GoJsDemo();
        this.genogram_dance();
    }
    // 获取 家谱编号
    var _gen_no;    
    this.getGenNo = function(){
        if(th.empty(_gen_no)){
            _gen_no = th.getUrlBind('gno');
        }
        return _gen_no;
    }
    // 家谱系统生成函数
    this.genogram_dance = function(){
        var _$ = go.GraphObject.make;
        var gChar = _$(go.Diagram,'genogram_dance',
        {
            initialAutoScale: go.Diagram.Uniform,
            initialContentAlignment: go.Spot.Center,
            "undoManager.isEnabled": true,
            // when a node is selected, draw a big yellow circle behind it
            nodeSelectionAdornmentTemplate:
              _$(go.Adornment, "Auto",
                { layerName: "Grid" },  // the predefined layer that is behind everything else
                _$(go.Shape, "Circle", { fill: "yellow", stroke: null }),
                _$(go.Placeholder)
              )
          });

        //   var model = _$(go.TreeModel);
          var model = _$(go.GraphLinksModel,
          { // declare support for link label nodes
            linkLabelKeysProperty: "labelKeys",
            // this property determines which template is used
            nodeCategoryProperty: "s"
          });

        // 根据那女性别 节点背景颜色不一致
        gChar.nodeTemplateMap.add("F",  // female
        _$(go.Node, "Vertical",
            // 节点上色
            { background: "red" },
          _$(go.TextBlock,
            new go.Binding("text", "n"))
        ));

        gChar.nodeTemplateMap.add("M",  // male
        _$(go.Node, "Vertical",
            // 节点上色
            { background: "green" },
          _$(go.TextBlock,
            new go.Binding("text", "n"))
        ));

        // var _Node = th.getJsVar('_node');
        // // th.log(_Node);
        // model.nodeDataArray = _Node;
        /*
        model.linkDataArray = (function(){
            var nds = _Node;
            for(var k = 0; k<nds.length; k++){}
        })();
        */
        // 特殊连线
        // the representation of each label node -- nothing shows on a Marriage Link
        gChar.nodeTemplateMap.add("LinkLabel",
            _$(go.Node, { selectable: false, width: 1, height: 1, fromEndSegmentLength: 20 }));
            
        gChar.linkTemplateMap.add("Couple",  // for marriage relationships
            _$(go.Link,
            { selectable: false },
            _$(go.Shape, { strokeWidth: 2, stroke: "blue" })
        ));

        function findMarriage(diagram, a, b) {  // A and B are node keys
            var nodeA = diagram.findNodeForKey(a);
            var nodeB = diagram.findNodeForKey(b);
            if (nodeA !== null && nodeB !== null) {
                var it = nodeA.findLinksBetween(nodeB);  // in either direction
                while (it.next()) {
                var link = it.value;
                // Link.data.category === "Marriage" means it's a marriage relationship
                if (link.data !== null && link.data.category === "Couple") return link;
                }
            }
            return null;
        }

        function setUpChart(){
            var nds = th.getJsVar('_node');
            model.nodeDataArray = nds;
            var links = [],nd,mkey,fkey,lnk;
            var CoupleLnksTask = {},cltKey,cltVal;
            for(var k = 0; k<nds.length;k++){
                nd = nds[k];
                mkey = nd.m? nd.m : null;
                fkey = nd.f? nd.f : null;                
                // var cplnk = findMarriage(gChar,mkey,fkey);
                // if(cplnk === null){
                    cltKey = (mkey && fkey)? mkey + '_' + fkey : null;
                    cltVal = (cltKey !== null && CoupleLnksTask[cltKey])? CoupleLnksTask[cltKey] : null;
                    if(mkey && fkey && cltVal === null){
                        var mlab = { s: "LinkLabel" };
                        model.addNodeData(mlab);
                        var label = [mlab.key];
                        lnk = {from:mkey,to:fkey,labelKeys:label,category: "Couple"};
                        cltKey = mkey + '_' + fkey;
                        cltVal = label[0];
                        CoupleLnksTask[cltKey] = cltVal;
                        links.push(lnk);
                    }
                    if(cltVal !== null){
                        lnk = {from:cltVal,to:nd.key};
                        links.push(lnk);
                    }
                    else if(mkey && fkey === null){
                        lnk = {from:mkey,to:nd.key};
                        links.push(lnk);
                    }
                    else if(fkey && mkey === null){
                        lnk = {from:fkey,to:nd.key};
                        links.push(lnk);
                    }

                // }
                // else{
                //     lnk = {from:cplnk.data[0],to:nd.key,labelKeys:mlab[0],category: "Couple"};
                //     links.push(lnk);
                // }
            }
            model.linkDataArray = links;
            return model;
        }
        gChar.model = setUpChart();
    }
    this.genogram_dance_exp1 = function(){
        var _$ = go.GraphObject.make;
        var gChar = _$(go.Diagram,'genogram_dance',
        {
            initialAutoScale: go.Diagram.Uniform,
            initialContentAlignment: go.Spot.Center,
            "undoManager.isEnabled": true,
            // when a node is selected, draw a big yellow circle behind it
            nodeSelectionAdornmentTemplate:
              _$(go.Adornment, "Auto",
                { layerName: "Grid" },  // the predefined layer that is behind everything else
                _$(go.Shape, "Circle", { fill: "yellow", stroke: null }),
                _$(go.Placeholder)
              )/*,
            layout:  // use a custom layout, defined below
              _$(GenogramLayout, { direction: 90, layerSpacing: 30, columnSpacing: 10 })
              */
          });

          var model = _$(go.TreeModel);
            /*
            model.nodeDataArray =
            [ // the "key" and "parent" property names are required,
            // but you can add whatever data properties you need for your app
            { key: "1",              name: "Don Meow",   source: "cat1.png" },
            { key: "2", parent: "1", name: "Demeter",    source: "cat2.png" },
            { key: "3", parent: "1", name: "Copricat",   source: "cat3.png" },
            { key: "4", parent: "3", name: "Jellylorum", source: "cat4.png" },
            { key: "5", parent: "3", name: "Alonzo",     source: "cat5.png" },
            { key: "6", parent: "2", name: "Munkustrap", source: "cat6.png" }
            ];
            */

            // 父母
            // n: name, s: sex, m: mother, f: father, ux: wife, vir: husband, a: attributes/markers
        
            var _Node = th.getJsVar('_node');
            model.nodeDataArray = _Node;
            // model.nodeDataArray = new go.TreeModel(_Node);
            // gChar.model = linkRelationArray(_Node);
            gChar.model = model;
    }
});