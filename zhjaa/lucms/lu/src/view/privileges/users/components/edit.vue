<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">修改</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="头像：">
        <upload v-if='formdataFinished' v-model="formData.head_image" :is-delete='false' :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
      </FormItem>
      <FormItem label="昵称：" prop="name">
        <Input v-model="formData.name"></Input>
      </FormItem>
      <FormItem label="邮箱：">
        <Input v-model="formData.email"></Input>
      </FormItem>
      <FormItem label="登录密码：">
        <Input type="password" v-model="formData.password"></Input>
      </FormItem>
      <FormItem label="登录密码确认：">
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
      <Button type="primary" @click="editExcute" :loading='saveLoading'>保存
                </Button>
    </div>
    <div class="demo-spin-container" v-if='spinLoading === true'>
      <Spin fix>
        <Icon type="load-c" size=18 class="spin-icon-load"></Icon>
        <div>加载中...</div>
      </Spin>
    </div>
  </Modal>

</div>
</template>
<script>
import Upload from '_c/common/upload'
import {
  edit,
  getInfoById
} from '@/api/user'

export default {
  components: {
    Upload
  },
  props: {
    modalId: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      spinLoading: true,
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
      formdataFinished: false,
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
          }
        ],
      },
    }
  },
  mounted() {
    if (this.modalId > 0) {
      this.getInfoByIdExcute()
    }
  },
  methods: {
    getInfoByIdExcute() {
      let t = this;
      getInfoById(t.modalId).then(res => {
        let res_data = res.data
        t.formData = {
          id: res_data.id,
          name: res_data.name,
          email: res_data.email,
          is_admin: res_data.is_admin,
          password: '',
          password_confirmation: '',
          head_image: {
            attachment_id: res_data.head_image.attachment_id,
            url: res_data.head_image.url
          },
        }
        t.imguploadConfig.default_list = [t.formData.head_image]
        t.formdataFinished = true
        t.spinLoading = false
      })

    },
    editExcute() {
      let t = this;
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          edit(t.modalId, t.formData).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$emit('on-edit-modal-success')
            this.$emit('on-edit-modal-hide')
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
      this.$emit('on-edit-modal-hide')
    },
    editContentChange(html, text) {
      // console.log(this.formData.content)
    },
    uploadChange(fileList, formatFileList) {}
  }
}
</script>
