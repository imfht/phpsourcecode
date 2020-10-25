<script>
    export default {
        data() {
            return {
                noticeList: [
                    {
                        goods: 'Sony/索尼ILCE-5100L A5100单镜（16-50mm）微单数码相机..',
                        time: '2017-05-30  16:21:45',
                    },
                    {
                        goods: 'Sony/索尼ILCE-5100L A5100单镜（16-50mm）微单数码相机..',
                        time: '2017-05-30  16:21:45',
                    },
                    {
                        goods: 'Sony/索尼ILCE-5100L A5100单镜（16-50mm）微单数码相机..',
                        time: '2017-05-30  16:21:45',
                    },
                ],
            };
        },
        methods: {
            deleteNotice(index) {
                this.noticeList.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="notice">
        <h4>预约到货通知</h4>
        <ul class="list-head clearfix">
            <li class="table-li">商品</li>
            <li class="table-li">操作</li>
            <li class="table-li">添加时间</li>
        </ul>
        <ul class="notice-list">
            <li v-for="(notice,index) in noticeList">
                <ul class="clearfix">
                    <li class="pull-left table-li">{{ notice.goods }}</li>
                    <li class="pull-right table-li">
                        <a class="delete-btn" @click="deleteNotice(index)">删除</a>
                    </li>
                    <li class="pull-right table-li">{{ notice.time }}</li>
                </ul>
            </li>
        </ul>
    </div>
</template>