<template>
    <div v-bind:style="styles" class="spinner spinner--spin-line">
        <div v-bind:style="lineStyles" class="spinner-inner">
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            size: {
                default: '40px',
            },
            color: {
                default: '#de2634',
            },
            stroke: {
                default: '6px',
            },
        },
        computed: {
            lineStyles() {
                return {
                    width: this.size,
                    height: this.stroke,
                    background: this.color,
                    borderRadius: this.stroke,
                };
            },
            styles() {
                return {
                    width: '100%',
                    height: this.size,
                };
            },
        },
    };
</script>
<style lang="less" scoped>
    .spinner--spin-line {
        background: rgba(255, 255, 255, 0.5);
    }
    .spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        height: 100%;
        z-index: 600;
        top: 0;
        * {
            line-height: 0;
            box-sizing: border-box;
        }
    }
    .spinner-inner {
        transform-origin: center center;
        animation: spin-line 4s ease infinite;
    }
    @keyframes spin-line {
        0% {
            transform: rotate(-20deg);
            height: 4px;
            width: 64px;
        }
        5% {
            height: 4px;
            width: 64px;
        }
        30% {
            transform: rotate(380deg);
            height: 4px;
            width: 64px;
        }
        40% {
            transform: rotate(360deg);
            height: 4px;
            width: 64px;
        }
        55% {
            transform: rotate(0deg);
            height: 4px;
            width: 64px;
        }
        65% {
            transform: rotate(0deg);
            height: 4px;
            width: 64px;
        }
        68% {
            transform: rotate(0deg);
            height: 4px;
        }
        75% {
            transform: rotate(0deg);
            height: 4px;
            width: 1px;
        }
        78% {
            height: 4px;
            width: 5px;
        }
        90% {
            height: 4px;
            width: 64px;
            transform: rotate(0deg);
        }
        99%, 100% {
            height: 4px;
            width: 64px;
            transform: rotate(-20deg);
        }
    }
</style>