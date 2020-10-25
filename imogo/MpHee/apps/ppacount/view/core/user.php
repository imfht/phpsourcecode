<?php print_r($us);?>
<!--必须首先引入该js库-->
<script src="https://publicexprod.alipay.com/deliveraddress/selectAddress.js"></script>

<button id="selectAddress" type='button' class="am-button am-button-blue">
    <h1>select</h1>
</button>

<script>
    document.getElementById('selectAddress').addEventListener('click', function () {
        // 调用am.selectAddress接口并传入回调函数
        /*am.selectAddress(function (data) {
            alert(JSON.stringify(data));
        })*/

    }, false);
	
	// 隐藏右按钮
AlipayJSBridge.call("hideOptionMenu");

// 设置标题
AlipayJSBridge.call("setTitle", {

    title: 'Hello',
    subtitle: '杭州'  //8.2
});
</script>