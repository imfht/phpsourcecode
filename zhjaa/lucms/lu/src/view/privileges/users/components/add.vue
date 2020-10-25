<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">添加</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="头像：">
        <upload v-model="formData.head_image" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
      </FormItem>
      <FormItem label="昵称：" prop="name">
        <Input v-model="formData.name"></Input>
      </FormItem>
      <FormItem label="邮箱：">
        <Input v-model="formData.email"></Input>
      </FormItem>
      <FormItem label="登录密码：" prop="password">
        <Input type="password" v-model="formData.password"></Input>
      </FormItem>
      <FormItem label="登录密码确认：" prop="password_confirmation">
        <Input type="password" v-model="formData.password_confirmation"></Input>
      </FormItem>
      <FormItem label="可登录后台：">
        <RadioGroup v-model="formData.is_admin">
          <Radio label="F">否</Radio>
          <Radio label="T">是</Radio>
        </RadioGroup>
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
} from '@/api/user'

import Upload from '_c/common/upload'

export default {
  components: {
    Upload
  },
  data() {
    const validatePassword = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入登录密码'))
      } else {
        if (this.formData.password !== '') {
          // 对第二个密码框单独验证
          this.$refs.formData.validateField('password_confirmation')
        }
        callback()
      }
    }
    const validatePasswordConfirm = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入确认密码'))
      } else if (value !== this.formData.password) {
        callback(new Error('两次密码不一致 '))
      } else {
        callback()
      }
    }
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        name: '',
        email: '',
        is_admin: 'F',
        password: '',
        password_confirmation: '',
        head_image: {
          attachment_id: 0,
          url: ''
        },
      },
      imguploadConfig: {
        headers: {
          'Authorization': window.access_token
        },
        format: ['jpg', 'jpeg', 'png', 'gif'],
        max_size: 500,
        upload_url: window.uploadUrl.uploadAvatar,
        file_name: 'file',
        multiple: false,
        file_num: 1,
        default_list: []
      },
      rules: {
        name: [{
            required: true,
            message: '请填写昵称',
            trigger: 'blur'
          },
          {
            type: 'string',
            min: 2,
            message: '昵称至少要 2 个字符',
            trigger: 'blur'
          }
        ],
        email: [{
            required: true,
            message: '请填写邮箱',
            trigger: 'blur'
          },
          {
            type: 'email',
            message: '邮箱格式不正确',
            trigger: 'blur'
          },
        ],
        password: [{
          validator: validatePassword,
          trigger: 'blur'
        }],
        password_confirmation: [{
          validator: validatePasswordConfirm,
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
    editContentChange(html, text) {
      // console.log(this.formData.content)
    },
    uploadChange(fileList, formatFileList) {}
  }
}
</script>
