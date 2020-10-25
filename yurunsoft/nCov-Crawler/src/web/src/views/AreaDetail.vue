<template>
  <div id="area-detail">
    <x-header>{{headerTitle}}</x-header>
    <divider v-text="getStatisticsTitle()"></divider>

    <divider>确诊、治愈及死亡趋势图</divider>
    <v-chart :data="areasDateSpan" @on-render="renderVChart">
      <v-scale x type="timeCat" mask="MM/DD" :tick-count="10" />
      <v-line series-field="type" shape="smooth" :colors="areasDateSpanColors" />
    </v-chart>

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
          <tr v-for="item in tableData.cities" @click="showCityDetail(item)">
            <td>
              <span v-text="item.city_name"></span>
              <x-icon type="ios-arrow-forward" size="24"></x-icon>
            </td>
            <td v-text="item.confirmed_count"></td>
            <td v-text="item.cured_count"></td>
            <td v-text="item.dead_count"></td>
          </tr>
        </tbody>
      </x-table>
    </div>
  </div>
</template>

<script>
import { XTable, XHeader, dateFormat } from "vux";
import { VChart, VLine, VScale } from "vux";
import axios from "axios";
import api from "@/utils/api";

export default {
  name: "AreaDetail",
  components: {
    VChart,
    VLine,
    VScale,
    XTable,
    XHeader,
  },
  data: () => {
    return {
      headerTitle: "",
      tableData: {},
      apiDate: {
        begin: "2020-01-28",
        end: dateFormat(new Date(), "YYYY-MM-DD")
      },
      areasDateSpan: [
        { time: "2016-08-08 00:30:00", value: 0, type: "确诊病例" }
      ],
      areasDateSpanColors: ["#e81123", "#00cc6a", "#4c4a48"],
    };
  },
  mounted() {
    let tableData = (this.tableData = JSON.parse(
      sessionStorage.getItem("areaDetail")
    ));
    if(null === tableData)
    {
      this.$router.replace('/');
    }
    this.headerTitle = tableData.province_name;
    this.loadData();
  },
  methods: {
    loadData() {
      let areasDateSpan = axios.get(api.url("areasDateSpan"), {
        params: {
          countryType: this.tableData.country_type,
          provinceName: this.tableData.province_name,
          beginDate: this.apiDate.begin,
          endDate: this.apiDate.end,
        },
      });
      axios
        .all([areasDateSpan])
        .then(
          axios.spread((areasDateSpanR) => {
            this.parseAreasDateSpan(areasDateSpanR.data.list);
            this.$vux.loading.hide();
          })
        )
        .catch(error => {
          this.$vux.toast.text(error, "middle");
          this.$vux.loading.hide();
        });
    },
    renderVChart({ chart }) {
      chart.tooltip({
        showTitle: true,
        showCrosshairs: true
      });
    },
    getStatisticsTitle() {
      return (
        "最后更新：" +
        dateFormat(this.tableData.modify_time, "YYYY-MM-DD HH:mm:ss")
      );
    },
    parseAreasDateSpan(data) {
      const map = {
        confirmed_count: "确诊病例",
        cured_count: "治愈人数",
        dead_count: "死亡人数",
      };
      let areasDateSpan = [];
      for (let item of data) {
        for (let k in map) {
          areasDateSpan.push({
            time: item.date,
            value: item[k],
            type: map[k]
          });
        }
      }
      this.areasDateSpan = areasDateSpan;
    },
    showCityDetail(data) {
      sessionStorage.setItem('cityDetail', JSON.stringify(data));
      this.$router.push('cityDetail');
    }
  }
};
</script>

<style lang="less">
@import "~@/assets/style/style.less";
#area-detail {
}
</style>