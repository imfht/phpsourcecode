/**
 * Created by 杨清云 on 2017-8-14.
 * 页面中的组件之中的逻辑关系
 */
function Page(o) {
    var m = o.Module;
    var c = o.Controller;
    var a = o.Action;

    // 原型链中存在对象，就直接返回
    if ((m in Page.prototype.app) && (c in Page.prototype.app[m]) && (a in Page.prototype.app[m][c])) {
        var obj = Page.prototype.app[m][c][a];
        //obj.init().select();  // 关闭页面或刷新，需要重新初始选择器

        return obj;
    }

    // 初始化对象
    if (o != undefined) {
        console.log("Page 对象 " + m + '/' + c + '/' + a + " 初始化 运行");
        this.ID = c + '_' + a + '_';
        this.CA = '#' + c + '_' + a + '_';
        this.url_children = 'p_m/' + m + '/p_c/' + c + '/p_a/' + a + '/';
        this.is_show = false;
    }

    this.init().select();  // 初始化选择器
    this.init().attr();  // 初始化属性
    this.init().attr_sub();  // 初始化子组件的属性
    this.init().event();  // 初始化事件

    // 赋给原型对象(共享给其他界面调用)
    if (!(m in Page.prototype.app)) {
        Page.prototype.app[m] = {};
    }
    if (!(c in Page.prototype.app[m])) {
        Page.prototype.app[m][c] = {};
    }
    Page.prototype.app[m][c][a] = this;

    return this;
}

Page.prototype = {
    constructor: Page,
    app: {}, // 整个客户端程序对象数据
    tab_page_map: {}, // tab 与 页面对象对照表,关闭tab页面时，需要销毁page对象

    // 处理 url
    url: function () {
        var obj = this;
        return {
            // 给tp生成的url添加参数
            // 一般tp生成的url是带扩展链接的 index.php/m/c/a.html
            // 在a.html插入参数 a/p_m/v_m/p_c/v_c/p_a/v_a.html
            add_param: function (s_url, a_param) {
                var pos_len = s_url.lastIndexOf('.');
                var a_url = s_url.substr(0, pos_len) + '/';
                var a_ext = s_url.substr(pos_len);
                switch (typeof(a_param)) {
                    case 'undefined':
                    case 'string':
                        if (a_param == undefined) {
                            a_param = obj.url_children;
                        }
                        a_url += a_param;
                        break;
                    case 'object':
                        for (var i in a_param) {
                            if (a_param.hasOwnProperty(i)) {
                                a_url += i + '/' + a_param[i] + '/';
                            }
                        }
                        break;
                }
                a_url = a_url.substr(0, a_url.length - 1);
                s_url = a_url + a_ext;
                return s_url;
            }
        };
    },

    /**
     * 初始化选择器与属性值
     */
    init: function () {
        //console.log('init 初始化参数 运行');
        var obj = this;

        return {
            // 选择器
            select: function () {
                obj.MG = $(obj.CA + 'MG');  // 列表表格
                obj.MG_Detail = $(obj.CA + 'MG_Detail');  // 备注表格
                obj.MG_Item = $(obj.CA + 'MG_Item');  // 明细表格
                obj.MG_Search = $(obj.CA + 'MG_Search');  // 表格搜索
                obj.MG_Main_Res = $(obj.CA + 'MG_Main_Res');  // 主物资表格
                obj.Tree_View = $(obj.CA + 'Tree_View');  // 树结构
                obj.Left_Lmt = $(obj.CA + 'Left_Lmt'); // 左侧检索条件
                obj.Fun_Win = $(obj.CA + "Fun_Win");  // 功能窗口
                obj.Record_Win = $(obj.CA + "Record_Win");  // 卡版记录窗口
                obj.Fm = $(obj.CA + 'Form');  // 表单
                //obj.left = $(obj.CA + 'left');
                //obj.left_right_menu = $(obj.CA + 'left_right_menu');
                //obj.choose_return = $(obj.CA + "choose_return");
                //obj.choose_cancel = $(obj.CA + "choose_cancel");
                //obj.record_img = $(obj.CA + 'record_img');

                // 准备废弃的
                //obj.data_grid = $(obj.CA + 'data_grid');
                //obj.fun_win = $(obj.CA + "fun_win");
                //obj.property_grid = $(obj.CA + 'property_grid');
                //obj.record_win = $(obj.CA + "record_win");

                return this;
            },

            // 对象属性值
            attr: function () {
                // 属性
                obj.open_win_id = undefined;    // 当前打开的窗口id，用一个win窗口,刷新不同链接
                obj.gui_build_state = {};   // 界面创建元素状态

                return this;
            },

            // 子级对象属性
            attr_sub: function () {
                // 表格
                var grid_init_param = function (o) {
                    // 当前编辑行
                    o.edit_state = undefined;
                    // 当前编辑行
                    o.edit_index = undefined;
                    // 当前编辑数据
                    o.edit_row_data = undefined;
                    // 改变之后调用，重新计算数据
                    o.where_data_chg = function () {
                        console.log('where_data_chg');
                        return 1;
                    };

                };
                // 表格控件，就初始化表格参数
                if (obj.MG.length != 0) {
                    grid_init_param(obj.MG);
                }
                if (obj.MG_Item.length != 0) {
                    grid_init_param(obj.MG_Item);
                }
                if (obj.MG_Main_Res.length != 0) {
                    grid_init_param(obj.MG_Main_Res);
                }

                return this;
            },

            // 事件初始化
            event: function () {
                obj.event = {
                    // 系统 窗口显示
                    // 之后再调用脚本 窗口初始方法
                    _func_show: function () {
                        console.log('event._func_show run');
                    },
                    func_show: function () {
                        obj.is_show = true;
                        console.log('event.func_show run');
                    },

                    // 打开记录窗口并加载数据
                    // 新增时加载默认数据
                    // 编辑时加载编辑的数据
                    _create_voucher: function () {
                        console.log('event._create_voucher.init');
                    },
                    create_voucher: function () {
                        console.log('event.create_voucher.init');
                    },

                    // 初始数据
                    _init_data: function () {
                        console.log('event._init_data.init');
                    },

                    // 表格单元格改变时
                    _cell_change: function () {
                        console.log('event._cell_change.init');
                        obj.event.cell_change();
                    },
                    cell_change: function () {
                        console.log('event.cell_change.init');
                    },

                    // 编辑对象改变时触发
                    _obj_change: function () {
                        console.log('event._obj_change.init');
                        obj.event.obj_change();
                    },
                    obj_change: function () {
                        console.log('event.obj_change.init');
                    },

                    // 插入一行明细时触发
                    _init_row: function () {
                        console.log('event.init_row.init');
                        obj.event.init_row();
                    },
                    init_row: function () {
                        console.log('event.init_row.init');
                    },

                    // 手动针对每一行执行表格数据
                    adjust_row: function () {
                        console.log('event.adjust_row.init');
                    },

                    // 保存时执行
                    _func_before_save: function () {
                        console.log('event._func_before_save.init');
                        obj.event.func_before_save();
                    },
                    func_before_save: function () {
                        console.log('event.func_before_save.init');
                    },

                    // 保存后时执行
                    _func_after_save: function () {
                        console.log('event._func_after_save.init');
                        obj.event.func_after_save();
                    },
                    func_after_save: function () {
                        console.log('event.func_after_save.init');
                    },

                    // 系统删除前执行
                    _func_before_delete: function () {
                        setTimeout(function () {
                            obj.seq.run_next();
                        }, 200);
                    },
                    func_before_delete: function () {
                        console.log('event.func_before_delete.init');
                        setTimeout(function () {
                            obj.seq.abort = 1;
                        }, 200);
                    },

                    // 保存后时执行
                    _func_after_delete: function () {
                        console.log('event._func_after_save.init');
                        obj.event.func_after_save();
                    },
                    func_after_delete: function () {
                        console.log('event.func_after_save.init');
                    },
                };
                return this;
            }
        };
    },

    // 循环自动运行
    auto_run: function () {
        console.log('auto_run 自动循环函数 运行');
        var obj = this;

        return {
            when_build_ele_finish: function () {
                // 创建回调函数，当整个页面控件构建完毕时，
                // 1 执行刷新事件，载入页面数据
                // 2 执行自定义脚本函数 index.Script.code.init_win
                var build_ele_finish = function () {
                    setTimeout(function () {
                        console.log('--[' + obj.ID + '] gui_build_finish_check and wait refresh run');
                        if (obj.base().check_gui_build_state()) {
                            // 尝试用异步顺序执行
                            // 可以优化一下 ，首次打开窗口时，执行多一些
                            // 再次打开某个窗口时，减少一些函数的调用，比如_func_show
                            var seq = obj.seq = obj.base().sequence_run();
                            console.log('is_show: ' + obj.is_show);
                            seq.add_step(1, function () {
                                if (obj.is_show == false) {
                                    obj.event._func_show();  // 窗口创建之后执行并且只执行一次
                                }
                                // 顺序异步
                                setTimeout(function () {
                                    obj.seq.run_next();
                                }, 200);
                            });
                            seq.add_step(2, function () {
                                obj.event.func_show();
                                setTimeout(function () {
                                    obj.seq.run_next();
                                }, 200);
                            });
                            seq.add_step(3, function () {
                                obj.event._create_voucher();  // 创建单据
                                setTimeout(function () {
                                    obj.seq.run_next();
                                }, 200);
                            });
                            seq.add_step(4, function () {
                                obj.event.create_voucher();
                                setTimeout(function () {
                                    obj.seq.run_next();
                                }, 200);
                            });
                            seq.add_step(5, function () {
                                obj.event._init_data();
                                setTimeout(function () {
                                    obj.seq.run_next();
                                    obj.seq.abort = 1;
                                }, 200);
                            });
                            seq.run({sec: 100, name: obj.ID + 'build_ele_finish'});
                        } else {
                            build_ele_finish();
                        }
                    }, 500);
                };
                build_ele_finish();
            },

        };
    },

    lmt: function () {
        var obj = this;
        return {
            // 建立左侧检索元素
            left_lmt_init: function () {
                console.log('page.lmt.left_lmt_init 建立左侧检索元素 运行');
                var elements = obj.data.Left_Lmt;
                if (elements == undefined) {
                    return;
                }
                var v_html = '';
                var item_data = [];  // 提取所有的检索元素保存在单独的数据中
                elements.forEach(function (v, k) {
                    // tab 页开始
                    v_html += '<div title="' + v.tab_title + '" style="padding:20px;display:none;">';

                    var v_table = '';
                    v_table = '<table>';
                    v.data.forEach(function (i_v, i_k) {
                        item_data.push(i_v);
                        v_table += '<tr><td><input id="' + obj.ID + i_v.id + '_check" name="' + i_v.id + '_check" type="checkbox" ';
                        if (i_v.default_checked == 1) {
                            v_table += 'checked = "checked"';
                        }

                        v_table += ' /></td>';
                        v_table += '<td style="width:80px">' + i_v.title + ':</td>';
                        v_table += '<td><input id="' + obj.ID + i_v.id + '" name="' + i_v.id + '" style="width:100px"/></td></tr>';
                    });
                    v_table += '</table>';

                    v_html += v_table + '</div>';  // tab 页结束
                });

                obj.Left_Lmt.append(v_html);

                // 给单选框绑定事件
                item_data.forEach(function (value, key) {
                    $(obj.CA + value.id + '_check').on('click', function () {
                        $(this).attr("checked", !$(this).attr("checked"));
                    });
                });

                // 创建 easyui 控件
                obj.Left_Lmt.data = item_data;
                obj.base().build_elements(item_data);
            },

            // 获取左侧检索元素的勾选项
            left_lmt_choose: function (ele_id) {
                var input_checked = $(obj.CA + ele_id).find("input:checked");
                var choose_ele = [];
                for(var i = 0; i < input_checked.length; i++) {
                    choose_ele.push($(input_checked[i]).attr('name'));
                }
                obj.Left_Lmt.choose_ele = choose_ele;
                return obj.lmt();
            },

            // 获取检索元素的当前值
            left_lmt_val: function () {
                // 检索状态与过滤
                var CA = obj.CA;
                var ele_val = [];
                obj.Left_Lmt.choose_ele.forEach(function (v, k) {
                    obj.Left_Lmt.data.forEach(function (v1, k1){
                        if(v == (v1['id'] + '_check')) {
                            switch (v1['type']) {
                                //日期
                                case 'datebox':
                                    ele_val.push({
                                        ele: v1['id'],
                                        val: $(CA + v1.id).datebox('getValue').date_ui_to_sys(),
                                        filter_col: v1['filter_col'],
                                        filter_type: v1['filter_type'],
                                    });
                                    break;
                                //输入框
                                case 'validatebox':
                                    ele_val.push({
                                        ele: v1['id'],
                                        val: $(CA + v1.id).val().split(','),
                                        filter_col: v1['filter_col'],
                                        filter_type: v1['filter_type'],
                                    });
                                    break;
                                //单选框
                                case 'checkbox':
                                    //$(CA + value.field).checked;
                                    break;
                                //下拉框
                                case 'combobox':
                                    ele_val.push({
                                        ele: v1['id'],
                                        val: $(CA + v1.id).combobox('getValue').split(','),
                                        filter_col: v1['filter_col'],
                                        filter_type: v1['filter_type'],
                                    });
                                    break;
                                // TODO :: 设定每个选项的默认值
                                default:
                                    console.log(v1.id + '字段值 获取不正确');
                            }
                        }
                    });
                });
                return ele_val;
            },

            // 根据条件过滤数据
            left_lmt_filter: function (lmt_ele_val, temp_arr) {
                console.log(lmt_ele_val);
                console.log(temp_arr);
                lmt_ele_val.forEach(function (v, k) {
                    switch(v['filter_type']) {
                        case '=':
                            temp_arr = temp_arr.$json_intersect(v['ele'], v['val']);
                            break;
                        case '>':
                        case '>=':
                        case '<':
                        case '<=':
                            break;
                        default :
                    }
                });
                console.log(temp_arr);
                return temp_arr
            },

        }
    },

    // 基础方法
    base: function () {
        var obj = this;
        return {
            // 服务检查session是否还有效的结果
            // 当客户端通过ajax方式，提交或获取数据的时候，
            // 服务器会判断session缓存是否还存在，如果不存在，就要求客户端重新进入首页或登陆
            // 获取用户信息并保存session
            check_session_result: function (data) {
                if (data.code == 0 && data.url) {
                    window.location.href = data.url;
                }
            },

            // 注册一个页面与tab的关系,以便销毁时使用,一般只在页面方法中调用
            page_register: function (page_obj) {
                var tab = $('#tabs').tabs('getSelected');
                var tab_index = $('#tabs').tabs('getTabIndex', tab);

                Page.prototype.tab_page_map[tab_index] = page_obj;
                return this;
            },

            // 页面关闭时，自动调用并销毁page_obj对象
            page_destroy: function (page_index) {
                console.log('page_destroy: ');
                var delete_obj = Page.prototype.tab_page_map[page_index];
                console.log(delete_obj);
                delete Page.prototype.app[delete_obj["Module"]][delete_obj["Controller"]];
                delete Page.prototype.tab_page_map[page_index];
                return this;
            },

            // 点击按钮
            click: function (button) {
                var CA = obj.CA;
                console.log('click 点击按钮 运行');

                $(CA + button).click();
            },

            /**
             * 检测当前窗口中所有的界面元素是否创建完毕
             * @returns {number}
             */
            check_gui_build_state: function () {
                var wait_check = obj.gui_build_state;
                if (JSON.stringify(wait_check) == '{}') {
                    return 0;
                }
                for (var p in wait_check) {
                    if (wait_check.hasOwnProperty(p)) {
                        console.log('检测的创建控件: ' + p);
                        if (wait_check[p] == false) {
                            return 0;
                        }
                    }
                }
                return 1;
            },

            /**
             * 建立页面元素
             * @param elements
             */
            build_elements: function (elements) {
                console.log('page.base.build_elements 建立页面元素数据 运行');
                var CA = obj.CA;
                elements.forEach(function (value, key) {
                    // TODO :: 设定每个选项的默认值
                    switch (value.type) {
                        //日期
                        case 'datebox':
                            $(CA + value.id).datebox({
                                required: value.required,
                            });
                            break;
                        //输入框
                        case 'validatebox':
                            $(CA + value.id).validatebox({
                                required: value.required,
                                validType: value.validType,
                            });
                            break;
                        //单选框
                        case 'checkbox':
                            //$(CA + value.field).checked;
                            break;
                        //下拉框
                        case 'combobox':
                            $(CA + value.id).combobox({
                                required: value.required,
                                data: value.combo_data,
                                valueField: value.value_id,
                                textField: value.text_id,
                            });
                            $(CA + value.id).combobox('setValue', value.default_val);
                            break;
                        // TODO :: 设定每个选项的默认值
                        default:
                            console.log('build_elements: ' + value.id + '字段class定义不正确');
                    }
                });
            },

            mb_build_elements: function (elements) {
                console.log('page.base.build_elements 建立页面元素数据 运行');
                var CA = obj.CA;
                elements.forEach(function (value, key) {
                    // TODO :: 设定每个选项的默认值
                    switch (value.type) {
                        // 数值
                        case 'numberbox':
                            $(CA + value.id).numberbox({
                                precision: value.precision || 2,
                            });
                            break;
                        // 时间
                        case 'timespinner':
                            $(CA + value.id).timespinner({
                                showSeconds: value.showSeconds || false,
                            });
                            break;
                        //日期
                        case 'datebox':
                            break;
                        //输入框
                        case 'validatebox':
                            break;
                        //单选框
                        case 'checkbox':
                            break;
                        //下拉框
                        case 'combobox':
                            $(CA + value.id).combobox({
                                required: value.required,
                                data: value.combo_data,
                                valueField: value.value_id,
                                textField: value.text_id,
                            });
                            break;
                        default:
                            console.log('build_elements: ' + value.id + '字段class定义不正确');
                    }
                });
            },

            // 异步顺序执行函数对象
            sequence_run: function () {
                var o = {};
                o = {
                    stop: 0,
                    abort: 0,
                    wait: 0,
                    curr_step: 1,
                    step: [],
                    con_num: 1,
                    run: function (set) {
                        setTimeout(function () {
                            if (o.abort == 1) {
                                console.log('异步 ' + (set.name || 'temp') + ' 中止执行...');
                                return;
                            }
                            if (isNull(set.max_num)) {
                                if (o.con_num > 200) {
                                    console.log('异步 ' + (set.name || 'temp') + ' 等待执行，超过默认最大次数 200');
                                    return;
                                }
                            } else {
                                if (o.con_num > set.max_num) {
                                    console.log('异步 ' + (set.name || 'temp') + ' 等待执行，超过设置最大次数 ' + set.max_num);
                                    return;
                                }
                            }
                            if (o.wait == 0) {
                                console.log('异步 ' + (set.name || 'temp') + ' 顺序  ' + (o.curr_step) + ' 运行');
                                // 如果 add_step 传入给 step 中没有回调执行函数
                                // curr_step 并没有在回调中 +1,
                                // 而是在 step[o.curr_step -1]中直接 +1了，那么
                                // 会导致 先执行 eba_rep_list.wait = 0,再执行 o.wait = 1;
                                // 异步中，再调用非异步的下一步时，也需要使用异步延时调用
                                /*
                                 setTimeout(function(){
                                 switch_seq.curr_step += 1;
                                 switch_seq.wait = 0;
                                 },200);
                                 */
                                o.step[o.curr_step - 1]();
                                o.wait = 1;
                                o.con_num = 1;
                                o.run(set);
                            } else {
                                if (set.name != undefined) {
                                    console.log('异步 ' + (o.curr_step + 1) + ' ,第' + o.con_num + '次, 等待执行 ' + (set.name || 'temp'));
                                } else {
                                    console.log('异步 ' + (o.curr_step + 1) + ' ,第' + o.con_num + '次, 等待执行');
                                }
                                o.con_num += 1;
                                o.run(set);
                            }
                        }, set.sec || 100);
                    },
                    init: function () {
                        this.abort = 0;
                        this.wait = 0;
                        this.curr_step = 1;
                        this.step = [];
                    },
                    run_next: function () {
                        this.curr_step += 1;
                        this.wait = 0;
                    },
                    add_step: function (num, cb) {
                        this.step.push(cb);
                    }
                };
                return o;
            },

        };
    },

    form: function () {
        var obj = this;
        return {
            // 设置字段只读
            field_set_readonly: function (key) {
                console.log('field_set_readonly 设置字段只读 运行');
                console.log('key: ' + key);
                $(this.CA + key).attr('readonly', 'readonly');
            },

            // 设置字段可写
            field_set_write: function (key) {
                console.log('field_set_write 设置字段可写 运行');
                $(this.CA + key).attr('readonly', null);
            },

            // 设置习惯参数
            check_value_init: function (id, url) {
                console.log('check_value_init 设置习惯参数 运行');
                //var user_para = this.user_para;
                var reg = /\./g;
                var rep_reg = '\\.';
                var rep_id = id.replace(reg, rep_reg);

                $.post(url + '/get_user_para_id', {para_id: id}, function (val) {
                    if (val == 1) {
                        $('#' + rep_id).attr('checked', true);
                    }
                });
            },

            // 习惯参数改变 - 勾选框
            check_values_change: function (id, url) {
                console.log('check_values_change 习惯参数改变 运行');
                //var obj = this;
                var reg = /\./g;
                var rep_reg = '\\.';
                var rep_id = id.replace(reg, rep_reg);

                //习惯参数
                $('#' + rep_id).change(function () {
                    switch (this.checked) {
                        case true:
                            $.post(url + '/set_user_para_id', {para_id: id, para_value: '1'}, function (info) {
                            });
                            break;
                        case false:
                            $.post(url + '/set_user_para_id', {para_id: id, para_value: '0'}, function (info) {
                            });
                            break;
                    }
                    obj.refresh();
                });
            },

            /* --------------------------------- */
            // 记录主键
            set_key: function (key) {
                obj.key = key;
            },

            /* --------------------------------- */

            /* --------------------------------- */
            // 表单载数据
            form_load_data: function (data) {
                console.log('form_load_data 表单载数据 运行');
                $(obj.CA + 'form').form('load', data);
            },

            // 表单清空
            form_clear: function () {
                console.log('form_clear 表单清空 运行');
                $(obj.CA + 'form').form('clear');
            },

            // 设置记录照片的路径
            record_img_init: function (src) {
                console.log('record_img_init 设置记录照片的路径 运行');
                $(obj.CA + 'record_img').attr('src', src);
            },

            // 刷新记录相关文件
            record_about_file_ref: function (key, value) {
                console.log('record_about_file_ref 刷新记录相关文件 运行');
                if (key && value) {
                    $(obj.CA + 'about_file_iframe').attr('src', this._url + "/about_file/" + key + "/" + value);

                    console.log('record_about_file_ref');
                    console.log($(obj.CA + 'about_file_iframe').attr('src'));
                } else {
                    $(obj.CA + 'about_file_iframe').attr('src', this._url + "/about_file/");
                }
            },

            // 主ID 改变时执行的方法，一般用于新增时，检查有没有重复的主ID
            record_pk_change: function (url, pk) {
                console.log('record_pk_change 主ID 改变时执行的方法，一般用于新增时，检查有没有重复的主ID 运行');
                var CA = obj.CA;
                var MCA = obj.MCA;
                var record = obj;

                $(CA + pk).on("change", function () {
                    var arg = {};
                    arg[pk] = $(CA + pk).val();

                    $.post(url + "/get_record_val" + MCA, arg, function (data) {
                        if (data instanceof Object) {
                            record.form_load_data(data);
                            if (data['record_img_path'] != null) {
                                record.set_record_img_src(data['record_img_path'] + '?t=' + new Date().getTime());
                            }
                        }
                    });
                });
            },

            // 记录 - 重新注册上传组件
            record_upload_file_init: function (url) {
                console.log('record_upload_file_init 记录 - 重新注册上传组件 运行');
                var CA = obj.CA;
                var MCA = obj.MCA;
                $(CA + 'upload_file').fileupload({
                    url: url + '/set_record_img' + MCA,
                    dataType: 'json',
                    done: function (e, data) {
                        if (data != null) {
                            $(CA + "record_img").attr('src', data.result + '?t=' + new Date().getTime());
                        }
                    },
                });
            },

            // 移动端字段初始化
            mb_field_init: function () {
                var elements = obj.data.Form_Field;
                if (elements == undefined) {
                    return;
                }
                var v_html = '';
                var cls = '';
                var item_data = [];  // 提取所有的检索元素保存在单独的数据中
                elements.forEach(function (i_v, i_k) {
                    item_data.push(i_v);
                    var data_op = '';
                    var tip = i_v.tip || i_v.title;
                    var read_only = '';
                    if (undefined != i_v.required) {
                        data_op = ' data-options="required:true" ';
                    }
                    if (undefined != i_v.read_only) {
                        read_only = ' readonly="readonly" ';
                    }
                    switch (i_v.type) {
                        case 'hidden':
                            v_html += '<div><input type="hidden" id="' + obj.ID + i_v.id + '" name="' + i_v.id + '" /></div>';
                            break;
                        default :
                            v_html += '<div style="margin-bottom:10px"><input class="easyui-' + i_v.type + '" ' + data_op + read_only + ' label="' + i_v.title + ':" prompt="' + tip + '"  id="' + obj.ID + i_v.id + '" name="' + i_v.id + '" style="width:100%"/></div>';
                    }
                });
                //console.log(obj.Fm);
                //console.log(v_html);
                obj.Fm.append(v_html);
                // 创建 easyui 控件
                obj.base().mb_build_elements(item_data);

                obj.gui_build_state.form = true;
            },

            /**
             * jqery序列化表单的数组转成表格的行对象
             * 用于添加表格的一行
             * @param arr
             */
            serialize_array_to_obj: function (arr) {
                var o = {};
                arr.forEach(function (v, k) {
                    var temp_name = '';
                    var temp_val = '';
                    for (var v_i in v) {
                        if (v.hasOwnProperty(v_i)) {
                            if (v_i == 'name') {
                                temp_name = v[v_i];
                            }
                            if (v_i == 'value') {
                                temp_val = v[v_i];
                            }
                        }
                        o[temp_name] = temp_val;
                    }
                });
                return o;
            },

            // 当前对象(子级)的表单内容 保存 到父级表格中
            save_data_to_datagrid: function (grid_id, fn) {
                var form_val = [];
                var row_obj = {};
                var dg = obj.father[grid_id];
                var index = 0;

                // 插入新的一行
                if (!obj.Fm.form('validate')) {
                    return;
                }
                form_val = $(obj.CA + 'Form').serializeArray();
                row_obj = {
                    row: obj.form().serialize_array_to_obj(form_val)
                };
                // 回调处理表单数据内容
                fn && fn(row_obj);

                if (obj.edit_state == 'edit') {
                    // 获取表格编辑的当前行索引，删除本行，再插入一行
                    index = obj.edit_index;
                    row_obj.index = index;
                    dg.datagrid('deleteRow', index);
                }

                dg.datagrid('insertRow', row_obj);
                if (obj.edit_state == 'add') {
                    index = dg.datagrid('getData').total - 1;
                    obj.edit_state = 'edit';
                    obj.edit_index = index;
                }
                dg.datagrid('selectRow', index);
                // 调用表格数据变化事件
                dg.where_data_chg();
            }

        };
    },

    linkbutton: function () {
        var obj = this;
        return {
            /**
             * 关闭窗口
             * @param close_id
             * @param win_obj 要关闭的窗口对象 默认 father.Fun_Win
             */
            win_close: function (close_id, win_obj) {
                console.log('win_close 关闭当前卡片窗口 运行');
                var CA = obj.CA;
                var win = win_obj || obj.father.Fun_Win;  // 默认关闭功能窗口

                $(CA + close_id).on('click', function () {
                    win.dialog("close");
                });
                return obj;
            },

            /**
             * 调用上级的新增按钮点击事件
             */
            record_add: function (id) {
                var CA = obj.CA;

                $(CA + id).on('click', function () {
                    console.log('record_add 记录新增按钮 运行');
                    obj.edit_state = 'add';
                    obj.event._create_voucher();
                });
                return obj;
            },

            /**
             * 调用记录单独定义的保存事件
             */
            record_save: function (id) {
                console.log('record_save 记录保存按钮 初始化');
                var CA = obj.CA;

                $(CA + id).on('click', function () {
                    console.log('record_save 记录保存按钮 运行');
                    obj.event._func_before_save();
                });
                return obj;
            },

            /**
             * 选择返回 初始化
             * @param op
             * op.c_type 选择的类型 表格,树
             * op.field  要选择的字段id
             * op.r_type 返回给什么类型
             * op.r_id   返回给哪个jquery 选择器 id
             */
            choose_return: function (op) {
                console.log('choose_return_init 选择确定返回 初始化');
                var father = obj.father;
                var CA = obj.CA;

                $(CA + 'choose_return').on('click', function () {
                    var data = [];
                    // 得到当前选择的值
                    if (op.c_type == 'data_grid') {
                        data = obj.MG.datagrid('getSelections');
                        console.log('op.c_field: ' + op.c_field);
                        console.log('data: ' + app.json_to_str(data));
                        data = data.$json_col(op.c_field);
                        console.log('data: ' + app.json_to_str(data));
                    }
                    if (op.c_type == 'tree') {

                    }

                    // 没有数据提前返回
                    if (isNull(data)) {
                        father.win.dialog('close');
                    }

                    // 将值赋给需要返回的对象
                    if (op.r_type == 'combobox') {
                        $('#' + op.r_id).combobox('setValues', data);
                    }
                    if (op.r_type == 'data_grid') {

                    }

                    father.win.dialog('close');
                });
                return obj;
            },

            /**
             * 选择 取消 初始化
             */
            choose_cancel: function () {
                console.log('choose_cancel_init 选择确定返回 初始化');
                var father = obj.father;
                var CA = obj.CA;

                $(CA + 'choose_cancel').on('click', function () {
                    father.win.dialog('close');
                });
                return obj;
            },

            /**
             * 界面新增按钮
             * @param o
             * but_id, 点击哪个按钮
             * record, 卡片记录对象
             * open_win_id, 本窗口标识
             * open_win_val, 窗口对象定义
             */
            index_add: function (o) {
                console.log("index_add 列表新增按钮 运行");
                var id = o.btn_id;
                var record = obj.child_record;
                var record_win = obj.Record_Win;
                var CA = obj.CA;

                $(CA + id).on('click', function () {
                    record.edit_state = 'add';
                    record.have_change = 0;
                    if (obj.open_win_id == o.open_win_id) {
                        record_win.dialog('open');
                    } else {
                        record_win.dialog(o.win_op);
                        obj.open_win_id = o.open_win_id;
                    }
                });
                return obj;
            },

            /**
             * 界面编辑按钮
             * @param o
             * o.btn_id 点击按钮
             * o.record  卡片记录对象
             * o.grid_id 表格名称
             */
            index_edit: function (o) {
                var id = o.btn_id;
                var record = obj.child_record;
                var dg = obj[o.grid_id];
                var win = obj.Record_Win;
                var CA = obj.CA;

                $(CA + id).on('click', function () {
                    var row = dg.datagrid('getSelected');
                    if (!row) {
                        $.messager.alert('提示', '没有选择记录');
                        return;
                    }
                    // 当前记录对象编辑行的数据
                    record.edit_state = 'edit';
                    record.have_change = 0;
                    record.edit_row_data = row;
                    record.edit_index = dg.datagrid('getRowIndex', row);
                    if (obj.open_win_id == o.open_win_id) {
                        win.dialog('open');
                    } else {
                        win.dialog(o.win_op);
                        obj.open_win_id = o.open_win_id;
                    }
                });
                return obj;
            },

            /**
             * 界面删除按钮
             * @param o
             * o.btn_id 按钮id
             * o.grid_id 表格名称
             * o.key id列
             * o.name 名称列
             */
            index_remove: function (o) {
                console.log('index_remove 界面删除按钮 运行');
                var CA = obj.CA;
                var id = o.btn_id;
                var dg = obj[o.grid_id];

                $(CA + id).on('click', function () {
                    var row = dg.datagrid('getSelected');
                    var row_num = dg.datagrid('getRowIndex', row);
                    var arg = {};
                    arg[o.key] = row[o.key];

                    if (isNull(row)) {
                        $.messager.alert('提示', '没有选择数据');
                        return;
                    }

                    $.messager.confirm('提示', '确认删除 第 ' + (row_num + 1) + ' 行: ' + row[o.key] + ':' + row[o.name] + '?',
                        function (r) {
                            if (r) {
                                dg.datagrid('deleteRow', row_num);
                                // 页面定义当表格数据发生变化时重新计算
                                dg.where_data_chg();
                            }
                        }
                    );
                });
                return obj;
            },

            /**
             * 界面打印按钮
             *
             * @param o
             */
            index_print: function (o) {
                console.log('index_print 界面打印按钮 初始化');
                var CA = obj.CA;
                $(CA + 'print').on('click', function () {
                    console.log('index_print 界面打印按钮 运行');
                    var datagrid_header = $(CA + 'body .datagrid-view2 .datagrid-header').clone();
                    var datagrid = $(CA + 'body .datagrid-view2 .datagrid-body').clone();
                    datagrid.contents().find("tbody").eq(0).prepend(datagrid_header.contents().find(".datagrid-header-row").html());

                    LODOP = getLodop();
                    if (LODOP == false) {
                        return
                    }
                    LODOP.PRINT_INIT("打印控件功能演示_Lodop功能_整页表格");
                    LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A4");
                    LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", datagrid.html());
                    LODOP.SET_PREVIEW_WINDOW(0, 0, 0, 800, 600, "");
                    LODOP.PREVIEW();
                    //LODOP.SET_SAVE_MODE("FILE_PROMPT",false);
                    //if (LODOP.SAVE_TO_FILE("c:\\abc.xls")) alert("导出成功！");
                });
                return obj;
            },

            /**
             * 界面导出按钮
             *
             * @param o
             */
            index_toout: function (o) {
                console.log('index_toout 界面导出按钮 初始化');
                var CA = obj.CA;
                $(CA + 'toout').on('click', function () {
                    console.log('index_toout 界面导出按钮 运行');
                    var datagrid_header = $(CA + 'body .datagrid-view2 .datagrid-header').clone();
                    var datagrid = $(CA + 'body .datagrid-view2 .datagrid-body').clone();
                    datagrid.contents().find("tbody").eq(0).prepend(datagrid_header.contents().find(".datagrid-header-row").html());

                    LODOP = getLodop();
                    if (LODOP == false) {
                        return
                    }
                    LODOP.PRINT_INIT("打印控件功能演示_Lodop功能_整页表格");
                    LODOP.SET_PRINT_PAGESIZE(2, 0, 0, "A4");
                    LODOP.ADD_PRINT_TABLE("2%", "1%", "96%", "98%", datagrid.html());
                    LODOP.SET_SAVE_MODE("FILE_PROMPT", false);
                    if (LODOP.SAVE_TO_FILE("c:\\abc.xls")) alert("导出至c:\\abc.xls成功！");
                });
                return obj;
            },

            /**
             * 刷新按钮
             * @param id
             */
            index_ref: function (id) {
                console.log('index_ref 刷新按钮 初始');
                var CA = obj.CA;
                //刷新表格
                $(CA + id).on('click', function () {
                    console.log('index_ref 刷新按钮 运行');
                    obj.event._create_voucher();
                });
                return obj;
            },

            /**
             * 选择更多按钮
             * choose_obj.title 标题
             * choose_obj.href 将要打开的选择链接
             * choose_obj.dict_id 字典
             * choose_obj.rid 返回给值的id对象
             */
            choose_more: function () {
                console.log('choose_more 按钮 初始');

                $('.pub_choose').on('click', function (e) {
                    console.log('choose_more 按钮 运行');
                    obj.choose_obj = this.getAttribute('data-op') || null;
                    if (obj.choose_obj == null) {
                        return;
                    }
                    obj.choose_obj = JSON.parse(eval('"' + record + '"'));
                    obj.fun_win_open({
                        title: obj.choose_obj.title || '选择',
                        url: obj.choose_obj.href,
                        fit: false
                    });
                });
                return obj;
            },

            /**
             * 功能对象退出按钮
             * todo::运行完其他功能之后，此按钮需要点击2次才能执行退出功能
             */
            index_back: function (id) {
                console.log('index_back 退出按钮 初始');
                var CA = obj.CA;

                $(CA + 'obj_back').on('click', function () {
                    console.log('index_back 退出按钮 运行');
                    mui.back();
                });
                return obj;
            },

            /**
             * 清除表单
             */
            form_clear: function () {
                console.log('form_clear 表单清空 初始');
                var CA = obj.CA;

                $(CA + 'clear').on('click', function () {
                    console.log('form_clear 表单清空 运行');
                    $(CA + 'Form').form('clear');

                });
                return obj;
            },

            /**
             * 打开窗口 || 指定按钮
             * @param o
             * o.fit 最大化
             * o.modal 遮罩
             * o.height 高度
             * o.width 宽度
             * o.closed 默认关闭
             * @returns {boolean}
             */
            win_open: function (o) {
                var CA = obj.CA;
                if (o.fit == undefined) {
                    o.fit = true;
                }
                if (o.modal == undefined) {
                    o.modal = true;
                }
                if (o.width == undefined) {
                    o.width = '80%';
                }
                if (o.height == undefined) {
                    o.height = '80%';
                }
                if (o.closed == undefined) {
                    o.closed = true;
                }
                if (o.btn_id == undefined) {
                    console.log('fun_win_open 打开窗口 运行');
                    $(CA + 'Win').dialog(o).dialog('open').dialog('refresh', o.url);
                }
                $(CA + o.btn_id).on("click", function () {
                    console.log('fun_win_open 指定按钮打开窗口 运行');
                    $(CA + 'Win').dialog(o).dialog('open').dialog('refresh', o.url);
                });
            },

            /**
             * 绑定按钮 与 某个方法 - 思考，如果把参数传递给方法
             * @param btn_id
             * @param fun
             */
            bind: function (btn_id, fun) {
                console.log('bind 绑定按钮 与 某个方法 - 思考，如果把参数传递给方法 运行');
                var CA = obj.CA;
                $(CA + btn_id).on('click', fun);
                return obj;
            },

            // 前一条按钮
            record_bef: function (url, key, desc) {
                console.log('record_bef 前一条按钮 初始');
                var CA = obj.CA;
                var MCA = obj.MCA;
                var father = obj.father;
                var load_record = father.form_load_record;
                var propertygrid = father.propertygrid;
                var record = father.record;
                $(CA + 'bef').on('click', function () {
                    console.log('record_bef 前一条按钮 运行');
                    var row = father.row_go('bef');
                    $(CA + 'form').form('clear');
                    load_record(url, MCA, key, row[key], desc, record);
                    propertygrid(url, key, row[key]);
                });
                return obj;
            },

            // 后一条按钮
            record_aft: function (url, key, desc) {
                console.log('record_aft 后一条按钮 初始');
                var CA = obj.CA;
                var father = obj.father;
                //var load_record = father.form_load_record;
                //var propertygrid = father.propertygrid;
                var record = father.record;
                $(CA + 'aft').on('click', function () {
                    console.log('record_aft 后一条按钮 运行');
                    var row = father.row_go('aft');
                    $(CA + 'form').form('clear');

                    //load_record(url, MCA, key, row[key], desc, record);
                    //propertygrid(url, key, row[key]);
                });
                return obj;
            },

            // 记录删除按钮
            // o.btn_id
            record_remove: function (o) {
                console.log('record_remove 记录删除按钮 初始');
                var CA = obj.CA;
                var id = o.btn_id;

                $(CA + id).on('click', function () {
                    console.log('record_remove 记录删除按钮 运行');
                });
                return this;
            },

        };
    },

    datagrid: function () {
        var obj = this;
        return {
            /**
             * 当一行编辑完成的时候
             * @param dg 表格
             * @returns {boolean}
             */
            end_edit: function (dg) {
                console.log('检测本行是否能结果编辑状态');
                console.log('第 ' + dg.edit_index + ' 行');
                var edit_index = dg.edit_index;
                if (edit_index == undefined) {
                    return true
                }

                // 要验证是否能结束当前行编辑模式
                // 其他自定义的验证方法
                // 空行 不能结束编辑
                var row_data = dg.datagrid('getSelected');
                console.log('要结果的本行数据: ');
                console.log(row_data);
                if (null == row_data) {
                    // 不能结束空行编辑
                    return false;
                }

                // note_info::
                // 常规验证: 定义在表格列的编辑器op里
                if (dg.datagrid('validateRow', edit_index)) {
                    dg.datagrid('endEdit', edit_index);
                    dg.edit_index = undefined;
                    return true;
                } else {
                    return false;
                }
            },

            /**
             * 表格行编辑 - 添加空行
             * @param id
             * @param dg
             * @param new_row_obj
             */
            row_add: function (id, dg, new_row_obj) {
                console.log('row_add 表格行编辑 - 添加空行 初始化');
                var endEditing = this.end_edit;
                var CA = obj.CA;

                $(CA + id).on("click", function () {
                    if (endEditing(dg)) {
                        dg.datagrid('appendRow', new_row_obj || {});
                        console.log('增加了第几行: ' + dg.datagrid('getRows').length);
                        dg.edit_index = dg.datagrid('getRows').length - 1;
                        dg.datagrid('selectRow', dg.edit_index).datagrid('beginEdit', dg.edit_index);
                    }
                });
            },

            /**
             * 表格行编辑 - 添加移除行
             * @param id
             * @param dg
             */
            row_remove: function (id, dg) {
                console.log('row_remove 表格行编辑 - 添加移除行 运行');
                var CA = obj.CA;

                $(CA + id).on("click", function () {
                    console.log('row_remove 运行');
                    if (dg.edit_index == undefined) {
                        return
                    }
                    dg.datagrid('cancelEdit', dg.edit_index).datagrid('deleteRow', dg.edit_index);
                    dg.edit_index = undefined;
                });
            },

            /**
             * 表格行编辑 - 保存
             * @param id
             * @param dg
             * @param cb
             */
            row_save: function (id, dg, cb) {
                console.log('row_save 表格行编辑 - 保存 初始化');
                var CA = obj.CA;
                var endEditing = this.end_edit;
                //var arg = this.obj_id;

                $(CA + id).on("click", function () {
                    if (endEditing(dg)) {
                        alert(JSON.stringify(dg.datagrid('getData')));
                        dg.datagrid('acceptChanges');
                        dg.datagrid('clearSelections');
                        alert(JSON.stringify(dg.datagrid('getData')));
                        //var post_data = $(CA + 'datagrid').datagrid('getData');
                        //arg['data'] = post_data;
                        cb && cb();
                    }
                });
            },

            /**
             * 表格行编辑 - 返回到改变之前
             * @param id
             * @param dg
             */
            row_undo: function (id, dg) {
                console.log('row_undo 表格行编辑 - 返回到改变之前 运行');
                var CA = obj.CA;

                $(CA + id).on("click", function () {
                    dg.datagrid('rejectChanges');
                    dg.edit_index = undefined;
                });
            },

            /**
             * 表格行编辑 - 查看改变
             * @param id
             * @param dg
             */
            row_change: function (id, dg) {
                console.log('row_change 表格行编辑 - 查看改变 运行');
                var CA = obj.CA;
                $(CA + id).on("click", function () {
                    var rows = dg.datagrid('getChanges');
                    alert(rows.length + ' rows are changed!');
                });
            },

            // 表格的高度
            // layout, title, grid
            get_height: function () {
                console.log('计算表格高度');
                var page_h = $('#layout').layout('panel', 'center').panel('options').height;
                var title_h = document.getElementsByClassName('panel-title')[0].offsetHeight;
                return page_h - title_h * 1.5;
            },

            // 普通表格
            /**
             * 表格的显示方案列，然后初始化表格
             *
             * @param op.grid_id 要初始化的表格名称
             * @param op.o 表格初始化参数
             */
            init: function (op) {
                console.log('datagrid_init, 表格初始化 ');
                var dg = obj[op.grid_id];

                // 需要格式化的单据金额列（非明细中）
                // 明细的单价列也是需要格式化
                // 单据中
                // discount_amount	单据金额
                // draw_amount	提成金额
                // mem_card_pay_amount	卡支付额
                // bank_card_pay_amount	银行支付额
                // gift_ticket_pay_amount	赠券支付额
                // io_amount	收支金额
                // pre_amount	预收支
                // main_res_total_amount	物资金额 - 这个是应该是单独查出来的，主物资金额

                var need_fmt_amount = [
                    'discount_amount', 'draw_amount', 'mem_card_pay_amount',
                    'bank_card_pay_amount', 'gift_ticket_pay_amount', 'io_amount',
                    'pre_amount', 'main_res_total_amount', 'in_ceil_price', 'in_ref_price',
                    'out_floor_price', 'out_ref_price'
                ];

                // 需要格式化的单据日期列
                var need_fmt_date = [
                    'voucher_date', 'create_date', 'check_date',
                    'date_lmt_res', 'date_lmt_ebm'
                ];

                var columns = op.o.columns;
                if (undefined == columns) {
                    columns = obj.data[op.grid_id].column;
                }

                // 格式化列内容
                for (var i = 0; i < columns.length; i++) {
                    // 格式化金额列
                    if (~need_fmt_amount.indexOf(columns[i]['field'])) {
                        columns[0][i]['formatter'] = function (value, row, index) {
                            return parseFloat(value) / 100;
                        }
                    }

                    // 格式化日期
                    if (~need_fmt_date.indexOf(columns[i]['field'])) {
                        columns[0][i]['formatter'] = function (value, row, index) {
                            if (typeof value == 'string' && value.length == 8) {
                                return value.date_sys_to_ui();
                            }
                        }
                    }
                }

                op.o.columns = [columns];
                dg.datagrid(op.o);
                // 标识构建的表格完成
                obj.gui_build_state[op.grid_id] = true;
                op.cb && op.cb();
            },

            /**
             * 返回数组中 json 中 某些列的对应值[合计、平均、最大、最小]
             * @param data  数组 json
             * @param col_arr  哪些列
             * @param r_type 返回类型
             * @returns {{}}
             */
            get_arr_json_col: function (data, col_arr, r_type) {
                var r_obj = {};
                if (r_type = 'sum') {
                    col_arr.forEach(function (v, k) {
                        var temp = 0;
                        data.forEach(function (val, key) {
                            if (!isNaN(parseFloat(val[v]))) {
                                temp += parseFloat(val[v]);
                            }
                        });
                        r_obj[v] = temp;
                    });
                }
                return r_obj;
            },

            /**
             * 重新计算表格数据中的某列的合计值
             * @param o
             */
            load_footer_data: function (o) {
                var dg = obj[o.grid_id];
                var footer_field = o.footer_field;
                var need_type = o.need_type;
                var footer_data = [];
                var dg_data = dg.datagrid('getData');

                footer_data.push(obj.datagrid().get_arr_json_col(dg_data.rows, footer_field, need_type));
                dg.datagrid('reloadFooter', footer_data);
                o.fn && o.fn(footer_data);
            },

            /**
             *  载入数据
             *  @param o
             *   o.footer_sum_field // 需要求和的列
             *   o.gd_obj // 数据表名称
             *   o.cb  // 回调方法
             */
            load_data: function (o) {
                //console.log('o: ' + app.json_to_str(o));
                var dg = obj[o.grid_id];
                var dg_data = {};
                var footer_field = o.footer_field;
                var need_type = o.need_type;

                var load_main_data = function () {
                    dg_data.total = o.data.length;
                    dg_data.rows = o.data;

                    // 表格重载数据
                    dg.datagrid('loadData', dg_data);
                    if (o.select_first) {
                        dg.datagrid('selectRow', 0)
                    }
                    // 页面定义当表格数据发生变化时重新计算
                    dg.where_data_chg();
                };

                load_main_data();
            },

            // 选中的行
            row_go: function (dg, to) {
                console.log('row_go 选中的行 运行');
                var row_selected, row_selected_index, maxlength;
                row_selected = dg.datagrid('getSelected');
                maxlength = $.Object.count.call(dg.datagrid('getRows'));

                row_selected_index = row_selected ? dg.datagrid('getRowIndex', row_selected) : 0;
                switch (to) {
                    case 'bef':
                        if (row_selected_index != 0) {
                            row_selected_index -= 1;
                        }
                        break;
                    case 'aft':
                        if (row_selected_index != maxlength - 1) {
                            row_selected_index += 1;
                        }
                        break;
                    case 'delete':
                        dg.datagrid('deleteRow', row_selected_index);
                        if (row_selected_index >= maxlength - 1) {
                            row_selected_index = maxlength - 2;
                        }
                        break;

                    default:
                    // code
                }

                dg.datagrid('selectRow', row_selected_index);
                row_selected = dg.datagrid('getSelected');
                return row_selected;
            },


        };
    },

    propertygrid: function () {
        var obj = this;
        return {
            // 右侧属性窗口
            init: function (url, k, v) {
                console.log('property_grid 右侧属性窗口 运行');
                var t_v = '字段';
                var v_v = '值';
                var property_grid = this.property_grid;
                var property_columns = [[
                    {field: 'name', title: t_v, width: 50, sortable: true},
                    {field: 'value', title: v_v, width: 150, resizable: false}
                ]];
                property_grid.propertygrid({
                    //url: "{:U('get_grid_data',array(module=>'Emp',table=>'EmpView'))}",   //远程获取json指定表格方式
                    url: url + '/property_grid_data/' + k + '/' + v,
                    showGroup: true,
                    scrollbarSize: 0,
                    columns: property_columns,
                    rowStyler: function (index, row) {
                        if (index == 0) {
                            return 'color:red;';
                        }
                    }
                });
            },
        };
    },

    searchbox: function () {
        var obj = this;
        return {
            // 搜索框初始化
            init: function (id, prompt) {
                console.log('search_init 搜索框初始化 运行');
                var CA = obj.CA;

                obj[id].searchbox({
                    menu: CA + id + "_Menu",
                    prompt: prompt,
                    width: '100%',
                    searcher: function (value, name) {
                        obj.event.search_data();
                    }
                });
                return this;
            },
        };
    },

    menu: function () {
        var obj = this;
        return {
            // 创建功能下拉菜单
            menu_item: function (menu_data, add_to_div) {
                if (menu_data == undefined) {
                    console.log('请传递' + 'menu_data');
                    return;
                }
                function to_html(v) {
                    var html = '';
                    var v_html = '';
                    var v_id = v['id'];
                    var menu_item = v['title'];
                    if (v['b']) { // 加粗
                        menu_item = '<b>' + menu_item + '</b>';
                    }
                    if (v['span']) {
                        menu_item = '<span>' + menu_item + '</span>';
                    }
                    if (v['id'] == 'menu-sep') {
                        // 分隔符
                        menu_item = '<div class="menu-sep"></div>';
                    } else if (v['have_item']) {
                        // 有子菜单
                        menu_item = '<div>' + menu_item;
                        // 子菜单开始
                        if (v['width']) {
                            menu_item += "<div style=\"width:" + v['width'] + "px;\">";
                        } else {
                            menu_item += "<div>";
                        }
                        // 循环子菜单 - 暂时只支持2级菜单
                        var fun_item_menu = '';
                        v['item'].forEach(function (i_v, i_k) {
                            fun_item_menu += to_html(i_v);
                        });

                        // 子菜单结束
                        menu_item = menu_item + fun_item_menu + "</div></div>";
                    } else {
                        var options = '';
                        var url = '';
                        // 没有子菜单
                        if (v['icon']) {
                            options = 'data-options="iconCls:\'' + v['icon'] + '\'"';
                        }
                        if (v['url']) {
                            url = ' url="' + v['url'] + '"';
                            options += url;
                        }

                        if (options != '') {
                            menu_item = '<div id="' + obj.ID + v['id'] + '" ' + options + '>' + menu_item + '</div>';
                        } else {
                            menu_item = '<div id="' + obj.ID + v['id'] + '">' + menu_item + '</div>';
                        }
                    }
                    html += menu_item;
                    return html;
                }

                var r_html = '';
                menu_data.forEach(function (v, k) {
                    r_html += to_html(v);
                });
                $(obj.CA + add_to_div).append(r_html);
                obj.gui_build_state.menu = true;
                //console.log(r_html);
                return r_html;
            },
            // 注意在对象中定义与菜单div命名同样的功能对象
            // 例如 index.popup_menu.change_emp_id 功能
            // 点击 change_emp_id 菜单选择时，调用这个自定义菜单选项
            menu_item_click_bind: function (menu_data, menu_div_id) {
                $(obj.CA + menu_div_id).menu({
                    onClick: function (item) {
                        // 调用菜单操作的窗口
                        menu_data.forEach(function (v, k) {
                            if ((obj.ID + v['id']) == item.id && v['item'] == undefined) {
                                if (obj[menu_div_id].hasOwnProperty(v['id'])) {
                                    obj[menu_div_id][v['id']](item);
                                }
                            } else if (v['item']) {
                                v['item'].forEach(function (i_v, i_k) {
                                    if ((obj.ID + i_v['id']) == item.id) {
                                        if (obj[menu_div_id].hasOwnProperty(i_v['id'])) {
                                            obj[menu_div_id][i_v['id']](item);
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            }

        };
    },

    menubutton: function () {
        var obj = this;
        return {
            // 左侧树右键菜单
            context_menu_init: function (obj, menu) {
                console.log('set_context_menu 左侧树右键菜单 运行');

                $(obj).on('contextmenu', function (e) {
                    e.preventDefault();
                    $(menu).menu('show', {
                        left: e.pageX,
                        top: e.pageY
                    });
                });
            },
            // 顶部菜单按钮初始
            // 插入到哪个元素之后
            linkbutton_init: function (data, ele, type) {
                console.log(obj.ID + ' linkbutton_init');
                console.log(data);
                if (!(Array.isArray(data) && data.length != 0)) {
                    console.log('menubutton:linkbutton_init 请传递 数组 data');
                    return;
                }
                var html = '';
                data.forEach(function (v, k) {
                    html += '<a id="' + obj.ID + v['id'] + '" href="#" class="easyui-linkbutton" data-options="iconCls:\'icon-' + v['icon'] + '\'">' + v['text'] + '</a>';
                });
                if (ele != null && type != null) {
                    if (type == 'after') {
                        $(obj.CA + ele).after(html);
                    } else if (type == 'before') {
                        $(obj.CA + ele).before(html);
                    } else if (type == 'append') {
                        $(obj.CA + ele).append(html);
                    }
                }

                // console.log(html);
                obj.gui_build_state.linkbutton = true;
                return html;
            },

            // 按钮功能绑定事件
            linkbutton_event: function (data, type, op) {
                console.log(obj.ID + ' linkbutton_event');
                if (!(Array.isArray(data) && data.length != 0)) {
                    console.log('menubutton:linkbutton_init 请传递 数组 data');
                    return;
                }
                if(type == 'index') {
                    data.forEach(function (v, k) {
                        if( v['id'] == 'add') {
                            obj.linkbutton().index_add({
                                btn_id: 'add',
                                open_win_id: 'record',
                                win_op: op.add.win_op,
                                //record: obj.child_record
                            })
                        }
                        if( v['id'] == 'edit') {
                            obj.linkbutton().index_edit({
                                btn_id: 'edit',
                                open_win_id: 'record',
                                win_op: op.edit.win_op,
                                grid_id: op.edit.grid_id,
                                //record: obj.child_record
                            })
                        }
                    });
                }
                return obj;
            },

        };

    },

    tree: function () {
        var obj = this;
        return {
            // 主菜单
            // 根据服务器传递的json数据，生成左侧主导航界面
            nag_menu_build: function (data) {
                console.log('tree().nag_menu_build()');
                var menu_data = [];
                data.forEach(function (v, k) {
                    var menu_item = {
                        text: v['name'],
                        state: 'closed',
                    };
                    if (v['item'].length > 0) {
                        menu_item['children'] = [];
                        v['item'].forEach(function (cv, ck) {
                            var child_item = {
                                text: cv['name'],
                                attributes: {
                                    module: cv['dll_id'],
                                    controller: cv['func_id']
                                }
                            };
                            menu_item['children'].push(child_item);
                        })
                    }
                    menu_data.push(menu_item);
                });
                return menu_data;
            },
            // 将后台数据转成前台树型数据
            // o.id
            // o.text
            // o.data
            // o.parent_id
            // o.children_id
            // o.name
            // o.pid
            tree_data_create: function (o) {
                var menu_data = [];
                o.data.forEach(function (v, k) {
                    var menu_item = {
                        id: v[o.id],
                        text: v[o.text],
                    };
                    if (v[o.parent_id] == o.pid) {
                        var children = obj.tree().tree_data_create({
                            id: o.id,
                            text: o.text,
                            state: 'closed',
                            data: o.data,
                            parent_id: o.parent_id,
                            children_id: o.children_id,
                            name: o.name,
                            pid: v[o.children_id]
                        });
                        if (children.length > 0) {
                            menu_item['state'] = 'closed';
                            menu_item[o.name] = children;
                        }
                        menu_data.push(menu_item);
                    }
                });
                //console.log(menu_data);
                return menu_data;
            },
            // 左侧树结构
            // o.data 树数据 数组
            // o.call_chg 是否点击调用事件 bool
            // o.choose_first 载入数据之后是否自动选择第一项 bool
            // o.success_cb 载入数据之后，再回调执行的函数
            tree_init: function (o) {
                console.log('tree_init 左侧树结构 运行');
                var CA = obj.CA;
                var tree = obj.Tree_View;
                //console.log(o.data);

                tree.tree({
                    lines: true,
                    data: o.data,
                    onClick: function (node) {
                        console.log('node: ' + node.id);
                        if (o.call_chg == true) {
                            obj.Tree_View.chg_tree_id = node.id;
                            obj.event.refresh_data();
                        }
                    },
                    onDblClick: function (node) {
                        tree.tree('toggle', node.target);
                    },
                    onLoadSuccess: function (node, data) {
                        if (o.choose_first == true) {
                            //默认选择第一个节点
                            $(CA + 'Tree_View li:eq(0)').find('div').addClass("tree-node-selected");
                            var n = tree.tree('getSelected');
                            obj.Tree_View.chg_tree_id = n.id;
                            if (n != null) {
                                tree.tree('select', n.target);
                            }
                        }
                        o.success_cb && o.success_cb();
                    }
                });
            },
        };
    },

    /* --------------------------------- */
    /* 初始化控件 */
// 卡片窗口
//record_win_init: function (url, title, fit) {
//    console.log('record_win_init 记录卡片 初始化');
//    var record_win = this.record_win;
//
//    record_win.dialog({
//        href: url,
//        fit: fit || true,
//        inline: true,
//        title: title,
//        closed: true,
//        maximizable: true,
//        minimizable: true,
//        maximized: true,
//    });
//},


// -------------------------------------------------------

}
