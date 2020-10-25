/*
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-23 22:01:06
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-24 09:54:06
 */

new Vue({
    el: '#backups',
    data: function () {
        return {
            tablename:[],
            logmessage:'',
            filename:'',
            achieveStatus:0,
            percentage:0,
            countnum:0,
            dialogVisible:false,
            tableData: [],
              search: ''
        }
    },
    created: function () {
        let that = this;
        that.$http.post('backlist', {}).then((response) => {
            console.log(response.data,response.data.message)
            if (response.data.code == 200) {
                that.tableData = response.data.data
                console.log(that.tableData)
            }
        }, (response) => {
            //响应错误回调
            console.log(response)
        });
    },
    methods: {
        tablenameinit:function(){
            let that = this;
            let tablename = [];
            $("#list :checkbox").each(function () {
                if (this.checked) {
                    var table = $(this).val();
                    tablename.push(table);
                }
            });
            that.tablename = tablename;
            return tablename;
        },
        // 优化表
        optimize:function () {
            let that = this
            let tablename = that.tablenameinit();
            
            this.$alert('开始优化数据表', '操作说明', {
                confirmButtonText: '确定',
                callback: action => {
                    that.$http.post('optimize', {tables: tablename}).then((response) => {
                        console.log(response.data,response.data.message)
                        if (response.data.code == 200) {
                            this.$message({
                                message:response.data.message,
                                type: 'success'
                              });
                        }
                    }, (response) => {
                        //响应错误回调
                        console.log(response)
                    });
                }
              });
            
        },
        // 修复表
        repair:function() {
            let that = this
            let tablename = that.tablenameinit();

            this.$alert('开始修复数据表', '操作说明', {
                confirmButtonText: '确定',
                callback: action => {
                    that.$http.post('repair', {tables: tablename}).then((response) => {
                        console.log(response.data,response.data.message)
                        if (response.data.code == 200) {
                            this.$message({
                                message:response.data.message,
                                type: 'success'
                              });
                        }
                    }, (response) => {
                        //响应错误回调
                        console.log(response)
                    });
                }
              });
            
            
        },
        Export:function() {
            let that = this
            that.achieveStatus = 0;

            let tablename = that.tablenameinit();
            that.$http.post('export', {tables: tablename}).then((response) => {
                   if (response.data.code == 200) {
                        that.countnum = response.data.data.countnum
                        var id = response.data.data.tab.id;
                        var start = response.data.data.tab.start;
                        that.dialogVisible = true;
                        that.logmessage = '数据库备份中，请勿关闭窗口'
                        that.filename= response.data.data.filename;
                        that.startExport(id, start);
                    }else{
                        this.$message({
                            message: response.data.message,
                            type: 'error'
                          });
                    }
            }, (response) => {
                //响应错误回调
                console.log(response)
            });
            
        },
        // 开始备份
        startExport:function (id, start) {
            let that = this
            let tablename = that.tablenameinit();
            let  jindu=0;
            let  percentage=0;
            that.$http.post('export-start', {id: id, start: start}).then((response) => {
                var achieveStatus = response.data.data.achieveStatus;
                var tabName = response.data.data.tablename;
                $("#" + tabName).text(response.data.message);
                that.achieveStatus = achieveStatus;
                if (achieveStatus == 0) {
                    if(response.data.data.tab.id && response.data.data.tab.id>id){
                        console.log(response.data.data.tab.id)
                       jindu = ((response.data.data.tab.id+1)/that.countnum)*100
                       percentage = parseFloat(jindu).toFixed(0);
                        that.percentage = Number(percentage);
                    }
                    that.startExport(response.data.data.tab.id, response.data.data.tab.start);
                } else {
                    console.log('完成数据处理',response.data)
                    console.log(response.data.message)
                }
                
            }, (response) => {
                //响应错误回调
                console.log(response)
            });
            
      
        },
        dialogsubmit:function(){
            this.dialogVisible = false;
        },
        handleClose:function(){
            let that = this
            let achieveStatus = that.achieveStatus
            if(!achieveStatus){
                this.$message({
                    message: '请等待数据处理完成',
                    type: 'error'
                  });
            }
        },
        startRestore:function (part, start) {
            let that = this;
            that.$http.post('restore-start',  {part: part, start: start}).then((response) => {
                    if (response.data.code == 200) {
                        var achieveStatus = response.data.data.achieveStatus;
                        if (achieveStatus == 0) {
                            that.dialogVisible = true;
                            that.logmessage = '还原中,请不要关闭本页面,可能会造成服务器卡顿[' + response.data.data.start + ']......'
                            console.log(response.data)
                            // jindu = ((response.data.data.tab.id+1)/that.countnum)*100
                            // percentage = parseFloat(jindu).toFixed(0);
                            //  that.percentage = Number(percentage);
                             
                            that.startRestore(response.data.data.part, response.data.data.start);

                        } else {
                            this.$message({
                                message: response.data.message,
                                type: 'success'
                            });
                            that.dialogVisible = false;
                        }
                    }else{
                        this.$message({
                            message: response.data.message,
                            type: 'error'
                        });
                    }
            }, (response) => {
                //响应错误回调
                console.log(response)
            });
         
        },
        handleEdit(index, row) {
            console.log(index, row);
            let that = this;
            that.$http.post('restore-init', {time: row.time}).then((response) => {
                if (response.data.code == 200) {
                    var part = response.data.data.part;
                    var start = response.data.data.start;
                    that.startRestore(part, start);
                 }else{
                     this.$message({
                         message: response.data.message,
                         type: 'error'
                       });
                 }
         }, (response) => {
             //响应错误回调
             console.log(response)
         });
        },
        handleDelete(index, row) {
            console.log(index, row);
            console.log(index, row);
            let that = this;
            that.$http.post('delete', {time: row.time}).then((response) => {
                if (response.data.code == 200) {
                    that.tableData.some((item, i)=>{
        　　　　　　　　　　if(item.time==row.time){
                                that.tableData.splice(i, 1)
        　　　　　　　　　　　　//在数组的some方法中，如果return true，就会立即终止这个数组的后续循环
        　　　　　　　　　　　　return true
        　　　　　　　　　　}
        　　　　　　　　})　　
                    }else{
                        this.$message({
                            message: response.data.message,
                            type: 'error'
                        });
                    }
            }, (response) => {
                //响应错误回调
                console.log(response)
            });
        }
    }
})