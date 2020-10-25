<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false fullscreen>
    <p slot="header">修改</p>
    <Row>
      <Col span="16">
      <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
        <FormItem label="分类：">
          <Select v-model="formData.category_id" filterable placeholder="请选择文章分类">
                <Option v-for="(item,key) in articleCategories" :value="item.id" :key="key">{{ item.name }} </Option>
            </Select>
        </FormItem>
        <FormItem label="标题：" prop="title">
          <Input v-model="formData.title"></Input>
        </FormItem>
        <FormItem label="封面：">
          <upload v-if='formdataFinished' v-model="formData.cover_image" :is-delete='false' :upload-config="imguploadConfig" @on-upload-change='uploadChange'></upload>
        </FormItem>
        <FormItem label="是否启用：">
          <RadioGroup v-model="formData.enable">
            <Radio label="F">禁用</Radio>
            <Radio label="T">启用</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="关键词：" prop="keywords">
          <Input type="textarea" v-model="formData.keywords" placeholder="以英文逗号隔开"></Input>
          <input-helper text="以英文逗号隔开"></input-helper>
        </FormItem>
        <FormItem label="描述：" prop="description">
          <Input type="textarea" v-model="formData.descriptions" placeholder="请输入"></Input>
        </FormItem>
        <FormItem label="文章内容：">
          <markdown-editor v-if="formdataFinished" :cache="false" v-model="formData.content" :value="formData.content" />
        </FormItem>
      </Form>
      </Col>

      <Col span="8" class="padding-left-20">
      <Card>
        <p slot="title">
          <Icon type="paper-airplane"></Icon>
          其它信息
        </p>
        <Form label-position="right" :label-width="80">
          <FormItem label="排序：">
            <Input v-model="formData.weight" placeholder="请输入序号"></Input>
          </FormItem>
          <FormItem label="置顶：">
            <Select size="small" style="width:20%" v-model="formData.top">
              <Option value="F">否</Option>
              <Option value="T">是</Option>
            </Select>
          </FormItem>
          <FormItem label="推荐：">
            <Select size="small" style="width:20%" v-model="formData.recommend">
              <Option value="F">否</Option>
              <Option value="T">是</Option>
            </Select>
          </FormItem>
          <FormItem label="公开度：">
            <Icon type="eye"></Icon><b>{{ Openness }}</b>
            <Button v-show="!editOpenness" size="small" type="text" @click="handleEditOpenness"><a>修改</a></Button>
            <transition name="openness-con">
              <div v-show="editOpenness" class="publish-time-picker-con">
                <RadioGroup v-model="formData.access_type" vertical>
                  <Radio label="PUB"> 公开</Radio>
                  <Radio label="PWD"> 密码
                    <Input v-show="formData.access_type === 'PWD'" v-model="formData.access_value" style="width:50%" size="small" placeholder="请输入密码" />
                  </Radio>
                  <Radio label="PRI">私密</Radio>
                </RadioGroup>
                <div>
                  <Button type="primary" @click="handleSaveOpenness">确认</Button>
                </div>
              </div>
            </transition>
          </FormItem>
          <FormItem label="标签：">
            <Select v-model="formData.tags" multiple filterable placeholder="请选择文章标签">
                <Option v-for="item in articleTags" :value="item.id" :key="item.id">{{ item.name }} </Option>
            </Select>
          </FormItem>
          <FormItem label="新建标签">
            <Input v-model="newTagName" search enter-button="新建" placeholder="标签名字" @on-search="addEditExcute" />
          </FormItem>
        </Form>
      </Card>
      </Col>

    </Row>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="editExcute" :loading='saveLoading'>保存 </Button>
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
import {
  edit,
  getInfoById
} from '@/api/article'

import {
  addEdit,
  getTagList
} from '@/api/tag'

import Upload from '_c/common/upload'
import InputHelper from '_c/common/input-helper'
import MarkdownEditor from '_c/markdown'
export default {
  components: {
    Upload,
    InputHelper,
    MarkdownEditor
  },
  props: {
    articleCategories: {
      default: {}
    },
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
        title: '',
        cover_image: {
          attachment_id: 0,
          url: '',
        },
        enable: 'F',
        keywords: '',
        description: '',
        content: '',
        category_id: 0,
        weight: 20,
        top: 'F',
        recommend: 'F',
        access_type: 'PUB',
        access_value: ''
      },
      formdataFinished: false,
      editOpenness: false,
      Openness: '公开',
      newTagName: '',
      articleTags: {},
      rules: {
        title: [{
          required: true,
          message: '请填写文章标题',
          trigger: 'blur'
        }],
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
    }
  },
  mounted() {
    this.getTagListExcute()
    this.getInfoByIdExcute()
  },
  methods: {
    getTagListExcute() {
      let t = this;
      getTagList().then(res => {
        t.articleTags = res.data;
      })
    },
    getInfoByIdExcute() {
      let t = this;
      getInfoById(t.modalId).then(res => {
        let res_data = res.data
        t.formData = {
          id: res_data.id,
          title: res_data.title,
          cover_image: {
            attachment_id: res_data.cover_image.attachment_id,
            url: res_data.cover_image.url,
          },
          enable: res_data.enable,
          keywords: res_data.keywords,
          descriptions: res_data.descriptions,
          category_id: res_data.category_id,
          weight: res_data.weight,
          top: res_data.top,
          recommend: res_data.recommend,
          access_type: res_data.access_type,
          access_value: res_data.access_value
        }

        t.handleSaveOpenness();
        t.imguploadConfig.default_list = [t.formData.cover_image]
        t.formData.tags = res_data.tagids;
        t.formData.content = res_data.content.raw
        t.formdataFinished = true
        t.spinLoading = false
      })

    },
    editExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          edit(t.modalId, t.formData).then(res => {
            t.saveLoading = false
            t.modalShow = false
            t.$emit('on-edit-modal-success')
            t.$emit('on-edit-modal-hide')
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
      this.$emit('on-edit-modal-hide')
    },
    uploadChange(fileList, formatFileList) {},
    handleEditOpenness() {
      this.editOpenness = !this.editOpenness;
    },
    handleSaveOpenness() {
      var access_type = this.formData.access_type;
      if (this.passwordValidate()) {
        this.Openness = (access_type === 'PUB') ? '公开' : (access_type === 'PWD') ? '密码' : '私密';
        this.editOpenness = false;
      }
    },
    passwordValidate() {
      var access_type = this.formData.access_type;
      var access_value = this.formData.access_value;
      if (access_type === 'PWD') {
        var patt = /^[a-zA-Z0-9]{4,8}$/;
        if (!patt.test(access_value)) {
          this.$Notice.error({
            title: '出错了',
            desc: '密码只能是4到8位的数字与字母'
          });
          return false;
        }

      }
      return true;
    },
    addEditExcute() {
      let t = this;
      addEdit({
        name: t.newTagName
      }).then(res => {
        t.getTagListExcute()
        t.$Notice.success({
          title: res.message
        })
      })
    }
  }
}
</script>
