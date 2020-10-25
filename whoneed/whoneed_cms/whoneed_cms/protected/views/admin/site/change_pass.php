<h2 class="contentTitle">密码修改</h2>


<div class="pageContent">

    <form method="post" action="/admin/site/changePass" onsubmit="return validateCallback(this)">
        <div class="pageFormContent nowrap" layoutH="97">

            <dl>
                <dt>原密码：</dt>
                <dd>
                    <input type="password" name="old_pass" class="required" />
                    <span class="info">必填</span>
                </dd>
            </dl>
            <dl>
                <dt>新密码：</dt>
                <dd>
                    <input type="password" name="new_pass" class="required" />
                    <span class="info">必填</span>
                </dd>
            </dl>
            <dl>
                <dt>新密码：</dt>
                <dd>
                    <input type="password" name="new_pass_agin" class="required" />
                    <span class="info">必填</span>
                </dd>
            </dl>
            <ul>
                <li><div class="buttonActive" style="margin: 10px;"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
                <li><div class="button" style="margin: 10px;"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
            </ul>
        </div>

        <div >

        </div>
    </form>

</div>


<script type="text/javascript">
    function customvalidXxx(element){
        if ($(element).val() == "xxx") return false;
        return true;
    }
</script>
