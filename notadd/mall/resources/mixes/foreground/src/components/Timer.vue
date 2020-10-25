<template>
    <p class="inherit">{{ time }}</p>
</template>

<script>
    export default{
        data() {
            return {
                time: '',
                flag: false,
                onOff: true,
            };
        },
        methods: {
            formate(time) {
                if (time >= 10) {
                    return time;
                }
                return `0${time}`;
            },
        },
        mounted() {
            const time = setInterval(() => {
                if (this.flag === true) {
                    clearInterval(time);
                }
                const endTime = new Date(this.endTime);
                const nowTime = new Date();
                if (nowTime > endTime) {
                    this.onOff = false;
                    this.$emit('mistake', this.onOff);
                    clearInterval(time);
                }
                const leftTime = parseInt((endTime.getTime() - nowTime.getTime()) / 1000, 10);
                const d = parseInt(leftTime / (24 * 60 * 60), 10);
                const h = this.formate(parseInt((leftTime / (60 * 60)) % 24, 10));
                const m = this.formate(parseInt((leftTime / 60) % 60, 10));
                const s = this.formate(parseInt(leftTime % 60, 10));
                if (leftTime <= 0) {
                    this.flag = true;
                    this.$emit('time-end');
                }
                this.time = `${d}天${h}小时${m}分${s}秒`;
            }, 500);
        },
        props: {
            endTime: {
                type: String,
            },
            misTake: {
                type: String,
                default: '已经超时',
            },
        },
    };
</script>
<style >
    .inherit{
        display: inherit;
    }
</style>