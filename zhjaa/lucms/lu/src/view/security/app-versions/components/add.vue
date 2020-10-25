<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="选择 App:" prop="port">
        <Select v-model="formData.port" filterable placeholder="请选择 app">
            <Option v-for="(item,key) in port" :value="key" :key="item">{{ item}} </Option>
        </Select>
      </FormItem>
      <FormItem label="选择系统:" prop="system">
        <Select v-model="formData.system" filterable placeholder="请选择系统">
            <Option v-for="(item,key) in system" :value="key" :key="item">{{ item}} </Option>
        </Select>
      </FormItem>
      <FormItem label="版本号：" prop="version_sn">
        <Input v-model="formData.version_sn"></Input>
      </FormItem>
      <FormItem label="描述：" prop="version_intro">
        <Input type='textarea' v-model="formData.version_intro"></Input>
      </FormItem>
      <FormItem label="上传包：">
        <upload v-model="formData.package" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
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
} from '@/api/app-version'

import Upload from '_c/common/upload-file'

import InputHelper from '_c/common/input-helper'

export default {
  props: {
    port: {
      default: []
    },
    system: {
      default: []
    }
  },
  components: {
    Upload,
    InputHelper
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        port: 'A',
        system: 'ALL',
        version_sn: '',
        version_intro: '',
        package: {
          attachment_id: 0,
          url: ''
        },
      },
      imguploadConfig: {
        headers: {
          'Authorization': window.access_token
        },
        format: ['wgt','txt'],
        max_size: 50000,
        upload_url: window.uploadUrl.uploadNewVersion,
        file_name: 'file',
        multiple: false,
        file_num: 1,
        default_list: [{
          name: '',
          attachment_id: 0,
          url: ''
        }]
      },
      rules: {
        version_sn: [{
            required: true,
            message: '请填写版本号',
            trigger: 'blur'
          },
          {
            type: 'string',
            min: 2,
            message: '版本号至少要 2 个字符',
            trigger: 'blur'
          }
        ],
        version_intro: [{
          required: true,
          message: '请填写描述',
          trigger: 'blur'
        }],
      },
    }
  },
  methods: {
    addExcute() {
      let t = this;
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
        } else {
          t.saveLoading = false
        }
      })
    },
    cancel() {
      this.modalShow = false
      this.$emit('on-add-modal-hide')
    },
    editContentChange(html, text) {},
    uploadChange(fileList, formatFileList) {}
  }
}
</script>
