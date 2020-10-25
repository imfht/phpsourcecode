
<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">发消息</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="标题：" prop="title">
        <Input v-model="formData.title" placeholder="请输消息标题"></Input>
      </FormItem>
      <FormItem label="消息内容：" prop="content">
        <Input type="textarea" v-model="formData.content" placeholder="请输入消息内容"></Input>
      </FormItem>
      <FormItem label="消息类型：">
        <Select v-model="formData.type" placeholder="消息类型">
          <Option v-for="(item,key) in messageType" :value="key" :key="key">{{ item }}</Option>
        </Select>
      </FormItem>
      <FormItem label="是否在首页提示：">
        <Select v-model="formData.is_alert_at_home" placeholder="是否在首页提示">
          <Option value="T" >是</Option>
          <Option value="F" >否</Option>
        </Select>
      </FormItem>
      <FormItem label="消息接收人：">
        <Select v-model="formData.users" multiple filterable remote :remote-method="getUserListExcute" :loading="searchLoading" placeholder="请输入手机号搜索">
            <Option v-for="(item, key) in userList" :value="item.id" :key="">{{item.name}}({{item.phone}})</Option>
        </Select>
        <input-helper text="不选将发送给所有人" :style-class="'input-helper-error'"></input-helper>
      </FormItem>
      <FormItem label="跳转 url：">
        <Input v-model="formData.url" placeholder="跳转 url"></Input>
      </FormItem>
    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="publishApiMessageExcute" :loading='saveLoading'>发送 </Button>
    </div>
  </Modal>
</div>
</template>
<script>
import {
  publishApiMessage,
  getUserList
} from '@/api/api-message'
import InputHelper from '_c/common/input-helper'
export default {
  components: {
    InputHelper
  },
  props: {
    messageType: {
      default: [],
    }
  },
  data() {
    return {
      modalShow: true,
      userList: [],
      searchLoading: false,
      saveLoading: false,
      formData: {
        title: '',
        type: '',
        url: '',
        users: '',
        content: '',
        is_alert_at_home:'F'
      },
      rules: {
        title: [{
          required: true,
          message: '请填写消息标题',
          trigger: 'blur'
        }],
        content: [{
          required: true,
          message: '请填写消息内容',
          trigger: 'blur'
        }],
        type: [{
          required: true,
          message: '请选择消息类型',
          trigger: 'blur'
        }],
      },
    }
  },
  methods: {
    getUserListExcute: function(input) {
      let t = this;
      if (input.length < 3) {
        return false
      }
      t.searchLoading = true
      getUserList(input).then(res => {
        this.userList = res.data
        t.searchLoading = false
      })
    },
    publishApiMessageExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          publishApiMessage(t.formData).then(res => {
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
