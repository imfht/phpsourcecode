<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">修改</p>
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
        <upload v-if='formdataFinished' v-model="formData.package" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
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
import Upload from '_c/common/upload-file'
import {
  edit,
  getInfoById
} from '@/api/app-version'

export default {
  components: {
    Upload
  },
  props: {
    modalId: {
      type: Number,
      default: 0
    },
    port: {
      default: []
    },
    system: {
      default: []
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      spinLoading: true,
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
      formdataFinished: false,
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
          port: res_data.port,
          system: res_data.system,
          version_sn: res_data.version_sn,
          version_intro: res_data.version_intro,
          package: {
            attachment_id: res_data.package.attachment_id,
            url: res_data.package.url
          },
        }
        t.imguploadConfig.default_list = [t.formData.package]
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
