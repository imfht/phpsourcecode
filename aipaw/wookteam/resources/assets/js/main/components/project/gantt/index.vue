<template>
    <div class="project-gstc-gantt">
        <GanttView :lists="lists" :menuWidth="windowMax768 ? 180 : 260" :itemWidth="80" @on-change="updateTime" @on-click="clickItem"/>
        <Dropdown class="project-gstc-dropdown-filtr" :style="windowMax768?{left:'142px'}:{}" @on-click="tapProject">
            <Icon class="project-gstc-dropdown-icon" :class="{filtr:filtrProjectId>0}" type="md-funnel" />
            <DropdownMenu slot="list">
                <DropdownItem :name="0" :class="{'dropdown-active':filtrProjectId==0}">{{$L('全部')}}</DropdownItem>
                <DropdownItem v-for="(item, index) in projectLabel" :key="index" :name="item.id" :class="{'dropdown-active':filtrProjectId==item.id}">{{item.title}} ({{item.taskLists.length}})</DropdownItem>
            </DropdownMenu>
        </Dropdown>
        <div class="project-gstc-close" @click="$emit('on-close')"><Icon type="md-close" /></div>
        <div class="project-gstc-edit" :class="{info:editShowInfo, visible:editData.length > 0}">
            <div class="project-gstc-edit-info">
                <Table class="tableFill" size="small" max-height="600" :columns="editColumns" :data="editData"></Table>
                <div class="project-gstc-edit-btns">
                    <Button :loading="editLoad > 0" size="small" type="text" @click="editSubmit(false)">{{$L('取消')}}</Button>
                    <Button :loading="editLoad > 0" size="small" type="primary" @click="editSubmit(true)">{{$L('保存')}}</Button>
                    <Icon type="md-arrow-dropright" class="zoom" @click="editShowInfo=false"/>
                </div>
            </div>
            <div class="project-gstc-edit-small">
                <div class="project-gstc-edit-text" @click="editShowInfo=true">{{$L('未保存计划时间')}}: {{editData.length}}</div>
                <Button :loading="editLoad > 0" size="small" type="text" @click="editSubmit(false)">{{$L('取消')}}</Button>
                <Button :loading="editLoad > 0" size="small" type="primary" @click="editSubmit(true)">{{$L('保存')}}</Button>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    .project-gstc-gantt {
        position: absolute;
        top: 15px;
        left: 15px;
        right: 15px;
        bottom: 15px;
        z-index: 1;
        transform: translateZ(0);
        background-color: #fdfdfd;
        border-radius: 3px;
        overflow: hidden;
        .project-gstc-dropdown-filtr {
            position: absolute;
            top: 38px;
            left: 222px;
            .project-gstc-dropdown-icon {
                cursor: pointer;
                color: #999;
                font-size: 20px;
                &.filtr {
                    color: #058ce4;
                }
            }
        }
        .project-gstc-close {
            position: absolute;
            top: 8px;
            left: 12px;
            cursor: pointer;
            &:hover {
                i {
                    transform: scale(1) rotate(45deg);
                }
            }
            i {
                color: #666666;
                font-size: 28px;
                transform: scale(0.92);
                transition: all .2s;
            }
        }
        .project-gstc-edit {
            position: absolute;
            bottom: 6px;
            right: 6px;
            background: #ffffff;
            border-radius: 4px;
            opacity: 0;
            transform: translate(120%, 0);
            transition: all 0.2s;
            &.visible {
                opacity: 1;
                transform: translate(0, 0);
            }
            &.info {
                .project-gstc-edit-info {
                    display: block;
                }
                .project-gstc-edit-small {
                    display: none;
                }
            }
            .project-gstc-edit-info {
                display: none;
                border: 1px solid #e4e4e4;
                background: #ffffff;
                padding: 6px;
                border-radius: 4px;
                width: 500px;
                .project-gstc-edit-btns {
                    margin: 12px 6px 4px;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                    .ivu-btn {
                        margin-right: 8px;
                        font-size: 13px;
                    }
                    .zoom {
                        font-size: 20px;
                        color: #444444;
                        cursor: pointer;
                        &:hover {
                            color: #57a3f3;
                        }
                    }
                }
            }
            .project-gstc-edit-small {
                border: 1px solid #e4e4e4;
                background: #ffffff;
                padding: 6px 12px;
                display: flex;
                align-items: center;
                .project-gstc-edit-text {
                    cursor: pointer;
                    text-decoration: underline;
                    color: #444444;
                    margin-right: 8px;
                    &:hover {
                        color: #57a3f3;
                    }
                }
                .ivu-btn {
                    margin-left: 4px;
                    font-size: 13px;
                }
            }
        }
    }
</style>
<script>
    import GanttView from "../../gantt/index";

    /**
     * 甘特图
     */
    export default {
        name: 'ProjectGantt',
        components: {GanttView },
        props: {
            projectLabel: {
                default: []
            },
        },

        data () {
            return {
                loadFinish: false,

                lists: [],

                editColumns: [],
                editData: [],
                editShowInfo: false,
                editLoad: 0,

                filtrProjectId: 0,
            }
        },

        mounted() {
            this.editColumns = [
                {
                    title: this.$L('任务名称'),
                    key: 'label',
                    minWidth: 150,
                    ellipsis: true,
                }, {
                    title: this.$L('原计划时间'),
                    minWidth: 135,
                    align: 'center',
                    render: (h, params) => {
                        if (params.row.notime === true) {
                            return h('span', '-');
                        }
                        return h('div', {
                            style: {},
                        }, [
                            h('div', $A.formatDate('Y-m-d H:i', Math.round(params.row.backTime.start / 1000))),
                            h('div', $A.formatDate('Y-m-d H:i', Math.round(params.row.backTime.end / 1000)))
                        ]);
                    }
                }, {
                    title: this.$L('新计划时间'),
                    minWidth: 135,
                    align: 'center',
                    render: (h, params) => {
                        return h('div', {
                            style: {},
                        }, [
                            h('div', $A.formatDate('Y-m-d H:i', Math.round(params.row.newTime.start / 1000))),
                            h('div', $A.formatDate('Y-m-d H:i', Math.round(params.row.newTime.end / 1000)))
                        ]);
                    }
                }
            ];
            //
            this.initData();
            this.loadFinish = true;
        },

        watch:{
            projectLabel: {
                handler() {
                    this.initData();
                },
                deep: true,
            }
        },

        methods: {
            initData() {
                this.lists = [];
                this.projectLabel.forEach((item) => {
                    if (this.filtrProjectId > 0) {
                        if (item.id != this.filtrProjectId) {
                            return;
                        }
                    }
                    item.taskLists.forEach((taskData) => {
                        let notime = taskData.startdate == 0 || taskData.enddate == 0;
                        let times = this.getTimeObj(taskData);
                        let start = times.start;
                        let end = times.end;
                        //
                        let color = '#058ce4';
                        if (taskData.complete) {
                            color = '#c1c1c1';
                        } else {
                            if (taskData.level === 1) {
                                color = '#ff0000';
                            }else if (taskData.level === 2) {
                                color = '#BB9F35';
                            }else if (taskData.level === 3) {
                                color = '#449EDD';
                            }else if (taskData.level === 4) {
                                color = '#84A83B';
                            }
                        }
                        //
                        let tempTime = { start, end };
                        let findData = this.editData.find((t) => { return t.id == taskData.id });
                        if (findData) {
                            findData.backTime = $A.cloneData(tempTime)
                            tempTime = $A.cloneData(findData.newTime);
                        }
                        //
                        this.lists.push({
                            id: taskData.id,
                            label: taskData.title,
                            time: tempTime,
                            notime: notime,
                            style: { background: color },
                        });
                    });
                });
                //
                if (this.lists.length == 0 && this.filtrProjectId == 0) {
                    this.$Modal.warning({
                        title: this.$L("温馨提示"),
                        content: this.$L('任务列表为空，请先添加任务。'),
                        onOk: () => {
                            this.$emit('on-close');
                        },
                    });
                }
            },

            updateTime(item) {
                let original = this.getRawTime(item.id);
                if (Math.abs(original.end - item.time.end) > 1000 || Math.abs(original.start - item.time.start) > 1000) {
                    //修改时间（变化超过1秒钟)
                    let backTime = $A.cloneData(original);
                    let newTime = $A.cloneData(item.time);
                    let findData = this.editData.find(({id}) => id == item.id);
                    if (findData) {
                        findData.newTime = newTime;
                    } else {
                        this.editData.push({
                            id: item.id,
                            label: item.label,
                            notime: item.notime,
                            backTime,
                            newTime,
                        })
                    }
                }
            },

            clickItem(item) {
                this.taskDetail(item.id);
            },

            editSubmit(save) {
                let triggerTask = [];
                this.editData.forEach((item) => {
                    if (save) {
                        this.editLoad++;
                        let timeStart = $A.formatDate('Y-m-d H:i', Math.round(item.newTime.start / 1000));
                        let timeEnd = $A.formatDate('Y-m-d H:i', Math.round(item.newTime.end / 1000));
                        let ajaxData = {
                            act: 'plannedtime',
                            taskid: item.id,
                            content: timeStart + "," + timeEnd,
                        };
                        $A.apiAjax({
                            url: 'project/task/edit',
                            method: 'post',
                            data: ajaxData,
                            error: () => {
                                this.lists.some((task) => {
                                    if (task.id == item.id) {
                                        this.$set(task, 'time', item.backTime);
                                        return true;
                                    }
                                });
                            },
                            success: (res) => {
                                if (res.ret === 1) {
                                    triggerTask.push({
                                        status: 'await',
                                        act: ajaxData.act,
                                        taskid: ajaxData.taskid,
                                        data: res.data,
                                    })
                                } else {
                                    this.lists.some((task) => {
                                        if (task.id == item.id) {
                                            this.$set(task, 'time', item.backTime);
                                            return true;
                                        }
                                    });
                                }
                            },
                            afterComplete: () => {
                                this.editLoad--;
                                if (this.editLoad <= 0) {
                                    triggerTask.forEach((info) => {
                                        if (info.status == 'await') {
                                            info.status = 'trigger';
                                            $A.triggerTaskInfoListener(info.act, info.data);
                                            $A.triggerTaskInfoChange(info.taskid);
                                        }
                                    });
                                }
                            },
                        });
                    } else {
                        this.lists.some((task) => {
                            if (task.id == item.id) {
                                this.$set(task, 'time', item.backTime);
                                return true;
                            }
                        })
                    }
                });
                this.editData = [];
            },

            getRawTime(taskId) {
                let times = null;
                this.projectLabel.some((item) => {
                    item.taskLists.some((taskData) => {
                        if (taskData.id == taskId) {
                            times = this.getTimeObj(taskData);
                            return true;
                        }
                    });
                    if (times) {
                        return true;
                    }
                });
                return times;
            },

            getTimeObj(taskData) {
                let start = taskData.startdate || taskData.indate;
                let end = taskData.enddate || (taskData.indate + 86400);
                if (end == start) {
                    end = Math.round(new Date($A.formatDate('Y-m-d 23:59:59', end)).getTime()/1000);
                }
                end = Math.max(end, start + 60);
                start*= 1000;
                end*= 1000;
                return {start, end};
            },

            tapProject(e) {
                this.filtrProjectId = $A.runNum(e);
                this.initData();
            },
        }
    }
</script>
