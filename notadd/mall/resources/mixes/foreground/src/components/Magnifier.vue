<script>
    export default {
//        父组件传进的参数(7个：外层包裹元素的坐标、盒子大小和间距、缩放比、图片地址)
        props: {
//             外层包裹元素的坐标
            wrapX: {
                type: Number,
                default: 0,
            },
            wrapY: {
                type: Number,
                default: 0,
            },
//             两个盒子的大小(包裹 原图和放大图 的盒子)
            boxWidth: {
                type: Number,
                default: 0,
            },
            boxHeight: {
                type: Number,
                default: 0,
            },
//             缩放比例: 浮动盒子和大图相对于 两个盒子的 缩放比
            scale: {
                type: Number,
                default: 1,
            },
//             图片地址
            imgSrc: {
                type: String,
                default: '',
            },
//             两个盒子的间距
            gap: {
                type: Number,
                default: 0,
            },
        },
        data() {
            return {
                show: false, /* 控制浮动盒子和大图的显示 */
                floatBoxLeft: 0, /* 浮动盒子的坐标(随鼠标移动) */
                floatBoxTop: 0,
                bigImgLeft: 0, /* 放大图的坐标(随鼠标移动) */
                bigImgTop: 0,
            };
        },
//         计算属性(通过父组件传入的属性计算的结果,5个计算属性：浮动盒子的大小、放大盒子的left、放大图的大小)
        computed: {
            floatBoxWidth() {
                return this.boxWidth / this.scale;
            },
            floatBoxHeight() {
                return this.boxHeight / this.scale;
            },
            bigBoxLeft() {
                return this.boxWidth + this.gap;
            },
            bigImgWidth() {
                return this.boxWidth * this.scale;
            },
            bigImgHeigth() {
                return this.boxHeight * this.scale;
            },
        },
        methods: {
//             鼠标移入、移出、移动事件,绑定在同一个元素#mark上
//             mouseover事件,不是mouseenter
            mouseOver() {
                this.show = true;
            },
            mouseLeave() {
                this.show = false;
            },
            mouseMove(event) {
                const that = this;
                const ev = event || window.event;
                let floatBoxLeft = ev.clientX - that.wrapX - (that.floatBoxWidth / 2);
                let floatBoxTop = ev.pageY - that.wrapY - (that.floatBoxHeight / 2);
//                 处理鼠标移动到边界情况
                if (floatBoxLeft < 0) {
                    floatBoxLeft = 0;
                } else if (floatBoxLeft > that.boxWidth - that.floatBoxWidth) {
                    floatBoxLeft = (that.boxWidth - that.floatBoxWidth);
                }
                if (floatBoxTop < 0) {
                    floatBoxTop = 0;
                } else if (floatBoxTop > that.boxHeight - that.floatBoxHeight) {
                    floatBoxTop = that.boxHeight - that.floatBoxHeight;
                }
//                 浮动盒子的坐标
                that.floatBoxLeft = floatBoxLeft;
                that.floatBoxTop = floatBoxTop;
//                 放大图的坐标
                that.bigImgLeft = -that.scale * floatBoxLeft;
                that.bigImgTop = -that.scale * floatBoxTop;
            },
        },
    };
</script>
<style>
    * {
        margin: 0;
        padding: 0;
    }

    #magnifier, #mark, #floatBox, #smallImg, #smallBox, #bigBox, #bigImg {
        position: absolute;
        z-index: 500;
    }

    #smallBox, #mark {
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        z-index: 505;
    }

    #floatBox {
        background-color: #fff;
        opacity: 0.5;
        z-index: 503;
    }

    #smallImg {
        left: 0;
        top: 0;
        z-index: 501;
    }

    #bigBox {
        top: 0px;
        overflow: hidden;
    }

    .magnifier-transition {
        border: 1px solid #DEDEDE;
        border-radius: 2px;
        transition: all .5s ease;
    }

    .magnifier-enter, .magnifier-leave {
        opacity: 0;
        height: 0;
    }
</style>
<template>
    <div id="magnifier" :style="{'left':wrapX + 'px','top':wrapY + 'px'}">
        <div id="smallBox" :style="{'width':boxWidth + 'px','height':boxHeight + 'px'}">
            <div id="mark" @mouseover="mouseOver" @mouseleave="mouseLeave" @mousemove="mouseMove"></div>
            <div id="floatBox"
                 :style="{'left':floatBoxLeft + 'px','top':floatBoxTop + 'px','width':floatBoxWidth + 'px','height':floatBoxHeight + 'px'}"
                 v-show='show'></div>
            <img id="smallImg" :style="{'width':boxWidth + 'px','height':boxHeight + 'px'}" :src="imgSrc">
        </div>
        <div id="bigBox" :style="{'width':boxWidth + 'px','height':boxHeight + 'px','left':bigBoxLeft+'px'}"
             v-show='show' transition="magnifier" transition-mode="out-in">
            <img id="bigImg"
                 :style="{'left':bigImgLeft + 'px','top':bigImgTop + 'px','width':bigImgWidth + 'px','height':imgSrc + 'px'}"
                 :src="imgSrc">
        </div>
    </div>
</template>