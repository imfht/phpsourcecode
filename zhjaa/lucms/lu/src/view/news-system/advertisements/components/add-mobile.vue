<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="1200">
    <p slot="header">添加</p>
    <Row>
      <Col span="24">
      <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
        <FormItem label="广告位：">
          <Select v-model="formData.advertisement_positions_id" filterable @on-change="positionHasChanged" placeholder="请选择广告位">
              <Option v-for="(item,key) in advertisementPositionsIds" :value="item.id" :key="key">{{ item.name }} </Option>
            </Select>
        </FormItem>
        <FormItem label="广告标题" prop="name">
          <Input v-model="formData.name" />
        </FormItem>
        <FormItem label="是否启用：">
          <RadioGroup v-model="formData.enable">
            <Radio label="F">禁用</Radio>
            <Radio label="T">启用</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="封面：">
          <upload v-model="formData.cover_image" :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
        </FormItem>
        <FormItem label="描述：">
          <Input type="textarea" v-model="formData.descriptions" :rows="4" />
        </FormItem>
        <FormItem label="广告内容：">
          <editor v-model="formData.content" @on-change="editContentChange" :upload-config='wangUploadConfig'></editor>
        </FormItem>
      </Form>
      <Form label-position="right" :label-width="100">
        <FormItem label="链接地址：">
          <Input v-model="formData.link_url" placeholder="请输入链接地址如： http://lucms.com" />
        </FormItem>
        <FormItem label="排序：">
          <Input v-model="formData.weight" placeholder="请输入序号" />
        </FormItem>
        <FormItem label="有效期：">
          <DatePicker type="datetimerange" placement="bottom-end" placeholder="请选择有效期，不选永久有效" confirm @on-clear="timeClear" @on-change="timeChanged" style="width:50%"></DatePicker>
        </FormItem>
        <FormItem label="键值对选择：" v-if="typeIsModel">
          <transition name="publish-time">
            <div class="publish-time-picker-con">
              <div class="margin-top-10"> 模型 &nbsp;&nbsp;
                <Input type="text" size="small" style="width:80%" v-model="formData.model_column_value.model" placeholder="如：App\Models\Article" />
              </div>
              <div class="margin-top-10"> 字段 &nbsp;&nbsp;
                <Input type="text" size="small" style="width:80%" v-model="formData.model_column_value.column" placeholder="如：slug" />
              </div>
              <div class="margin-top-10"> 字段值
                <Input type="text" size="small" style="width:80%" v-model="formData.model_column_value.value" placeholder="mark-down-preview" />
              </div>
            </div>
          </transition>
        </FormItem>
      </Form>
      </Col>
    </Row>

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
} from '@/api/advertisement'

import Editor from '_c/editor'
import Upload from '_c/common/upload'
export default {
  components: {
    Editor,
    Upload
  },
  props: {
    advertisementPositionsIds: {
      default: {}
    }
  },
  data() {
    return {
      modalShow: true,
      saveLoading: false,
      formData: {
        name: '',
        enable: 'F',
        advertisement_positions_id: 0,
        advertisement_positions_type: '',
        model_column_value: {
          model: '',
          column: '',
          value: '',
        },
        link_url: '',
        start_at: '',
        end_at: '',
        weight: 20,
        descriptions: '',
        content: '',
        cover_image: {
          attachment_id: 0,
          url: ''
        }
      },
      wangUploadConfig: {
        headers: {
          'Authorization': window.access_token
        },
        wang_size: 1 * 1024 * 1024, // 1M
        uploadUrl: window.uploadUrl.uploadWang,
        params: {},
        max_length: 3,
        file_name: 'file',
        z_index: 10000,
        heightStyle: 'wang-editor-text-300'
      },
      imguploadConfig: {
        headers: {
          'Authorization': window.access_token
        },
        format: ['jpg', 'jpeg', 'png', 'gif'],
        max_size: 800, // 800KB
        upload_url: window.uploadUrl.uploadAdvertisement,
        file_name: 'file',
        multiple: false,
        file_num: 1,
        default_list: []
      },
      rules: {
        name: [{
          required: true,
          message: '请填写广告标题',
          trigger: 'blur'
        }],
      },
    }
  },
  computed: {
    typeIsModel() {
      return (this.formData.advertisement_positions_type == 'model') ? true : false
    },
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
            t.saveLoading = false
          })
        }
      })
    },
    positionHasChanged() {
      let t = this
      var key = t.formData.advertisement_positions_id
      t.formData.advertisement_positions_type = t.advertisementPositionsIds[key].type
    },
    timeChanged: function(value, date_type) {
      let t = this
      t.formData.start_at = value[0]
      t.formData.end_at = value[1]
    },
    timeClear() {
      let t = this
      t.formData.start_at = ''
      t.formData.end_at = ''
    },
    cancel() {
      this.$emit('on-add-modal-hide')
      this.modalShow = false
    },
    editContentChange(html, text) {
      // console.log(this.formData.content)
    },
    uploadChange(fileList, formatFileList) {}

  },
}
</script>
