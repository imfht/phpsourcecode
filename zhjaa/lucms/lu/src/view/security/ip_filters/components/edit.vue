<template>
<div>
  <Modal v-model="modalShow" :closable='false' :mask-closable=false width="600">
    <p slot="header">修改</p>
    <Form ref="formData" :model="formData" :rules="rules" label-position="left" :label-width="100">
      <FormItem label="类型" prop="type">
        <Select v-model="formData.type" filterable placeholder="请选择类型">
          <Option v-for="(item,key) in types" :value="key" :key="key">{{ item }}</Option>
        </Select>
      </FormItem>
      <FormItem label="ip" prop="ip">
        <Input v-model="formData.ip" placeholder="请输入ip"></Input>
      </FormItem>
    </Form>
    <div slot="footer">
      <Button type="text" @click="cancel">取消</Button>
      <Button type="primary" @click="addEditExcute" :loading='saveLoading'>保存 </Button>
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
  addEdit,
  getInfoById
} from '@/api/ip-filter'

export default {
  props: {
    types: {
      type: Object,
      default: []
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
        id: 0,
        ip: '',
        type: ''
      },
      rules: {
        ip: [{
          required: true,
          message: '请填写ip',
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
          type: res_data.type,
          ip: res_data.ip
        }
        t.spinLoading = false;
      })

    },
    addEditExcute() {
      let t = this
      t.$refs.formData.validate((valid) => {
        if (valid) {
          t.saveLoading = true
          addEdit(t.formData).then(res => {
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
    }
  }
}
</script>
