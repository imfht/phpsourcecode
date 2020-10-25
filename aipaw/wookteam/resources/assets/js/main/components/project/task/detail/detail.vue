<template>
    <div v-if="detail.isassign!==true" class="project-task-detail-window" :class="{'task-detail-show': visible}">
        <div class="task-detail-bg" @click="handleBgClose"></div>
        <div class="task-detail-main"
             @drop.prevent="commentPasteDrag($event, 'drag')"
             @dragover.prevent="commentDragOver(true)"
             @dragleave.prevent="commentDragOver(false)">
            <div class="detail-left">
                <div class="detail-title-box detail-icon">
                    <Input v-model="detail.title"
                           :disabled="!!loadData.title"
                           type="textarea"
                           class="detail-title-input"
                           ref="titleInput"
                           :rows="1"
                           :autosize="{minRows:1,maxRows:5}"
                           maxlength="60"
                           @on-keydown="titleKeydown"
                           @on-blur="handleTask('title')"/>
                    <div v-if="detail.projectTitle && urlProjectid != detail.projectid" class="subtitle">
                        {{$L('所属项目：')}}
                        <span class="project-title" @click="openProject(detail.projectid)">{{detail.projectTitle}}</span>
                    </div>
                    <div class="subtitle">
                        <span class="z-nick"><UserView :username="detail.createuser"/></span>
                        {{$L('创建于：')}}
                        <span>{{$A.formatDate("Y-m-d H:i:s", detail.indate)}}</span>
                    </div>
                </div>
                <div class="detail-desc-box detail-icon">
                    <div class="detail-h2"><strong class="active">{{$L('描述')}}</strong></div>
                    <DescEditor :taskid="detail.id" :desc="detail.desc" :placeholder="$L('添加详细描述...')" @save-success="handleTask('desc')"/>
                </div>
                <ul class="detail-text-box">
                    <li v-if="detail.startdate > 0 && detail.enddate > 0" class="text-time detail-icon">
                        <span>{{$L('计划时间：')}}</span>
                        <em>
                            {{$A.formatDate("Y-m-d H:i", detail.startdate)}} {{$L('至')}} {{$A.formatDate("Y-m-d H:i", detail.enddate)}}
                            <em v-if="detail.overdue" class="overdue">[{{$L('已超期')}}]</em>
                        </em>
                    </li>
                    <li class="text-username detail-icon">
                        <span>{{$L('负责人：')}}</span>
                        <template v-if="typeof detail.username!=='undefined'">
                            <em v-if="detail.username"><UserView :username="detail.username" showimg/></em>
                            <em v-else>
                                <div class="uname-no">{{$L('暂无负责人')}}</div>
                                <Button :loading="!!loadData.claim" class="uname-button" type="primary" size="small" @click="handleTask('claimb')">{{$L('认领任务')}}</Button>
                            </em>
                            <em v-if="detail.type=='assign' && !detail.reassign">
                                <Button v-if="detail.username==usrName" class="uname-button" type="success" size="small" @click="handleTask('reassign')">{{$L('确认接收')}}</Button>
                                <div v-else class="uname-text">[{{$L('等待确认')}}]</div>
                            </em>
                        </template>
                    </li>
                    <li v-if="followerLength(detail.follower) > 0" class="text-follower detail-icon">
                        <span>{{$L('关注者：')}}</span>
                        <em>
                            <Tag v-for="(fname, findex) in detail.follower" :key="findex" closable @on-close="handleTask('unattention', {username:fname,uisynch:true})"><UserView :username="fname" showimg/></Tag>
                        </em>
                    </li>
                    <li class="text-level detail-icon">
                        <span>{{$L('优先级：')}}</span>
                        <em :class="`p${detail.level}`">{{levelFormt(detail.level)}}</em>
                    </li>
                    <li class="text-status detail-icon">
                        <span>{{$L('任务状态：')}}</span>
                        <em v-if="detail.complete" class="complete">{{$L('已完成')}}<span class="completedate">({{$A.formatDate("Y-m-d H:i", detail.completedate)}})</span></em>
                        <em v-else class="unfinished">{{$L('未完成')}}</em>
                    </li>
                </ul>
                <div class="detail-h2 detail-subtask-icon detail-icon">
                    <strong class="active">{{$L('子任务')}}</strong>
                    <div class="detail-button">
                        <Button class="detail-button-batch" size="small" @click="subtaskBatchAdd">{{$L('批量添加子任务')}}</Button>
                        <Button class="detail-button-btn" size="small" @click="handleTask('subtaskAdd')">{{$L('添加子任务')}}</Button>
                    </div>
                </div>
                <div class="detail-subtask-box">
                    <div v-if="detail.subtask.length == 0" class="detail-subtask-none">{{$L('暂无子任务')}}</div>
                    <div v-else>
                        <Progress class="detail-subtask-progress" :percent="subtaskProgress" :stroke-width="5" status="active" />
                        <draggable
                            v-model="detail.subtask"
                            draggable=".detail-subtask-item"
                            handle=".detail-subtask-rmenu"
                            :animation="150"
                            @sort="handleTask('subtaskBlur')">
                            <div v-for="(subitem, subindex) in detail.subtask" :key="subindex" :data-id="subitem.id" class="detail-subtask-item">
                                <Checkbox v-model="subitem.status"
                                          true-value="complete"
                                          false-value="unfinished"
                                          @on-change="handleTask('subtaskBlur')"></Checkbox>
                                <UserView v-if="subitem.uname"
                                          :username="subitem.uname"
                                          imgsize="20"
                                          imgfontsize="14"
                                          :showname="false"
                                          showimg/>
                                <Input v-model="subitem.detail"
                                       type="textarea"
                                       class="detail-subtask-input"
                                       :readonly="subitem.status=='complete'"
                                       :ref="`subtaskInput_${subindex}`"
                                       :class="{'subtask-complete':subitem.status=='complete'}"
                                       :rows="1"
                                       :autosize="{minRows:1,maxRows:5}"
                                       maxlength="255"
                                       :placeholder="$L('子任务描述...')"
                                       @on-keydown="subtaskKeydown(subindex, $event)"
                                       @on-blur="handleTask('subtaskBlur')"/>
                                <div class="detail-subtask-right" :style="subitem.stip==='show'?{opacity:1}:{}">
                                    <Icon type="md-menu" class="detail-subtask-ricon detail-subtask-rmenu"/>
                                    <Poptip
                                        class="detail-subtask-ricon"
                                        transfer
                                        @on-popper-show="$set(subitem, 'stip', 'show')"
                                        @on-popper-hide="[$set(subitem, 'stip', ''), handleTask('subtaskBlur')]">
                                        <Icon type="md-person" />
                                        <div slot="content">
                                            <div style="width:280px">
                                                {{$L('子任务负责人')}}
                                                <UserInput
                                                    v-model="subitem.uname"
                                                    :projectid="detail.projectid"
                                                    :transfer="false"
                                                    :placeholder="$L('输入关键词搜索')"
                                                    style="margin:5px 0 3px"></UserInput>
                                            </div>
                                        </div>
                                    </Poptip>
                                    <div v-if="subitem.detail==''" class="detail-subtask-ricon">
                                        <Icon type="md-trash" @click="handleTask('subtaskDelete', subindex)"/>
                                    </div>
                                    <Poptip v-else
                                        class="detail-subtask-ricon"
                                        transfer
                                        confirm
                                        :title="$L('你确定你要删除这个子任务吗?')"
                                        @on-ok="handleTask('subtaskDelete', subindex)"
                                        @on-popper-show="$set(subitem, 'stip', 'show')"
                                        @on-popper-hide="$set(subitem, 'stip', '')"><Icon type="md-trash" /></Poptip>
                                </div>
                            </div>
                        </draggable>
                    </div>
                </div>
                <div :style="`${detail.filenum>0?'':'display:none'}`">
                    <div class="detail-h2 detail-file-box detail-icon">
                        <strong class="active">{{$L('附件')}}</strong>
                        <div class="detail-button">
                            <Button class="detail-button-btn" size="small" @click="handleTask('fileupload')">{{$L('添加附件')}}</Button>
                        </div>
                    </div>
                    <project-task-files ref="projectUpload" :taskid="taskid" :projectid="detail.projectid" :simple="true" @change="handleTask('filechange', $event)"></project-task-files>
                </div>
                <div class="detail-h2 detail-comment-box detail-icon"><strong class="link" :class="{active:logType=='评论'}" @click="logType='评论'">{{$L('评论')}}</strong><em></em><strong class="link" :class="{active:logType=='日志'}" @click="logType='日志'">{{$L('操作记录')}}</strong></div>
                <div class="detail-log-box">
                    <project-task-logs ref="log" :logtype="logType" :projectid="detail.projectid" :taskid="taskid" :pagesize="5"></project-task-logs>
                </div>
                <div class="detail-footer-box">
                    <WInput class="comment-input" v-model="commentText" type="textarea" :rows="1" :autosize="{ minRows: 1, maxRows: 3 }" :maxlength="255" @on-keydown="commentKeydown" @on-input-paste="commentPasteDrag" :placeholder="$L('输入评论，Enter发表评论，Shift+Enter换行')" />
                    <Button :loading="!!loadData.comment" :disabled="!commentText" type="primary" @click="handleTask('comment')">评 论</Button>
                </div>
            </div>
            <div v-if="detail.username" class="detail-right" :class="{'open-menu':openMenu}">
                <Button v-if="detail.complete" :loading="!!loadData.unfinished" icon="md-checkmark-circle-outline" class="btn" @click="handleTask('unfinished')">{{$L('标记未完成')}}</Button>
                <Button v-else :loading="!!loadData.complete" icon="md-radio-button-off" class="btn" @click="handleTask('complete')">{{$L('标记已完成')}}</Button>
                <Dropdown trigger="click" class="block" @on-click="handleTask" @on-visible-change="handleSubwinToggle">
                    <Button :loading="!!loadData.level" icon="md-funnel" class="btn">{{$L('优先级')}}</Button>
                    <DropdownMenu slot="list">
                        <DropdownItem v-for="level in [1,2,3,4]" :key="level" :name="`level-${level}`" :class="`p${level}`">{{levelFormt(level)}}<Icon v-if="detail.level==level" type="md-checkmark" class="checkmark"/></DropdownItem>
                    </DropdownMenu>
                </Dropdown>
                <Poptip placement="bottom" class="block" @on-popper-show="[handleUsernameShow(),handleSubwinToggle(true)]" @on-popper-hide="handleSubwinToggle(false)" transfer>
                    <Button :loading="!!loadData.username" icon="md-person" class="btn">{{$L('负责人')}}</Button>
                    <div slot="content">
                        <div style="width:280px">
                            {{$L('选择负责人')}}
                            <UserInput v-model="detail.newusername" :projectid="detail.projectid" :nousername="detail.username" :transfer="false" @change="handleTask('usernameb', $event)" :placeholder="$L('输入关键词搜索')" style="margin:5px 0 3px"></UserInput>
                        </div>
                    </div>
                </Poptip>
                <Poptip ref="timeRef" placement="bottom" class="block" @on-popper-show="[handleTask('inittime'),handleSubwinToggle(true)]" @on-popper-hide="handleSubwinToggle(false)" transfer>
                    <Button :loading="!!loadData.plannedtime || !!loadData.unplannedtime" icon="md-calendar" class="btn">{{$L('计划时间')}}</Button>
                    <div slot="content">
                        <div style="width:280px">
                            {{$L('选择日期范围')}}
                            <Date-picker
                                v-model="timeValue"
                                :options="timeOptions"
                                :placeholder="$L('日期范围')"
                                format="yyyy-MM-dd HH:mm"
                                type="datetimerange"
                                placement="bottom"
                                @on-ok="handleTask('plannedtimeb')"
                                @on-clear="handleTask('unplannedtimeb')"
                                style="display:block;margin:5px 0 3px"></Date-picker>
                        </div>
                    </div>
                </Poptip>
                <Button icon="md-attach" class="btn" @click="handleTask('fileupload')">{{$L('添加附件')}}</Button>
                <Poptip ref="attentionRef" v-if="detail.username == usrName" placement="bottom" class="block" @on-popper-show="[handleAttentionShow(),handleSubwinToggle(true)]" @on-popper-hide="handleSubwinToggle(false)" transfer>
                    <Button :loading="!!loadData.attention" icon="md-at" class="btn">{{$L('关注人')}}</Button>
                    <div slot="content">
                        <div style="width:280px">
                            {{$L('选择关注人')}}
                            <UserInput :projectid="detail.projectid" :multiple="true" :transfer="false" v-model="detail.attentionLists" :placeholder="$L('输入关键词搜索')" style="margin:5px 0 3px" @on-confirm="handleTask('attention', true)"></UserInput>
                        </div>
                    </div>
                </Poptip>
                <Button v-else-if="haveAttention(detail.follower)" :loading="!!loadData.unattention" icon="md-at" class="btn" @click="handleTask('unattention', {username:usrName})">{{$L('取消关注')}}</Button>
                <Button v-else :loading="!!loadData.attention" icon="md-at" class="btn" @click="handleTask('attentiona')">{{$L('关注任务')}}</Button>
                <Button v-if="!detail.archived" :loading="!!loadData.archived" icon="md-filing" class="btn" @click="handleTask('archived')">{{$L('归档')}}</Button>
                <Button v-else :loading="!!loadData.unarchived" icon="md-filing" class="btn" @click="handleTask('unarchived')">{{$L('取消归档')}}</Button>
                <Button :loading="!!loadData.delete" icon="md-trash" class="btn" type="error" ghost @click="handleTask('deleteb')">{{$L('删除')}}</Button>
            </div>
            <div v-if="detail.complete" class="detail-complete"><Icon type="md-checkmark-circle-outline" /></div>
            <div class="detail-menu" @click="openMenu=!openMenu"><Icon type="md-menu" size="24"/></div>
            <div class="detail-cancel"><em @click="visible=false"></em></div>
            <div v-if="detailDragOver" class="detail-drag-over"><div class="detail-drag-text">{{$L('拖动到这里添加附件至 %', detail.title)}}</div></div>
        </div>
    </div>
</template>

<script>
    import ProjectTaskLogs from "../logs";
    import ProjectTaskFiles from "../files";
    import draggable from 'vuedraggable'
    import cloneDeep from "lodash/cloneDeep";
    import WInput from "../../../iview/WInput";
    import DescEditor from "./DescEditor";

    export default {
        components: {DescEditor, WInput, ProjectTaskFiles, ProjectTaskLogs, draggable},
        data() {
            return {
                taskid: 0,
                detail: {},
                detailDragOver: false,

                visible: false,

                subwinVisible: 0,

                urlProjectid: 0,

                bakData: {},
                loadData: {},
                loadRand: {},

                commentText: '',
                logType: '评论',

                timeValue: [],
                timeOptions: {},

                openMenu: false,
            }
        },
        beforeCreate() {
            let doms = document.querySelectorAll('.project-task-detail-window');
            for (let i = 0; i < doms.length; ++i) {
                if (doms[i].parentNode != null) doms[i].parentNode.removeChild(doms[i]);
            }
        },
        created() {
            let lastSecond = (e) => {
                return new Date($A.formatDate("Y-m-d 23:59:29", Math.round(e / 1000)))
            };
            this.timeOptions = {
                shortcuts: [{
                    text: this.$L('今天'),
                    value() {
                        return [new Date(), lastSecond(new Date().getTime())];
                    }
                }, {
                    text: this.$L('明天'),
                    value() {
                        let e = new Date();
                        e.setDate(e.getDate() + 1);
                        return [new Date(), lastSecond(e.getTime())];
                    }
                }, {
                    text: this.$L('本周'),
                    value() {
                        return [$A.getData('今天', true), lastSecond($A.getData('本周结束2', true))];
                    }
                }, {
                    text: this.$L('本月'),
                    value() {
                        return [$A.getData('今天', true), lastSecond($A.getData('本月结束', true))];
                    }
                }, {
                    text: this.$L('3天'),
                    value() {
                        let e = new Date();
                        e.setDate(e.getDate() + 3);
                        return [new Date(), lastSecond(e.getTime())];
                    }
                }, {
                    text: this.$L('5天'),
                    value() {
                        let e = new Date();
                        e.setDate(e.getDate() + 5);
                        return [new Date(), lastSecond(e.getTime())];
                    }
                }, {
                    text: this.$L('7天'),
                    value() {
                        let e = new Date();
                        e.setDate(e.getDate() + 7);
                        return [new Date(), lastSecond(e.getTime())];
                    }
                }]
            };
        },
        mounted() {
            let match = (window.location.pathname + "").match(/\/project\/panel\/(\d+)$/i);
            this.urlProjectid = match ? match[1] : 0;
            //
            this.$nextTick(() => {
                let dom = this.$el;
                if (parseInt(this.taskid) === 0) {
                    if (dom.parentNode != null) dom.parentNode.removeChild(dom);
                    return;
                }
                //
                dom.addEventListener('transitionend', () => {
                    if (dom !== null && dom.parentNode !== null && !this.visible) {
                        dom.parentNode.removeChild(dom);
                    }
                }, false);
                //
                setTimeout(() => {
                    this.visible = true;
                }, 0)
            });
            this.bakData = cloneDeep(this.detail);
            this.getTaskDetail();
            //
            $A.setOnTaskInfoListener('components/project/task/detail',(act, detail) => {
                if (detail.id != this.taskid) {
                    return;
                }
                if (detail.__modifyUsername == this.usrName) {
                    return;
                }
                this.getTaskDetail();
            }, true);
        },
        watch: {
            taskid() {
                this.bakData = cloneDeep(this.detail);
                this.getTaskDetail();
            }
        },
        computed: {
            subtaskProgress() {
                const countLists = this.detail.subtask;
                if (countLists.length === 0) {
                    return 0;
                }
                const completeLists = countLists.filter((item) => { return item.status == 'complete'});
                return parseFloat(((completeLists.length / countLists.length) * 100).toFixed(2));
            }
        },
        methods: {
            levelFormt(p) {
                switch (parseInt(p)) {
                    case 1:
                        return this.$L("重要且紧急") + " (P1)";
                    case 2:
                        return this.$L("重要不紧急") + " (P2)";
                    case 3:
                        return this.$L("紧急不重要") + " (P3)";
                    case 4:
                        return this.$L("不重要不紧急") + " (P4)";
                }
            },

            titleKeydown(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    e.target.blur();
                }
            },

            descKeydown(e) {
                if (e.keyCode == 13) {
                    if (e.shiftKey) {
                        return;
                    }
                    e.preventDefault();
                    e.target.blur();
                }
            },

            commentKeydown(e) {
                if (e.keyCode == 13) {
                    if (e.shiftKey) {
                        return;
                    }
                    e.preventDefault();
                    this.handleTask('comment');
                }
            },

            commentDragOver(show) {
                let random = (this.__detailDragOver = $A.randomString(8));
                if (!show) {
                    setTimeout(() => {
                        if (random === this.__detailDragOver) {
                            this.detailDragOver = show;
                        }
                    }, 150);
                } else {
                    this.detailDragOver = show;
                }
            },

            commentPasteDrag(e, type) {
                this.detailDragOver = false;
                const files = type === 'drag' ? e.dataTransfer.files : e.clipboardData.files;
                const postFiles = Array.prototype.slice.call(files);
                if (postFiles.length > 0) {
                    e.preventDefault();
                    postFiles.forEach((file) => {
                        this.$refs.projectUpload.upload(file);
                    });
                }
            },

            subtaskKeydown(subindex, e) {
                if (e.keyCode == 13) {
                    if (e.shiftKey) {
                        return;
                    }
                    e.preventDefault();
                    this.handleTask('subtaskEnter', subindex);
                }
            },

            followerLength(follower) {
                if (follower instanceof Array) {
                    return follower.length;
                } else {
                    return 0;
                }
            },

            followerToStr(follower) {
                if (follower instanceof Array) {
                    return follower.join(",");
                } else {
                    return '';
                }
            },

            haveAttention(follower) {
                if (follower instanceof Array) {
                    return follower.filter((uname) => { return uname == this.usrName }).length > 0
                } else {
                    return 0;
                }
            },

            getTaskDetail() {
                $A.apiAjax({
                    url: 'project/task/detail',
                    data: {
                        taskid: this.taskid,
                    },
                    error: () => {
                        alert(this.$L('网络繁忙，请稍后再试！'));
                        this.visible = false;
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.detail = res.data;
                            this.bakData = cloneDeep(this.detail);
                            this.$nextTick(() => {
                                this.$refs.titleInput.resizeTextarea();
                                this.detail.subtask.forEach((temp, index) => {
                                    this.$refs['subtaskInput_' + (index)][0].resizeTextarea();
                                })
                            });
                        } else {
                            this.$Modal.error({
                                title: this.$L('温馨提示'),
                                content: res.msg,
                                onOk: () => {
                                    this.visible = false;
                                }
                            });
                        }
                    }
                });
            },

            subtaskBatchAdd() {
                this.inputValue = "";
                this.$Modal.confirm({
                    width: 560,
                    render: (h) => {
                        return h('div', [
                            h('div', {
                                style: {
                                    fontSize: '16px',
                                    fontWeight: '500',
                                    marginBottom: '20px',
                                }
                            }, this.$L('批量添加子任务')),
                            h('Input', {
                                props: {
                                    type: 'textarea',
                                    rows: 4,
                                    autosize: {minRows: 4, maxRows: 30},
                                    value: this.inputValue,
                                    placeholder: this.$L('使用换行添加多个子任务')
                                },
                                on: {
                                    input: (val) => {
                                        this.inputValue = val;
                                    }
                                }
                            })
                        ])
                    },
                    loading: true,
                    onOk: () => {
                        if (this.inputValue) {
                            let tempArray = this.inputValue.split(/\n/);
                            tempArray.forEach((detail) => {
                                detail = detail.trim();
                                detail && this.detail.subtask.push({
                                    id: $A.randomString(6),
                                    uname: '',
                                    time: Math.round(new Date().getTime()/1000),
                                    status: 'unfinished',
                                    detail: detail,
                                    stip: ''
                                });
                            });
                            this.handleTask('subtask', () => {
                                this.$Modal.remove();
                            });
                        } else {
                            this.$Modal.remove();
                        }
                    },
                });
            },

            handleUsernameShow() {
                this.$set(this.detail, 'newusername', '')
            },

            handleAttentionShow() {
                this.$set(this.detail, 'attentionLists', this.followerToStr(this.detail.follower))
            },

            handleBgClose() {
                if (this.subwinVisible > 0) {
                    return;
                }
                this.visible = false;
            },

            handleSubwinToggle(visible) {
                if (visible) {
                    this.subwinVisible++;
                } else {
                    this.subwinVisible--;
                }
            },

            handleTask(act, eve) {
                let ajaxData = {
                    act: act,
                    taskid: this.taskid,
                };
                let ajaxCallback = () => {};
                //
                switch (act) {
                    case 'title':
                        if (this.detail[act] == this.bakData[act]) {
                            return;
                        }
                        if (act == 'title' && !this.detail[act]) {
                            this.$set(this.detail, act, this.bakData[act]);
                            return;
                        }
                        ajaxData.content = this.detail[act];
                        ajaxCallback = (res) => {
                            if (res !== 1) {
                                this.$set(this.detail, act, this.bakData[act]);
                            }
                        };
                        break;

                    case 'desc':
                        this.logType == '日志' && this.$refs.log.getLists(true, true);
                        return;

                    case 'subtaskAdd':
                        if (!$A.isArray(this.detail.subtask)) {
                            this.detail.subtask = [];
                        }
                        this.detail.subtask.push({
                            id: $A.randomString(6),
                            uname: '',
                            time: Math.round(new Date().getTime()/1000),
                            status: 'unfinished',
                            detail: '',
                            stip: ''
                        });
                        this.$nextTick(() => {
                            this.$refs['subtaskInput_' + (this.detail.subtask.length  - 1)][0].focus();
                        });
                        return;

                    case 'subtaskDelete':
                        this.detail.subtask.splice(eve, 1);
                        this.handleTask('subtaskBlur');
                        return;

                    case 'subtaskEnter':
                        if (!$A.isArray(this.detail.subtask)) {
                            this.detail.subtask = [];
                        }
                        if (eve + 1 >= this.detail.subtask.length) {
                            this.handleTask('subtaskAdd');
                            return;
                        }
                        this.$refs['subtaskInput_' + (eve + 1)][0].focus();
                        return;

                    case 'subtaskBlur':
                        this.handleTask('subtask');
                        return;

                    case 'subtask':
                        let tempArray = cloneDeep(this.detail[act]);
                        while (tempArray.length > 0 && tempArray[tempArray.length - 1].detail == '') {
                            tempArray.splice(tempArray.length - 1, 1);
                        }
                        tempArray.forEach((item) => {
                            if (typeof item.stip !== "undefined") {
                                delete item.stip;
                            }
                        });
                        if ($A.jsonStringify(tempArray) === $A.jsonStringify(this.bakData[act])) {
                            return;
                        }
                        ajaxData.content = tempArray;
                        ajaxCallback = (res) => {
                            if (res !== 1) {
                                this.$set(this.detail, act, cloneDeep(this.bakData[act]));
                            }
                            typeof eve === "function" && eve(res);
                        };
                        break;

                    case 'fileupload':
                        this.$refs.projectUpload.uploadHandleClick();
                        return;

                    case 'filechange':
                        let filenum = $A.runNum(this.detail.filenum);
                        switch (eve) {
                            case 'up':
                                this.$set(this.detail, 'filenum', filenum + 1);
                                break;
                            case 'error':
                            case 'delete':
                                this.$set(this.detail, 'filenum', filenum - 1);
                                break;
                        }
                        if (eve == 'add' || eve == 'delete') {
                            this.logType == '日志' && this.$refs.log.getLists(true, true);
                            $A.triggerTaskInfoChange(ajaxData.taskid);
                        }
                        return;

                    case 'claimb':
                        this.$Modal.confirm({
                            title: this.$L('认领任务'),
                            content: this.$L('你确定认领任务“%”吗？', this.detail.title),
                            onOk: () => {
                                this.handleTask('claim', eve);
                            }
                        });
                        return;

                    case 'claim':
                    case 'reassign':
                    case 'complete':
                    case 'unfinished':
                    case 'archived':
                    case 'unarchived':
                        break;

                    case 'archived2':
                        ajaxData.act = 'complete';
                        ajaxCallback = (res) => {
                            if (res === 1 && !this.detail.archived) {
                                this.handleTask('archived');
                                return false;
                            }
                        };
                        break;

                    case 'level-1':
                    case 'level-2':
                    case 'level-3':
                    case 'level-4':
                        ajaxData.act = 'level';
                        ajaxData.content = act.substring(6);
                        break;

                    case 'usernameb':
                        if (!eve.username) {
                            return;
                        }
                        this.$Modal.confirm({
                            title: this.$L('修改负责人'),
                            content: this.$L('你确定修改负责人设置为“%”吗？', (eve.nickname || eve.username)),
                            onOk: () => {
                                this.handleTask('username', eve);
                            }
                        });
                        return;

                    case 'username':
                        if (!eve.username) {
                            return;
                        }
                        ajaxData.content = eve.username;
                        break;

                    case 'inittime':
                        if (this.detail.startdate > 0 && this.detail.enddate > 0) {
                            this.timeValue = [$A.formatDate("Y-m-d H:i", this.detail.startdate), $A.formatDate("Y-m-d H:i", this.detail.enddate)]
                        } else {
                            this.timeValue = [];
                        }
                        return;

                    case 'plannedtimeb':
                        let temp = $A.date2string(this.timeValue, "Y-m-d H:i");
                        if (!temp[0] || !temp[1]) {
                            this.$Modal.error({title: this.$L('温馨提示'), content: this.$L('请选择一个有效时间！')});
                            return;
                        }
                        this.$Modal.confirm({
                            title: this.$L('修改计划时间'),
                            content: this.$L('你确定将任务计划时间设置为“%”吗？', temp[0] + "~" + temp[1]),
                            onOk: () => {
                                this.handleTask('plannedtime');
                            }
                        });
                        return;

                    case 'plannedtime':
                        this.timeValue = $A.date2string(this.timeValue, "Y-m-d H:i");
                        ajaxData.content = this.timeValue[0] + "," + this.timeValue[1];
                        this.$refs.timeRef.handleClose();
                        break;

                    case 'unplannedtimeb':
                        this.$Modal.confirm({
                            title: this.$L('取消计划时间'),
                            content: this.$L('你确定将任务计划时间取消吗？'),
                            onOk: () => {
                                this.handleTask('unplannedtime');
                            }
                        });
                        return;

                    case 'unplannedtime':
                        this.$refs.timeRef.handleClose();
                        break;

                    case 'attentiona':
                        ajaxData.act = "attention";
                        ajaxData.content = this.usrName;
                        break;

                    case 'attention':
                        if (!this.detail.attentionLists) {
                            return;
                        }
                        ajaxData.mode = eve ? 'clean' : '';
                        ajaxData.content = this.detail.attentionLists;
                        this.$refs.attentionRef.handleClose();
                        break;

                    case 'unattention':
                        ajaxData.content = eve.username;
                        if (eve.uisynch === true) {
                            let bakFollower = cloneDeep(this.detail.follower);
                            this.$set(this.detail, 'follower', this.detail.follower.filter((uname) => { return uname != eve }));
                            ajaxCallback = (res) => {
                                if (res !== 1) {
                                    this.$set(this.detail, 'follower', bakFollower);
                                }
                            };
                        }
                        break;

                    case 'deleteb':
                        this.$Modal.confirm({
                            title: this.$L('删除提示'),
                            content: this.$L('您确定要删除此任务吗？'),
                            onOk: () => {
                                this.handleTask('delete');
                            },
                        });
                        return;

                    case 'delete':
                        ajaxCallback = (res) => {
                            if (res === 1) {
                                this.$Modal.info({
                                    title: this.$L('温馨提示'),
                                    content: this.$L('任务已删除，点击确定关闭窗口。'),
                                    onOk: () => {
                                        this.visible = false;
                                    }
                                });
                                return false;
                            }
                        };
                        break;

                    case 'comment':
                        if (!this.commentText) {
                            return;
                        }
                        ajaxData.content = this.commentText;
                        ajaxCallback = (res) => {
                            if (res === 1) {
                                this.commentText = "";
                                this.logType == '评论' && this.$refs.log.getLists(true, true);
                            }
                        };
                        break;

                    default: {
                        return;
                    }
                }
                //
                let loadRand = $A.randomString(6);
                this.$set(this.loadRand, ajaxData.act, loadRand);
                this.$set(this.loadData, ajaxData.act, true);
                let runTime = Math.round(new Date().getTime());
                $A.apiAjax({
                    url: 'project/task/edit',
                    method: 'post',
                    data: ajaxData,
                    complete: () => {
                        if (this.loadRand[ajaxData.act] !== loadRand) {
                            return;
                        }
                        this.$set(this.loadData, ajaxData.act, false);
                    },
                    error: () => {
                        if (this.loadRand[ajaxData.act] !== loadRand) {
                            return;
                        }
                        ajaxCallback(-1);
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (this.loadRand[ajaxData.act] !== loadRand) {
                            return;
                        }
                        runTime = Math.round(new Date().getTime()) - runTime;
                        if (res.ret === 1) {
                            let tempArray = cloneDeep(this.detail.subtask);
                            this.detail = res.data;
                            this.bakData = cloneDeep(this.detail);
                            while (tempArray.length > 0 && tempArray[tempArray.length - 1].detail == '') {
                                tempArray.splice(tempArray.length - 1, 1);
                                this.detail.subtask.push({
                                    id: $A.randomString(6),
                                    uname: '',
                                    time: Math.round(new Date().getTime()/1000),
                                    status: 'unfinished',
                                    detail: '',
                                    stip: ''
                                });
                            }
                            $A.triggerTaskInfoListener(ajaxData.act, res.data);
                            $A.triggerTaskInfoChange(ajaxData.taskid);
                            setTimeout(() =>  {
                                if (ajaxCallback(1) !== false) {
                                    this.logType == '日志' && this.$refs.log.getLists(true, true);
                                    this.$Message.success(res.msg);
                                }
                            }, Math.max(0, 350 - runTime));
                        } else {
                            setTimeout(() =>  {
                                ajaxCallback(0);
                                this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                            }, Math.max(0, 350 - runTime));
                        }
                    }
                });
            },

            openProject(projectid) {
                try {
                    this.visible = false;
                    $A.app.$router.push({
                        name: 'project-panel',
                        params: {projectid: projectid, statistics: '', other: {}}
                    });
                } catch (e) {
                    this.visible = true;
                }
            }
        }
    }
</script>

<style lang="scss">
    .project-task-detail-window {
        .detail-title-box {
            .detail-title-input {
                textarea {
                    margin: -7px 0 3px -2px;
                    font-size: 20px;
                    font-weight: 600;
                    border: 2px solid #ffffff;
                    padding: 2px;
                    cursor: pointer;
                    color: #172b4d;
                    background: #ffffff;
                    width: 100%;
                    border-radius: 3px;
                    resize: none;
                }
            }
        }
        .detail-subtask-input {
            flex: 1;
            border: 0;
            background: #ffffff;
            margin-left: 2px;
            border-bottom: 1px solid #f6f6f6;
            textarea {
                border: 0;
                box-shadow: none;
                outline: none;
                resize: none;
                min-height: auto;
                padding-left: 0;
                padding-right: 0;
                &:focus {
                    color: #333333;
                }
            }
            &.subtask-complete {
                textarea {
                    text-decoration: line-through;
                    color: #999;
                }
            }
        }
    }
</style>
<style lang="scss" scoped>
    .project-task-detail-window {
        position: fixed;
        z-index: 1001;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        transition: all .3s;
        opacity: 0;
        pointer-events: unset;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        &.task-detail-show {
            opacity: 1;
        }

        .task-detail-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }

        .task-detail-main {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: row;
            width: 92%;
            max-width: 800px;
            max-height: 92%;
            background: #ffffff;
            overflow: visible;
            border-radius: 4px;
            padding: 10px 20px 2px;
            transform: translateZ(0);
            .detail-left {
                flex: 1;
                padding: 0 8px;
                overflow: auto;
                .detail-h2 {
                    color: #172b4d;
                    font-size: 16px;
                    display: flex;
                    align-items: center;
                    line-height: 26px;
                    strong {
                        font-size: 14px;
                        font-weight: normal;
                        &.link {
                            cursor: pointer;
                        }
                        &.active {
                            font-size: 16px;
                            font-weight: bold;
                        }
                    }
                    em {
                        margin: 0 9px;
                        width: 1px;
                        height: 10px;
                        background: #cccccc;
                    }
                    .detail-button {
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        position: absolute;
                        right: 12px;
                        top: 50%;
                        transform: translate(0, -50%);
                        &:hover {
                            .detail-button-batch {
                                display: inline-block;
                            }
                        }
                        .detail-button-btn,
                        .detail-button-batch {
                            font-size: 12px;
                            opacity: 0.9;
                            transition: all 0.3s;
                            margin-left: 5px;
                            &:hover {
                                opacity: 1;
                            }
                        }
                        .detail-button-batch {
                            display: none;
                        }
                    }
                }
                .detail-icon {
                    position: relative;
                    padding-left: 26px;
                    &:before {
                        font-family: zenicon;
                        font-size: 20px;
                        color: #42526e;
                        font-weight: 600;
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 26px;
                        height: 26px;
                        line-height: 26px;
                    }
                }
                .detail-title-box {
                    margin-top: 12px;
                    margin-bottom: 12px;
                    &:before {
                        content: "\E740";
                    }
                    .subtitle {
                        padding-top: 3px;
                        font-size: 12px;
                        color: #606266;
                        .project-title {
                            cursor: pointer;
                            &:hover {
                                color: #57a3f3;
                                text-decoration: underline;
                            }
                        }
                    }
                }
                .detail-desc-box {
                    &:before {
                        content: "\E75E";
                    }
                }
                .detail-text-box {
                    margin-bottom: 12px;
                    li {
                        color: #606266;
                        font-size: 14px;
                        line-height: 32px;
                        word-break: break-all;
                        display: flex;
                        &:before {
                            font-weight: normal;
                            color: #606266;
                            font-size: 14px;
                            padding-left: 4px;
                            line-height: 32px;
                        }
                        &.text-time {
                            &:before {
                                content: "\E706";
                            }
                        }
                        &.text-username {
                            &:before {
                                content: "\E903";
                            }
                            .uname-no {
                                display: inline-block;
                                color: #888888;
                            }
                            .uname-button {
                                font-size: 12px;
                                margin-left: 6px;
                            }
                            .uname-text {
                                line-height: 24px;
                                color: #666666;
                                font-size: 12px;
                                margin-left: 6px;
                            }
                        }
                        &.text-follower {
                            &:before {
                                content: "\E90D";
                            }
                            .ivu-tag {
                                padding: 0 6px;
                            }
                            .user-view-inline {
                                height: 20px;
                                line-height: 20px;
                                vertical-align: top;
                            }
                        }
                        &.text-level {
                            &:before {
                                content: "\E725";
                            }
                        }
                        &.text-status {
                            &:before {
                                content: "\E6AF";
                            }
                        }
                        > span {
                            white-space: nowrap;
                        }
                        > em {
                            margin-left: 4px;
                            padding-top: 5px;
                            line-height: 22px;
                            &.p1 {
                                color: #ed3f14;
                            }
                            &.p2 {
                                color: #ff9900;
                            }
                            &.p3 {
                                color: #19be6b;
                            }
                            &.p4 {
                                color: #666666;
                            }
                            &.complete {
                                color: #666666;
                                .completedate {
                                    font-size: 12px;
                                    padding-left: 4px;
                                    opacity: 0.6;
                                }
                            }
                            &.overdue,
                            > em.overdue{
                                color: #ff0000;
                            }
                            &.unfinished {
                                color: #19be6b;
                            }
                        }
                    }
                }
                .detail-file-box {
                    &:before {
                        content: "\E8B9";
                        font-size: 16px;
                        padding-left: 2px;
                    }
                }
                .detail-subtask-icon {
                    &:before {
                        content: "\E819";
                        font-size: 16px;
                        padding-left: 2px;
                    }
                }
                .detail-subtask-box {
                    padding: 12px;
                    margin-bottom: 4px;
                    .detail-subtask-progress {
                        margin: 2px 0 6px;
                    }
                    .detail-subtask-item {
                        display: flex;
                        flex-direction: row;
                        align-items: center;
                        margin: 0 2px 0 -6px;
                        padding-top: 4px;
                        padding-left: 8px;
                        position: relative;
                        background-color: #ffffff;
                        &:hover {
                            .detail-subtask-right {
                                opacity: 1;
                            }
                        }
                        .detail-subtask-right {
                            opacity: 0;
                            position: absolute;
                            top: 50%;
                            right: 0;
                            padding: 0 6px;
                            transform: translate(0, -50%);
                            background: #ffffff;
                            border-radius: 3px 0 0 3px;
                            transition: all 0.3s;
                            cursor: pointer;
                            box-shadow: -3px 0px 3px 0px rgba(45, 45, 45, 0.1);
                            .detail-subtask-ricon {
                                &:hover {
                                    opacity: 1;
                                }
                                display: inline-block;
                                opacity: 0.9;
                                width: 18px;
                                height: 26px;
                                line-height: 26px;
                                font-size: 16px;
                                text-align: center;
                            }
                        }
                    }
                    .detail-subtask-none {
                        color: #666666;
                        padding: 0 12px;
                    }
                }
                .detail-comment-box {
                    &:before {
                        content: "\E753";
                    }
                }
                .detail-footer-box {
                    border-top: 1px solid #e5e5e5;
                    display: flex;
                    flex-direction: row;
                    padding-top: 20px;
                    padding-bottom: 16px;
                    .comment-input {
                        margin-right: 12px;
                    }
                }
            }
            .detail-right {
                margin: 38px 0 6px;
                padding-left: 12px;
                overflow-x: hidden;
                overflow-y: auto;
                .block {
                    display: block;
                    .p1 {
                        color: #ed3f14;
                    }
                    .p2 {
                        color: #ff9900;
                    }
                    .p3 {
                        color: #19be6b;
                    }
                    .p4 {
                        color: #666666;
                    }
                    .checkmark {
                        margin-left: 8px;
                        margin-right: -8px;
                    }
                }
                .btn {
                    display: block;
                    width: 118px;
                    text-align: left;
                    margin-top: 8px;
                    padding-left: 10px;
                    padding-right: 10px;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }
            }
            .detail-complete {
                display: inline-block;
                pointer-events: none;
                position: absolute;
                top: 6px;
                right: 23%;
                font-size: 72px;
                color: #19be6b;
                opacity: 0.2;
                z-index: 1;
            }
            .detail-menu {
                display: none;
                position: absolute;
                top: 10px;
                right: 64px;
                text-align: right;
                width: auto;
                height: 38px;
                z-index: 5;
                align-items: center;
            }
            .detail-cancel {
                position: absolute;
                top: 10px;
                right: 20px;
                text-align: right;
                width: auto;
                height: 38px;
                z-index: 5;
                em {
                    display: inline-block;
                    width: 38px;
                    height: 38px;
                    cursor: pointer;
                    border-radius: 50%;
                    transform: scale(0.92);
                    &:after,
                    &:before {
                        position: absolute;
                        content: "";
                        top: 50%;
                        left: 50%;
                        width: 2px;
                        height: 20px;
                        background-color: #EE2321;
                        transform: translate(-50%, -50%) rotate(45deg) scale(0.6, 1);
                        transition: all .2s;
                    }
                    &:before {
                        position: absolute;
                        transform: translate(-50%, -50%) rotate(-45deg) scale(0.6, 1);
                    }
                    &:hover {
                        &:after,
                        &:before {
                            background-color: #ff0000;
                            transform: translate(-50%, -50%) rotate(135deg) scale(0.6, 1);
                        }
                        &:before {
                            background-color: #ff0000;
                            transform: translate(-50%, -50%) rotate(45deg) scale(0.6, 1);
                        }
                    }
                }
            }
            .detail-drag-over {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 6;
                background-color: rgba(255, 255, 255, 0.78);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 4px;
                &:before {
                    content: "";
                    position: absolute;
                    top: 16px;
                    left: 16px;
                    right: 16px;
                    bottom: 16px;
                    border: 2px dashed #7b7b7b;
                    border-radius: 12px;
                }
                .detail-drag-text {
                    padding: 12px;
                    font-size: 18px;
                    color: #666666;
                }
            }
        }
        @media (max-width: 768px) {
            .task-detail-main {
                padding: 10px 12px 2px;
                .detail-left {
                    margin-top: 32px;
                    .detail-icon {
                        padding-left: 22px;
                    }
                }
                .detail-right {
                    transform: translate(200%, 0);
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    margin: 0;
                    padding: 48px 18px;
                    background: #ffffff;
                    box-shadow: 0 1px 6px 0 rgba(32, 33, 36, 0.28);
                    z-index: 4;
                    transition: all 0.3s;
                    border-top-right-radius: 4px;
                    border-bottom-right-radius: 4px;
                    &.open-menu {
                        transform: translate(0, 0);
                    }
                }
                .detail-menu {
                    display: flex;
                }
            }
        }
    }
</style>
