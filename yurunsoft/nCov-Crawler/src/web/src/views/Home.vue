<template>
  <div id="app">
    <divider>全国新型冠状病毒肺炎疫情实时动态</divider>
    <card :header="{title: getStatisticsTitle()}">
      <div slot="content" class="card-demo-flex card-demo-content01">
        <div class="vux-1px-r">
          <span class="confirmed" v-text="statistics.confirmedCount"></span>
          <br />确诊病例
        </div>
        <div class="vux-1px-r">
          <span class="suspected" v-text="statistics.suspectedCount"></span>
          <br />疑似病例
        </div>
        <div class="vux-1px-r">
          <span class="cured" v-text="statistics.curedCount"></span>
          <br />治愈人数
        </div>
        <div>
          <span class="dead" v-text="statistics.deadCount"></span>
          <br />死亡人数
        </div>
      </div>
    </card>

    <divider>全国病例新增趋势图</divider>
    <v-chart :data="statisticsDateSpanA" @on-render="renderVChart">
      <v-scale x type="timeCat" mask="MM/DD" :tick-count="10" />
      <v-area series-field="type" shape="smooth" :colors="statisticsDateSpanAColors" />
      <v-line series-field="type" shape="smooth" :colors="statisticsDateSpanAColors" />
    </v-chart>

    <divider>全国治愈及死亡趋势图</divider>
    <v-chart :data="statisticsDateSpanB" @on-render="renderVChart">
      <v-scale x type="timeCat" mask="MM/DD" :tick-count="10" />
      <v-line series-field="type" shape="smooth" :colors="statisticsDateSpanBColors" />
    </v-chart>

    <divider>全国各省数据</divider>
    <div class="data-table-box">
      <x-table full-bordered>
        <thead>
          <tr>
            <th>地区</th>
            <th>确诊</th>
            <th>治愈</th>
            <th>死亡</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in areasData.china" :key="item.province_id" @click="showAreaDetail(item)">
            <td>
              <span v-text="item.province_short_name"></span>
              <x-icon type="ios-arrow-forward" size="24"></x-icon>
            </td>
            <td v-text="item.confirmed_count"></td>
            <td v-text="item.cured_count"></td>
            <td v-text="item.dead_count"></td>
          </tr>
        </tbody>
      </x-table>
    </div>

    <divider>国外数据</divider>
    <div class="data-table-box">
      <x-table full-bordered>
        <thead>
          <tr>
            <th style="8em">地区</th>
            <th>确诊</th>
            <th>治愈</th>
            <th>死亡</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in areasData.foreign" :key="item.province_id" @click="showAreaDetail(item)">
            <td>
              <span v-text="item.province_name"></span>
              <x-icon type="ios-arrow-forward" size="24"></x-icon>
            </td>
            <td v-text="item.confirmed_count"></td>
            <td v-text="item.cured_count"></td>
            <td v-text="item.dead_count"></td>
          </tr>
        </tbody>
      </x-table>
    </div>

    <divider class="foot-text-1">数据来源：丁香园</divider>
    <divider class="foot-text-1">获取开源代码：<a href="https://gitee.com/yurunsoft/nCov-Crawler" target="_blank">https://gitee.com/yurunsoft/nCov-Crawler</a></divider>

  </div>
</template>

<script>
import { Card, dateFormat, XTable } from "vux";
import { VChart, VLine, VScale, VArea } from "vux";
import axios from "axios";
import api from "@/utils/api";

export default {
  name: "home",
  components: {
    Card,
    VChart,
    VLine,
    VScale,
    VArea,
    XTable
  },
  data() {
    return {
      statistics: {
        id: 0,
        createTime: 0,
        modifyTime: 0,
        infectSource: "",
        passWay: "",
        imgUrl: "",
        dailyPic: "",
        summary: "",
        countRemark: "",
        confirmedCount: 0,
        suspectedCount: 0,
        curedCount: 0,
        deadCount: 0,
        virus: "",
        remark1: "",
        remark2: "",
        remark3: "",
        remark4: "",
        remark5: "",
        generalRemark: "",
        abroadRemark: ""
      },
      statisticsDate: {
        begin: "2020-01-28",
        end: dateFormat(new Date(), "YYYY-MM-DD")
      },
      statisticsDateSpanA: [
        { time: "2016-08-08 00:30:00", value: 0, type: "确诊病例" }
      ],
      statisticsDateSpanAColors: ["#e81123", "#ee600c"],
      statisticsDateSpanB: [
        { time: "2016-08-08 00:30:00", value: 0, type: "确诊病例" }
      ],
      statisticsDateSpanBColors: ["#00cc6a", "#4c4a48"],
      areasData: {
        china: [],
        foreign: []
      },
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      this.$vux.loading.show({
        text: "数据加载中"
      });

      let statistics = axios.get(api.url("statistics"));
      let statisticsDateSpan = axios.get(api.url("statisticsDateSpan"), {
        params: {
          beginDate: this.statisticsDate.begin,
          endDate: this.statisticsDate.end
        }
      });
      let areas = axios.get(api.url("areas"));

      axios
        .all([statistics, statisticsDateSpan, areas])
        .then(
          axios.spread((statisticsR, statisticsDateSpanR, areasR) => {
            this.statistics = statisticsR.data.data;
            this.parseStatisticsDateSpan(statisticsDateSpanR.data.list);
            this.areasData = areasR.data;
            this.$vux.loading.hide();
          })
        )
        .catch(error => {
          this.$vux.toast.text(error, "middle");
          this.$vux.loading.hide();
        });
    },
    getStatisticsTitle() {
      return (
        "最后更新：" +
        dateFormat(this.statistics.modifyTime, "YYYY-MM-DD HH:mm:ss")
      );
    },
    parseStatisticsDateSpan(data) {
      const mapA = {
        confirmedCount: "确诊病例",
        suspectedCount: "疑似病例"
      };
      const mapB = {
        curedCount: "治愈人数",
        deadCount: "死亡人数"
      };
      let statisticsDateSpanA = [],
        statisticsDateSpanB = [];
      for (let item of data) {
        for (let k in mapA) {
          statisticsDateSpanA.push({
            time: item.date,
            value: item[k],
            type: mapA[k]
          });
        }
        for (let k in mapB) {
          statisticsDateSpanB.push({
            time: item.date,
            value: item[k],
            type: mapB[k]
          });
        }
      }
      this.statisticsDateSpanA = statisticsDateSpanA;
      this.statisticsDateSpanB = statisticsDateSpanB;
    },
    renderVChart({ chart }) {
      chart.tooltip({
        showTitle: true,
        showCrosshairs: true
      });
    },
    showAreaDetail(data) {
      sessionStorage.setItem('areaDetail', JSON.stringify(data));
      this.$router.push('areaDetail');
    }
  }
};
</script>

<style lang="less">
@import "~@/assets/style/style.less";
#app {
  .card-demo-flex {
    display: flex;
  }
  .card-demo-content01 {
    padding: 10px 0;
  }
  .card-padding {
    padding: 15px;
  }
  .card-demo-flex > div {
    flex: 1;
    text-align: center;
    font-size: 12px;
  }
  .card-demo-flex span {
    font-size: 16px;
    font-weight: bold;
    line-height: 32px;
    &.confirmed {
      color: @confirmed;
    }
    &.suspected {
      color: @suspected;
    }
    &.cured {
      color: @cured;
    }
    &.dead {
      color: @dead;
    }
  }
  .foot-text-1 {
    font-size: 12px;
    color: #767676;
  }
}
</style>
