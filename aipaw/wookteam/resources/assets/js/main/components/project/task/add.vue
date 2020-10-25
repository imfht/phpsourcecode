<template>
    <div class="task-input-box">
        <div v-if="!addText" class="input-placeholder">
            <Icon type="md-create" size="18"/>&nbsp;{{addFocus?`${$L('输入任务，回车即可保存')}`:placeholder}}
        </div>
        <div class="input-enter">
            <Input
                v-model="addText"
                ref="addInput"
                type="textarea"
                class="input-enter-textarea"
                :class="{bright:addFocus===true,highlight:!!addText}"
                element-id="project-panel-enter-textarea"
                @on-focus="onFocus(true)"
                @on-blur="onFocus(false)"
                :autosize="{ minRows: 1, maxRows: 6 }"
                :maxlength="255"
                @on-keydown="addKeydown"/>
            <div v-if="!!addText" class="input-enter-module">
                <Tooltip :content="$L('重要且紧急')" placement="bottom" transfer><div @click="addLevel=1" class="enter-module-icon p1"><Icon v-if="addLevel=='1'" type="md-checkmark" /></div></Tooltip>
                <Tooltip :content="$L('重要不紧急')" placement="bottom" transfer><div @click="addLevel=2" class="enter-module-icon p2"><Icon v-if="addLevel=='2'" type="md-checkmark" /></div></Tooltip>
                <Tooltip :content="$L('紧急不重要')" placement="bottom" transfer><div @click="addLevel=3" class="enter-module-icon p3"><Icon v-if="addLevel=='3'" type="md-checkmark" /></div></Tooltip>
                <Tooltip :content="$L('不重要不紧急')" placement="bottom" transfer><div @click="addLevel=4" class="enter-module-icon p4"><Icon v-if="addLevel=='4'" type="md-checkmark" /></div></Tooltip>
                <div class="enter-module-flex"></div>
                <Poptip placement="bottom" @on-popper-show="nameTipDisabled=true" @on-popper-hide="nameTipDisabled=false" transfer>
                    <Tooltip placement="bottom" :disabled="nameTipDisabled">
                        <div class="enter-module-icon user">
                            <UserImg :info="addUserInfo" class="avatar"/>
                        </div>
                        <div slot="content">
                            {{$L('负责人')}}: <UserView :username="addUserInfo.username"/>
                        </div>
                    </Tooltip>
                    <div slot="content">
                        <div style="width:240px">
                            {{$L('选择负责人')}}
                            <UserInput v-model="addUserInfo.username" :projectid="projectid" @change="changeUser" :placeholder="$L('留空默认: 自己')" style="margin:5px 0 3px"></UserInput>
                        </div>
                    </div>
                </Poptip>
                <div class="enter-module-btn">
                    <Button class="enter-module-btn-1" type="info" size="small" @click="clickAdd(false)">{{$L('添加任务')}}</Button>
                    <Dropdown class="enter-module-btn-drop" @on-click="dropAdd" placement="bottom-end" transfer>
                        <Button class="enter-module-btn-2" type="info" size="small"><Icon type="ios-arrow-down"></Icon></Button>
                        <DropdownMenu slot="list" class="enter-module-btn-drop-list">
                            <DropdownItem name="insertbottom">{{$L('添加至列表结尾')}}</DropdownItem>
                        </DropdownMenu>
                    </Dropdown>
                </div>
            </div>
        </div>
        <div v-if="loadIng > 0" class="load-box" @click.stop="">
            <div class="load-box-main"><w-loading></w-loading></div>
        </div>
    </div>
</template>

<style lang="scss">
    .enter-module-btn-drop-list {
        .ivu-dropdown-item {
            padding: 5px 16px;
            font-size: 12px !important;
        }
    }
</style>
<style lang="scss" scoped>
    .task-input-box {
        position: relative;
        margin-top: 5px;
        margin-bottom: 20px;
        min-height: 70px;
        .input-placeholder,
        .input-enter {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
        }
        .input-placeholder {
            z-index: 1;
            height: 40px;
            line-height: 40px;
            color: rgba(0, 0, 0, .36);
            padding-left: 12px;
            padding-right: 12px;
        }
        .input-enter {
            z-index: 2;
            position: relative;
            background-color: transparent;
            .input-enter-textarea {
                border-radius: 4px;
                padding-left: 12px;
                padding-right: 12px;
                color: rgba(0, 0, 0, 0.85);
                &.bright {
                    background-color: rgba(46, 73, 136, .08);
                }
                &.highlight {
                    background-color: #ffffff;
                }
            }
            .input-enter-module {
                display: flex;
                width: 100%;
                margin-top: 8px;
                align-items: center;
                .enter-module-icon {
                    display: inline-block;
                    width: 16px;
                    height: 16px;
                    margin-right: 5px;
                    border-radius: 4px;
                    vertical-align: middle;
                    cursor: pointer;
                    &.p1 {
                        background-color: #ff0000;
                    }
                    &.p2 {
                        background-color: #BB9F35;
                    }
                    &.p3 {
                        background-color: #449EDD;
                    }
                    &.p4 {
                        background-color: #84A83B;
                    }
                    &.user {
                        width: 24px;
                        height: 24px;
                        margin-left: 10px;
                        margin-right: 10px;
                        .avatar {
                            width: 24px;
                            height: 24px;
                            font-size: 14px;
                            line-height: 24px;
                            border-radius: 12px;
                        }
                        i {
                            line-height: 24px;
                            font-size: 16px;
                        }
                    }
                    i {
                        width: 100%;
                        height: 100%;
                        color: #ffffff;
                        line-height: 16px;
                        font-size: 14px;
                        transform: scale(0.85);
                        vertical-align: 0;
                    }
                }
                .enter-module-flex {
                    flex: 1;
                }
                .enter-module-btn {
                    button {
                        font-size: 12px;
                    }
                    .enter-module-btn-1 {
                        border-top-right-radius: 0;
                        border-bottom-right-radius: 0;
                    }
                    .enter-module-btn-2 {
                        padding: 0 2px;
                        border-top-left-radius: 0;
                        border-bottom-left-radius: 0;
                    }
                    .enter-module-btn-drop {
                        margin-left: -4px;
                        border-left: 1px solid #c0daff;
                    }
                }
            }
        }
        .load-box {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9;
            .load-box-main {
                width: 24px;
                height: 24px;
            }
        }
    }
</style>
<script>
    export default {
        name: 'ProjectAddTask',
        props: {
            placeholder: {
                type: String,
                default: ''
            },
            projectid: {
                type: Number,
                default: 0
            },
            labelid: {
                type: Number,
                default: 0
            },
        },
        data() {
            return {
                loadIng: 0,

                addText: '',
                addLevel: 2,
                addUserInfo: {},
                addFocus: false,

                nameTipDisabled: false,
            }
        },
        mounted() {
            this.addUserInfo = $A.cloneData(this.usrInfo);
        },
        methods: {
            changeUser(user) {
                if (typeof user.username === "undefined") {
                    this.addUserInfo = $A.cloneData(this.usrInfo);
                } else {
                    this.addUserInfo = user;
                }
            },
            dropAdd(name) {
                if (name == 'insertbottom') {
                    this.clickAdd(true);
                }
            },
            clickAdd(insertbottom = false) {
                let addText = this.addText.trim();
                if ($A.count(addText) == 0 || this.loadIng > 0) {
                    return;
                }
                this.loadIng++;
                $A.apiAjax({
                    url: 'project/task/add',
                    data: {
                        projectid: this.projectid,
                        labelid: this.labelid,
                        title: addText,
                        level: this.addLevel,
                        username: this.addUserInfo.username,
                        insertbottom: insertbottom ? 1 : 0,
                    },
                    complete: () => {
                        this.loadIng--;
                    },
                    error: () => {
                        alert(this.$L('网络繁忙，请稍后再试！'));
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.addText = '';
                            this.addFocus = false;
                            this.$Message.success(res.msg);
                            res.data.insertbottom = insertbottom;
                            this.$emit('on-add-success', res.data);
                            $A.triggerTaskInfoListener('create', res.data);
                            $A.triggerTaskInfoChange(res.data.id);
                        } else {
                            this.$Modal.error({title: this.$L('温馨提示'), content: res.msg});
                        }
                    }
                });
            },
            addKeydown(e) {
                if (e.keyCode == 13) {
                    if (e.shiftKey) {
                        return;
                    }
                    this.clickAdd(false);
                    e.preventDefault();
                }
            },
            setFocus() {
                this.$refs.addInput.focus();
            },
            onFocus(focus) {
                this.addFocus = focus;
                this.$emit('on-focus', focus);
            }
        }
    }
</script>
