<template>
  <div id="city-detail">
    <x-header>{{headerTitle}}</x-header>
    <divider v-text="getStatisticsTitle()"></divider>

    <divider>确诊、治愈及死亡趋势图</divider>
    <v-chart :data="cityDateSpan" @on-render="renderVChart">
      <v-scale x type="timeCat" mask="MM/DD" :tick-count="10" />
      <v-line series-field="type" shape="smooth" :colors="cityDateSpanColors" />
    </v-chart>

  </div>
</template>

<script>
import { XHeader, dateFormat } from "vux";
import { VChart, VLine, VScale } from "vux";
import axios from "axios";
import api from "@/utils/api";

export default {
  name: "AreaDetail",
  components: {
    VChart,
    VLine,
    VScale,
    XHeader,
  },
  data: () => {
    return {
      headerTitle: "",
      cityData: {},
      apiDate: {
        begin: "2020-01-28",
        end: dateFormat(new Date(), "YYYY-MM-DD")
      },
      cityDateSpan: [
        { time: "2016-08-08 00:30:00", value: 0, type: "确诊病例" }
      ],
      cityDateSpanColors: ["#e81123", "#00cc6a", "#4c4a48"],
    };
  },
  mounted() {
    let cityData = (this.cityData = JSON.parse(
      sessionStorage.getItem("cityDetail")
    ));
    if(null === cityData)
    {
      this.$router.replace('/');
    }
    this.headerTitle = cityData.city_name;
    this.loadData();
  },
  methods: {
    loadData() {
      let cityDateSpan = axios.get(api.url("cityDateSpan"), {
        params: {
          parentId: this.cityData.parent_id,
          cityName: this.cityData.city_name,
          beginDate: this.apiDate.begin,
          endDate: this.apiDate.end,
        },
      });
      axios
        .all([cityDateSpan])
        .then(
          axios.spread((cityDateSpanR) => {
            this.parseCityDateSpan(cityDateSpanR.data.list);
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
        dateFormat(this.cityData.modify_time, "YYYY-MM-DD HH:mm:ss")
      );
    },
    parseCityDateSpan(data) {
      const map = {
        confirmed_count: "确诊病例",
        cured_count: "治愈人数",
        dead_count: "死亡人数",
      };
      let cityDateSpan = [];
      for (let item of data) {
        for (let k in map) {
          cityDateSpan.push({
            time: item.date,
            value: item[k],
            type: map[k]
          });
        }
      }
      this.cityDateSpan = cityDateSpan;
    },
  }
};
</script>

<style lang="less">
@import "~@/assets/style/style.less";
#city-detail {
}
</style>