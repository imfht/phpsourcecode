<template>
    <drawer-tabs-container>
        <div class="project-setting">
            <Form ref="formSystem" :model="formSystem" :label-width="110" @submit.native.prevent>
                <div class="project-setting-title">{{$L('项目信息')}}:</div>
                <FormItem :label="$L('项目简介')">
                    <Input v-model="formSystem.project_desc" type="textarea" :autosize="{minRows:3,maxRows:20}" style="max-width:450px"/>
                </FormItem>

                <div class="project-setting-title">{{$L('项目权限')}}:</div>
                <FormItem prop="project_role_export">
                    <div slot="label">
                        <Tooltip :content="$L('任务列表导出Excel')" transfer>{{$L('导出列表')}}</Tooltip>
                    </div>
                    <Checkbox :value="true" disabled>{{$L('项目负责人')}}</Checkbox>
                    <CheckboxGroup v-model="formSystem.project_role_export" class="project-setting-group">
                        <Checkbox label="member">{{$L('项目成员')}}</Checkbox>
                    </CheckboxGroup>
                </FormItem>

                <div class="project-setting-title">{{$L('任务权限')}}:</div>
                <FormItem :label="$L('添加任务')">
                    <Checkbox :value="true" disabled>{{$L('项目负责人')}}</Checkbox>
                    <CheckboxGroup v-model="formSystem.add_role" class="project-setting-group">
                        <Checkbox label="member">{{$L('项目成员')}}</Checkbox>
                    </CheckboxGroup>
                </FormItem>
                <FormItem :label="$L('修改任务')">
                    <Checkbox :value="true" disabled>{{$L('项目负责人')}}</Checkbox>
                    <CheckboxGroup v-model="formSystem.edit_role" class="project-setting-group">
                        <Checkbox label="owner">{{$L('任务负责人')}}</Checkbox>
                        <Checkbox label="member">{{$L('项目成员')}}</Checkbox>
                    </CheckboxGroup>
                </FormItem>
                <FormItem :label="$L('标记完成')">
                    <Checkbox :value="true" disabled>{{$L('项目负责人')}}</Checkbox>
                    <CheckboxGroup v-model="formSystem.complete_role" class="project-setting-group">
                        <Checkbox label="owner">{{$L('任务负责人')}}</Checkbox>
                        <Checkbox label="member">{{$L('项目成员')}}</Checkbox>
                    </CheckboxGroup>
                </FormItem>
                <FormItem :label="$L('归档任务')">
                    <Checkbox :value="true" disabled>{{$L('项目负责人')}}</Checkbox>
                    <CheckboxGroup v-model="formSystem.archived_role" class="project-setting-group">
                        <Checkbox label="owner">{{$L('任务负责人')}}</Checkbox>
                        <Checkbox label="member">{{$L('项目成员')}}</Checkbox>
                    </CheckboxGroup>
                </FormItem>
                <FormItem :label="$L('删除任务')">
                    <Checkbox :value="true" disabled>{{$L('项目负责人')}}</Checkbox>
                    <CheckboxGroup v-model="formSystem.del_role" class="project-setting-group">
                        <Checkbox label="owner">{{$L('任务负责人')}}</Checkbox>
                        <Checkbox label="member">{{$L('项目成员')}}</Checkbox>
                    </CheckboxGroup>
                </FormItem>

                <div class="project-setting-title">{{$L('面板显示')}}:</div>
                <FormItem :label="$L('显示已完成')">
                    <div>
                        <RadioGroup v-model="formSystem.complete_show">
                            <Radio label="show">{{$L('显示')}}</Radio>
                            <Radio label="hide">{{$L('隐藏')}}</Radio>
                        </RadioGroup>
                    </div>
                    <div v-if="formSystem.complete_show=='show'" class="form-placeholder">
                        {{$L('项目面板显示已完成的任务。')}}
                    </div>
                    <div v-else class="form-placeholder">
                        {{$L('项目面板隐藏已完成的任务。')}}
                    </div>
                </FormItem>
                <FormItem>
                    <Button :loading="loadIng > 0" type="primary" @click="handleSubmit('formSystem')">{{$L('提交')}}</Button>
                    <Button :loading="loadIng > 0" @click="handleReset('formSystem')" style="margin-left: 8px">{{$L('重置')}}</Button>
                </FormItem>
            </Form>
        </div>
    </drawer-tabs-container>
</template>

<style lang="scss" scoped>
    .project-setting {
        padding: 0 12px;
        .project-setting-title {
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
        }
        .project-setting-group {
            display: inline-block;
        }
        .form-placeholder {
            font-size: 12px;
            color: #999999;
        }
        .form-placeholder:hover {
            color: #000000;
        }
    }
</style>
<script>
    import DrawerTabsContainer from "../DrawerTabsContainer";
    export default {
        name: 'ProjectSetting',
        components: {DrawerTabsContainer},
        props: {
            projectid: {
                default: 0
            },
            canload: {
                type: Boolean,
                default: true
            },
        },
        data () {
            return {
                loadYet: false,

                loadIng: 0,

                formSystem: {},
            }
        },
        mounted() {
            if (this.canload) {
                this.loadYet = true;
                this.getSetting();
            }
        },

        watch: {
            projectid() {
                if (this.loadYet) {
                    this.getSetting();
                }
            },
            canload(val) {
                if (val && !this.loadYet) {
                    this.loadYet = true;
                    this.getSetting();
                }
            }
        },

        methods: {
            getSetting(save) {
                this.loadIng++;
                $A.apiAjax({
                    url: 'project/setting?act=' + (save ? 'save' : 'get'),
                    data: Object.assign(this.formSystem, {
                        projectid: this.projectid
                    }),
                    complete: () => {
                        this.loadIng--;
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            this.formSystem = res.data;
                            this.formSystem__reset = $A.cloneData(this.formSystem);
                            if (save) {
                                this.$Message.success(this.$L('修改成功'));
                                this.$emit('on-change', res.data);
                            }
                        } else {
                            if (save) {
                                this.$Modal.error({title: this.$L('温馨提示'), content: res.msg });
                            }
                        }
                    }
                });
            },
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        switch (name) {
                            case "formSystem": {
                                this.getSetting(true);
                                break;
                            }
                        }
                    }
                })
            },
            handleReset(name) {
                if (typeof this[name + '__reset'] !== "undefined") {
                    this[name] = $A.cloneData(this[name + '__reset']);
                    return;
                }
                this.$refs[name].resetFields();
            },
        }
    }
</script>
