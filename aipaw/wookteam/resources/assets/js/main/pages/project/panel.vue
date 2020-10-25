<template>
    <div class="w-main project-panel">

        <v-title>{{$L('项目面板')}}-{{$L('轻量级的团队在线协作')}}</v-title>

        <div class="w-nav">
            <div class="nav-row">
                <div class="w-nav-left">
                    <div class="page-nav-left">
                        <span class="bold">{{projectDetail.title}}</span>
                        <div v-if="loadIng > 0" class="page-nav-loading"><w-loading></w-loading></div>
                        <div v-else class="page-nav-refresh"><em @click="getDetail(true)">{{$L('刷新')}}</em></div>
                    </div>
                </div>
                <div class="w-nav-flex"></div>
                <div class="w-nav-right">
                    <span class="ft hover" :class="{active:filtrTask!=''}">
                        <Dropdown @on-click="(res)=>{filtrTask=res}" transfer>
                            <Icon type="md-funnel" class="icon"/> {{$L('筛选')}}
                            <DropdownMenu slot="list">
                                <DropdownItem name="" :class="{'dropdown-active':filtrTask==''}">{{$L('全部任务')}}</DropdownItem>
                                <DropdownItem name="persons" :class="{'dropdown-active':filtrTask=='persons'}">{{$L('我负责的任务')}}</DropdownItem>
                                <DropdownItem name="follower" :class="{'dropdown-active':filtrTask=='follower'}">{{$L('我关注的任务')}}</DropdownItem>
                                <DropdownItem name="create" :class="{'dropdown-active':filtrTask=='create'}">{{$L('我创建的任务')}}</DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                    </span>
                    <span class="m768-show-i">
                        <Dropdown @on-click="openProjectDrawer" trigger="click" transfer>
                            <Icon type="md-menu" size="18"/>
                            <DropdownMenu slot="list">
                                <DropdownItem name="lists">{{$L('列表')}}</DropdownItem>
                                <DropdownItem name="projectGanttShow">{{$L('甘特图')}}</DropdownItem>
                                <DropdownItem name="files">{{$L('文件')}}</DropdownItem>
                                <DropdownItem  name="logs">{{$L('动态')}}</DropdownItem>
                                <DropdownItem name="openProjectSettingDrawer">{{$L('设置')}}</DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                    </span>
                    <span class="m768-hide-i">
                        <span class="ft hover" @click="openProjectDrawer('lists')"><i class="ft icon">&#xE89E;</i> {{$L('列表')}}</span>
                        <span class="ft hover" :class="{active:projectGanttShow}" @click="projectGanttShow=!projectGanttShow"><i class="ft icon">&#59141;</i> {{$L('甘特图')}}</span>
                        <span class="ft hover" @click="openProjectDrawer('files')"><i class="ft icon">&#xE701;</i> {{$L('文件')}}</span>
                        <span class="ft hover" @click="openProjectDrawer('logs')"><i class="ft icon">&#xE753;</i> {{$L('动态')}}</span>
                        <span class="ft hover" @click="openProjectSettingDrawer('setting')"><i class="ft icon">&#xE7A7;</i> {{$L('设置')}}</span>
                    </span>
                </div>
            </div>
        </div>

        <w-content>
            <draggable
                v-model="projectLabel"
                class="label-box"
                draggable=".label-draggable"
                :style="{visibility: projectGanttShow ? 'hidden' : 'visible'}"
                :animation="150"
                :disabled="projectSortDisabled || windowMax768"
                @sort="projectSortUpdate(true)">
                <div
                    v-if="projectLabel.length > 0"
                    v-for="label in projectLabel"
                    :key="label.id"
                    class="label-item label-draggable"
                    :class="{'label-scroll': label.hasScroll === true && label.endScroll !== true}"
                    @mouseenter="projectMouse(label)">
                    <div class="label-body">
                        <div class="title-box">
                            <div v-if="label.loadIng === true" class="title-loading">
                                <w-loading></w-loading>
                            </div>
                            <h2>{{label.title}}</h2>
                            <Dropdown trigger="click" @on-click="handleLabel($event, label)" transfer>
                                <Icon type="ios-more"/>
                                <DropdownMenu slot="list">
                                    <Dropdown-item name="refresh">{{$L('刷新列表')}}</Dropdown-item>
                                    <Dropdown-item name="rename">{{$L('重命名')}}</Dropdown-item>
                                    <Dropdown-item name="delete">{{$L('删除')}}</Dropdown-item>
                                </DropdownMenu>
                            </Dropdown>
                        </div>
                        <ScrollerY
                            :ref="'box_' + label.id"
                            class="task-box"
                            @on-scroll="projectBoxScroll($event, label)">
                            <draggable
                                v-model="label.taskLists"
                                class="task-main"
                                :class="[filtrTask ? 'filtr-' + filtrTask : '']"
                                group="task"
                                draggable=".task-draggable"
                                :animation="150"
                                :disabled="projectSortDisabled || windowMax768"
                                @sort="projectSortUpdate(false)"
                                @remove="projectSortUpdate(false)">
                                <div v-for="task in label.taskLists"
                                     :key="task.id"
                                     class="task-item task-draggable"
                                     :class="{'persons-item':isPersonsTask(task), 'follower-item': isFollowerTask(task), 'create-item': isCreateTask(task)}">
                                <div class="task-shadow" :class="[
                                        'p'+task.level,
                                        task.complete ? 'complete' : '',
                                        task.overdue ? 'overdue' : '',
                                        task.isNewtask === true ? 'newtask' : ''
                                    ]" @click="openTaskModal(task)">
                                        <div class="subtask-progress"><em :style="{width: subtaskProgress(task) + '%'}"></em></div>
                                        <div class="task-title">{{task.title}}<Icon v-if="task.desc" type="ios-list-box-outline" /></div>
                                        <div class="task-more">
                                            <div v-if="task.overdue" class="task-status">{{$L('已超期')}}</div>
                                            <div v-else-if="task.complete" class="task-status">{{$L('已完成')}}</div>
                                            <div v-else class="task-status">{{$L('未完成')}}</div>
                                            <div class="task-persons" :class="{'persons-more':task.persons.length > 1}">
                                                <Tooltip
                                                    v-for="(person, iper) in task.persons"
                                                    class="task-userimg"
                                                    :key="iper"
                                                    :content="person.nickname || person.username"
                                                    transfer>
                                                    <UserImg :info="person" class="avatar"/>
                                                </Tooltip>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div slot="footer">
                                    <project-add-task
                                        :ref="'add_' + label.id"
                                        :placeholder='`${$L("添加任务至")}"${label.title}"`'
                                        :projectid="label.projectid"
                                        :labelid="label.id"
                                        @on-add-success="addTaskSuccess($event, label)"></project-add-task>
                                </div>
                            </draggable>
                        </ScrollerY>
                    </div>
                    <div class="label-bottom" @click="projectFocus(label)">
                        <Icon class="label-bottom-icon" type="ios-add" />
                    </div>
                </div>
                <div v-if="loadDetailed" slot="footer" class="label-item label-create" @click="addLabel">
                    <div class="label-body">
                        <div class="trigger-box ft hover"><i class="ft icon">&#xE8C8;</i>{{$L('添加一个新列表')}}</div>
                    </div>
                </div>
            </draggable>
            <project-gantt v-if="projectGanttShow" @on-close="projectGanttShow=false" :projectLabel="projectLabel"></project-gantt>
        </w-content>

        <WDrawer v-model="projectDrawerShow" maxWidth="1080">
            <Tabs v-if="projectDrawerShow" v-model="projectDrawerTab">
                <TabPane :label="$L('任务列表')" name="lists">
                    <project-task-lists :canload="projectDrawerShow && projectDrawerTab == 'lists'" :projectid="projectid" :labelLists="projectSimpleLabel"></project-task-lists>
                </TabPane>
                <TabPane :label="$L('文件列表')" name="files">
                    <project-task-files :canload="projectDrawerShow && projectDrawerTab == 'files'" :projectid="projectid"></project-task-files>
                </TabPane>
                <TabPane :label="$L('项目动态')" name="logs">
                    <project-task-logs :canload="projectDrawerShow && projectDrawerTab == 'logs'" :projectid="projectid"></project-task-logs>
                </TabPane>
            </Tabs>
        </WDrawer>

        <WDrawer v-model="projectSettingDrawerShow" maxWidth="1000">
            <Tabs v-if="projectSettingDrawerShow" v-model="projectSettingDrawerTab">
                <TabPane :label="$L('项目设置')" name="setting">
                    <project-setting :canload="projectSettingDrawerShow && projectSettingDrawerTab == 'setting'" :projectid="projectid" @on-change="getDetail"></project-setting>
                </TabPane>
                <TabPane :label="$L('已归档任务')" name="archived">
                    <project-archived :canload="projectSettingDrawerShow && projectSettingDrawerTab == 'archived'" :projectid="projectid"></project-archived>
                </TabPane>
                <TabPane :label="$L('项目统计')" name="statistics">
                    <project-statistics ref="statistics" :canload="projectSettingDrawerShow && projectSettingDrawerTab == 'statistics'" :projectid="projectid"></project-statistics>
                </TabPane>
                <TabPane :label="$L('成员管理')" name="member">
                    <project-users :canload="projectSettingDrawerShow && projectSettingDrawerTab == 'member'" :projectid="projectid"></project-users>
                </TabPane>
            </Tabs>
        </WDrawer>
    </div>
</template>

<style lang="scss">
    #project-panel-enter-textarea {
        background: transparent;
        background: none;
        outline: none;
        border: 0;
        resize: none;
        padding: 0;
        margin: 8px 0;
        line-height: 22px;
        border-radius: 0;
        color: rgba(0, 0, 0, 0.85);
        &:focus {
            border-color: transparent;
            box-shadow: none;
        }
    }
</style>
<style lang="scss" scoped>
    .project-panel {
        .label-box {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: flex-start;
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            height: 100%;
            padding: 15px;
            transform: translateZ(0);
            .label-item {
                flex-grow: 0;
                flex-shrink: 0;
                flex-basis: auto;
                position: relative;
                overflow: hidden;
                height: 100%;
                padding-right: 15px;
                &.label-create {
                    cursor: pointer;
                    &:hover {
                        .trigger-box {
                            transform: translate(0, -50%) scale(1.1);
                        }
                    }
                }
                &.label-scroll {
                    &:hover {
                        .label-bottom {
                            transform: translate(-50%, 0);
                        }
                    }
                }
                .label-body {
                    width: 300px;
                    height: 100%;
                    border-radius: 0.15rem;
                    background-color: #ebecf0;
                    overflow: hidden;
                    position: relative;
                    display: flex;
                    flex-direction: column;
                    .title-box {
                        padding: 0 12px;
                        font-weight: bold;
                        color: #666666;
                        position: relative;
                        cursor: move;
                        display: flex;
                        align-items: center;
                        width: 100%;
                        height: 42px;
                        .title-loading {
                            width: 16px;
                            height: 16px;
                            margin-right: 6px;
                        }
                        h2 {
                            flex: 1;
                            font-size: 16px;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                        }
                        i {
                            font-weight: 500;
                            font-size: 18px;
                            height: 100%;
                            line-height: 42px;
                            width: 42px;
                            text-align: right;
                            cursor: pointer;
                        }
                    }
                    .task-box {
                        position: relative;
                        flex: 1;
                        width: 100%;
                        padding: 0 12px 2px;
                        transform: translateZ(0);
                        .task-main {
                            display: flex;
                            flex-direction: column;
                            &.filtr-persons {
                                .task-item {
                                    display: none;
                                    &.persons-item {
                                        display: block;
                                    }
                                }
                            }
                            &.filtr-follower {
                                .task-item {
                                    display: none;
                                    &.follower-item {
                                        display: block;
                                    }
                                }
                            }
                            &.filtr-create {
                                .task-item {
                                    display: none;
                                    &.create-item {
                                        display: block;
                                    }
                                }
                            }
                        }
                        .task-item {
                            width: 100%;
                            &.task-draggable {
                                .task-shadow {
                                    cursor: pointer;
                                    &:hover{
                                        box-shadow: 0 0 4px 0 rgba(0, 0, 0, 0.38);
                                    }
                                }
                            }
                            .task-shadow {
                                margin: 5px 0 4px;
                                padding: 8px 10px 8px 8px;
                                background-color: #ffffff;
                                border-left: 2px solid #BF9F03;
                                border-right: 0;
                                color: #091e42;
                                border-radius: 3px;
                                box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
                                transition: all 0.3s;
                                transform: scale(1);
                                &.p1 {
                                    border-left-color: #ff0000;
                                }
                                &.p2 {
                                    border-left-color: #BB9F35;
                                }
                                &.p3 {
                                    border-left-color: #449EDD;
                                }
                                &.p4 {
                                    border-left-color: #84A83B;
                                }
                                &.complete {
                                    border-left-color: #c1c1c1;
                                    .task-title {
                                        color: #666666;
                                        text-decoration: line-through;
                                    }
                                    .task-more {
                                        .task-status {
                                            color: #666666;
                                        }
                                    }
                                }
                                &.overdue {
                                    .task-title {
                                        font-weight: bold;
                                    }
                                    .task-more {
                                        .task-status {
                                            color: #ff0000;
                                        }
                                    }
                                }
                                &.newtask {
                                    transform: scale(1.5);
                                }
                                .task-title {
                                    font-size: 12px;
                                    color: #091e42;
                                    word-break: break-all;
                                    .ivu-icon {
                                        font-size: 14px;
                                        color: #afafaf;
                                        vertical-align: top;
                                        padding: 2px 4px;
                                        transform: scale(0.94);
                                    }
                                }
                                .task-more {
                                    min-height: 30px;
                                    display: flex;
                                    align-items: flex-end;
                                    .task-status {
                                        color: #19be6b;
                                        font-size: 12px;
                                        flex: 1;
                                    }
                                    .task-persons {
                                        max-width: 150px;
                                        &.persons-more {
                                            text-align: right;
                                            .task-userimg {
                                                width: 20px;
                                                height: 20px;
                                                margin-left: 4px;
                                                margin-top: 4px;
                                                .avatar {
                                                    width: 20px;
                                                    height: 20px;
                                                    font-size: 12px;
                                                    line-height: 20px;
                                                }
                                            }
                                        }
                                        .task-userimg {
                                            width: 26px;
                                            height: 26px;
                                            vertical-align: bottom;
                                            .avatar {
                                                width: 26px;
                                                height: 26px;
                                                font-size: 14px;
                                                line-height: 26px;
                                                border-radius: 13px;
                                            }
                                        }
                                    }
                                }
                                .subtask-progress {
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    z-index: -1;
                                    border-radius: 0 3px 3px 0;
                                    overflow: hidden;
                                    pointer-events: none;
                                    em {
                                        display: block;
                                        height: 100%;
                                        background-color: rgba(3, 150, 242, 0.07);
                                    }
                                }
                            }
                        }
                    }
                    .trigger-box {
                        text-align: center;
                        font-size: 16px;
                        color: #666;
                        width: 100%;
                        position: absolute;
                        top: 50%;
                        transform: translate(0, -50%) scale(1);
                        transition: all 0.3s;
                    }
                }
                .label-bottom {
                    position: absolute;
                    left: 50%;
                    bottom: 14px;
                    z-index: 1;
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    background-color: #2db7f5;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: transform 0.2s;
                    transform: translate(-50%, 200%);
                    cursor: pointer;
                    .label-bottom-icon {
                        color: #ffffff;
                        font-size: 36px;
                    }
                }
            }
        }
    }
</style>
<script>
    import draggable from 'vuedraggable'

    import WContent from "../../components/WContent";
    import ProjectAddTask from "../../components/project/task/add";
    import ProjectTaskLists from "../../components/project/task/lists";
    import ProjectTaskFiles from "../../components/project/task/files";
    import ProjectTaskLogs from "../../components/project/task/logs";
    import ProjectArchived from "../../components/project/archived";
    import ProjectUsers from "../../components/project/users";
    import ProjectStatistics from "../../components/project/statistics";
    import WDrawer from "../../components/iview/WDrawer";
    import ProjectGantt from "../../components/project/gantt/index";
    import ProjectSetting from "../../components/project/setting";
    import ScrollerY from "../../../_components/ScrollerY";
    import orderBy from "lodash/orderBy";

    export default {
        components: {
            ScrollerY,
            ProjectSetting,
            ProjectGantt,
            WDrawer,
            ProjectStatistics,
            ProjectUsers,
            ProjectArchived,
            ProjectTaskLogs,
            ProjectTaskFiles, ProjectTaskLists, ProjectAddTask, draggable, WContent},
        data () {
            return {
                loadIng: 0,
                loadDetailed: false,

                projectid: 0,
                projectDetail: {},
                projectLabel: [],
                projectSimpleLabel: [],
                projectSortData: '',
                projectSortDisabled: false,

                projectDrawerShow: false,
                projectDrawerTab: 'lists',

                projectSettingDrawerShow: false,
                projectSettingDrawerTab: 'setting',

                projectGanttShow: false,

                filtrTask: '',
                routeName: '',
            }
        },
        mounted() {
            this.routeName = this.$route.name;
            $A.setOnTaskInfoListener('pages/project-panel',(act, detail) => {
                if (detail.projectid != this.projectid) {
                    return;
                }
                //
                switch (act) {
                    case 'addlabel':        // 添加分类
                        let tempLists = this.projectLabel.filter((res) => { return res.id == detail.labelid });
                        if (tempLists.length == 0) {
                            this.projectLabel.push(Object.assign(detail, {id: detail.labelid}));
                            this.projectSortData = this.getProjectSort();
                        }
                        return;

                    case 'deletelabel':     // 删除分类
                        this.projectLabel.some((label, index) => {
                            if (label.id == detail.labelid) {
                                this.projectLabel.splice(index, 1);
                                this.projectSortData = this.getProjectSort();
                                return true;
                            }
                        });
                        return;

                    case 'deleteproject':   // 删除项目
                        return;

                    case "labelsort":       // 调整分类排序
                    case "tasksort":        // 调整任务排序
                        if (detail.__modifyUsername != this.usrName) {
                            if (this.routeName == this.$route.name) {
                                this.$Modal.confirm({
                                    title: this.$L("更新提示"),
                                    content: this.$L('团队成员（%）调整了%，<br/>更新时间：%。<br/><br/>点击【确定】加载最新数据。', detail.nickname, this.$L(act == 'labelsort' ? '分类排序' : '任务排序'), $A.formatDate("Y-m-d H:i:s", detail.time)),
                                    onOk: () => {
                                        this.getDetail(true);
                                    }
                                });
                            } else {
                                this.getDetail(true);
                            }
                        }
                        return;
                }
                //
                this.projectLabel.forEach((label) => {
                    label.taskLists.some((task, i) => {
                        if (task.id == detail.id) {
                            label.taskLists.splice(i, 1, detail);
                            return true;
                        }
                    });
                });
                //
                switch (act) {
                    case "delete":          // 删除任务
                    case "archived":        // 归档
                        this.projectLabel.forEach((label) => {
                            label.taskLists.some((task, i) => {
                                if (task.id == detail.id) {
                                    label.taskLists.splice(i, 1,);
                                    return true;
                                }
                            });
                        });
                        this.projectSortData = this.getProjectSort();
                        break;

                    case "create":          // 创建任务
                        this.projectLabel.some((label) => {
                            if (label.id == detail.labelid) {
                                let tempLists = label.taskLists.filter((res) => { return res.id == detail.id });
                                if (tempLists.length == 0) {
                                    detail.isNewtask = true;
                                    if (detail.insertbottom) {
                                        label.taskLists.push(detail);
                                    } else {
                                        label.taskLists.unshift(detail);
                                    }
                                    this.$nextTick(() => {
                                        this.$set(detail, 'isNewtask', false);
                                    });
                                }
                                return true;
                            }
                        });
                        break;

                    case "unarchived":      // 取消归档
                        this.projectLabel.forEach((label) => {
                            if (label.id == detail.labelid) {
                                let index = label.taskLists.length;
                                label.taskLists.some((task, i) => {
                                    if (detail.inorder > task.inorder || (detail.inorder == task.inorder && detail.id > task.id)) {
                                        index = i;
                                        return true;
                                    }
                                });
                                label.taskLists.splice(index, 0, detail);
                            }
                        });
                        this.projectSortData = this.getProjectSort();
                        break;


                    case "complete":        // 标记完成
                    case "unfinished":      // 标记未完成
                        this.taskNewSort();
                        break;
                }
            }, true);
        },
        activated() {
            this.projectid = this.$route.params.projectid;
            if (typeof this.$route.params.other === "object") {
                this.$set(this.projectDetail, 'title', $A.getObject(this.$route.params.other, 'title'));
            }
            if (this.$route.params.statistics === '已完成') {
                this.projectSettingDrawerTab = 'statistics';
                this.projectSettingDrawerShow = true;
                this.$nextTick(() => {
                    this.$refs.statistics.setTaskType('已完成');
                });
            }
        },
        deactivated() {
            if ($A.getToken() === false) {
                this.projectid = 0;
            }
            this.projectGanttShow = false;
            this.projectDrawerShow = false;
            this.projectSettingDrawerShow = false;
        },
        watch: {
            projectid(val) {
                if ($A.runNum(val) <= 0) {
                    return;
                }
                this.projectDetail = {};
                this.projectLabel = [];
                this.projectSimpleLabel = [];
                this.getDetail();
            },
            '$route' (To) {
                if (To.name == 'project-panel') {
                    this.projectid = To.params.projectid;
                }
            }
        },
        methods: {
            getDetail(successTip) {
                this.loadIng++;
                $A.apiAjax({
                    url: 'project/detail',
                    data: {
                        projectid: this.projectid,
                    },
                    complete: () => {
                        this.loadIng--;
                        this.loadDetailed = true;
                    },
                    error: () => {
                        this.goBack({name:'project'});
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.projectLabel = res.data.label;
                            this.taskNewSort();
                            this.projectDetail = res.data.project;
                            this.projectSimpleLabel = res.data.simpleLabel;
                            this.projectSortData = this.getProjectSort();
                            if (successTip === true) {
                                this.$Message.success(this.$L('刷新成功！'));
                            }
                        } else {
                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                        }
                    }
                });
            },
            getProjectSort() {
                let sortData = "",
                    taskData = "";
                this.projectLabel.forEach((label) => {
                    taskData = "";
                    label.taskLists.forEach((task) => {
                        if (taskData) taskData+= "-";
                        taskData+= task.id;
                    });
                    if (sortData) sortData+= ";";
                    sortData+= label.id + ":" + taskData;
                });
                return sortData;
            },
            handleLabel(event, labelDetail) {
                switch (event) {
                    case 'refresh': {
                        this.refreshLabel(labelDetail);
                        break;
                    }
                    case 'rename': {
                        this.renameLabel(labelDetail);
                        break;
                    }
                    case 'delete': {
                        this.deleteLabel(labelDetail);
                        break;
                    }
                }
            },

            refreshLabel(item) {
                this.$set(item, 'loadIng', true);
                $A.apiAjax({
                    url: 'project/task/lists',
                    data: {
                        projectid: this.projectid,
                        labelid: item.id,
                    },
                    complete: () => {
                        this.$set(item, 'loadIng', false);
                    },
                    error: () => {
                        window.location.reload();
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.$set(item, 'taskLists', res.data.lists);
                        } else {
                            window.location.reload();
                        }
                    }
                });
            },

            renameLabel(item) {
                this.renameValue = "";
                this.$Modal.confirm({
                    render: (h) => {
                        return h('div', [
                            h('div', {
                                style: {
                                    fontSize: '16px',
                                    fontWeight: '500',
                                    marginBottom: '20px',
                                }
                            }, this.$L('重命名列表')),
                            h('Input', {
                                props: {
                                    value: this.renameValue,
                                    autofocus: true,
                                    placeholder: this.$L('请输入新的列表名称')
                                },
                                on: {
                                    input: (val) => {
                                        this.renameValue = val;
                                    }
                                }
                            })
                        ])
                    },
                    loading: true,
                    onOk: () => {
                        if (this.renameValue) {
                            this.$set(item, 'loadIng', true);
                            let title = this.renameValue;
                            $A.apiAjax({
                                url: 'project/label/rename',
                                data: {
                                    projectid: this.projectid,
                                    labelid: item.id,
                                    title: title,
                                },
                                complete: () => {
                                    this.$set(item, 'loadIng', false);
                                },
                                error: () => {
                                    this.$Modal.remove();
                                    alert(this.$L('网络繁忙，请稍后再试！'));
                                },
                                success: (res) => {
                                    this.$Modal.remove();
                                    this.$set(item, 'title', title);
                                    setTimeout(() => {
                                        if (res.ret === 1) {
                                            this.$Message.success(res.msg);
                                        } else {
                                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                                        }
                                    }, 350);
                                }
                            });
                        } else {
                            this.$Modal.remove();
                        }
                    },
                });
            },

            deleteLabel(item) {
                let redTip = item.taskLists.length > 0 ? ('<div style="color:red;font-weight:500">' + this.$L('注：将同时删除列表下所有任务') + '</div>') : '';
                this.$Modal.confirm({
                    title: this.$L('删除列表'),
                    content: '<div>' + this.$L('你确定要删除此列表吗？') + '</div>' + redTip,
                    loading: true,
                    onOk: () => {
                        $A.apiAjax({
                            url: 'project/label/delete',
                            data: {
                                projectid: this.projectid,
                                labelid: item.id,
                            },
                            error: () => {
                                this.$Modal.remove();
                                alert(this.$L('网络繁忙，请稍后再试！'));
                            },
                            success: (res) => {
                                this.$Modal.remove();
                                this.projectLabel.some((label, index) => {
                                    if (label.id == item.id) {
                                        this.projectLabel.splice(index, 1);
                                        this.projectSortData = this.getProjectSort();
                                        return true;
                                    }
                                });
                                setTimeout(() => {
                                    if (res.ret === 1) {
                                        this.$Message.success(res.msg);
                                        $A.triggerTaskInfoListener('deletelabel', {labelid: item.id, projectid: item.projectid});
                                    } else {
                                        this.$Modal.error({title: this.$L('温馨提示'), content: res.msg });
                                    }
                                }, 350);
                            }
                        });
                    }
                });
            },

            addLabel() {
                this.labelValue = "";
                this.$Modal.confirm({
                    render: (h) => {
                        return h('div', [
                            h('div', {
                                style: {
                                    fontSize: '16px',
                                    fontWeight: '500',
                                    marginBottom: '20px',
                                }
                            }, this.$L('添加列表')),
                            h('Input', {
                                props: {
                                    value: this.labelValue,
                                    autofocus: true,
                                    placeholder: this.$L('请输入列表名称')
                                },
                                on: {
                                    input: (val) => {
                                        this.labelValue = val;
                                    }
                                }
                            })
                        ])
                    },
                    loading: true,
                    onOk: () => {
                        if (this.labelValue) {
                            let data = {
                                projectid: this.projectid,
                                title: this.labelValue
                            };
                            $A.apiAjax({
                                url: 'project/label/add',
                                data: data,
                                error: () => {
                                    this.$Modal.remove();
                                    alert(this.$L('网络繁忙，请稍后再试！'));
                                },
                                success: (res) => {
                                    this.$Modal.remove();
                                    this.projectLabel.push(res.data);
                                    this.projectSortData = this.getProjectSort();
                                    $A.triggerTaskInfoListener('addlabel', Object.assign(data, {labelid: res.data.id}));
                                    setTimeout(() => {
                                        if (res.ret === 1) {
                                            this.$Message.success(res.msg);
                                        } else {
                                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                                        }
                                    }, 350);
                                }
                            });
                        } else {
                            this.$Modal.remove();
                        }
                    },
                });
            },

            addTaskSuccess(taskDetail, label) {
                if (label.taskLists instanceof Array) {
                    taskDetail.isNewtask = true;
                    if (taskDetail.insertbottom) {
                        label.taskLists.push(taskDetail);
                    } else {
                        label.taskLists.unshift(taskDetail);
                    }
                    this.$nextTick(() => {
                        this.$set(taskDetail, 'isNewtask', false);
                    });
                } else {
                    this.refreshLabel(label);
                }
            },

            openProjectDrawer(tab) {
                if (tab == 'projectGanttShow') {
                    this.projectGanttShow = !this.projectGanttShow;
                    return;
                } else if (tab == 'openProjectSettingDrawer') {
                    this.openProjectSettingDrawer('setting')
                    return;
                }
                this.projectDrawerTab = tab;
                this.projectDrawerShow = true;
            },

            openProjectSettingDrawer(tab) {
                this.projectSettingDrawerTab = tab;
                this.projectSettingDrawerShow = true;
            },

            projectSortUpdate(isLabel) {
                let oldSort = this.projectSortData;
                let newSort = this.getProjectSort();
                if (oldSort == newSort) {
                    return;
                }
                this.projectSortData = newSort;
                this.projectSortDisabled = true;
                this.loadIng++;
                $A.apiAjax({
                    url: 'project/sort',
                    data: {
                        projectid: this.projectid,
                        oldsort: oldSort,
                        newsort: newSort,
                        label: isLabel === true ? 1 : 0
                    },
                    complete: () => {
                        this.projectSortDisabled = false;
                        this.loadIng--;
                    },
                    error: () => {
                        this.getDetail();
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.projectLabel.forEach((label) => {
                                let length = label.taskLists.length;
                                label.taskLists.forEach((task, index) => {
                                    task.inorder = length - index;
                                });
                            });
                            this.taskNewSort();
                            //
                            this.$Message.success(res.msg);
                            $A.triggerTaskInfoListener(isLabel ? 'labelsort' : 'tasksort', { projectid: this.projectid, nickname: $A.getNickName(), time: Math.round(new Date().getTime()/1000) });
                        } else {
                            this.getDetail();
                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                        }
                    }
                });
            },

            projectMouse(label) {
                let hasScroll = false;
                let el = this.$refs['box_' + label.id]
                if (el && el.length > 0) {
                    el = el[0].$el;
                    hasScroll = el.scrollHeight > el.offsetHeight;
                }
                this.$set(label, 'hasScroll', hasScroll)
            },

            projectBoxScroll(e, label) {
                this.$set(label, 'endScroll', e.scrollE < 50)
            },

            projectFocus(label) {
                let el = this.$refs['add_' + label.id];
                if (el && el.length > 0) {
                    el[0].setFocus();
                }
                el = this.$refs['box_' + label.id];
                if (el && el.length > 0) {
                    el[0].scrollToBottom(false);
                }
            },

            subtaskProgress(task) {
                const {subtask, complete} = task;
                if (subtask.length === 0) {
                    return complete ? 100 : 0;
                }
                const completeLists = subtask.filter((item) => { return item.status == 'complete'});
                return parseFloat(((completeLists.length / subtask.length) * 100).toFixed(2));
            },

            openTaskModal(taskDetail) {
                this.taskDetail(taskDetail);
            },

            taskNewSort() {
                this.$nextTick(() => {
                    this.projectLabel.forEach((item) => { item.taskLists = this.taskReturnNewSort(item.taskLists) });
                })
            },

            taskReturnNewSort(lists) {
                let tmpLists = orderBy(lists, ['complete', 'inorder'], ['asc', 'desc']);
                let array = [];
                array.unshift(...tmpLists.filter(({complete}) => complete ));
                array.unshift(...tmpLists.filter(({complete}) => !complete ));
                return array;
            },

            isPersonsTask(task) {
                return task.persons && !!task.persons.find(({username}) => username == this.usrInfo.username);
            },

            isFollowerTask(task) {
                return task.follower && task.follower.indexOf(this.usrInfo.username) !== -1;
            },

            isCreateTask(task) {
                return task.createuser == this.usrInfo.username;
            }
        },
    }
</script>
