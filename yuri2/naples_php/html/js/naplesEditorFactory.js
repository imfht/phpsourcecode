/**
 * Created by Yuri2 on 2017/1/13.
 */
//富文本编辑器助手
function NaplesEditor() {
    this.data={};
    if (typeof NaplesEditor._initialized == "undefined") {
        NaplesEditor.prototype.has = function(key) {
            if (key==undefined){
                return false;
            }else{
                return this.data[key]==undefined;
            }
        };
        NaplesEditor.prototype.get = function(key) {
            if (key==undefined){
                return this.data;
            }else{
                return this.data[key];
            }
        };
        NaplesEditor.prototype.set = function(key,value) {
            if (value==undefined){
                this.data=key;
            }else{
                this.data[key]=value;
            }
        };
        NaplesEditor.prototype.createEditor=function(id,isFull,srvUrl){
            if (isFull == undefined){
                isFull=false;
            }
            var ue;
            if (isFull){
                 ue = UE.getEditor(id,{
                    toolbars: [
                        [
                            //全局操作
                            'undo', //撤销
                            'redo', //重做
                            'source', //源代码
                            'cleardoc', //清空文档
                            'pasteplain', //纯文本粘贴模式
                            'selectall', //全选
                            'removeformat', //清除格式
                            'drafts', // 从草稿箱加载
                            'template', //模板
                            'background', //背景
                            'autotypeset', //自动排版
                            'print', //打印
                            'preview', //预览
                            'help', //帮助
                            'fullscreen', //全屏
                            '|',
                        // ],
                        // [
                            //表格
                            'inserttable', //插入表格
                            'insertrow', //前插入行
                            'insertcol', //前插入列
                            'mergeright', //右合并单元格
                            'mergedown', //下合并单元格
                            'deleterow', //删除行
                            'deletecol', //删除列
                            'splittorows', //拆分成行
                            'splittocols', //拆分成列
                            'splittocells', //完全拆分单元格
                            'deletecaption', //删除表格标题
                            'inserttitle', //插入标题
                            'mergecells', //合并多个单元格
                            'deletetable', //删除表格
                            'insertparagraphbeforetable', //"表格前插入行"
                            'edittable', //表格属性
                            'edittd', //单元格属性
                            '|',

                        // ],
                        // [
                            //插入
                            'anchor', //锚点
                            'horizontal', //分隔线
                            'customstyle', //自定义标题
                            'insertorderedlist', //有序列表
                            'insertunorderedlist', //无序列表
                            'snapscreen', //截图
                            'imagenone', //默认
                            'imageleft', //左浮动
                            'imageright', //右浮动
                            'imagecenter', //居中
                            'simpleupload', //单图上传
                            'insertimage', //多图上传
                            'attachment', //附件
                            'map', //Baidu地图
                            'insertvideo', //视频
                            'scrawl', //涂鸦
                            // 'music', //音乐
                            'time', //时间
                            'date', //日期
                            'insertframe', //插入Iframe
                            '|',

                        // ],
                        // [
                            //文字排版
                            'bold', //加粗
                            'indent', //首行缩进
                            'italic', //斜体
                            'underline', //下划线
                            'strikethrough', //删除线
                            'subscript', //下标
                            'fontborder', //字符边框
                            'superscript', //上标
                            'formatmatch', //格式刷
                            'blockquote', //引用
                            'insertcode', //代码语言
                            'fontfamily', //字体
                            'fontsize', //字号
                            'lineheight', //行间距
                            'justifyleft', //居左对齐
                            'justifyright', //居右对齐
                            'justifycenter', //居中对齐
                            'justifyjustify', //两端对齐
                            'forecolor', //字体颜色
                            'backcolor', //背景色
                            'touppercase', //字母大写
                            'tolowercase', //字母小写
                            'paragraph', //段落格式
//                    'directionalityltr', //从左向右输入
//                    'directionalityrtl', //从右向左输入
                            'rowspacingtop', //段前距
                            'rowspacingbottom', //段后距
                            'pagebreak', //分页

                            'link', //超链接
                            'unlink', //取消链接
                            'emotion', //表情
                            'spechars', //特殊字符
                            'searchreplace', //查询替换
                        ],
//                [
//                    'wordimage', //图片转存
//                    'edittip ', //编辑提示
//                    'webapp', //百度应用
//                    'charts', // 图表
//                ]
                    ],
                     serverUrl:srvUrl
                });
            }
            else{
                 ue = UE.getEditor(id,{
                    toolbars: [
                        [
                            //全局操作
                            'undo', //撤销
                            'redo', //重做
                            //文字排版
                            'bold', //加粗
                            'underline', //下划线
                            'strikethrough', //删除线
                            'forecolor', //字体颜色
                            'link', //超链接
                            'unlink', //取消链接
                            'emotion', //表情
                        ]
                    ],
                     serverUrl:srvUrl
                 });
            }
            return ue;
        };
        NaplesEditor._initialized = true;
    }
}