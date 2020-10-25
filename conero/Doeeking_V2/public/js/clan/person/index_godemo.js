// gojs 实例 ~
function GoJsDemo(){
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

    // ----------------------------------------------------------------- 实例 1 简单绘图练习 ---------------------------------------------------------------------
    (function(offer){
        if(offer === true) return;
        var model;
        model = _$(go.Model);
        model = _$(go.GraphLinksModel); // 链表图
        // model = _$(go.TreeModel);    // 树形图
       gChar.nodeTemplate =
        _$(go.Node, "Horizontal",
        // 节点上色
        { background: "#44CCFF" },
            _$(go.TextBlock,
            // TextBlock.text is bound to Node.data.key
            new go.Binding("text", "n"))
        );

       model.nodeDataArray = [
            {key:1,n: "Alpha" },
            {key:2,n: "Joshua" },
            {key:4,n: "Beta"},
            {key:3,n: "Gamma" }
        ];
        model.linkDataArray = 
        [
            {from:1,to:2},
            {from:2,to:3},
            {from:3,to:4},
            {from:4,to:1},
            {from:4,to:2},
            {from:4,to:3},
            {from:4,to:4}
        ];
        gChar.model = model;
    })(true);

    // ----------------------------------------------------------------- 实例 2  简单家谱应用 ---------------------------------------------------------------------
    (function(offer){
        if(offer === true) return;
        var model = _$(go.GraphLinksModel); // 链表图
        // var model = _$(go.Model); // 链表图
        // 节点模板
        gChar.nodeTemplate =
        _$(go.Node, "Vertical",
        // 节点上色
        { background: "green",padding:2 },
            _$(go.TextBlock,
            // TextBlock.text is bound to Node.data.key
            new go.Binding("text", "n"))
        );
        
        // 连线模板
        // define a Link template that routes orthogonally, with no arrowhead
        /*
        gChar.linkTemplate =
            _$(go.Link,
                // default routing is go.Link.Normal
                // default corner is 0
                { routing: go.Link.Orthogonal, corner: 5 },
                _$(go.Shape, { strokeWidth: 3, stroke: "#555" }) // the link shape

                // if we wanted an arrowhead we would also add another Shape with toArrow defined:
                // $(go.Shape, { toArrow: "Standard", stroke: null }
                );
        */
        gChar.linkTemplate =  // for parent-child relationships
            _$(go.Link,
            {
                routing: go.Link.Orthogonal, curviness: 15,
                layerName: "Background", selectable: false,
                fromSpot: go.Spot.Bottom, toSpot: go.Spot.Top
            },
            _$(go.Shape, { strokeWidth: 1 })
            );

        gChar.linkTemplateMap.add("Marriage",  // for marriage relationships
            _$(go.Link,
            { selectable: false },
            _$(go.Shape, { strokeWidth: 1, stroke: "blue" })
        ));
            
        function findMarriage(diagram, a, b) {  // A and B are node keys
            var nodeA = diagram.findNodeForKey(a);
            var nodeB = diagram.findNodeForKey(b);
            if (nodeA !== null && nodeB !== null) {
                var it = nodeA.findLinksBetween(nodeB);  // in either direction
                while (it.next()) {
                var link = it.value;
                // Link.data.category === "Marriage" means it's a marriage relationship
                if (link.data !== null && link.data.category === "Marriage") return link;
                }
            }
            return null;
        }
        // { key: 0, n: "Aaron", s: "M", m:-10, f:-11, ux: 1, a: ["C", "F", "K"] },
        /*
        model.nodeDataArray = [
          { key: 0, n: "Aaron", s: "M", m:-10, f:-11, ux: 1, a: ["C", "F", "K"] },
          { key: 1, n: "Alice", s: "F", m:-12, f:-13, a: ["B", "H", "K"] },
          { key: 2, n: "Bob", s: "M", m: 1, f: 0, ux: 3, a: ["C", "H", "L"] },
          { key: 3, n: "Barbara", s: "F", a: ["C"] },
          { key: 4, n: "Bill", s: "M", m: 1, f: 0, ux: 5, a: ["E", "H"] },
          { key: 5, n: "Brooke", s: "F", a: ["B", "H", "L"] },
          { key: 6, n: "Claire", s: "F", m: 1, f: 0, a: ["C"] },
          { key: 7, n: "Carol", s: "F", m: 1, f: 0, a: ["C", "I"] },
          { key: 8, n: "Chloe", s: "F", m: 1, f: 0, vir: 9, a: ["E"] },
          { key: 9, n: "Chris", s: "M", a: ["B", "H"] },
          { key: 10, n: "Ellie", s: "F", m: 3, f: 2, a: ["E", "G"] },
          { key: 11, n: "Dan", s: "M", m: 3, f: 2, a: ["B", "J"] },
          { key: 12, n: "Elizabeth", s: "F", vir: 13, a: ["J"] },
          { key: 13, n: "David", s: "M", m: 5, f: 4, a: ["B", "H"] },
          { key: 14, n: "Emma", s: "F", m: 5, f: 4, a: ["E", "G"] },
          { key: 15, n: "Evan", s: "M", m: 8, f: 9, a: ["F", "H"] },
          { key: 16, n: "Ethan", s: "M", m: 8, f: 9, a: ["D", "K"] },
          { key: 17, n: "Eve", s: "F", vir: 16, a: ["B", "F", "L"] },
          { key: 18, n: "Emily", s: "F", m: 8, f: 9 },
          { key: 19, n: "Fred", s: "M", m: 17, f: 16, a: ["B"] },
          { key: 20, n: "Faith", s: "F", m: 17, f: 16, a: ["L"] },
          { key: 21, n: "Felicia", s: "F", m: 12, f: 13, a: ["H"] },
          { key: 22, n: "Frank", s: "M", m: 12, f: 13, a: ["B", "H"] },

          // "Aaron"'s ancestors
          { key: -10, n: "Paternal Grandfather", s: "M", m: -33, f: -32, ux: -11, a: ["A", "S"] },
          { key: -11, n: "Paternal Grandmother", s: "F", a: ["E", "S"] },
          { key: -32, n: "Paternal Great", s: "M", ux: -33, a: ["F", "H", "S"] },
          { key: -33, n: "Paternal Great", s: "F", a: ["S"] },
          { key: -40, n: "Great Uncle", s: "M", m: -33, f: -32, a: ["F", "H", "S"] },
          { key: -41, n: "Great Aunt", s: "F", m: -33, f: -32, a: ["B", "I", "S"] },
          { key: -20, n: "Uncle", s: "M", m: -11, f: -10, a: ["A", "S"] },

          // "Alice"'s ancestors
          { key: -12, n: "Maternal Grandfather", s: "M", ux: -13, a: ["D", "L", "S"] },
          { key: -13, n: "Maternal Grandmother", s: "F", m: -31, f: -30, a: ["H", "S"] },
          { key: -21, n: "Aunt", s: "F", m: -13, f: -12, a: ["C", "I"] },
          { key: -22, n: "Uncle", s: "M", ux: -21 },
          { key: -23, n: "Cousin", s: "M", m: -21, f: -22 },
          { key: -30, n: "Maternal Great", s: "M", ux: -31, a: ["D", "J", "S"] },
          { key: -31, n: "Maternal Great", s: "F", m: -50, f: -51, a: ["B", "H", "L", "S"] },
          { key: -42, n: "Great Uncle", s: "M", m: -30, f: -31, a: ["C", "J", "S"] },
          { key: -43, n: "Great Aunt", s: "F", m: -30, f: -31, a: ["E", "G", "S"] },
          { key: -50, n: "Maternal Great Great", s: "F", ux: -51, a: ["D", "I", "S"] },
          { key: -51, n: "Maternal Great Great", s: "M", a: ["B", "H", "S"] }
        ];
        */
        model.nodeDataArray = [
            // Joshua & Brximl
            {key:-1,n:"Joshua",s:"M"},
            {key:1,n:"Brximl",s:"F"},
            {key:2,n:"Bill",m:1,f:-1,s:"M"},
            {key:3,n:"Joe",m:1,f:-1,s:"M"},
            {key:4,n:"Emma",m:1,f:-1,s:"F"},
            {key:5,n:"Brximlna",m:1,f:-1,s:"F"},
            {key:6,n:"Lucie",s:"F"},
            {key:7,n:"Sarch",m:6,f:3,s:"F"},
            {key:8,n:"John",m:6,f:3,s:"M"},
            {key:9,n:"Jess",s:"F"},
            {key:10,n:"Juan",s:"M",m:9,f:8},

            // Joshua & ZhouHjan
            {key:11,n:"ZhouHjan",s:"F"},
            {key:12,n:"hjan",s:"F",m:11,f:-1},
            {key:13,n:"Carlsen",s:"M",m:11,f:-1},
            {key:14,n:"Daniel",s:"M",m:11,f:-1},
            {key:15,n:"Rechvina",s:"F"},
            {key:15,n:"Doeeking",s:"M",m:15,f:14}
        ];

        var linkRelation = function(){
            var nd,mdata,mlab;
            // Cro.log(model.nodeDataArray);
            var Marriage = {},mKey;
            for(var k = 0; k<model.nodeDataArray.length; k++){
                nd = model.nodeDataArray[k];
                if(Cro.empty(nd)) continue;
                if(nd.m && nd.f){
                    mKey = nd.m + '' + nd.f;
                    // Cro.log(findMarriage(gChar,nd.m,nd.f));
                    if(Marriage[mKey] === undefined){
                        Marriage[mKey] = 'exist';
                        mlab = { s: "LinkLabel" };
                        model.addNodeData(mlab);
                        mdata = { from: nd.m, to: nd.f,labelKeys: [mlab.key],category: "Marriage" };
                        // model.addLinkData(mdata);
                        // Cro.log(mdata,'f***k');
                        // gChar.model.addLinkData(mdata);
                        model.linkDataArray.push(mdata);
                    }
                }
                if(nd.key && nd.m){
                    mdata = { from: nd.f, to: nd.key};
                    model.linkDataArray.push(mdata);
                }
            
            }
            Cro.log(model);
        }
        linkRelation();
        Cro.log(model.linkDataArray);
        /*
        model.linkDataArray = 
        [
            {from:1,to:2},
            {from:2,to:3},
            {from:3,to:4},
            {from:4,to:1},
            {from:4,to:2},
            {from:4,to:3},
            {from:4,to:4}
        ];
        */
        gChar.model = model;
    })(true);
    // ----------------------------------------------------------------- 实例 3  简单绘图练习(中间线；连接) ---------------------------------------------------------------------
    (function(offer){
        if(offer) return;
        var model;
        // model = _$(go.Model);
        model = _$(go.GraphLinksModel,
          { // declare support for link label nodes
            linkLabelKeysProperty: "labelKeys",
            // this property determines which template is used
            nodeCategoryProperty: "s"
          });
        // model = _$(go.GraphLinksModel); // 链表图
        // model = _$(go.TreeModel);    // 树形图
       gChar.nodeTemplate =
        _$(go.Node, "Horizontal",
        // 节点上色
        { background: "#44CCFF" },
            _$(go.TextBlock,
            // TextBlock.text is bound to Node.data.key
            new go.Binding("text", "n"))
        );
        var randMk = function(len){
            var tmp = 1;
           var ret = [];
           while(tmp <= len){
               ret.push({
                   key: tmp,
                   n: '4'
               });
               tmp++;
           }
           return ret
        }
        var zimuArr = function(len){
            len = len && !isNaN(len) ? len:5;
            var arr = [];
            var start = 65;
            while(len > 0){
                arr.push();
                len--;
            }
            return arr;

        }
        var size = 20;
        size = [1,20];
      // 节点数据生成
       model.nodeDataArray = (function(len){
           var tmp = 1;
           var ret = [],json;
           if(Cro.is_array(len) && len.length > 0){
               tmp = len[0]; len = len[1];
               while(tmp <= len){
                   json = {
                        key: tmp,
                        n: tmp,
                        s: (ret.length %2 == 0? 'M':'F')
                    };
                    ret.push(json);
                    tmp++;
               }
           }
           else{
            while(tmp <= len){
                json = {
                    key: tmp,
                    n: Math.floor(Math.random()*1000),
                    s: (ret.length %2 == 0? 'M':'F')
                };
                ret.push(json);
                tmp++;
            }
           }
           return ret;
       })(size);
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
        // 特殊连线
        // the representation of each label node -- nothing shows on a Marriage Link
        gChar.nodeTemplateMap.add("LinkLabel",
            _$(go.Node, { selectable: false, width: 1, height: 1, fromEndSegmentLength: 20 }));
            
        // gChar.linkTemplate =  // for parent-child relationships
        // _$(go.Link,
        //   {
        //     routing: go.Link.Orthogonal, curviness: 15,
        //     layerName: "Background", selectable: false,
        //     fromSpot: go.Spot.Bottom, toSpot: go.Spot.Top
        //   },
        //   _$(go.Shape, { strokeWidth: 1 })
        // );

        gChar.linkTemplateMap.add("Couple",  // for marriage relationships
            _$(go.Link,
            { selectable: false },
            _$(go.Shape, { strokeWidth: 2, stroke: "blue" })
        ));

        // Cro.log(model.nodeDataArray); //
        // 数据维护
        var mlab = { s: "LinkLabel" };
            model.addNodeData(mlab);
        var label0 = [mlab.key];
        var mlab1 = { s: "LinkLabel" };
            model.addNodeData(mlab1);
        var label1 = [mlab1.key];
        var mlab2 = { s: "LinkLabel" };
            model.addNodeData(mlab2);
        var label2 = [mlab2.key];
        var mlab3 = { s: "LinkLabel" };
            model.addNodeData(mlab3);
        var label3 = [mlab3.key];
        var mlab4 = { s: "LinkLabel" };
            model.addNodeData(mlab4);
        var label4 = [mlab4.key];
        model.linkDataArray = 
            [
                {from:1,to:2,labelKeys:label0,category: "Couple"},
                {from:label0[0],to:3},
                // {from:mlab.key[0],to:3},
                {from:label0[0],to:4},
                {from:4,to:5,labelKeys:label1,category: "Couple"},
                {from:label1[0],to:6},
                {from:label1[0],to:7},
                {from:label1[0],to:8},
                {from:6,to:9,labelKeys:label2,category: "Couple"},
                {from:label2[0],to:10},
                {from:label2[0],to:11},
                {from:label2[0],to:12},
                {from:label3[0],to:13},
                {from:label3[0],to:14},
                {from:label3[0],to:15},
                {from:7,to:16,labelKeys:label3,category: "Couple"},
                {from:13,to:17},
                {from:14,to:18},
                {from:label4[0],to:19},
                {from:15,to:20,labelKeys:label4,category: "Couple"},
            ];
        Cro.log(model.linkDataArray);
        // 函数自动新增
        // model.linkDataArray = (function(){
        //     var ret = [];
        //     var nodes = model.nodeDataArray;
        //     return ret;
        // })();

        gChar.model = model;
    })();
}