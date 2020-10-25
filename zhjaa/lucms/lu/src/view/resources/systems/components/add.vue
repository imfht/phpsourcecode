
<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="配置标识：" prop="flag">
        <Input v-model="formData.flag" placeholder="请输入配置标识"></Input>
        <input-helper text="英文字母与下划线组成"></input-helper>
      </FormItem>
      <FormItem label="配置标题：" prop="title">
        <Input v-model="formData.title" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="分组：" prop="system_config_group">
        <Select v-model="formData.system_config_group" filterable placeholder="请选择配置分组">
            <Option v-for="(item,key) in configGroup" :value="key" :key="key">{{ item.title }} </Option>
        </Select>
      </FormItem>
      <FormItem label="配置值：">
        <Input type="textarea" v-model="formData.value" placeholder="请输入"></Input>
      </FormItem>
      <FormItem label="是否启用：">
        <RadioGroup v-model="formData.enable">
          <Radio label="F">禁用</Radio>
          <Radio label="T">启用</Radio>
        </RadioGroup>
      </FormItem>
      <FormItem label="描述：">
        <Input type="textarea" :rows="3" v-model="formData.description" placeholder="请输入"></Input>
      </FormItem>
    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="addExcute" :loading='saveLoading'>保存 </Button>
    </div>
  </Modal>
</div>
</template>
<script>
import {
  add
} from '@/api/systems'

import Upload from '_c/common/upload'
import InputHelper from '_c/common/input-helper'
export default {
  components: {
    Upload,
    InputHelper
  },
  props: {
    configType: {
      type: Object,
      value: []
    },
    configGroup: {
      type: Object,
      value: []
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        flag: '',
        title: '',
        system_config_group: '',
        value: '',
        description: '',
        enable: 'T'
      },
      rules: {
        flag: [{
          required: true,
          message: '请填写配置标识',
          trigger: 'blur'
        }],
        title: [{
          required: true,
          message: '请填写配置标题',
          trigger: 'blur'
        }],
        system_config_group: [{
          required: true,
          message: '请选择配置分组',
          trigger: 'blur'
        }]
      },
    }
  },
  methods: {
    addExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          add(t.formData).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$emit('on-add-modal-success')
            t.$emit('on-add-modal-hide')
            t.$Notice.success({
              title: res.message
            })
          }, function(error) {
            t.saveLoading = false;
          })
        }
      })
    },
    cancel() {
      this.modalShow = false
      this.$emit('on-add-modal-hide')
    },
    uploadChange(fileList, formatFileList) {}
  }
}
</script>
