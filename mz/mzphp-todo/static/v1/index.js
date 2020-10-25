(function (window, document) {
    /**
     * init system
     * @param data
     * @returns {*}
     */
    function systemInit(list, conf) {
        // 定义选项卡
        var tabList = [{
            index: 0,
            name: '待办',
            filter: 'uncompletedList'
        }, {
            index: 1,
            name: '已办',
            filter: 'completedList'
        }, {
            index: 2,
            name: '全部',
            filter: 'allList'
        }];
        return new Vue({
            // 绑定DOM
            el: '#todo-page',
            // 数据定义
            data: {
                // 底部选项卡的激活状态
                tabActive: 'index',
                // 选项卡当前位置
                tabIndex: 0,
                // 选项卡
                tabList: tabList,
                // 过滤方法
                filterFunc: '',
                // 新增内容绑定input
                newTodo: '',
                // 将要删除事项
                delTodo: null,
                // todoList 数据
                todoList: list || []
            },
            // 绑定方法
            methods: {
                // 添加事项
                add: function (label) {
                    if (!label) {
                        return;
                    }
                    // 压入数组第一个
                    this.todoList.unshift({
                        // 内容
                        todo: label,
                        // 是否完成(完成后记录时间)
                        completed: 0,
                        // 添加时间
                        dateline: Math.round(new Date().getTime() / 1000)
                    });
                    // 清空输入框
                    this.newTodo = '';
                },
                // 删除事项
                del: function (todo) {
                    // 删除事项
                    this.todoList.$remove(todo);
                    // 清空将要删除的事项
                    this.delTodo = null;
                },
                // 事项完成
                done: function (todo) {
                    // 完成后写入时间戳，重复点击时切换是否完成
                    todo.completed = todo.completed == 0 ? Math.round(new Date().getTime() / 1000) : 0;
                },
                // 选项卡切换事件
                tabChange: function (tab) {
                    var that = this;
                    // 激活第几个选项卡
                    this.tabIndex = tab.index;
                    // 列表过滤方法
                    this.filterFunc = function (todo) {
                        switch (that.tabIndex) {
                            case 0:
                                return todo.completed == 0 ? true : false;
                            case 1:
                                return todo.completed > 0 ? true : false;
                            case 2:
                                return true;
                        }
                    };
                },
                // 统计当前选项卡的数量
                todoCount: function (tab) {
                    var count = 0;
                    var todoList = this.todoList;
                    for (var i in todoList) {
                        switch (tab.index) {
                            case 0:
                                count += todoList[i].completed == 0 ? 1 : 0;
                                break;
                            case 1:
                                count += todoList[i].completed > 0 ? 1 : 0;
                                break;
                            case 2:
                                count += 1;
                                break;
                        }
                    }
                    // fix wont show bug
                    return count ? "" + count : 0;
                }
            },
            // 监控数据
            watch: {
                // 监控 todoList 变量
                todoList: function (val) {
                    window.API.post('edit', {data: JSON.stringify(this.todoList)}, function (data) {
                        if(!data || data.error>0){
                            console.log('SaveToDoError', data);
                        }else{
                            console.log('SaveToDoSuccess', data);
                        }
                    });
                }
            },
            // 实例创建完成
            created: function () {
                // 切换选项卡
                this.tabChange(tabList[0]);
            }
        });
    }

    // 从 api 接口拉取数据
    window.API.get('list', function (data) {
        // 初始化系统
        systemInit(data.extend.list, data.extend.conf);
    });

})(window, document);