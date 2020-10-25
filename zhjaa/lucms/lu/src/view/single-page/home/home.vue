<template>
  <div>
    <Row :gutter="20">
      <i-col span="4" v-for="(infor, i) in inforCardData" :key="`infor-${i}`" style="height: 120px;">
        <infor-card shadow :color="infor.color" :icon="infor.icon" :icon-size="36">
          <count-to :end="infor.count" count-class="count-style" />
          <p>{{ infor.title }}</p>
        </infor-card>
      </i-col>
    </Row>
    <Row style="margin-top: 20px;">
      <div class="demo-spin-container" v-if="tableLoading">
        <Spin fix>
          <Icon type="load-c" size=18 class="spin-icon-load"></Icon>
          <div>加载中...</div>
        </Spin>
      </div>
      <Card shadow>
        <div style='width:100%'>
          <Icon style="margin:0 auto" type="logo-android" size="50" color='red' v-if="unread_message_count" />
          <Icon style="margin:0 auto" type="logo-android" size="50" color='green' v-else />
        </div>
        <Alert type="error" v-if="unread_message_count">您有 {{ unread_message_count }} 条未读消息，<a href="https://fbdxt.gooneplusone.com/dashboard#/admin-messages">点击前往查看</a></Alert>
        <Alert type="success" v-else>暂时没有未处理的消息</Alert>
      </Card>
    </Row>

  </div>
</template>

<script>
    import InforCard from '_c/info-card'
    import CountTo from '_c/count-to'
    import {
        ChartPie,
        ChartBar
    } from '_c/charts'
    import Example from './example.vue'

    import {
        getStatisticsData
    } from '@/api/home'
    export default {
        name: 'home',
        components: {
            InforCard,
            CountTo,
            ChartPie,
            ChartBar,
            Example
        },
        data() {
            return {
                tableLoading: true,
                inforCardData: {},
                unread_message_count: 0,
            }
        },
        mounted() {
            this.getStatisticsDataExcute()
        },
        methods: {
            getStatisticsDataExcute() {
                let t = this
                getStatisticsData().then(res => {
                    t.tableLoading = false
                    let res_data = res.data
                    t.unread_message_count = res_data.unread_message
                    t.inforCardData = [{
                        title: '充电桩',
                        icon: 'md-person-add',
                        count: 300,
                        color: '#2d8cf0'
                    },{
                        title: '车辆',
                        icon: 'md-person-add',
                        count: 3000,
                        color: '#2d8cf0'
                    }, ]
                })
            },
        }
    }
</script>

<style lang="less">
  .count-style {
    font-size: 50px;
  }
</style>

