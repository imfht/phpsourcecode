<form action="index.php?step=2" method="post" name="theform" id="theform">
    <div class="install">
        <h1>注册协议</h1>
        <textarea>版权所有 (C)2012，Yuncart.com 保留所有权利。

Yuncart是由Yuncart项目组独立开发的程序，基于PHP脚本和MySQL数据库。本程序源码开放的，任何人都可以从互联网上免费下载，并可以在不违反本协议规定的前提下进行使用而无需缴纳程序使用费。

为了使你正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：

一、本授权协议适用且仅适用于Yuncart任何版本，Yuncart官方拥有对本授权协议的最终解释权和修改权。

二、协议许可的权利和限制
1、您可以在完全遵守本最终用户授权协议的基础上，将本软件应用于非商业用途，而不必支付软件版权授权费用，但我们也不承诺对个人用户提供任何形式的技术支持。
2、您可以在协议规定的约束和限制范围内修改Yuncart源代码或界面风格以适应您的网站要求，但不可以公开对外发布。
3、您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。
4、未经商业授权，不得将本软件用于商业用途(企业网站或以盈利为目的经营性网站)，否则我们将保留追究的权力。

三、免责声明
1、本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。
2、用户出于自愿而使用本软件，您必须了解使用本软件的风险，任何情况下，程序的质量风险和性能风险完全由您承担。有可能证实该程序存在漏洞，您需要估算与承担所有必需服务，恢复，修正，甚至崩溃所产生的代价！在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
3、请务必仔细阅读本授权协议，在您同意授权协议的全部条件后，即可继续Yuncart的安装。即：您一旦开始安装Yuncart，即被视为完全同意本授权协议的全部内容，如果出现纠纷，我们将根据相关法律和协议条款追究责任。

        </textarea>	
        <div class="btn">
            <p>
                <input type="checkbox" value="1" name="copyright" id="copyright" autocomplete="off" checked/>
                <em>我已看过并同意安装许可协议</em>
            </p>
            <p>
                <input type="submit" class="long-button" value="下一步" id="next"/>
            </p>
        </div>
    </div>	
</form>
<script type="text/javascript">
    $("#copyright").click(function () {
        $("#next").prop("disabled", !$(this).prop("checked"));
    });
</script>